<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ImageController extends Controller
{
    /**
     * Serve profile photos from storage
     */
    public function profilePhoto($filename)
    {
        $path = 'profile_photos/' . $filename;
        
        // Check if file exists
        if (!Storage::disk('public')->exists($path)) {
            abort(404, 'Image not found');
        }
        
        // Get the file contents
        $fileContents = Storage::disk('public')->get($path);
        
        // Get file info for mime type
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $mimeTypes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'svg' => 'image/svg+xml',
        ];
        
        $mimeType = $mimeTypes[$extension] ?? 'application/octet-stream';
        
        // Return the file response with proper headers
        return response($fileContents, 200)
            ->header('Content-Type', $mimeType)
            ->header('Cache-Control', 'public, max-age=31536000')
            ->header('Expires', gmdate('D, d M Y H:i:s \G\M\T', time() + 31536000));
    }
    
    /**
     * Serve any file from storage with security checks
     */
    public function serveFile(Request $request, $path)
    {
        // Security: Only allow image file types
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
        
        // Check file extension
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (!in_array($extension, $allowedExtensions)) {
            abort(403, 'File type not allowed');
        }
        
        // Check if file exists
        if (!Storage::disk('public')->exists($path)) {
            abort(404, 'File not found');
        }
        
        // Get the file contents
        $fileContents = Storage::disk('public')->get($path);
        
        // Get mime type
        $mimeTypes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'svg' => 'image/svg+xml',
        ];
        
        $mimeType = $mimeTypes[$extension] ?? 'application/octet-stream';
        
        // Return the file response with proper headers
        return response($fileContents, 200)
            ->header('Content-Type', $mimeType)
            ->header('Cache-Control', 'public, max-age=31536000')
            ->header('Expires', gmdate('D, d M Y H:i:s \G\M\T', time() + 31536000));
    }
}
