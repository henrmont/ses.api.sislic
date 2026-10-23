<?php

namespace App\Http\Controllers;

use App\Models\Dfd;
use App\Models\DfdItem;
use App\Models\DfdMember;
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

    /*
    |--------------------------------------------------------------------------
    | Gestão de DFDs (Cabeçalho)
    |--------------------------------------------------------------------------
    */

    /**
     * Listar DFDs pendentes/ativos.
     */
    public function getDfds(): JsonResponse
    {
        $this->authorize('sislic/dfd listar');

        $professionalId = Professional::where('user_id', auth()->id())->value('id');

        $dfds = Dfd::query()
            ->where('owner_professional_id', $professionalId)
            ->where('is_archived', false)
            ->with([
                'workplace',
                'ownerProfessional',
                'validateProfessional',
                'items',
                'members.professional',
            ])
            ->latest('id')
            ->get();

        return response()->json($dfds, JsonResponse::HTTP_OK);
    }

    /**
     * Listar DFDs arquivados.
     */
    public function getArchivedDfds(): JsonResponse
    {
        $this->authorize('sislic/dfd listar');

        $dfds = Dfd::query()
            ->where('is_archived', true)
            ->with([
                'workplace',
                'ownerProfessional',
                'validateProfessional',
                'items',
                'members.professional',
            ])
            ->latest('id')
            ->get();

        return response()->json($dfds, JsonResponse::HTTP_OK);
    }

    /**
     * Cadastrar novo DFD.
     */
    public function createDfd(Request $request): JsonResponse
    {
        $this->authorize('sislic/dfd criar');

        return $this->dfdService->createDfd($request);
    }

    /**
     * Atualizar DFD.
     */
    public function updateDfd(Dfd $dfd, Request $request): JsonResponse
    {
        $this->authorize('sislic/dfd atualizar');

        return $this->dfdService->updateDfd($dfd, $request);
    }

    /**
     * Excluir um DFD.
     */
    public function deleteDfd(Dfd $dfd): JsonResponse
    {
        $this->authorize('sislic/dfd deletar');

        return $this->dfdService->deleteDfd($dfd);
    }

    /*
    |--------------------------------------------------------------------------
    | Tramitação e Mudança de Estados
    |--------------------------------------------------------------------------
    */

    /**
     * Arquivar um DFD.
     */
    public function archiveDfd(Dfd $dfd): JsonResponse
    {
        $this->authorize('sislic/dfd atualizar');

        return $this->dfdService->archiveDfd($dfd);
    }

    /**
     * Validar DFD.
     */
    public function validateDfd(Dfd $dfd, Request $request): JsonResponse
    {
        $this->authorize('sislic/dfd validar');

        return $this->dfdService->validateDfd($dfd, $request);
    }

    /*
    |--------------------------------------------------------------------------
    | Gestão de Itens (DfdItem)
    |--------------------------------------------------------------------------
    */

    /**
     * Listar itens de um DFD.
     */
    public function getItems(Dfd $dfd): JsonResponse
    {
        $this->authorize('sislic/dfd listar');

        $items = $dfd->items()
            ->oldest('item_number')
            ->get();

        return response()->json($items, JsonResponse::HTTP_OK);
    }

    /**
     * Adicionar item ao DFD.
     */
    public function createItem(Dfd $dfd, Request $request): JsonResponse
    {
        $this->authorize('sislic/dfd atualizar');

        return $this->dfdService->createItem($dfd, $request);
    }

    /**
     * Atualizar item do DFD.
     */
    public function updateItem(DfdItem $item, Request $request): JsonResponse
    {
        $this->authorize('sislic/dfd atualizar');

        return $this->dfdService->updateItem($item, $request);
    }

    /**
     * Remover item do DFD.
     */
    public function deleteItem(DfdItem $item): JsonResponse
    {
        $this->authorize('sislic/dfd atualizar');

        return $this->dfdService->deleteItem($item);
    }

    /*
    |--------------------------------------------------------------------------
    | Gestão de Membros (DfdMember)
    |--------------------------------------------------------------------------
    */

    /**
     * Listar membros de um DFD.
     */
    public function getMembers(Dfd $dfd): JsonResponse
    {
        $this->authorize('sislic/dfd listar');

        $members = $dfd->members()
            ->with('professional')
            ->oldest('id')
            ->get();

        return response()->json($members, JsonResponse::HTTP_OK);
    }

    /**
     * Adicionar membro ao DFD.
     */
    public function createMember(Dfd $dfd, Request $request): JsonResponse
    {
        $this->authorize('sislic/dfd atualizar');

        return $this->dfdService->createMember($dfd, $request);
    }

    /**
     * Atualizar membro do DFD.
     */
    public function updateMember(DfdMember $member, Request $request): JsonResponse
    {
        $this->authorize('sislic/dfd atualizar');

        return $this->dfdService->updateMember($member, $request);
    }

    /**
     * Remover membro do DFD.
     */
    public function deleteMember(DfdMember $member): JsonResponse
    {
        $this->authorize('sislic/dfd atualizar');

        return $this->dfdService->deleteMember($member);
    }
}