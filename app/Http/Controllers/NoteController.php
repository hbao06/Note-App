<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Note;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\NoteImage;   
use App\Models\Label;   
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Models\SharedNote;
use App\Events\NoteUpdated;
use App\Notifications\NoteSharedNotification;

class NoteController extends Controller
{
    public function index()
    {
        $notes = Note::with(['labels', 'sharedNotes'])
            ->where('user_id', Auth::id())
            ->orderByDesc('is_pinned')
            ->orderByDesc('pinned_at')
            ->orderByDesc('updated_at')
            ->get();

        // CHỈ lấy những nhãn mà chính User này đã tạo hoặc đang sử dụng
        $allLabels = Label::whereHas('notes', function ($q) {
            $q->where('user_id', Auth::id());
        })
        ->select('id', 'name')
        ->get();

        return view('notes.index', compact('notes', 'allLabels'));
    }

    // GIAO DIỆN EDITOR CHUNG (CREATE + EDIT)
    public function editor(Note $note = null)
    {
        if (!$note) {
            $canEdit = true;

            $view = request()->ajax()
                ? 'notes.partials.editor'
                : 'notes.editor';

            return view($view, [
                'note' => null,
                'canEdit' => $canEdit
            ]);
        }

        if ($note->note_password && !session("unlocked_note_{$note->id}")) {
            return response()->json([
                'locked' => true,
                'note_id' => $note->id
            ], 423);
        }

        $isOwner = $note->user_id === auth()->id();

        $share = \App\Models\SharedNote::where('note_id', $note->id)
            ->where('recipient_id', auth()->id())
            ->first();

        if (!$isOwner && !$share) {
            abort(403);
        }

        $canEdit = $isOwner || ($share && $share->permission === 'edit');

        $view = request()->ajax()
            ? 'notes.partials.editor'
            : 'notes.editor';

        return view($view, [
            'note' => $note,
            'canEdit' => $canEdit
        ]);
    }
    // =============== AUTOSAVE ===============
    public function autosave(Request $request)
    {
        if (!$request->id) {
            $note = Note::create([
                'user_id' => Auth::id(),
                'title' => $request->title ?? '',
                'content' => $request->content ?? '',
            ]);

            broadcast(new NoteUpdated($note, Auth::id()))->toOthers();

            return response()->json([
                'status' => 'created',
                'note_id' => $note->id
            ]);
        }

        $note = Note::findOrFail($request->id);

        $isOwner = $note->user_id === Auth::id();

        $shared = SharedNote::where('note_id', $note->id)
            ->where('recipient_id', Auth::id())
            ->where('permission', 'edit')
            ->first();

        $canEdit = $isOwner || $shared;

        if (!$canEdit) {
            return response()->json(['error' => 'No permission'], 403);
        }

        $note->update([
            'title' => $request->title ?? '',
            'content' => $request->content ?? '',
        ]);

        broadcast(new NoteUpdated($note, Auth::id()))->toOthers();

       return response()->json([
            'status' => 'updated',
            'note_id' => $note->id,
            'title' => $note->title,
            'content' => $note->content,
            'updated_at' => $note->updated_at->format('c'),
        ]);
    }
    // =======================================


    // CRUD GỐC — GIỮ NGUYÊN ĐỂ KHÔNG ẢNH HƯỞNG
    public function create()
    {
        return response()->json([
            'status' => 'created',
            'note_id' => $note->id,
            'title' => $note->title,
            'content' => $note->content,
            'updated_at' => $note->updated_at->format('c'),
        ]);
    }

    public function store(Request $request)
    {
        Note::create([
            'user_id' => Auth::id(),
            'title'   => $request->title,
            'content' => $request->content,
        ]);

        return redirect()->route('notes.index');
    }

    public function edit(Note $note)
    {
        return view('notes.edit', compact('note'));
    }

    // UPDATE
    public function update(Request $request, Note $note)
    {
        $isOwner = $note->user_id === auth()->id();

        $canEdit = \App\Models\SharedNote::where('note_id', $note->id)
            ->where('recipient_id', auth()->id())
            ->where('permission', 'edit')
            ->exists();

        if (!$isOwner && !$canEdit) {
            abort(403); // 🔥 chặn luôn
        }

        $note->update([
            'title' => $request->title,
            'content' => $request->content,
        ]);

        broadcast(new NoteUpdated($note, auth()->id()))->toOthers();

        return response()->json([
            'status' => 'updated',
            'note_id' => $note->id
        ]);
    }

    public function destroy(Note $note)
    {
        $note->delete();
        return redirect()->route('notes.index');
    }

