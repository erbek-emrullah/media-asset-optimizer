<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Image;

class ImageController extends Controller
{
    public function index()
    {
        $sizes = config('image_sizes.sizes');
        return view('dashboard', compact('sizes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'required|file|mimetypes:image/jpeg,image/png,image/bmp,image/svg+xml,image/webp,image/gif|max:15360',
        ]);

        $file = $request->file('image');
        $checksum = hash_file('sha256', $file->getRealPath());

        $existing = Image::where('file_checksum', $checksum)->first();
        if ($existing) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Bu dosya zaten yüklü.'], 409);
            }
            return back()->withErrors(['image' => 'Bu dosya zaten yuklu.']);
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

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Görsel yüklendi',
                'image' => $imageRecord,
            ], 201);
        }

        return back()->with('success', 'Gorsel basariyla yuklendi!');
    }

    public function ajaxList(Request $request)
    {
        $images = Image::latest()->paginate(12);
        return response()->json($images);
    }
}