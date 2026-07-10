<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TourController extends Controller
{
    public function complete(Request $request): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->has_completed_tour = true;
        $user->save();

        return response()->json(['ok' => true]);
    }

    public function reset(Request $request): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->has_completed_tour = false;
        $user->save();

        return response()->json(['ok' => true]);
    }
}
