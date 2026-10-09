<?php

use App\Http\Controllers\DfdController;
use App\Http\Controllers\DfdValidateController;
use App\Http\Controllers\EtpController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WorkstationController;
use App\Http\Middleware\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['api', Auth::class])
    ->prefix('sislic/users')
    ->name('users.')
    ->controller(UserController::class)
    ->group(function () {
        Route::get('/me', 'getMe')->name('me');
        // Listagem
        Route::get('/', 'getUsers')->name('index');
        Route::get('roles', 'getRoles')->name('roles.index');

        // CRUD principal
        Route::post('/', 'createUser')->name('store');
        Route::put('{user}', 'updateUser')->name('update');
        Route::delete('{user}', 'deleteUser')->name('destroy');

        // Ações de estado e relacionamentos
        Route::patch('{user}/lock', 'lockUser')->name('lock');
        Route::patch('{user}/validate', 'validateUser')->name('validate');
        Route::patch('{user}/roles', 'rolesUser')->name('roles.update');

        // Validações assíncronas (se declaradas no Controller)
        Route::get('exists-email/{email}/{currentEmail?}', 'emailUserExists')->name('exists.email');
        Route::get('exists-cns/{cns}/{currentCns?}', 'cnsUserExists')->name('exists.cns');
    });

Route::middleware(['api', Auth::class])
    ->prefix('sislic/roles')
    ->name('roles.')
    ->controller(RoleController::class)
    ->group(function () {
        // Listagens
        Route::get('/', 'getRoles')->name('index');
        Route::get('permissions', 'getPermissions')->name('permissions.index');

        // CRUD principal
        Route::post('/', 'createRole')->name('store');
        Route::put('{role}', 'updateRole')->name('update');
        Route::delete('{role}', 'deleteRole')->name('destroy');
    });