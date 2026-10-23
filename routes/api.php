<?php

use App\Http\Controllers\DfdController;
use App\Http\Controllers\DfdValidateController;
use App\Http\Controllers\EtpController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WorkplaceController;
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

        // Locais de trabalho (Workplaces vinculados ao Profissional)
        Route::get('professionals/{professional}/workplaces', 'getProfessionalWorkplaces')->name('professionals.workplaces.index');
        Route::post('professionals/{professional}/workplaces', 'attachWorkplace')->name('professionals.workplaces.store');
        Route::delete('professionals/{professional}/workplaces/{professional_workplace}', 'detachWorkplace')->name('professionals.workplaces.destroy');

        // Ações de estado e relacionamentos
        Route::patch('{user}/lock', 'lockUser')->name('lock');
        Route::patch('{user}/validate', 'validateUser')->name('validate');
        Route::patch('{user}/roles', 'rolesUser')->name('roles.update');

        // Validações assíncronas
        Route::get('exists-email/{email}/{currentEmail?}', 'emailUserExists')->name('exists.email');
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

Route::middleware(['api', Auth::class])
    ->prefix('sislic/workplaces')
    ->name('workplaces.')
    ->controller(WorkplaceController::class)
    ->group(function () {
        // Listagem e Exibição (Read)
        Route::get('/', 'getWorkplaces')->name('index');

        // Escrita e Modificação (Create, Update, Delete)
        Route::post('/', 'createWorkplace')->name('store');
        Route::put('{workplace}', 'updateWorkplace')->name('update');
        Route::delete('{workplace}', 'deleteWorkplace')->name('destroy');
    });

Route::middleware(['api', Auth::class])
    ->prefix('sislic/dfds')
    ->name('dfds.')
    ->controller(DfdController::class)
    ->group(function () {
        // Listagens
        Route::get('/', 'getDfds')->name('index');
        Route::get('archived', 'getArchivedDfds')->name('archived');

        // CRUD principal (DFD Header)
        Route::post('/', 'createDfd')->name('store');
        Route::put('{dfd}', 'updateDfd')->name('update');
        Route::delete('{dfd}', 'deleteDfd')->name('destroy');

        // Tramitação e Mudança de Estados
        Route::patch('{dfd}/archive', 'archiveDfd')->name('archive');
        Route::patch('{dfd}/validate', 'validateDfd')->name('validate');

        // Gestão de Itens (DfdItem)
        Route::get('{dfd}/items', 'getItems')->name('items.index');
        Route::post('{dfd}/items', 'createItem')->name('items.store');
        Route::put('items/{item}', 'updateItem')->name('items.update');
        Route::delete('items/{item}', 'deleteItem')->name('items.destroy');

        // Gestão de Membros (DfdMember)
        Route::get('{dfd}/members', 'getMembers')->name('members.index');
        Route::post('{dfd}/members', 'createMember')->name('members.store');
        Route::put('members/{member}', 'updateMember')->name('members.update');
        Route::delete('members/{member}', 'deleteMember')->name('members.destroy');
    });