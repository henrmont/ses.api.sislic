<?php

namespace App\Http\Controllers;

use App\Models\Workplace;
use App\Services\WorkplaceService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WorkplaceController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected WorkplaceService $workplaceService
    ) {}

    /**
     * Listar todos os locais de trabalho.
     */
    public function getWorkplaces(): JsonResponse
    {
        $this->authorize('sislic/local de trabalho listar');

        $workplaces = Workplace::query()
            ->with(['parent'])
            ->orderBy('budget_entity', 'asc')
            ->get();

        return response()->json($workplaces, JsonResponse::HTTP_OK);
    }

    /**
     * Exibir um local de trabalho específico.
     */
    public function getWorkplace(Workplace $workplace): JsonResponse
    {
        $this->authorize('sislic/local de trabalho listar');

        return response()->json($workplace->load(['parent', 'children']), JsonResponse::HTTP_OK);
    }

    /**
     * Criar um novo local de trabalho.
     */
    public function createWorkplace(Request $request): JsonResponse
    {
        $this->authorize('sislic/local de trabalho criar');

        $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'budget_entity' => ['required', 'string', 'max:10'],
            'parent_id'     => ['nullable', 'exists:workplaces,id'],
        ]);

        return $this->workplaceService->createWorkplace($request);
    }

    /**
     * Atualizar um local de trabalho existente.
     */
    public function updateWorkplace(Workplace $workplace, Request $request): JsonResponse
    {
        $this->authorize('sislic/local de trabalho atualizar');

        $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'budget_entity' => ['required', 'string', 'max:10'],
            'parent_id'     => ['nullable', 'exists:workplaces,id', "different:{$workplace->id}"],
        ]);

        return $this->workplaceService->updateWorkplace($workplace, $request);
    }

    /**
     * Excluir um local de trabalho.
     */
    public function deleteWorkplace(Workplace $workplace): JsonResponse
    {
        $this->authorize('sislic/local de trabalho deletar');

        return $this->workplaceService->deleteWorkplace($workplace);
    }
}