    // UPLOAD MULTIPLE IMAGES
    public function uploadImages(Request $request, Note $note)
    {
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('note_images', 'public');

                $note->images()->create([
                    'image_path' => $path
                ]);
            }
        }

        return response()->json(['status' => 'ok']);
    }

    // DELETE IMAGE
    public function deleteImage(NoteImage $image)
    {
        // Xoá file
        Storage::disk('public')->delete($image->image_path);

        // Xoá record DB
        $image->delete();

        return response()->json(['status' => 'deleted']);
    }


    // PIN NOTE
    public function togglePin(Note $note)
    {
        $note->is_pinned = ! $note->is_pinned;
        $note->save();

        return response()->json([
            'is_pinned' => $note->is_pinned,
        ]);
    }

    // SEARCH
    public function search(Request $request)
    {
        $q = $request->q;

        $notes = Note::with('labels') // PHẢI CÓ thêm cái này
            ->where('user_id', Auth::id())
            ->where(function ($query) use ($q) {
                $query->where('title', 'like', "%$q%")
                    ->orWhere('content', 'like', "%$q%");
            })
            ->orderByDesc('is_pinned')
            ->orderByDesc('updated_at')
            ->get();

        return response()->json($notes);
    }
    // FILTER LABEL
    public function filter(Request $request)
    {
        $query = Note::where('user_id', Auth::id());

        if ($request->labels) {
            $query->whereHas('labels', function ($q) use ($request) {
                $q->whereIn('labels.id', $request->labels);
            });
        }

        $notes = $query
            ->orderByDesc('is_pinned')
            ->orderByDesc('updated_at')
            ->get();

        return response()->json($notes);
    }

    public function attachLabels(Request $request, Note $note)
    {
        Log::info('Attach labels', [
            'note_id' => $note->id,
            'labels' => $request->label_ids
        ]);

        $note->labels()->syncWithoutDetaching($request->label_ids);

        return response()->json([
            'status' => 'attached',
            'note_id' => $note->id,
            'labels' => $note->labels
        ]);
    }

    public function detachLabel($noteId, $labelId)
    {
        $note = Note::findOrFail($noteId);

        $note->labels()->detach($labelId);

        return response()->json([
            'status' => 'detached'
        ]);
    }

    // SET PASSWORD
    public function setPassword(Request $request, Note $note)
    {
        if ($note->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'password' => 'required|min:4|confirmed'
        ]);

        $note->update([
            'note_password' => Hash::make($request->password)
        ]);

        return response()->json(['status' => 'locked']);
    }

    public function verifyPassword(Request $request, Note $note)
    {
        if (!$note->note_password) {
            return response()->json(['status' => 'no_password']);
        }

        if (!Hash::check($request->password, $note->note_password)) {
            return response()->json(['error' => 'Wrong password'], 403);
        }

        // 🔥 QUAN TRỌNG: lưu session
        session(["unlocked_note_{$note->id}" => true]);

        return response()->json(['status' => 'success']);
    }

    public function removePassword(Request $request, Note $note)
    {
        if (!Hash::check($request->password, $note->note_password)) {
            return response()->json(['error' => 'Wrong password'], 403);
        }

        $note->update([
            'note_password' => null
        ]);

        // clear session
        session()->forget("unlocked_note_{$note->id}");

        return response()->json(['status' => 'unlocked']);
    }

    // SHARE NOTE
    public function share(Request $request, Note $note)
    {
        if ($note->user_id !== auth()->id()) {
            return response()->json([
                'message' => 'Bạn không có quyền chia sẻ ghi chú này.'
            ], 403);
        }

        $request->validate([
            'emails' => ['required', 'array', 'min:1'],
            'emails.*' => ['required', 'email'],
            'permission' => ['required', 'in:read,edit'],
        ]);

        $emails = collect($request->emails)
            ->map(fn ($email) => strtolower(trim($email)))
            ->filter()
            ->unique();

        $notFoundEmails = [];
        $sharedEmails = [];
        $skippedEmails = [];
        $unverifiedEmails = [];

        foreach ($emails as $email) {

            $user = \App\Models\User::where('email', $email)->first();

            // EMAIL KHÔNG TỒN TẠI

            if (!$user) {
                $notFoundEmails[] = $email;
                continue;
            }

            if (!$user->hasVerifiedEmail()) {
                $unverifiedEmails[] = $email;
                continue;
            }


            // SHARE CHÍNH MÌNH
            if ($user->id === auth()->id()) {
                $skippedEmails[] = $email;
                continue;
            }

            $share = SharedNote::updateOrCreate(
                [
                    'note_id' => $note->id,
                    'recipient_id' => $user->id
                ],
                [
                    'owner_id' => auth()->id(),
                    'permission' => $request->permission
                ]
            );

            $user->notify(new NoteSharedNotification($note, auth()->user()));

            $sharedEmails[] = $email;
        }

        return response()->json([
            'status' => 'shared',
            'shared' => $sharedEmails,
            'not_found' => $notFoundEmails,
            'unverified' => $unverifiedEmails,
            'skipped' => $skippedEmails,
        ]);
    }


    // SHARE WITH
    public function sharedWithMe()
    {
        auth()->user()->unreadNotifications
            ->where('type', 'App\Notifications\NoteSharedNotification')
            ->markAsRead();

        $shares = \App\Models\SharedNote::with(['note', 'owner'])
            ->where('recipient_id', auth()->id())
            ->latest()
            ->get();

        return view('notes.shared', [
            'shared' => $shares
        ]);
    }

    public function revokeShare(Note $note, $userId)
    {
        if ($note->user_id !== auth()->id()) {
            abort(403);
        }

        \App\Models\SharedNote::where('note_id', $note->id)
            ->where('recipient_id', $userId)
            ->delete();

        return response()->json(['status' => 'revoked']);
    }

    public function getShares(Note $note)
    {
        return \App\Models\SharedNote::with('recipient')
            ->where('note_id', $note->id)
            ->get();
    }

    public function updateSharePermission(Request $request, Note $note, $userId)
    {
        if ($note->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'permission' => 'required|in:read,edit'
        ]);

        SharedNote::where('note_id', $note->id)
            ->where('recipient_id', $userId)
            ->update([
                'permission' => $request->permission
            ]);

        return response()->json(['status' => 'updated']);
    }

}