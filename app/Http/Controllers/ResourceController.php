<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use App\Models\ResourceRating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ResourceController extends Controller
{
    /**
     * Muestra la lista de recursos y el Top 3.
     */
    public function index(Request $request)
    {
        // 1. OBTENER EL TOP 3 (Mejores calificados)
        $topResources = Resource::withAvg('ratings', 'rating')
            ->withCount('ratings')
            ->orderByDesc('ratings_avg_rating')
            ->orderByDesc('downloads')
            ->take(3)
            ->get();

        // 2. OBTENER EL RESTO DE RECURSOS (Con filtros)
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

    /**
     * Guarda un nuevo recurso subido por el usuario.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:100',
            'category' => 'required|string',
            'file' => 'required|file|max:51200', // Máximo 50MB
        ]);

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

        return redirect()->back()->with('success', '¡Recurso compartido exitosamente!');
    }

    /**
     * Maneja la descarga y cuenta +1.
     * CORREGIDO: Usamos response()->download para evitar errores de IDE y mejorar compatibilidad.
     */
    public function download(Resource $resource)
    {
        // 1. Incrementamos el contador
        $resource->increment('downloads');

        // 2. Verificamos que el archivo exista físicamente
        if (!Storage::disk('public')->exists($resource->file_path)) {
            return back()->with('error', 'El archivo físico no se encuentra.');
        }

        // 3. Obtenemos la ruta absoluta para la descarga
        $filePath = Storage::disk('public')->path($resource->file_path);
        
        // 4. Aseguramos que el nombre de descarga tenga la extensión correcta (ej: Guia.pdf)
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $downloadName = $resource->title . '.' . $extension;

        // 5. Forzamos la descarga usando el helper de respuesta
        return response()->download($filePath, $downloadName);
    }

    /**
     * Guarda la calificación (Estrellas).
     */
    public function rate(Request $request, Resource $resource)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5'
        ]);

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
    }
}