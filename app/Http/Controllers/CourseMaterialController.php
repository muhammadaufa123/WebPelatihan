<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CourseMaterialController extends Controller
{
    /**
     * Display a listing of materials.
     */
    public function index()
    {
        $files = collect(Storage::disk('public')->files('materials'));
        return view('materials.index', ['files' => $files]);
    }

    /**
     * Store a newly uploaded material.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'material' => 'required|file|mimes:pdf,doc,docx,ppt,pptx|max:5120',
        ]);

        $path = Storage::disk('public')->putFile('materials', $validated['material']);

        return back()->with('material_path', $path);
    }
}

