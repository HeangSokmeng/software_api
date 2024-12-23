<?php

namespace App\Http\Controllers;

use ApiResponse;
use App\Models\Author;
use App\Models\Category;
use App\Models\Document;
use App\Services\UserService;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentsController extends Controller
{
    protected $dir = 'DocumentPhotos';
    public function docValidation(Request $req)
    {
        return validator($req->all(), [
            'doc_name' => 'nullable|string|max:255',
            'author_id' => 'nullable',
            'category_id' => 'nullable',
            'doc_title' => 'nullable|string|max:255',
            'doc_color' => 'nullable|string|max:50',
            'doc_size' => 'nullable|string|max:50',
            'doc_page' => 'nullable|integer',
            'doc_created_date' => 'nullable|date',
            'doc_published_date' => 'nullable|date',
            'doc_publication_year' => 'nullable|integer',
            'doc_keywords' => 'nullable|string',
            'doc_photo' => 'nullable',
            // 'doc_file' => 'required|file|mimes:pdf,doc,docx|max:20480', // Ensure doc_file is required and valid
        ]);
    }


    public function saveDocument(Request $req) {
        $validator = $this->docValidation($req);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422); // Return validation errors
        }

        $input = $validator->validated();
        $user = UserService::getAuthUser();
        $auth_id = $input['author_id'];
        $cate_id = $input['category_id'];
        $auth = Author::find($auth_id);
        $cate = Category::find($cate_id);

        if (!$auth_id) return ApiResponse::JsonResult(null, false, 'Input auth id');
        if (!$cate_id) return ApiResponse::JsonResult(null, false, 'Input category id');
        if (!$user) return ApiResponse::NotFound('User not found');
        if (!$cate) return ApiResponse::NotFound('Category not found');
        if (!$auth) return ApiResponse::NotFound('Not found Author');

        $input['doc_publication_year'] = $input['doc_publication_year'] ?? null;
        $input['create_uid'] = $user->user_id;
        $input['update_uid'] = $user->user_id;
        $input['company_id'] = $user->company_id;

        // if ($req->hasFile('doc_file') && $req->file('doc_file')->isValid()) {
        //     $input['doc_file'] = $req->file('doc_file')->store('public/uploads/documents');
        // } else {
        //     return response()->json(['error' => 'Invalid or missing file'], 400);
        // }




       $base64Photo = $input['doc_photo'] ?? null;
        unset($input['doc_photo']);
        $photo = Helper::base64ToImageFile($base64Photo, $user->company_id, $this->dir);
        $input['doc_photo'] = $photo;
        $create = Document::create($input);
        if (!$create) {
            Helper::deleteImageFile($photo, $user->company_id, $this->dir);
            return ApiResponse::Error('Fail to create');
        }
        return ApiResponse::JsonResult($create, false, 'Created');

    }


    public function getListDocuments(Request $req) {
        $user = UserService::getAuthUser();
        $docs= Document::with('creator', 'category', 'author')
            ->orderBy('id', 'desc')
            ->get();
        foreach ($docs as $d) {
            $d->doc_photo = Helper::getImageUrl($d->doc_photo, $user->company_id, $this->dir) ?? null;
            $d->create_by = $d->creator->username ?? 'Null';
            $d->category_name = $d->category->cate_name?? 'Null';
            $d->author_name = $d->author->auth_name?? 'Null';
            unset($d->creator, $d->category, $d->author);
        }
        return ApiResponse::Pagination($docs, $req, "Success");
    }



    public function getOneDocument($id) {
        $user = UserService::getAuthUser();
        $doc = Document::with('creator')->find($id);
        if (!$doc) return ApiResponse::NotFound('Document not found');
        $doc->doc_photo = Helper::getImageUrl($doc->doc_photo, $user->company_id, $this->dir);
        $doc->create_by = $doc->creator->username ?? 'Unknown';
        unset($doc->doc_photo, $doc->creator);
        return ApiResponse::JsonResult($doc, false, 'Success');
    }

    public function updateDocument($id, Request $req) {
    // Validate input
    $validator = $this->docValidation($req);
    if ($validator->fails()) {
        return ApiResponse::ValidateFail($validator->errors()->first(), $validator->errors());
    }

    $input = $validator->validated();
    $user = UserService::getAuthUser();

    // Find the document
    $doc = Document::find($id);
    if (!$doc) {
        return ApiResponse::NotFound('Document not found');
    }

    // Handle file update for doc_file
    if ($req->hasFile('doc_file')) {
        $file = $req->file('doc_file');
        if ($file instanceof \Illuminate\Http\UploadedFile) {
            // Store new file
            $filePath = $file->storeAs($this->dir, uniqid() . '.' . $file->getClientOriginalExtension());

            // Delete old file if it exists
            if ($doc->doc_file) {
                Storage::delete($doc->doc_file);
            }

            $input['doc_file'] = $filePath;
        }
    } else {
        unset($input['doc_file']); // Remove invalid file input if no new file is uploaded
    }

    // Handle photo update similarly...

    // Update user ID for tracking
    $input['update_uid'] = $user->user_id;

    // Perform update
    if ($doc->update($input)) {
        return ApiResponse::JsonResult($doc->fresh(), true, 'Document updated successfully');
    } else {
        // Clean up in case of failure
        if (isset($newPhoto)) {
            Helper::deleteImageFile($newPhoto, $user->company_id, $this->dir);
        }
        if (isset($filePath)) {
            Storage::delete($filePath);
        }
        return ApiResponse::Error('Failed to update document');
    }
}




    public function  deleteDocument($id){
        $user = UserService::getAuthUser();
        $doc = Document::find($id);
        if (!$doc) return ApiResponse::NotFound('Document not found');
        $delete = $doc->delete();
        if (!$delete) return ApiResponse::Error('Fail to delete');
        Helper::deleteImageFile($doc->doc_photo, $user->company_id, $this->dir);
        return ApiResponse::JsonResult(Null, false, 'Deleted');
    }
}
