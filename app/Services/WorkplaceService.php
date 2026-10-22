<?php

namespace App\Services;

use App\Models\Workplace;
use Exception;
use Illuminate\Database\Connection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WorkplaceService
{
    /**
     * Conexão com o banco padrão (Sislic).
     */
    private function sislic(): Connection
    {
        return DB::connection();
    }

    /**
     * Conexão com o banco Core.
     */
    private function core(): Connection
    {
        return DB::connection('core');
    }

    /**
     * Criar um novo local de trabalho.
     */
    public function createWorkplace(Request $request): JsonResponse
    {
        try {
            $this->core()->beginTransaction();

            $workplace = Workplace::on('core')->create([
                'parent_id'     => $request->input('parent_id'),
                'name'          => trim($request->input('name')),
                'budget_entity' => trim($request->input('budget_entity')),
            ]);

            $this->core()->commit();

            return response()->json($workplace, JsonResponse::HTTP_CREATED);
        } catch (Exception $e) {
            $this->core()->rollBack();
            Log::error('Erro ao criar local de trabalho: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json(['message' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Atualizar um local de trabalho existente.
     */
    public function updateWorkplace(Workplace $workplace, Request $request): JsonResponse
    {
        try {
            $this->core()->beginTransaction();

            $workplace->update([
                'parent_id'     => $request->input('parent_id'),
                'name'          => trim($request->input('name')),
                'budget_entity' => trim($request->input('budget_entity')),
            ]);

            $this->core()->commit();

            return response()->json($workplace, JsonResponse::HTTP_OK);
        } catch (Exception $e) {
            $this->core()->rollBack();
            Log::error('Erro ao atualizar local de trabalho: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json(['message' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Excluir um local de trabalho.
     */
    public function deleteWorkplace(Workplace $workplace): JsonResponse
    {
        try {
            $this->core()->beginTransaction();

            // Desvincula filhos para evitar órfãos inconsistentes ou deleta conforme regra de negócio
            if ($workplace->children()->exists()) {
                $workplace->children()->update(['parent_id' => $workplace->parent_id]);
            }

            $workplace->delete();

            $this->core()->commit();

            return response()->json(['message' => 'Local de trabalho excluído com sucesso.'], JsonResponse::HTTP_OK);
        } catch (Exception $e) {
            $this->core()->rollBack();
            Log::error('Erro ao excluir local de trabalho: ' . $e->getMessage());

            return response()->json(['message' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
}