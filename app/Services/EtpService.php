<?php

namespace App\Services;

use App\Models\Dfd;
use App\Models\DfdItem;
use App\Models\Etp;
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

class EtpService
{
    /**
     * Conexão com o banco padrão (TFD).
     */
    private function sislic(): Connection
    {
        return DB::connection();
    }

    /**
     * Criar ou associar usuário ao módulo TFD com seus dados profissionais.
     */
    public function createEtp(Request $request): JsonResponse
    {
        try {
            Etp::create([
                'workstation_id' => $request->workstation_id,
                'owner_professional_id' => Professional::where('user_id', auth()->id())->first()->id,
                'process' => $request->process,
                'organ' => $request->organ,
                'budget_unit' => $request->budget_unit,
            ]);

            return response()->json(['message' => 'ETP criado com sucesso.'], JsonResponse::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Erro ao criar ETP: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json(['message' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Atualizar um DFD existente.
     */
    public function updateEtp(Etp $etp, Request $request): JsonResponse
    {
        try {
            $etp->update([
                'process' => $request->process,
                'organ' => $request->organ,
                'budget_unit' => $request->budget_unit,
            ]);

            return response()->json(['message' => 'ETP atualizado com sucesso.'], JsonResponse::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Erro ao atualizar ETP: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json(['message' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Deletar um DFD.
     */
    public function deleteEtp(Etp $etp): JsonResponse
    {
        try {
            $etp->delete();

            return response()->json(['message' => 'ETP deletado com sucesso.'], JsonResponse::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Erro ao deletar ETP: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json(['message' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Sobrestar um DFD.
     */
    public function haltedEtp(Etp $etp): JsonResponse
    {
        try {
            $etp->update([
                'is_owner_bookmark' => !$etp->is_owner_bookmark,
            ]);

            return response()->json(['message' => 'ETP sobrestado com sucesso.'], JsonResponse::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Erro ao sobrestar ETP: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json(['message' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Encaminhar para validação um DFD, atribuindo o profissional de validação.
     */
    public function processEtpToValidate(Etp $etp, Request $request): JsonResponse
    {
        try {
            $etp->update([
                'validate_professional_id' => $request->validate_professional_id,
            ]);

            return response()->json(['message' => 'ETP encaminhado para validação com sucesso.'], JsonResponse::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Erro ao encaminhar ETP para validação: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json(['message' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Transferir solicitação a partir do setor de processos.
     */
    public function moveEtpFromProcesses(Etp $etp): JsonResponse
    {
        try {
            $etp->update([
                'validate_professional_id' => null,
            ]);

            return response()->json(['message' => 'ETP transferido com sucesso.'], JsonResponse::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Erro ao mover ETP de processos: ' . $e->getMessage());

            return response()->json(['message' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Transferir solicitação a partir do setor "Outros".
     */
    public function moveEtpFromOthers(Etp $etp): JsonResponse
    {
        try {
            $userProfessional = Professional::where('user_id', auth()->id())->first();

            $etp->update([
                'owner_professional_id' => $userProfessional?->id,
            ]);

            return response()->json(['message' => 'ETP transferido com sucesso.'], JsonResponse::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Erro ao mover ETP de outros: ' . $e->getMessage());

            return response()->json(['message' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Finalizar devolução/retorno atribuído à solicitação.
     */
    public function finishBackEtp(Etp $etp): JsonResponse
    {
        try {
            $etp->update(['back_to_owner' => null]);

            return response()->json(['message' => 'ETP atualizado com sucesso.'], JsonResponse::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Erro ao finalizar retorno do ETP: ' . $e->getMessage());

            return response()->json(['message' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }



}
