<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ImageController extends Controller
{
    public function upload()
    {
        return view('upload');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'required|image|max:15360',
        ]);

        $file = $request->file('image');

        // checksum hesapla
        $checksum = hash_file('sha256', $file->getRealPath());

        $existing = \App\Models\Image::where('file_checksum', $checksum)->first();
        if ($existing) {
            return back()->withErrors(['image' => 'Bu dosya zaten yuklu.']);
        }

        $folder = now()->format('Y/m');
        $path = $file->store('originals/' . $folder);

        $imageRecord = \App\Models\Image::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'mime_type' => $file->getMimeType(),
            'folder' => $folder,
            'file_checksum' => $checksum,
            'path' => $path,
        ]);

        // boyutlandirma icin kuyruga at
        \App\Jobs\ProcessImageVariants::dispatch($imageRecord);

        return back()->with('success', 'Gorsel basariyla yuklendi!');
    }

    public function list()
    {
        $images = \App\Models\Image::latest()->get();
        return view('list', compact('images'));
    }
}