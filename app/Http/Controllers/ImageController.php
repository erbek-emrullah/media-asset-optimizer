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
        // 1. Dosyayi kontrol et
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'required|image|max:15360',
        ]);

        $file = $request->file('image');

        // 2. Checksum hesapla (ayni dosya tekrar yuklenmesin)
        $checksum = hash_file('sha256', $file->getRealPath());

        $existing = \App\Models\Image::where('file_checksum', $checksum)->first();
        if ($existing) {
            return back()->withErrors(['image' => 'Bu gorsel daha once yuklenmis.']);
        }

        // 3. Klasor yolunu olustur (2026/08 gibi)
        $folder = now()->format('Y/m');

        // 4. Dosyayi diske kaydet
        $path = $file->store('originals/' . $folder, 'public');

        // 5. Veritabanina yaz
        \App\Models\Image::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'mime_type' => $file->getMimeType(),
            'folder' => $folder,
            'file_checksum' => $checksum,
        ]);

        return back()->with('success', 'Gorsel basariyla yuklendi!');
    }

    public function list()
    {
        $images = \App\Models\Image::latest()->get();
        return view('list', compact('images'));
    }
}