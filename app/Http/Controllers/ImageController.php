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
        // ileride dosya yukleme kodu buraya gelecek
        return back()->with('success', 'Test basarili');
    }

    public function list()
    {
        $images = \App\Models\Image::latest()->get();
        return view('list', compact('images'));
    }
}