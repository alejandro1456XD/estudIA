<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use App\Models\ResourceRating;
use App\Services\GamificationService; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Log; 

class ResourceController extends Controller
{
   
    public function index(Request $request)
    {
        
        $topResources = Resource::withAvg('ratings', 'rating')
            ->withCount('ratings')
            ->orderByDesc('ratings_avg_rating')
            ->orderByDesc('downloads')
            ->take(3)
            ->get();

        
        $query = Resource::query()->with('user');

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category') && $request->category !== 'Todos') {
            $query->where('category', $request->category);
        }

        $resources = $query->latest()->paginate(12);

        return view('resources.index', compact('topResources', 'resources'));
    }

   
    public function store(Request $request, GamificationService $gamification)
    {
        $request->validate([
            'title' => 'required|string|max:100',
            'category' => 'required|string',
            'file' => 'required|file|max:51200', 
        ]);

        
        DB::beginTransaction();
        $path = null; 

        try {
            $file = $request->file('file');
            $mime = $file->getMimeType();
            
            
            $type = 'file';
            if (str_contains($mime, 'pdf')) $type = 'pdf';
            elseif (str_contains($mime, 'image')) $type = 'image';
            elseif (str_contains($mime, 'video')) $type = 'video';
            elseif (str_contains($mime, 'zip') || str_contains($mime, 'rar')) $type = 'zip';

            
            $sizeBytes = $file->getSize();
            $size = number_format($sizeBytes / 1048576, 2) . ' MB';

            
            $path = $file->store('resources', 'public');

            
            Resource::create([
                'user_id' => Auth::id(),
                'title' => $request->title,
                'category' => $request->category,
                'file_path' => $path,
                'file_type' => $type,
                'file_size' => $size,
                'downloads' => 0
            ]);

            try {
                $gamification->earn(Auth::user(), 'upload_resource');
            } catch (\Exception $e) {
                Log::error("Error gamificación al subir recurso: " . $e->getMessage());
            }

            DB::commit(); 

            return redirect()->back()->with('success', '¡Recurso compartido exitosamente! Has ganado monedas.');

        } catch (\Exception $e) {
            DB::rollBack(); 
            
            
            if ($path && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }

            Log::error("Error subiendo recurso: " . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Hubo un error al subir tu recurso. Por favor intenta de nuevo.')
                ->withInput();
        }
    }

   
    public function download(Resource $resource)
    {
        try {
            
            if (!Storage::disk('public')->exists($resource->file_path)) {
                return back()->with('error', 'El archivo físico no se encuentra disponible.');
            }

            $resource->increment('downloads');

            
            $filePath = Storage::disk('public')->path($resource->file_path);
            $extension = pathinfo($filePath, PATHINFO_EXTENSION);
            
            
            $cleanTitle = preg_replace('/[^A-Za-z0-9\- ]/', '', $resource->title);
            $downloadName = $cleanTitle . '.' . $extension;

            return response()->download($filePath, $downloadName);

        } catch (\Exception $e) {
            Log::error("Error en descarga: " . $e->getMessage());
            return back()->with('error', 'Error al procesar la descarga.');
        }
    }

    
    public function rate(Request $request, Resource $resource)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5'
        ]);

        try {
            ResourceRating::updateOrCreate(
                [
                    'resource_id' => $resource->id,
                    'user_id' => Auth::id()
                ],
                [
                    'rating' => $request->rating
                ]
            );

            return redirect()->back()->with('success', '¡Gracias por calificar este recurso!');

        } catch (\Exception $e) {
            Log::error("Error al calificar: " . $e->getMessage());
            return redirect()->back()->with('error', 'No se pudo guardar tu calificación.');
        }
    }
}