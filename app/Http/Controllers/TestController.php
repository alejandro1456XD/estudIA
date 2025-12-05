<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Test;
use App\Models\User;
use App\Services\FileParserService;
use App\Services\GeminiService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TestController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();
        $tests = $user->tests()->latest()->get();
        return view('tests.index', compact('tests'));
    }

    public function create()
    {
        return view('tests.create');
    }

    public function store(Request $request, FileParserService $parser, GeminiService $gemini)
    {
        
        $request->validate([
            'name' => 'required|string|max:255',
            'test_type' => 'required|in:exam,practice',
            'mode' => 'required|in:file,prompt',
        ]);

      
        $test = Test::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'source_content' => 'Procesando contenido...', 
            'status' => 'pending', 
            'quiz_structure' => [],
        ]);

        try {
           
            $sourceContent = "";
            
            if ($request->mode === 'file') {
                if (!$request->hasFile('study_file') || !$request->file('study_file')->isValid()) {
                    throw new \Exception("No subiste ningún archivo válido.");
                }
                
                $sourceContent = $parser->extractText($request->file('study_file'));
                
                if (str_starts_with($sourceContent, 'Error')) {
                    throw new \Exception($sourceContent);
                }

            } else {
                $sourceContent = $request->prompt_input;
                if (empty($sourceContent)) {
                    throw new \Exception("El campo de tema o apuntes no puede estar vacío.");
                }
            }

            if (strlen($sourceContent) < 10) { 
                throw new \Exception("El contenido es demasiado corto.");
            }

            $test->update(['source_content' => $sourceContent]);

            
            $quizData = $gemini->generateContent($sourceContent, $request->test_type);

            if ($quizData) {
                $quizData['type'] = $request->test_type; 
                
                $test->update([
                    'quiz_structure' => $quizData,
                    'status' => 'generated'
                ]);

                return redirect()->route('tests.index')->with('status', '¡Actividad creada con éxito!');
            } else {
                throw new \Exception("La IA no pudo generar la actividad.");
            }

        } catch (\Exception $e) {
            Log::error("Error TestController: " . $e->getMessage());
            
            if ($test) {
                $test->update(['status' => 'failed']);
            }
            
            return redirect()->route('tests.index')->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function show(Test $test)
    {
        if ($test->user_id !== Auth::id()) {
            abort(403);
        }
        return view('tests.show', compact('test'));
    }

    // --- NUEVO MÉTODO PARA BORRAR ---
    public function destroy(Test $test)
    {
        // Seguridad: Solo el dueño puede borrar
        if ($test->user_id !== Auth::id()) {
            abort(403);
        }

        $test->delete();

        return redirect()->route('tests.index')->with('status', 'Evaluación eliminada correctamente.');
    }
}