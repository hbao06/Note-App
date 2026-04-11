<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\NoteController;


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
});

require __DIR__.'/auth.php';



