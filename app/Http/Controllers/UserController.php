<?php

namespace App\Http\Controllers;

use App\Models\Professional;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected UserService $userService
    ) {}

    public function getMe(): JsonResponse
    {
        $me = User::with('roles.permissions', 'professional.types')->find(auth()->id());

        return response()->json($me, JsonResponse::HTTP_OK);
    }

    /**
     * Listar usuários do sistema TFD.
     */
    public function getUsers(): JsonResponse
    {
        $this->authorize('sislic/usuário listar');

        $users = User::query()
            ->sislic()
            ->with(['roles', 'professional.types'])
            ->where('email', '!=', 'admin@sislic.com')
            ->latest('id')
            ->get();

        return response()->json($users, JsonResponse::HTTP_OK);
    }

    /**
     * Listar perfis/roles do TFD.
     */
    public function getRoles(): JsonResponse
    {
        $this->authorize('sislic/usuário listar');

        $roles = Role::query()
            ->with('permissions')
            ->where('name', 'LIKE', 'sislic%')
            ->get();

        return response()->json($roles, JsonResponse::HTTP_OK);
    }

    /**
     * Criar um novo usuário.
     */
    public function createUser(Request $request)
    {
        $this->authorize('sislic/usuário criar');

        return $this->userService->createUser($request);
    }

    /**
     * Travar/Destravar edição de um usuário.
     */
    public function lockUser(User $user)
    {
        $this->authorize('sislic/usuário travar');

        return $this->userService->lockUser($user);
    }

    /**
     * Validar/Invalidar status de um usuário.
     */
    public function validateUser(User $user)
    {
        $this->authorize('sislic/usuário validar');

        return $this->userService->validateUser($user);
    }

    /**
     * Atualizar dados de um usuário.
     */
    public function updateUser(User $user, Request $request)
    {
        $this->authorize('sislic/usuário atualizar');

        return $this->userService->updateUser($user, $request);
    }

    /**
     * Excluir um usuário.
     */
    public function deleteUser(User $user)
    {
        $this->authorize('sislic/usuário deletar');

        return $this->userService->deleteUser($user);
    }

    /**
     * Atualizar as regras/permissões atribuídas ao usuário.
     */
    public function rolesUser(User $user, Request $request)
    {
        $this->authorize('sislic/usuário atualizar');

        return $this->userService->rolesUser($user, $request);
    }

    /*
    |--------------------------------------------------------------------------
    | Validadores Assíncronos (Existência de Registros)
    |--------------------------------------------------------------------------
    */

    /**
     * Verifica se o e-mail informado já existe no banco.
     */
    public function emailUserExists(string $email, ?string $currentEmail = null): JsonResponse
    {
        $this->authorize('sislic/usuário listar');

        $exists = User::query()
            ->sislic()
            ->where('email', $email)
            ->when($currentEmail, fn ($query) => $query->where('email', '!=', $currentEmail))
            ->exists();

        return response()->json($exists, JsonResponse::HTTP_OK);
    }

    /**
     * Verifica se o CNS informado já existe no banco.
     */
    public function cnsUserExists(string $cns, ?string $currentCns = null): JsonResponse
    {
        $this->authorize('sislic/usuário listar');

        $exists = Professional::query()
            ->where('cns', $cns)
            ->when($currentCns, fn ($query) => $query->where('cns', '!=', $currentCns))
            ->exists();

        return response()->json($exists, JsonResponse::HTTP_OK);
    }
}