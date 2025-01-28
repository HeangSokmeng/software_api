<?php

namespace App\Http\Controllers\web;

use ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Document;
use Helper;
use Illuminate\Http\Request;

class WebServiceController extends Controller
{
    protected $dir = 'DocumentPhotos';
    public function doc(Request $req)
    {
        $docs =  Document::orderByDesc('id')->get();
        foreach ($docs as $d) {
            $d->doc_photo_url = Helper::getImageUrl($d->doc_photo, 1, $this->dir) ?? null;
            $d->doc_file_url = Helper::getImageUrl($d->doc_file, 1, $this->dir) ?? null;
            $d->create_by = $d->creator->username ?? 'Null';
            $d->category_name = $d->category->cate_name ?? 'Null';
            $d->author_name = $d->author->auth_name ?? 'Null';
            unset($d->creator, $d->category, $d->author);
        }
        return ApiResponse::Pagination($docs, $req);
    }
    public function docOne(Request $req, $id)
    {
        $doc = Document::find($id);
        if (!$doc) {
            return response()->json(['error' => 'Document not found'], 404);
        }
        $doc->doc_photo_url = Helper::getImageUrl($doc->doc_photo, 1, $this->dir) ?? null;
        $doc->doc_file_url = Helper::getImageUrl($doc->doc_file, 1, $this->dir) ?? null;
        $doc->create_by = $doc->creator->username ?? 'Null';
        $doc->category_name = $doc->category->cate_name ?? 'Null';
        $doc->author_name = $doc->author->auth_name ?? 'Null';
        unset($doc->creator, $doc->category, $doc->author);
        return ApiResponse::JsonResult($doc);
    }

    public function category()
    {
        $categories = Category::groupBy('cate_id')->get(['cate_id', 'cate_name']);
        return ApiResponse::JsonResult($categories);
    }

    public function getDocByCategory($id)
    {
        $docs = Document::where('category_id', $id)->orderByDesc('id')->get();
        foreach ($docs as $d) {
            $d->doc_photo_url = Helper::getImageUrl($d->doc_photo, 1, $this->dir) ?? null;
            $d->doc_file_url = Helper::getImageUrl($d->doc_file, 1, $this->dir) ?? null;
            $d->create_by = $d->creator->username ?? 'Null';
            $d->category_name = $d->category->cate_name ?? 'Null';
            $d->author_name = $d->author->auth_name ?? 'Null';
            unset($d->creator, $d->category, $d->author);
        }
        return ApiResponse::JsonResult($docs);
    }

    public function download($id)
    {
        try {
            $document = Document::findOrFail($id);

            // Construct the full file path
            $filePath = public_path('uploads/images/1/DocumentPhotos/' . $document->doc_file);

            // Check if file exists
            if (!file_exists($filePath)) {
                return response()->json([
                    'message' => 'File not found'
                ], 404);
            }

            // Return file download response
            return response()->download(
                $filePath,
                $document->doc_name ?? $document->doc_file, // Original name or stored name
                [
                    'Content-Type' => mime_content_type($filePath),
                    'Cache-Control' => 'no-cache, no-store, must-revalidate',
                    'Pragma' => 'no-cache',
                    'Expires' => '0'
                ]
            );

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Download failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
