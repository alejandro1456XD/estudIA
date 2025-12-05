<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected $apiKey;
    
   
    protected $models = [
        'gemini-2.5-flash',
        'gemini-2.0-flash',
        'gemini-1.5-flash',
    ];

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY');
        
        if (!$this->apiKey) {
            throw new \Exception("Falta la GEMINI_API_KEY en el archivo .env");
        }
    }

    public function generateContent(string $content, string $type): ?array
    {
        
        if ($type === 'practice') {
            $role = "Eres un mentor técnico senior exigente.";
            $instruction = "Diseña un DESAFÍO TÉCNICO COMPLEJO simulando un entorno laboral real. 
            El escenario debe incluir problemas comunes, restricciones y requerimientos no funcionales.
            Responde ÚNICAMENTE con un JSON válido: { \"title\": \"...\", \"scenario\": \"...\", \"tasks\": [\"...\"], \"deliverables\": \"...\", \"evaluation_criteria\": [\"...\"] }";
        } else {
            $role = "Eres un profesor universitario de nivel avanzado.";
            $instruction = "Genera un EXAMEN DIFÍCIL de nivel profesional basado en el texto proporcionado.
            Las preguntas deben requerir análisis crítico, deducción o aplicación de conceptos (casos de uso), NO solo memorización de definiciones. Evita preguntas obvias.
            Responde ÚNICAMENTE con un JSON válido: { \"questions\": [ { \"id\": 1, \"question\": \"...\", \"options\": [\"...\"], \"correct_answer\": \"...\", \"explanation\": \"Explicación detallada del porqué es la correcta.\" } ] }";
        }

        $prompt = "$role $instruction. \n\nANALIZA PROFUNDAMENTE EL SIGUIENTE CONTENIDO:\n" . substr($content, 0, 25000);

        $lastException = null;

        foreach ($this->models as $model) {
            try {
                $result = $this->callGemini($model, $prompt);
                if ($result) {
                    return $result;
                }
            } catch (\Exception $e) {
                Log::warning("Fallo con modelo $model: " . $e->getMessage());
                $lastException = $e;
            }
        }

        throw $lastException ?? new \Exception("Ningún modelo de IA pudo procesar la solicitud.");
    }

    protected function callGemini(string $modelName, string $prompt): ?array
    {
        $modelName = str_replace('models/', '', $modelName);
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$modelName}:generateContent";

        $response = Http::withoutVerifying()
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post("{$url}?key={$this->apiKey}", [
                'contents' => [['parts' => [['text' => $prompt]]]],
                'generationConfig' => [
                    'temperature' => 0.8, 
                    'maxOutputTokens' => 5000,
                ],
                'safetySettings' => [
                    ['category' => 'HARM_CATEGORY_HARASSMENT', 'threshold' => 'BLOCK_ONLY_HIGH'],
                    ['category' => 'HARM_CATEGORY_HATE_SPEECH', 'threshold' => 'BLOCK_ONLY_HIGH'],
                    ['category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT', 'threshold' => 'BLOCK_ONLY_HIGH'],
                    ['category' => 'HARM_CATEGORY_DANGEROUS_CONTENT', 'threshold' => 'BLOCK_ONLY_HIGH'],
                ]
            ]);

        if ($response->failed()) {
            $errorBody = $response->json();
            $errorMessage = $errorBody['error']['message'] ?? $response->body();
            
            if ($response->status() === 404) {
                throw new \Exception("Modelo no encontrado ($modelName).");
            }
            throw new \Exception("Gemini API Error ({$response->status()}): " . $errorMessage);
        }

        $data = $response->json();
        $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';

        if (empty($text)) {
            throw new \Exception("Respuesta vacía.");
        }

        
        if (preg_match('/\{.*\}/s', $text, $matches)) {
            $cleanJson = $matches[0];
        } else {
            $cleanJson = $text;
        }
        
        $cleanJson = str_replace(['```json', '```', 'json'], '', $cleanJson);
        $decoded = json_decode($cleanJson, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $cleanJson = preg_replace('/,\s*([\]}])/m', '$1', $cleanJson);
            $decoded = json_decode($cleanJson, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception("Error JSON: " . json_last_error_msg());
            }
        }

        return $decoded;
    }
}