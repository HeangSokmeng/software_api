<?php

namespace App\Http\Controllers;

use ApiResponse;
use App\Models\Author;
use App\Models\Category;
use App\Models\Document;
use App\Services\UserService;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DocumentsController extends Controller
{
    protected $dir = 'DocumentPhotos';
    public function docValidation(Request $req)
    {
        return validator($req->all(), [
            'doc_name' => 'nullable|string|max:255',
            'author_id' => 'required|exists:authors,auth_id',
            'category_id' => 'required|exists:categories,cate_id',
            'doc_title' => 'nullable|string|max:255',
            'doc_color' => 'nullable|string|max:50',
            'doc_size' => 'nullable|string|max:50',
            'doc_page' => 'nullable|integer',
            'doc_created_date' => 'nullable|date',
            'doc_published_date' => 'nullable|date',
            'doc_publication_year' => 'nullable',
            'doc_keywords' => 'nullable|string',
            'doc_photo' => 'nullable',
            'doc_file' => 'nullable|file|mimes:pdf,doc,docx|max:20480',
        ]);
    }

    public function saveDocument(Request $req)
    {
        try {
            // Validate the request
            $validator = $this->docValidation($req);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            // Get validated input
            $input = $validator->validated();
            // Handle nullable fields
            $nullableFields = [
                'doc_name',
                'author_id',
                'category_id',
                'doc_title',
                'doc_color',
                'doc_size',
                'doc_page',
                'doc_created_date',
                'doc_published_date',
                'doc_publication_year',
                'doc_keywords',
                'doc_photo',
                'doc_file',
            ];
            foreach ($nullableFields as $field) {
                if (empty($input[$field])) {
                    $input[$field] = null;
                }
            }
            // Filter input to remove null values
            $input = array_filter($input, fn($value) => $value !== null);
            // Get authenticated user
            $user = UserService::getAuthUser();
            if (!$user) {
                return ApiResponse::NotFound('User not found');
            }
            // Handle document file upload
            if ($req->hasFile('doc_file')) {
                $docFile = $req->file('doc_file');
                $docFileName = time() . '_' . $docFile->getClientOriginalName();
                $destinationPath = 'D:\programming\laravel\sarana\ass_software\webApi\public\uploads\images\1\DocumentPhotos';
                $docFile->move($destinationPath, $docFileName);
                $input['doc_file'] = $docFileName;
            }
            // Handle photo file upload
            if ($req->hasFile('doc_photo')) {
                $photoFile = $req->file('doc_photo');
                $photoFileName = time() . '_photo_' . $photoFile->getClientOriginalName();
                $destinationPath = 'D:\programming\laravel\sarana\ass_software\webApi\public\uploads\images\1\DocumentPhotos';
                $photoFile->move($destinationPath, $photoFileName);
                $input['doc_photo'] = $photoFileName;
            } else {
                $input['doc_photo'] = null;
            }
            $input['create_uid'] = $user->user_id;
            $input['update_uid'] = $user->user_id;
            $input['company_id'] = $user->company_id;
            $document = Document::create($input);
            if (!$document) {
                if (isset($input['doc_file'])) {
                    unlink($destinationPath . '/' . $input['doc_file']);
                }
                if (isset($input['doc_photo'])) {
                    unlink($destinationPath . '/' . $input['doc_photo']);
                }
                return ApiResponse::Error('Failed to create document');
            }
            return ApiResponse::JsonResult($document, true, 'Document created successfully');
        } catch (\Exception $e) {
            Log::error('Document creation error: ' . $e->getMessage());
            return ApiResponse::Error('An error occurred while creating the document');
        }
    }


    public function getListDocuments(Request $req)
    {
        $user = UserService::getAuthUser();
        $docs = Document::with('creator', 'category', 'author')
            ->orderBy('id', 'desc')
            ->get();
        foreach ($docs as $d) {
            $d->doc_photo_url = Helper::getImageUrl($d->doc_photo, $user->company_id, $this->dir) ?? null;
            $d->doc_file_url = Helper::getImageUrl($d->doc_file, $user->company_id, $this->dir) ?? null;
            $d->create_by = $d->creator->username ?? 'Null';
            $d->category_name = $d->category->cate_name ?? 'Null';
            $d->author_name = $d->author->auth_name ?? 'Null';
            unset($d->creator, $d->category, $d->author);
        }
        return ApiResponse::Pagination($docs, $req, "Success");
    }


    public function getOneDocument($id)
    {
        $user = UserService::getAuthUser();
        $doc = Document::with('creator')->find($id);
        if (!$doc) return ApiResponse::NotFound('Document not found');
        $doc->doc_photo = Helper::getImageUrl($doc->doc_photo, $user->company_id, $this->dir);
        $doc->create_by = $doc->creator->username ?? 'Unknown';
        unset($doc->doc_photo, $doc->creator);
        return ApiResponse::JsonResult($doc, false, 'Success');
    }

    // public function updateDocument(Request $req, $id)
    // {
    //     try {
    //         $document = Document::find($id);
    //         if (!$document) {
    //             return ApiResponse::NotFound('Document not found');
    //         }
    //         $rules = [
    //             'doc_name' => 'required|string|max:255',
    //             'author_id' => 'required|exists:authors,id',
    //             'category_id' => 'required|exists:categories,id',
    //             'description' => 'nullable|string',
    //             'doc_file' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
    //             'doc_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
    //         ];
    //         $validator = Validator::make($req->all(), $rules);
    //         if ($validator->fails()) {
    //             return response()->json(['errors' => $validator->errors()], 422);
    //         }
    //         $updateData = $validator->validated();
    //         $updateData['update_uid'] = UserService::getAuthUser()->user_id;
    //         if ($req->hasFile('doc_file')) {
    //             if ($document->doc_file) {
    //                 unlink(public_path($this->dir . '/' . $document->doc_file));
    //             }
    //             $docFile = $req->file('doc_file');
    //             $updateData['doc_file'] = time() . '_' . $docFile->getClientOriginalName();
    //             $docFile->move(public_path($this->dir), $updateData['doc_file']);
    //         }
    //         if ($req->hasFile('doc_photo')) {
    //             if ($document->doc_photo) {
    //                 unlink(public_path($this->dir . '/' . $document->doc_photo));
    //             }
    //             $photoFile = $req->file('doc_photo');
    //             $updateData['doc_photo'] = time() . '_photo_' . $photoFile->getClientOriginalName();
    //             $photoFile->move(public_path($this->dir), $updateData['doc_photo']);
    //         }
    //         $document->update($updateData);
    //         return ApiResponse::JsonResult($document->fresh(), true, 'Document updated successfully');
    //     } catch (\Exception $e) {
    //         Log::error('Document update error: ' . $e->getMessage());
    //         return ApiResponse::Error('An error occurred while updating the document');
    //     }
    // }

    public function updateDocument(Request $req, $id)
{
    try {
        // Find the document
        $document = Document::find($id);
        if (!$document) {
            return ApiResponse::NotFound('Document not found');
        }

        // Validate the request
        $validator = $this->docValidation($req);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Get validated input
        $input = $validator->validated();

        // Handle nullable fields
        $nullableFields = [
            'doc_name',
            'author_id',
            'category_id',
            'doc_title',
            'doc_color',
            'doc_size',
            'doc_page',
            'doc_created_date',
            'doc_published_date',
            'doc_publication_year',
            'doc_keywords',
            'doc_photo',
            'doc_file',
        ];

        foreach ($nullableFields as $field) {
            if (empty($input[$field])) {
                $input[$field] = null;
            }
        }

        // Filter input to remove null values
        $input = array_filter($input, fn($value) => $value !== null);

        // Get authenticated user
        $user = UserService::getAuthUser();
        if (!$user) {
            return ApiResponse::NotFound('User not found');
        }

        $destinationPath = 'D:\programming\laravel\sarana\ass_software\webApi\public\uploads\images\1\DocumentPhotos';

        // Handle document file upload
        if ($req->hasFile('doc_file')) {
            // Delete old file if exists
            if ($document->doc_file && file_exists($destinationPath . '/' . $document->doc_file)) {
                unlink($destinationPath . '/' . $document->doc_file);
            }

            $docFile = $req->file('doc_file');
            $docFileName = time() . '_' . $docFile->getClientOriginalName();
            $docFile->move($destinationPath, $docFileName);
            $input['doc_file'] = $docFileName;
        }

        // Handle photo file upload
        if ($req->hasFile('doc_photo')) {
            // Delete old photo if exists
            if ($document->doc_photo && file_exists($destinationPath . '/' . $document->doc_photo)) {
                unlink($destinationPath . '/' . $document->doc_photo);
            }

            $photoFile = $req->file('doc_photo');
            $photoFileName = time() . '_photo_' . $photoFile->getClientOriginalName();
            $photoFile->move($destinationPath, $photoFileName);
            $input['doc_photo'] = $photoFileName;
        }

        $input['update_uid'] = $user->user_id;

        // Update the document
        $updated = $document->update($input);

        if (!$updated) {
            // If update fails, delete any newly uploaded files
            if (isset($input['doc_file'])) {
                unlink($destinationPath . '/' . $input['doc_file']);
            }
            if (isset($input['doc_photo'])) {
                unlink($destinationPath . '/' . $input['doc_photo']);
            }
            return ApiResponse::Error('Failed to update document');
        }

        return ApiResponse::JsonResult($document, true, 'Document updated successfully');

    } catch (\Exception $e) {
        Log::error('Document update error: ' . $e->getMessage());
        return ApiResponse::Error('An error occurred while updating the document');
    }
}

    public function deleteDocument($id)
    {
        try {
            // Get authenticated user
            $user = UserService::getAuthUser();
            if (!$user) return ApiResponse::NotFound('User not found');
            // Find document
            $doc = Document::find($id);
            if (!$doc)  return ApiResponse::NotFound('Document not found');
            // Store file paths before deletion
            $photoPath = $doc->doc_photo;
            $filePath = $doc->doc_file;
            // Delete the document record
            $deleted = $doc->delete();
            if (!$deleted) return ApiResponse::Error('Failed to delete document record');
            // Delete associated files
            try {
                // Delete photo if exists
                if ($photoPath && file_exists(public_path($this->dir . '/' . $photoPath))) unlink(public_path($this->dir . '/' . $photoPath));
                // Delete document file if exists
                if ($filePath && file_exists(public_path($this->dir . '/' . $filePath))) {
                    unlink(public_path($this->dir . '/' . $filePath));
                }
            } catch (\Exception $e) {
                Log::error('Error deleting document files: ' . $e->getMessage());
                // Continue execution even if file deletion fails
                // The database record is already deleted
            }
            return ApiResponse::JsonResult(null, true, 'Document and associated files deleted successfully');
        } catch (\Exception $e) {
            Log::error('Document deletion error: ' . $e->getMessage());
            return ApiResponse::Error('An error occurred while deleting the document');
        }
    }



}
