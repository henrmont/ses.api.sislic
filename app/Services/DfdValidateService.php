<?php

namespace App\Services;

use App\Models\Dfd;
use App\Models\DfdItem;
use App\Models\Module;
use App\Models\Professional;
use App\Models\ProfessionalWorkstation;
use App\Models\User;
use App\Models\UserModule;
use App\Models\Workstation;
use Exception;
use Illuminate\Database\Connection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class DfdValidateService
{
    /**
     * Sobrestar um DFD.
     */
    public function haltedDfd(Dfd $dfd): JsonResponse
    {
        try {
            $dfd->update([
                'is_validate_bookmark' => !$dfd->is_validate_bookmark,
            ]);

            return response()->json(['message' => 'DFD sobrestado com sucesso.'], JsonResponse::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Erro ao sobrestar DFD: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json(['message' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Realizar a devolução/retorno da solicitação para o setor/papel especificado.
     */
    public function undoDfd(Dfd $dfd, Request $request): JsonResponse
    {
        try {
            switch ($request->to) {
                case 'owner':
                    $dfd->update([
                        'back_to_owner' => $dfd->back_to_owner . '; ' . $request->reason
                    ]);
                    break;
            }

            return response()->json(['message' => 'DFD retornado com sucesso.'], JsonResponse::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Erro ao desfazer/retornar DFD: ' . $e->getMessage());

            return response()->json(['message' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Restaurar solicitação arquivada.
     */
    public function archiveDfd(Dfd $dfd): JsonResponse
    {
        try {
            $dfd->update([
                'is_archived' => true
            ]);

            return response()->json(['message' => 'DFD transferido com sucesso.'], JsonResponse::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Erro ao mover solicitação do arquivo: ' . $e->getMessage());

            return response()->json(['message' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Restaurar solicitação arquivada.
     */
    public function approveDfd(Dfd $dfd): JsonResponse
    {
        try {
            $dfd->update([
                'is_valid' => !$dfd->is_valid
            ]);

            return response()->json(['message' => 'DFD aprovado com sucesso.'], JsonResponse::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Erro ao mover solicitação do arquivo: ' . $e->getMessage());

            return response()->json(['message' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Restaurar solicitação arquivada.
     */
    public function moveDfdFromArchive(Dfd $dfd): JsonResponse
    {
        try {
            $dfd->update([
                'is_archived' => false
            ]);

            return response()->json(['message' => 'DFD transferido com sucesso.'], JsonResponse::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Erro ao mover solicitação do arquivo: ' . $e->getMessage());

            return response()->json(['message' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Transferir solicitação a partir do setor "Outros".
     */
    public function moveDfdFromOthers(Dfd $dfd): JsonResponse
    {
        try {
            $userProfessional = Professional::where('user_id', auth()->id())->first();

            $dfd->update([
                'validate_professional_id' => $userProfessional?->id,
            ]);

            return response()->json(['message' => 'DFD transferido com sucesso.'], JsonResponse::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Erro ao mover DFD de outros: ' . $e->getMessage());

            return response()->json(['message' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }


}
