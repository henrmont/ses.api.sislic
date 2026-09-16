<?php

namespace App\Services;

use App\Models\Module;
use App\Models\Workstation;
use Exception;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class WorkstationService
{
    /**
     * Criar uma nova estação de trabalho.
     */
    public function createWorkstation(Request $request): JsonResponse
    {
        try {
            Workstation::on('core')->create([
                'name' => $request->name,
                'code' => $request->code,
            ]);

            return response()->json(['message' => 'Estação de trabalho criada com sucesso.'], JsonResponse::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Erro ao criar estação de trabalho: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json(['message' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Atualizar uma estação de trabalho.
     */
    public function updateWorkstation(Request $request, Workstation $workstation): JsonResponse
    {
        try {
            $workstation->update([
                'name' => $request->name,
                'code' => $request->code,
            ]);

            return response()->json(['message' => 'Estação de trabalho atualizada com sucesso.'], JsonResponse::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Erro ao atualizar estação de trabalho: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'Erro ao atualizar estação de trabalho.'], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Deletar uma estação de trabalho.
     */
    public function deleteWorkstation(Workstation $workstation): JsonResponse
    {
        try {
            $workstation->delete();

            return response()->json(['message' => 'Estação de trabalho deletada com sucesso.'], JsonResponse::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Erro ao deletar estação de trabalho: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'Erro ao deletar estação de trabalho.'], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
}