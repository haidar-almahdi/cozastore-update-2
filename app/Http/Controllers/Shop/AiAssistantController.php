<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Services\AiAssistantService;
use Illuminate\Http\Request;

class AiAssistantController extends Controller
{
    public function chat(Request $request, AiAssistantService $assistant)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        return response()->json(
            $assistant->handle($validated['message'])
        );
    }
}
