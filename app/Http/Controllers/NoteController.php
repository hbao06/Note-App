<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Note;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Storage;
use App\Models\NoteImage;   
use App\Models\Label;   
use Illuminate\Support\Facades\Log;

class NoteController extends Controller
{
    public function index()
    {
        $notes = Note::with('labels')
            ->where('user_id', Auth::id())
            ->orderByDesc('is_pinned')
            ->orderByDesc('pinned_at')
            ->orderByDesc('updated_at')
            ->get();

        // CHỈ lấy những nhãn mà chính User này đã tạo hoặc đang sử dụng
        $allLabels = Label::where('user_id', Auth::id())
            ->select('id', 'name')
            ->distinct()
            ->get();

        return view('notes.index', compact('notes', 'allLabels'));
    }

    // GIAO DIỆN EDITOR CHUNG (CREATE + EDIT)
    public function editor(Note $note = null)
    {
        return view('notes.editor', [
            'note' => $note
        ]);
    }

    // =============== AUTOSAVE ===============
    public function autosave(Request $request)
    {
        // Nếu chưa có note → tạo note mới
        if (!$request->id) {

            $newNote = Note::create([
                'user_id' => Auth::id(),
                'title' => $request->title ?? '',
                'content' => $request->content ?? '',
            ]);

            return response()->json([
                'status' => 'created',
                'note_id' => $newNote->id
            ]);
        }

        // Nếu đã có note → update
        $note = Note::where('id', $request->id)
            ->where('user_id', Auth::id()) // bảo mật
            ->firstOrFail();

        $note->update([
            'title' => $request->title ?? '',
            'content' => $request->content ?? '',
        ]);

        return response()->json([
            'status' => 'updated',
            'note_id' => $note->id
        ]);
    }
    // =======================================


    // CRUD GỐC — GIỮ NGUYÊN ĐỂ KHÔNG ẢNH HƯỞNG
    public function create()
    {
        return view('notes.create');
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

    public function update(Request $request, Note $note)
    {
        $note->update([
            'title'   => $request->title,
            'content' => $request->content,
        ]);

        return redirect()->route('notes.index');
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
        if ($note->is_pinned) {
            $note->update([
                'is_pinned' => false,
                'pinned_at' => null
            ]);
        } else {
            $note->update([
                'is_pinned' => true,
                'pinned_at' => now()
            ]);
        }

        return response()->json(['status' => 'ok']);
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
}