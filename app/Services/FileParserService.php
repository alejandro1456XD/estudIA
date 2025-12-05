<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\Log;

class FileParserService
{
    
    public function extractText(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $path = $file->getRealPath();

        try {
            switch (strtolower($extension)) {
                case 'pdf':
                    return $this->parsePdf($path);
                case 'txt':
                    return file_get_contents($path);
                default:
                    return "Error: Formato no soportado. Por favor sube un PDF o TXT.";
            }
        } catch (\Exception $e) {
            Log::error("Error al parsear archivo: " . $e->getMessage());
            return "Error al leer el archivo. Intenta con otro documento.";
        }
    }

    protected function parsePdf(string $path): string
    {
        $parser = new Parser();
        $pdf = $parser->parseFile($path);
        return $pdf->getText();
    }
}