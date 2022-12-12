<?php

namespace App\Http\Controllers;

use App\Models\Fileupload;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class FileuploadController extends Controller
{
    //
    public function index()
    {
    }
    public function create()
    {
    }
    public function store(Request $request)
    {
        $originalName = $request->file('file')->getClientOriginalName();
        $imageName = time() . '_' . $originalName .  '.' . $request->file->extension();
        $request->file->move(public_path('images'), $imageName);
        $image = Image::make(public_path('images/' . $imageName));
        $image->save();
        $fileupload = new Fileupload();
        $fileupload->filename = $imageName;
        $fileupload->save();
        // Run steghide on the image to check to see if it's possible
        $fileupload->runSteghide();
        return response()->json([
            'message' => 'Image uploaded successfully',
            'filename' => $imageName
        ]);
    }
    public function list()
    {
        $fileuploads = Fileupload::all();
        return response()->json([
            'message' => 'Image list',
            'data' => $fileuploads
        ]);
    }
    public function show($id)
    {
        return response()->json([
            'message' => 'Image show',
            'data' => Fileupload::find($id)
        ]);
    }
    public function delete($id)
    {
        $fileupload = Fileupload::find($id);
        Storage::delete('images/' . $fileupload->filename);
        $fileupload->delete();
        return response()->json([
            'message' => 'Image deleted successfully',
            'filename' => $fileupload->filename
        ]);
    }

    public function steg($id)
    {
        $fileupload = Fileupload::find($id);
        $output = $fileupload->runSteghide();
        return response()->json([
            'message' => 'Image steghide',
            'data' => $output
        ]);
    }
}
