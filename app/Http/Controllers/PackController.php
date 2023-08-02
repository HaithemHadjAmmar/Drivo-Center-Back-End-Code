<?php

namespace App\Http\Controllers;

use App\Models\Pack;


class PackController extends Controller
{
    /**
     * Get all packs.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPack()
    {
        $packs = Pack::all();
        return response()->json([
            'success' => true,
            'data' => $packs,
        ]);
    }
}

