<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthorsController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\DocumentsController;
use App\Http\Controllers\GeneralSettingController;
use App\Http\Controllers\GenresController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\TechnologyController;
use App\Http\Middleware\CheckAuth;
use App\Http\Middleware\CheckUserRole;
use Illuminate\Support\Facades\Route;

/**
 * Public Routes (No authentication required)
 */

//  Route::group(['prefix' => 'author'], function () {
//     Route::post('/', [AuthorsController::class, 'saveAuthors'])->name('admin.author.save');
//     Route::get('/', [AuthorsController::class, 'getListAuthors'])->name('admin.author.list');
//     Route::get('/{id}', [AuthorsController::class, 'getOneAuthor'])->name('admin.author.one');
//     Route::put('/{id}', [AuthorsController::class, 'updateAuthor'])->name('admin.author.update');
//     Route::delete('/{id}', [AuthorsController::class, 'deleteAuthor'])->name('admin.author.delete');
// });
Route::group(['prefix' => 'auth'], function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/register', [AuthController::class, 'register'])->name('register'); // Consider making this general
});

/**
 * Protected Routes with CheckAuth Middleware
 * These routes require authentication
 */
Route::middleware([CheckAuth::class, CheckUserRole::class])->group(function () {
    Route::group(['prefix' => 'author'], function () {
        Route::post('/', [AuthorsController::class, 'saveAuthors'])->name('admin.author.save');
        Route::get('/', [AuthorsController::class, 'getListAuthors'])->name('admin.author.list');
        Route::get('/{id}', [AuthorsController::class, 'getOneAuthor'])->name('admin.author.one');
        Route::put('/{id}', [AuthorsController::class, 'updateAuthor'])->name('admin.author.update');
        Route::delete('/{id}', [AuthorsController::class, 'deleteAuthor'])->name('admin.author.delete');
    });
    // Superadmin routes
    Route::group(['prefix' => 'superadmin'], function () {
        Route::post('/register', [AuthController::class, 'register'])->name('superadmin.register'); // Optional, if different
        Route::group(['prefix' => 'auth'], function () {
            Route::post('/logout', [AuthController::class, 'logout'])->name('superadmin.logout');
            Route::get('/profile', [AuthController::class, 'profile'])->name('superadmin.profile');
            Route::get('/listUser', [AuthController::class, 'getListUser'])->name('superadmin.listUser');
        });
        // Services, Technologies and Roles routes
        Route::group(['prefix' => 'service'], function () {
            Route::post('/', [ServiceController::class, 'saveService'])->name('superadmin.service.save');
            Route::get('/', [ServiceController::class, 'getListServices'])->name('superadmin.service.list');
            Route::get('/{id}', [ServiceController::class, 'getOneService'])->name('superadmin.service.show');
            Route::put('/{id}', [ServiceController::class, 'updateService'])->name('superadmin.service.update');
            Route::delete('/{id}', [ServiceController::class, 'deleteService'])->name('superadmin.service.delete');
        });
        Route::group(['prefix' => 'technology'], function () {
            Route::post('/', [TechnologyController::class, 'saveTechnology'])->name('superadmin.technology.save');
            Route::get('/', [TechnologyController::class, 'getListTechnologies'])->name('superadmin.technology.list');
            Route::get('/{id}', [TechnologyController::class, 'getOneTechnology'])->name('superadmin.technology.show');
            Route::put('/{id}', [TechnologyController::class, 'updateTechnology'])->name('superadmin.technology.update');
            Route::delete('/{id}', [TechnologyController::class, 'deleteTechnology'])->name('superadmin.technology.delete');
        });
        Route::group(['prefix' => 'role'], function () {
            Route::post('/', [RoleController::class, 'saveRole'])->name('superadmin.role.save');
            Route::get('/', [RoleController::class, 'getListRoles'])->name('superadmin.role.list');
            Route::get('/{id}', [RoleController::class, 'getOneRole'])->name('superadmin.role.show');
            Route::put('/{id}', [RoleController::class, 'updateRoles'])->name('superadmin.role.update');
            Route::delete('/{id}', [RoleController::class, 'deleteRole'])->name('superadmin.role.delete');
        });
        Route::group(['prefix' => 'author'], function () {
            Route::post('/', [AuthorsController::class, 'saveAuthors'])->name('admin.author.save');
            Route::get('/', [AuthorsController::class, 'getListAuthors'])->name('admin.author.list');
            Route::get('/{id}', [AuthorsController::class, 'getOneAuthor'])->name('admin.author.one');
            Route::put('/{id}', [AuthorsController::class, 'updateAuthor'])->name('admin.author.update');
            Route::delete('/{id}', [AuthorsController::class, 'deleteAuthor'])->name('admin.author.delete');
        });
        Route::group(['prefix' => 'category'], function () {
            Route::post('/', [CategoriesController::class, 'saveCategory'])->name('admin.category.save');
            Route::get('/', [CategoriesController::class, 'getListCategories'])->name('admin.category.list');
            Route::get('/{id}', [CategoriesController::class, 'getOneCategory'])->name('admin.category.one');
            Route::put('/{id}', [CategoriesController::class, 'updateCategory'])->name('admin.category.update');
            Route::delete('/{id}', [CategoriesController::class, 'deleteCategory'])->name('admin.category.delete');
        });
        Route::group(['prefix' => 'genre'], function () {
            Route::post('/', [GenresController::class, 'saveGenre'])->name('admin.genre.save');
            Route::get('/', [GenresController::class, 'getListGenres'])->name('admin.genre.list');
            Route::get('/{id}', [GenresController::class, 'getOneGenre'])->name('admin.genre.one');
            Route::put('/{id}', [GenresController::class, 'updateGenre'])->name('admin.genre.update');
            Route::delete('/{id}', [GenresController::class, 'deleteGenre'])->name('admin.genre.delete');
        });
        Route::group(['prefix' => 'document'], function () {
            Route::post('/', [DocumentsController::class, 'saveDocument'])->name('admin.document.save');
            Route::get('/', [DocumentsController::class, 'getListDocuments'])->name('admin.document.list');
            Route::get('/{id}', [DocumentsController::class, 'getOneDocument'])->name('admin.document.one');
            Route::put('/{id}', [DocumentsController::class, 'updateDocument'])->name('admin.document.update');
            Route::delete('/{id}', [DocumentsController::class, 'deleteDocument'])->name('admin.document.delete');
        });
        Route::group(['prefix' => 'setting/option'], function () {
            Route::get('/role', [GeneralSettingController::class, 'getOptionRole'])->name('admin.option.role');
        });
    });

    // Admin routes
    Route::group(['prefix' => 'admin'], function () {
        // Admin-specific routes could go here, if different than Superadmin
        Route::post('/register', [AuthController::class, 'register'])->name('admin.register');
        // Route::group(['prefix' => 'author'], function () {
        //     Route::post('/', [AuthorsController::class, 'saveAuthors'])->name('admin.author.save');
        //     Route::get('/', [AuthorsController::class, 'getListAuthors'])->name('admin.author.list');
        //     Route::get('/{id}', [AuthorsController::class, 'getOneAuthor'])->name('admin.author.one');
        //     Route::put('/{id}', [AuthorsController::class, 'updateAuthor'])->name('admin.author.update');
        //     Route::delete('/{id}', [AuthorsController::class, 'deleteAuthor'])->name('admin.author.delete');
        // });
        Route::group(['prefix' => 'category'], function () {
            Route::post('/', [CategoriesController::class, 'saveCategory'])->name('admin.category.save');
            Route::get('/', [CategoriesController::class, 'getListCategories'])->name('admin.category.list');
            Route::get('/{id}', [CategoriesController::class, 'getOneCategory'])->name('admin.category.one');
            Route::put('/{id}', [CategoriesController::class, 'updateCategory'])->name('admin.category.update');
            Route::delete('/{id}', [CategoriesController::class, 'deleteCategory'])->name('admin.category.delete');
        });
        Route::group(['prefix' => 'genre'], function () {
            Route::post('/', [GenresController::class, 'saveGenre'])->name('admin.genre.save');
            Route::get('/', [GenresController::class, 'getListGenres'])->name('admin.genre.list');
            Route::get('/{id}', [GenresController::class, 'getOneGenre'])->name('admin.genre.one');
            Route::put('/{id}', [GenresController::class, 'updateGenre'])->name('admin.genre.update');
            Route::delete('/{id}', [GenresController::class, 'deleteGenre'])->name('admin.genre.delete');
        });
        Route::group(['prefix' => 'document'], function () {
            Route::post('/', [DocumentsController::class, 'saveDocument'])->name('admin.document.save');
            Route::get('/', [DocumentsController::class, 'getListDocuments'])->name('admin.document.list');
            Route::get('/{id}', [DocumentsController::class, 'getOneDocument'])->name('admin.document.one');
            Route::put('/{id}', [DocumentsController::class, 'updateDocument'])->name('admin.document.update');
            Route::delete('/{id}', [DocumentsController::class, 'deleteDocument'])->name('admin.document.delete');
        });
    });
});
