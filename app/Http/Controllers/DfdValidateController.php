<?php

namespace App\Http\Controllers;

use App\Models\Dfd;
use App\Services\DfdValidateService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DfdValidateController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected DfdValidateService $dfdValidateService
    ) {}

    /**
     * Listar DFDs que estão em validação.
     */
    public function getDfds(): JsonResponse
    {
        $this->authorize('sislic/dfd validar');

        $dfds = Dfd::query()
            ->byWorkstation()
            ->with('ownerProfessional', 'validateProfessional', 'itens')
            ->latest('id')
            ->get();

        return response()->json($dfds, JsonResponse::HTTP_OK);
    }

    /**
     * Sobrestar um DFD
     */
    public function haltedDfd(Dfd $dfd)
    {
        $this->authorize('sislic/dfd validar');

        return $this->dfdValidateService->haltedDfd($dfd);
    }

    /**
     * Desfazer ação / devolver solicitação no fluxo de passagens.
     */
    public function undoDfd(Dfd $dfd, Request $request)
    {
        $this->authorize('sislic/dfd validar');

        return $this->dfdValidateService->undoDfd($dfd, $request);
    }

    /**
     * Mover solicitação a partir do arquivo.
     */
    public function moveDfdFromArchive(Dfd $dfd)
    {
        $this->authorize('sislic/dfd validar');

        return $this->dfdValidateService->moveDfdFromArchive($dfd);
    }

    /**
     * Mover solicitação a partir do setor "Outros".
     */
    public function moveDfdFromOthers(Dfd $dfd)
    {
        $this->authorize('sislic/dfd validar');

        return $this->dfdValidateService->moveDfdFromOthers($dfd);
    }

    /**
     * Arquivar a solicitação de passagem.
     */
    public function archiveDfd(Dfd $dfd)
    {
        $this->authorize('sislic/dfd validar');

        return $this->dfdValidateService->archiveDfd($dfd);
    }

    /**
     * Finalizar devolução da solicitação de passagem.
     */
    public function approveDfd(Dfd $dfd)
    {
        $this->authorize('sislic/dfd validar');

        return $this->dfdValidateService->approveDfd($dfd);
    }


}
