<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Image;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'image' => ['required', 'file', 'mimetypes:image/jpeg,image/png,image/bmp,image/svg+xml,image/webp,image/gif', 'max:15360'],
        ]);

        $file = $request->file('image');
        $checksum = hash_file('sha256', $file->getRealPath());

        $existing = Image::where('file_checksum', $checksum)->first();
        if ($existing) {
            return response()->json(['error' => 'bu dosya zaten yuklu'], 409);
        }

        $folder = now()->format('Y/m');
        $path = $file->store('originals/' . $folder);

        $imageRecord = Image::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'mime_type' => $file->getMimeType(),
            'folder' => $folder,
            'file_checksum' => $checksum,
            'path' => $path,
        ]);

        \App\Jobs\ProcessImageVariants::dispatch($imageRecord);

        return response()->json([
            'message' => 'gorsel yuklendi',
            'image_id' => $imageRecord->id,
        ], 201);
    }

    public function index()
    {
        $images = Image::latest()->paginate(20);

        return response()->json($images);
    }
}