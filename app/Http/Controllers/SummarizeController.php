<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SummarizeController extends Controller
{
    private string $apiUrl;
    private string $apiKey;

    public function __construct()
    {
        $this->apiUrl = 'https://api.openai.com/v1/';
        $this->apiKey = config('openai.api_key');
    }

    public function __invoke(Request $request): JsonResponse
    {
        $this->validate($request, [
            'prompt' => 'required|string',
            'temperature' => 'numeric',
            'top_p' => 'numeric',
            'frequency_penalty' => 'numeric',
            'presence_penalty' => 'numeric',
        ]);

        $response = Http::withOptions(['verify' => false])
            ->withToken($this->apiKey)
            ->baseUrl($this->apiUrl)
            ->post('engines/davinci/completions', [
                'prompt' => $this->constructPrompt($request['prompt']),
                'temperature' => $request->input('temperature', 0.3),
                'max_tokens' => 60,
                'top_p' => $request->input('top_p', 1.0),
                'frequency_penalty' => $request->input('frequency_penalty', 0.0),
                'presence_penalty' => $request->input('presence_penalty', 0.0),
            ]);

        return response()->json($response->json());
    }

    protected function constructPrompt(string $prompt): string
    {
        return $prompt."\n\ntl;dr:";
    }
}
