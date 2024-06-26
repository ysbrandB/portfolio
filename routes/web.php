<?php

use App\Http\Controllers\AttributeController;
use App\Http\Controllers\AttributeTypeController;
use App\Http\Controllers\GraphController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\QuestionController;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::resource('items', ItemController::class)->except(['show', 'index'])->middleware('auth');
Route::resource('items.photos', PhotoController::class)->only(['store', 'destroy'])->middleware('auth');
//item route that takes the hashid and returns the item
Route::get('/', static fn (Request $request) => (new ItemController())->index($request))->name('items.index');
Route::group(['prefix' => 'items'], static function (): void {
    Route::get('/item/{public_id}', static fn (string $publicId) => (new ItemController())->show($publicId))->name('items.show');
});

Route::resource('attribute_types', AttributeTypeController::class)->middleware('auth');
Route::resource('questions', QuestionController::class)->except(['show'])->middleware('auth');
Route::resource('attributes', AttributeController::class)->except(['show'])->middleware('auth');
Route::group(['prefix' => 'graph'], static function (): void {
    Route::get('/', static fn (Request $request) => (new GraphController())->index($request))->name('graph.index');
    Route::post('/syncSelected', static function (Request $request): void {
        (new GraphController())->syncSelected($request);
    })->name('graph.syncSelected');
});

Route::get('/dashboard', static fn () => Inertia::render('Dashboard'))->name('dashboard');

Route::middleware('auth')->group(static function (): void {
    Route::get('/profile', static fn (Request $request): \Inertia\Response => (new \App\Http\Controllers\ProfileController())->edit($request))->name('profile.edit');
    Route::patch('/profile', static fn (\App\Http\Requests\ProfileUpdateRequest $profileUpdateRequest): \Illuminate\Http\RedirectResponse => (new \App\Http\Controllers\ProfileController())->update($profileUpdateRequest))->name('profile.update');
    Route::delete('/profile', static fn (Request $request): \Illuminate\Http\RedirectResponse => (new \App\Http\Controllers\ProfileController())->destroy($request))->name('profile.destroy');
});

require __DIR__.'/auth.php';
