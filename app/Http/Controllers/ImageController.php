<?php

namespace App\Http\Controllers;

use App\Models\Image; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function destroy($id)
    {
        // Find the image by ID
        $image = Image::findOrFail($id);

        // Fix the file path by removing "/storage/" 
        // "storage/images/a.jpeg" becomes "images/a.jpeg"
        $filePath = str_replace('/storage/', '', $image->file_path); // Now it becomes "images/a.jpeg"

        // Check if file exists in the 'public' disk
        if (Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        } else {
            dd("Error on deleting image.");
        }

        // Delete the image record from the database
        $image->delete();

        return response()->json(['message' => 'Image deleted successfully'], 200);
    }

}
