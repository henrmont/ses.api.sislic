<?php

namespace App\Http\Controllers;

use App\Models\Dfd;
use App\Models\DfdItem;
use App\Models\Professional;
use App\Services\DfdService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DfdController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected DfdService $dfdService
    ) {}

    /**
     * Listar DFDs.
     */
    public function getDfds(): JsonResponse
    {
        $this->authorize('sislic/dfd listar');

        $dfds = Dfd::query()
            ->byWorkstation()
            ->with('workstation', 'ownerProfessional', 'validateProfessional', 'itens')
            ->latest('id')
            ->get();

        return response()->json($dfds, JsonResponse::HTTP_OK);
    }

    /**
     * Listar DFDs.
     */
    public function getWorkstations(): JsonResponse
    {
        $this->authorize('sislic/dfd listar');

        $professional = Professional::query()
            ->where('user_id', auth()->id())
            ->with('workstations')
            ->first();

        return response()->json($professional->workstations, JsonResponse::HTTP_OK);
    }

    /**
     * Listar itens de um DFD.
     */
    public function getDfdItems(Dfd $dfd): JsonResponse
    {
        $this->authorize('sislic/dfd listar');

        $dfdItems = $dfd->itens;

        return response()->json($dfdItems, JsonResponse::HTTP_OK);
    }

    

    /**
     * Criar um novo DFD.
     */
    public function createDfd(Request $request)
    {
        $this->authorize('sislic/dfd criar');

        return $this->dfdService->createDfd($request);
    }

    /**
     * Atualizar um DFD existente.
     */
    public function updateDfd(Request $request, Dfd $dfd)
    {
        $this->authorize('sislic/dfd atualizar');

        return $this->dfdService->updateDfd($request, $dfd);
    }

    /**
     * Deletar um DFD.
     */
    public function deleteDfd(Dfd $dfd)
    {
        $this->authorize('sislic/dfd deletar');

        return $this->dfdService->deleteDfd($dfd);
    }

    /**
     * Sobrestar um DFD
     */
    public function haltedDfd(Dfd $dfd)
    {
        $this->authorize('sislic/dfd atualizar');

        return $this->dfdService->haltedDfd($dfd);
    }

    /**
     * Listar profissionais de validação do DFD ligados às mesmas estações do owner.
     */
    public function getValidateProfessionals(): JsonResponse
    {
        $this->authorize('sislic/dfd atualizar');

        // Recupera as estações de trabalho do profissional logado
        $user = auth()->user();
        $workstationIds = $user?->professional?->workstations->pluck('id')->toArray() ?? [];

        $validateProfessionals = Professional::query()
            ->with('user')
            ->withCount('validateDfds')
            ->where('type', 'DFD Validação')
            ->when(empty($workstationIds), function ($query) {
                // Se não pertencer a nenhuma estação, retorna resultado vazio
                $query->whereRaw('1 = 0');
            }, function ($query) use ($workstationIds) {
                // Filtra apenas profissionais que compartilham ao menos uma workstation
                $query->whereHas('workstations', function ($q) use ($workstationIds) {
                    $q->whereIn('workstations.id', $workstationIds);
                });
            })
            ->get();

        return response()->json($validateProfessionals, JsonResponse::HTTP_OK);
    }

    /**
     * Encaminhar para validação um DFD, atribuindo o profissional de validação.
     */
    public function processDfdToValidate(Dfd $dfd, Request $request)
    {
        $this->authorize('sislic/dfd atualizar');

        return $this->dfdService->processDfdToValidate($dfd, $request);
    }

    /**
     * Movimentar solicitação a partir da aba de processos.
     */
    public function moveDfdFromProcesses(Dfd $dfd)
    {
        $this->authorize('sislic/dfd atualizar');

        return $this->dfdService->moveDfdFromProcesses($dfd);
    }

    /**
     * Criar um item de DFD.
     */
    public function createItem(Dfd $dfd, Request $request)
    {
        $this->authorize('sislic/dfd atualizar');

        return $this->dfdService->createItem($dfd, $request);
    }

    /**
     * Atualizar um item de DFD.
     */
    public function updateItem(Dfd $dfd, DfdItem $item, Request $request)
    {
        $this->authorize('sislic/dfd atualizar');

        return $this->dfdService->updateItem($item, $request);
    }

    /**
     * Deletar um item de DFD.
     */
    public function deleteItem(Dfd $dfd, DfdItem $item)
    {
        $this->authorize('sislic/dfd atualizar');

        return $this->dfdService->deleteItem($item);
    }

    /**
     * Movimentar solicitação a partir do setor "Outros".
     */
    public function moveDfdFromOthers(Dfd $dfd)
    {
        $this->authorize('sislic/dfd atualizar');

        return $this->dfdService->moveDfdFromOthers($dfd);
    }

    /**
     * Finalizar o retorno de uma solicitação.
     */
    public function finishBackDfd(Dfd $dfd)
    {
        $this->authorize('sislic/dfd atualizar');

        return $this->dfdService->finishBackDfd($dfd);
    }
}
