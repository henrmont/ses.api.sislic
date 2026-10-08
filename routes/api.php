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
        // Listagem
        Route::get('/', 'getUsers')->name('index');
        Route::get('{user}/workstations', 'getUserWorkstations')->name('workstations.index');
        Route::get('roles', 'getRoles')->name('roles.index');

        // CRUD principal
        Route::post('/', 'createUser')->name('store');
        Route::put('{user}', 'updateUser')->name('update');
        Route::delete('{user}', 'deleteUser')->name('destroy');

        // Ações de estado e relacionamentos
        Route::patch('{user}/lock', 'lockUser')->name('lock');
        Route::patch('{user}/validate', 'validateUser')->name('validate');
        Route::patch('{user}/roles', 'rolesUser')->name('roles.update');
        Route::post('{professional}/workstations/{workstation}', 'attachWorkstation')->name('workstations.attach');
        Route::delete('workstations/{professional_workstation}', 'detachWorkstation')->name('workstations.detach');

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

// Route::middleware(['api', Auth::class])
//     ->prefix('sislic/workstations')
//     ->name('workstations.')
//     ->controller(WorkstationController::class)
//     ->group(function () {
//         // Listagem
//         Route::get('/', 'getWorkstations')->name('index');

//         // CRUD principal
//         Route::post('/', 'createWorkstation')->name('store');
//         Route::put('{workstation}', 'updateWorkstation')->name('update');
//         Route::delete('{workstation}', 'deleteWorkstation')->name('destroy');
//     });

Route::middleware(['api', Auth::class])
    ->prefix('sislic/dfds')
    ->name('dfds.')
    ->controller(DfdController::class)
    ->group(function () {
        // Listagem
        Route::get('/', 'getDfds')->name('index');
        Route::get('workstations', 'getWorkstations')->name('workstations.index');
        Route::get('{dfd}/itens', 'getDfdItems')->name('itens.index');

        // CRUD principal
        Route::post('/', 'createDfd')->name('store');
        Route::put('{dfd}', 'updateDfd')->name('update');
        Route::delete('{dfd}', 'deleteDfd')->name('destroy');

        // CRUD de itens
        Route::post('{dfd}/itens', 'createItem')->name('itens.store');
        Route::put('{dfd}/itens/{item}', 'updateItem')->name('itens.update');
        Route::delete('{dfd}/itens/{item}', 'deleteItem')->name('itens.destroy');

        // Ações de estado
        Route::patch('{dfd}/halted', 'haltedDfd')->name('halted');
        Route::patch('{dfd}/process-to-validate', 'processDfdToValidate')->name('process-to-validate');
        Route::patch('{dfd}/move-from-processes', 'moveDfdFromProcesses')->name('move-from-processes');
        Route::patch('{dfd}/move-from-others', 'moveDfdFromOthers')->name('move-from-others');
        Route::patch('{dfd}/finish-back', 'finishBackDfd')->name('finish-back');

        // Consultas e dados auxiliares
        Route::get('validate-professionals', 'getValidateProfessionals')->name('validate-professionals.index');
    });

Route::middleware(['api', Auth::class])
    ->prefix('sislic/dfd-validates')
    ->name('dfd-validates.')
    ->controller(DfdValidateController::class)
    ->group(function () {
        // Listagem
        Route::get('/', 'getDfds')->name('index');

        // Ações de estado
        Route::patch('{dfd}/halted', 'haltedDfd')->name('halted');
        Route::patch('{dfd}/archive', 'archiveDfd')->name('archive');
        Route::patch('{dfd}/approve', 'approveDfd')->name('approve');
        Route::patch('{dfd}/undo', 'undoDfd')->name('undo');
        Route::patch('{dfd}/move-from-others', 'moveDfdFromOthers')->name('move-from-others');
        Route::patch('{dfd}/move-from-archive', 'moveDfdFromArchive')->name('move-from-archive');
        
    });

    Route::middleware(['api', Auth::class])
    ->prefix('sislic/etps')
    ->name('etps.')
    ->controller(EtpController::class)
    ->group(function () {
        // Listagem
        Route::get('/', 'getEtps')->name('index');
        Route::get('workstations', 'getWorkstations')->name('workstations.index');

        // CRUD principal
        Route::post('/', 'createEtp')->name('store');
        Route::put('{etp}', 'updateEtp')->name('update');
        Route::delete('{etp}', 'deleteEtp')->name('destroy');
       
    });
