<?php

namespace App\Http\Controllers;

use App\Models\Etp;
use App\Models\Professional;
use App\Services\EtpService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EtpController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected EtpService $etpService
    ) {}

    /**
     * Listar DFDs.
     */
    public function getEtps(): JsonResponse
    {
        $this->authorize('sislic/etp listar');

        $etps = Etp::query()
            ->byWorkstation()
            ->with('workstation', 'ownerProfessional', 'validateProfessional')
            ->latest('id')
            ->get();

        return response()->json($etps, JsonResponse::HTTP_OK);
    }

    /**
     * Listar DFDs.
     */
    public function getWorkstations(): JsonResponse
    {
        $this->authorize('sislic/etp listar');

        $professional = Professional::query()
            ->where('user_id', auth()->id())
            ->with('workstations')
            ->first();

        return response()->json($professional->workstations, JsonResponse::HTTP_OK);
    }

    /**
     * Criar um novo DFD.
     */
    public function createEtp(Request $request)
    {
        $this->authorize('sislic/etp criar');

        return $this->etpService->createEtp($request);
    }

    /**
     * Atualizar um DFD existente.
     */
    public function updateEtp(Etp $etp, Request $request)
    {
        $this->authorize('sislic/etp atualizar');

        return $this->etpService->updateEtp($etp, $request);
    }

    /**
     * Deletar um DFD.
     */
    public function deleteDfd(Etp $etp)
    {
        $this->authorize('sislic/etp deletar');

        return $this->etpService->deleteDfd($dfd);
    }

    /**
     * Sobrestar um DFD
     */
    public function haltedDfd(Etp $etp)
    {
        $this->authorize('sislic/etp atualizar');

        return $this->etpService->haltedDfd($dfd);
    }

    /**
     * Listar profissionais de validação do DFD ligados às mesmas estações do owner.
     */
    public function getValidateProfessionals(): JsonResponse
    {
        $this->authorize('sislic/etp atualizar');

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
    public function processDfdToValidate(Etp $etp, Request $request)
    {
        $this->authorize('sislic/etp atualizar');

        return $this->etpService->processDfdToValidate($dfd, $request);
    }

    /**
     * Movimentar solicitação a partir da aba de processos.
     */
    public function moveDfdFromProcesses(Etp $etp)
    {
        $this->authorize('sislic/etp atualizar');

        return $this->etpService->moveDfdFromProcesses($dfd);
    }

    /**
     * Criar um item de DFD.
     */
    public function createItem(Etp $etp, Request $request)
    {
        $this->authorize('sislic/etp atualizar');

        return $this->etpService->createItem($dfd, $request);
    }

    /**
     * Atualizar um item de DFD.
     */
    public function updateItem(Etp $etp, DfdItem $item, Request $request)
    {
        $this->authorize('sislic/etp atualizar');

        return $this->etpService->updateItem($item, $request);
    }

    /**
     * Deletar um item de DFD.
     */
    public function deleteItem(Etp $etp, DfdItem $item)
    {
        $this->authorize('sislic/etp atualizar');

        return $this->etpService->deleteItem($item);
    }

    /**
     * Movimentar solicitação a partir do setor "Outros".
     */
    public function moveDfdFromOthers(Etp $etp)
    {
        $this->authorize('sislic/etp atualizar');

        return $this->etpService->moveDfdFromOthers($dfd);
    }

    /**
     * Finalizar o retorno de uma solicitação.
     */
    public function finishBackDfd(Etp $etp)
    {
        $this->authorize('sislic/etp atualizar');

        return $this->etpService->finishBackDfd($dfd);
    }
}
