<?php

namespace App\Http\Controllers;

use App\Models\Image; // Assuming you have an Image model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function destroy($id)
    {
        // Find the image by ID
        $image = Image::findOrFail($id);

        // Optionally, delete the image file from storage
        if (Storage::exists($image->file_path)) {
            Storage::delete($image->file_path);
        }

        // Delete the image record from the database
        $image->delete();

        // Return a response
        return response()->json(['message' => 'Image deleted successfully'], 200);
    }
}
