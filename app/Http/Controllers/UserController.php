<?php

namespace App\Http\Controllers;

use App\Models\Professional;
use App\Models\ProfessionalWorkplace;
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
        $me = User::with('roles.permissions', 'professional.types', 'professional.workplaces')->find(auth()->id());

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
            ->with(['roles', 'professional.types', 'professional.workplaces'])
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

    /**
     * Listar os locais de trabalho vinculados a um profissional.
     * GET /api/sislic/users/professionals/{professional}/workplaces
     */
    public function getProfessionalWorkplaces(Professional $professional): JsonResponse
    {
        $this->authorize('sislic/usuário listar');

        $workplaces = $professional->workplaces()->get();

        return response()->json($workplaces, JsonResponse::HTTP_OK);
    }

    /**
     * Vincular um local de trabalho ao profissional.
     * POST /api/sislic/users/professionals/{professional}/workplaces
     */
    public function attachWorkplace(Professional $professional, Request $request)
    {
        $this->authorize('sislic/usuário atualizar');

        return $this->userService->attachWorkplace($professional, $request);
    }

    /**
     * Remover o vínculo de um local de trabalho com o profissional.
     * DELETE /api/sislic/users/professionals/{professional}/workplaces/{professional_workplace}
     */
    public function detachWorkplace(Professional $professional, ProfessionalWorkplace $professional_workplace)
    {
        $this->authorize('sislic/usuário atualizar');

        return $this->userService->detachWorkplace($professional, $professional_workplace);
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
}