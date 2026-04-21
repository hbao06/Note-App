<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\NoteController;

use App\Models\Label;
use Illuminate\Http\Request;
use App\Models\Note;

use App\Http\Controllers\LabelController;





Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ================= NOTES CRUD =================
    Route::get('/notes', [NoteController::class, 'index'])->name('notes.index');
    Route::post('/notes', [NoteController::class, 'store'])->name('notes.store');
    Route::put('/notes/{note}', [NoteController::class, 'update'])->name('notes.update');
    Route::delete('/notes/{note}', [NoteController::class, 'destroy'])->name('notes.destroy');

    // ======== NEW unified editor (correct) ========
    Route::get('/notes/editor', [NoteController::class, 'editor'])->name('notes.editor');
    Route::get('/notes/editor/{note}', [NoteController::class, 'editor'])->name('notes.editor.edit');

    Route::post('/notes/autosave', [NoteController::class, 'autosave'])->name('notes.autosave');

    // Upload ảnh cho note
    Route::post('/notes/{note}/images', [NoteController::class, 'uploadImages'])
        ->name('notes.images.upload');
    // Xoá ảnh
    Route::delete('/notes/images/{image}', [NoteController::class, 'deleteImage'])
        ->name('notes.images.delete');
    
            // PIN NOTE
    Route::post('/notes/{note}/pin', [NoteController::class, 'togglePin'])
        ->name('notes.pin');

    // SEARCH
    Route::get('/notes/search', [NoteController::class, 'search'])->name('notes.search');

    // LABEL
    Route::post('/notes/{note}/labels', [NoteController::class, 'syncLabels']);
    Route::get('/labels', [LabelController::class, 'index']);
    Route::post('/labels', [LabelController::class, 'store']);
    Route::put('/labels/{label}', [LabelController::class, 'update']);
    Route::delete('/labels/{label}', [LabelController::class, 'destroy']);

    // FILTER LABEL
    Route::get('/notes/filter', [NoteController::class, 'filter']);

    Route::post('/notes/{note}/labels', [NoteController::class, 'attachLabels']);
    Route::delete('/notes/{note}/labels/{label}', [NoteController::class, 'detachLabel']);


    // PASSWORD NOTE
    Route::post('/notes/{note}/set-password', [NoteController::class, 'setPassword']);
    Route::post('/notes/{note}/verify-password', [NoteController::class, 'verifyPassword']);
    Route::post('/notes/{note}/remove-password', [NoteController::class, 'removePassword']);

    // route editor (đảm bảo đúng)
    Route::get('/notes/editor/{note}', [NoteController::class, 'editor']); 

    // SHARE WITH
    Route::get('/notes/shared', [NoteController::class, 'sharedWithMe'])
    ->name('notes.shared');

    Route::get('/notes/shared', [NoteController::class, 'sharedNotes'])
    ->name('notes.shared');
});
require __DIR__.'/auth.php';



