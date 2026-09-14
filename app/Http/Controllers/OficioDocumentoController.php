<?php

namespace App\Http\Controllers;

use App\Models\Oficio;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\HeaderUtils;

class OficioDocumentoController extends Controller
{
    public function __invoke(Media $media): BinaryFileResponse
    {
        abort_unless(
            $media->model_type === Oficio::class
            && in_array($media->collection_name, ['pdfs', 'adjuntos'], true)
            && is_file($media->getPath()),
            404,
        );

        $extension = pathinfo($media->file_name, PATHINFO_EXTENSION);
        $nombreArchivo = $extension === '' ? $media->name : $media->name.'.'.$extension;

        return response()->file($media->getPath(), [
            'Content-Type' => $media->mime_type ?? 'application/pdf',
            'Content-Disposition' => HeaderUtils::makeDisposition(
                HeaderUtils::DISPOSITION_INLINE,
                $nombreArchivo,
            ),
        ]);
    }
}
