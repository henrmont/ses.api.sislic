<?php

namespace App\Services;

use App\Models\Dfd;
use App\Models\DfdItem;
use App\Models\DfdMember;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DfdService
{
    /**
     * Cadastrar novo DFD com seus itens e membros.
     */
    public function createDfd(Request $request): JsonResponse
    {
        $dfd = DB::transaction(function () use ($request) {
            $dfdData = $request->except(['items', 'members']);
            $dfd = Dfd::create($dfdData);

            if ($request->has('items') && is_array($request->items)) {
                $dfd->items()->createMany($request->items);
            }

            if ($request->has('members') && is_array($request->members)) {
                $dfd->members()->createMany($request->members);
            }

            return $dfd;
        });

        return response()->json(
            $dfd->load(['workplace', 'ownerProfessional', 'validateProfessional', 'items', 'members.professional']),
            JsonResponse::HTTP_CREATED
        );
    }

    /**
     * Atualizar DFD e sincronizar itens e membros.
     */
    public function updateDfd(Dfd $dfd, Request $request): JsonResponse
    {
        DB::transaction(function () use ($dfd, $request) {
            $dfdData = $request->except(['items', 'members']);
            $dfd->update($dfdData);

            if ($request->has('items') && is_array($request->items)) {
                $dfd->items()->delete();
                $dfd->items()->createMany($request->items);
            }

            if ($request->has('members') && is_array($request->members)) {
                $dfd->members()->delete();
                $dfd->members()->createMany($request->members);
            }
        });

        return response()->json(
            $dfd->load(['workplace', 'ownerProfessional', 'validateProfessional', 'items', 'members.professional']),
            JsonResponse::HTTP_OK
        );
    }

    /**
     * Excluir um DFD (SoftDelete).
     */
    public function deleteDfd(Dfd $dfd): JsonResponse
    {
        $dfd->delete();

        return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
    }

    /**
     * Operações de Tramitação / Alteração de Estado
     */
    public function archiveDfd(Dfd $dfd): JsonResponse
    {
        $dfd->update(['is_archived' => true]);

        return response()->json($dfd, JsonResponse::HTTP_OK);
    }

    public function validateDfd(Dfd $dfd, Request $request): JsonResponse
    {
        $dfd->update([
            'is_valid' => true,
            'validate_professional_id' => $request->validate_professional_id,
        ]);

        return response()->json($dfd, JsonResponse::HTTP_OK);
    }

    /*
    |--------------------------------------------------------------------------
    | Gestão Individual de Itens
    |--------------------------------------------------------------------------
    */

    public function createItem(Dfd $dfd, Request $request): JsonResponse
    {
        $item = $dfd->items()->create($request->all());

        return response()->json($item, JsonResponse::HTTP_CREATED);
    }

    public function updateItem(DfdItem $item, Request $request): JsonResponse
    {
        $item->update($request->all());

        return response()->json($item, JsonResponse::HTTP_OK);
    }

    public function deleteItem(DfdItem $item): JsonResponse
    {
        $item->delete();

        return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
    }

    /*
    |--------------------------------------------------------------------------
    | Gestão Individual de Membros
    |--------------------------------------------------------------------------
    */

    public function createMember(Dfd $dfd, Request $request): JsonResponse
    {
        $member = $dfd->members()->create($request->all());

        return response()->json($member->load('professional'), JsonResponse::HTTP_CREATED);
    }

    public function updateMember(DfdMember $member, Request $request): JsonResponse
    {
        $member->update($request->all());

        return response()->json($member->load('professional'), JsonResponse::HTTP_OK);
    }

    public function deleteMember(DfdMember $member): JsonResponse
    {
        $member->delete();

        return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
    }
}