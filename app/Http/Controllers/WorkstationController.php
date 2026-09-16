<?php

namespace App\Http\Controllers;

use App\Models\Workstation;
use App\Services\WorkstationService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WorkstationController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected WorkstationService $workstationService
    ) {}

    /**
     * Listar todas as estações de trabalho.
     */
    public function getWorkstations(): JsonResponse
    {
        $this->authorize('sislic/estação de trabalho listar');

        $workstations = Workstation::query()
            ->orderBy('id', 'desc')
            ->get();

        return response()->json($workstations, JsonResponse::HTTP_OK);
    }

    /**
     * Criar uma nova estação de trabalho.
     */
    public function createWorkstation(Request $request)
    {
        $this->authorize('sislic/estação de trabalho criar');

        return $this->workstationService->createWorkstation($request);
    }

    /**
     * Atualizar uma estação de trabalho.
     */
    public function updateWorkstation(Request $request, Workstation $workstation)
    {
        $this->authorize('sislic/estação de trabalho atualizar');

        return $this->workstationService->updateWorkstation($request, $workstation);
    }

    /**
     * Deletar uma estação de trabalho.
     */
    public function deleteWorkstation(Workstation $workstation)
    {
        $this->authorize('sislic/estação de trabalho deletar');

        return $this->workstationService->deleteWorkstation($workstation);
    }

}
