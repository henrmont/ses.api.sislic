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

class DfdService
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
    public function createDfd(Request $request): JsonResponse
    {
        try {
            Dfd::create([
                'workstation_id' => $request->workstation_id,
                'owner_professional_id' => Professional::where('user_id', auth()->id())->first()->id,
                'name' => $request->name,
                'description' => $request->description,
                'justification' => $request->justification,
            ]);

            return response()->json(['message' => 'DFD criado com sucesso.'], JsonResponse::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Erro ao criar DFD: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json(['message' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Atualizar um DFD existente.
     */
    public function updateDfd(Request $request, Dfd $dfd): JsonResponse
    {
        try {
            $dfd->update([
                'workstation_id' => $request->workstation_id,
                'name' => $request->name,
                'description' => $request->description,
                'justification' => $request->justification,
            ]);

            return response()->json(['message' => 'DFD atualizado com sucesso.'], JsonResponse::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Erro ao atualizar DFD: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json(['message' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Deletar um DFD.
     */
    public function deleteDfd(Dfd $dfd): JsonResponse
    {
        try {
            $dfd->delete();

            return response()->json(['message' => 'DFD deletado com sucesso.'], JsonResponse::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Erro ao deletar DFD: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json(['message' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Sobrestar um DFD.
     */
    public function haltedDfd(Dfd $dfd): JsonResponse
    {
        try {
            $dfd->update([
                'is_owner_bookmark' => !$dfd->is_owner_bookmark,
            ]);

            return response()->json(['message' => 'DFD sobrestado com sucesso.'], JsonResponse::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Erro ao sobrestar DFD: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json(['message' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Encaminhar para validação um DFD, atribuindo o profissional de validação.
     */
    public function processDfdToValidate(Dfd $dfd, Request $request): JsonResponse
    {
        try {
            $dfd->update([
                'validate_professional_id' => $request->validate_professional_id,
            ]);

            return response()->json(['message' => 'DFD encaminhado para validação com sucesso.'], JsonResponse::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Erro ao encaminhar DFD para validação: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json(['message' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Transferir solicitação a partir do setor de processos.
     */
    public function moveDfdFromProcesses(Dfd $dfd): JsonResponse
    {
        try {
            $dfd->update([
                'validate_professional_id' => null,
            ]);

            return response()->json(['message' => 'DFD transferido com sucesso.'], JsonResponse::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Erro ao mover DFD de processos: ' . $e->getMessage());

            return response()->json(['message' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Criar um item de DFD.
     */
    public function createItem(Dfd $dfd, Request $request): JsonResponse
    {
        try {
            $dfd->itens()->create([
                'name' => $request->name,
                'amount' => $request->amount,
            ]);

            return response()->json(['message' => 'Item de DFD criado com sucesso.'], JsonResponse::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Erro ao criar item de DFD: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json(['message' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Atualizar um item de DFD.
     */
    public function updateItem(DfdItem $item, Request $request): JsonResponse
    {
        try {
            $item->update([
                'name' => $request->name,
                'amount' => $request->amount,
            ]);

            return response()->json(['message' => 'Item de DFD atualizado com sucesso.'], JsonResponse::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Erro ao atualizar item de DFD: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json(['message' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Deletar um item de DFD.
     */
    public function deleteItem(DfdItem $item): JsonResponse
    {
        try {
            $item->delete();

            return response()->json(['message' => 'Item de DFD deletado com sucesso.'], JsonResponse::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Erro ao deletar item de DFD: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

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
                'owner_professional_id' => $userProfessional?->id,
            ]);

            return response()->json(['message' => 'DFD transferido com sucesso.'], JsonResponse::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Erro ao mover DFD de outros: ' . $e->getMessage());

            return response()->json(['message' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Finalizar devolução/retorno atribuído à solicitação.
     */
    public function finishBackDfd(Dfd $dfd): JsonResponse
    {
        try {
            $dfd->update(['back_to_owner' => null]);

            return response()->json(['message' => 'DFD atualizado com sucesso.'], JsonResponse::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Erro ao finalizar retorno do DFD: ' . $e->getMessage());

            return response()->json(['message' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }



}
