<?php

namespace App\Http\Controllers;

use ApiResponse;
use App\Models\Document;
use App\Models\Service;
use App\Services\UserService;
use Helper;
use Illuminate\Http\Request;

class DocumentsController extends Controller
{
    protected $dir = 'DocumentPhotos';
    public function docValidation(Request $req)
    {
        return validator($req->all(), [
            'doc_name' => 'required|string',
            'author_id' => 'required',
            'category_id' => 'required',
            'genre_id' => 'required|string',
            'doc_title' => 'nullable',
            'doc_color' => 'nullable|string',
            'doc_size' => 'nullable|string',
            'doc_page' => 'nullable',
            'doc_created_date' => 'nullable',
            'doc_published_date' => 'nullable',
            'doc_publication_year' => 'nullable',
            'doc_keywords' => 'nullable',
            'photo' => 'nullable',
        ]);
    }

    public function saveDocument(Request $req){
        $validator = $this->docValidation($req);
        if ($validator->fails()) return ApiResponse::ValidateFail($validator->errors()->first(), $validator->errors());
        $input = $validator->validated();
        $user = UserService::getAuthUser();
        $author = $input['author_id'];
        $category = $input['category_id'];
        if (!$user) return ApiResponse::NotFound('User not found');
        if(!$category) return ApiResponse::NotFound('Category not found');
        if (!$author) return ApiResponse::NotFound('Not found Author');
        $input['create_uid'] = $user->user_id;
        $input['update_uid'] = $user->user_id;
        $input['company_id'] = $user->company_id;
        $base64Photo = $input['photo'] ?? null;
        unset($input['photo']);
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
        $docs= Document::with('creator')
            ->orderBy('id', 'desc')
            ->get();
        foreach ($docs as $d) {
            $d->image_url = Helper::getImageUrl($d->doc_photo, $user->company_id, $this->dir);
            $d->create_by = $d->creator->username ?? 'Unknown';
            unset($d->photo_file_name, $d->creator);
        }
        return ApiResponse::Pagination($docs, $req, "Success");
    }

    public function getOneDocument($id) {
        $user = UserService::getAuthUser();
        $doc = Document::with('creator')->find($id);
        if (!$doc) return ApiResponse::NotFound('Document not found');
        $doc->image_url = Helper::getImageUrl($doc->doc_photo, $user->company_id, $this->dir);
        $doc->create_by = $doc->creator->username ?? 'Unknown';
        unset($service->photo_file_name, $service->creator);
        return ApiResponse::JsonResult($doc, false, 'Success');
    }

    public function updateDocument($id, Request $req) {
        $validator = $this->docValidation($req);
        if ($validator->fails()) return ApiResponse::ValidateFail($validator->errors()->first(), $validator->errors());
        $input = $validator->validated();
        $user = UserService::getAuthUser();
        if (!$user) return ApiResponse::Unauthorized('Not found');
        $doc = Document::find($id);
        if (!$doc) return ApiResponse::NotFound('Not found');
        $photo = $input['photo'] ?? null;
        unset($input['photo']);
        if (Helper::isValidBase64Image($photo) || !$photo) {
            $photo = Helper::base64ToImageFile($photo, $user->company_id, $this->dir);
            Helper::deleteImageFile($doc->doc_photo, $user->company_id, $this->dir);
            $input['doc_photo'] = $photo;
        }
        $input['update_uid'] = $user->user_id;
        return $doc->update($input)
        ? ApiResponse::JsonResult($doc, false, 'Updated')
        : ApiResponse::Error('Failed to update');
    }


    public function  deleteDocument($id){
        $user = UserService::getAuthUser();
        $doc = Service::find($id);
        if (!$doc) return ApiResponse::NotFound('Document not found');
        $delete = $doc->delete();
        if (!$delete) return ApiResponse::Error('Fail to delete');
        Helper::deleteImageFile($doc->doc_photo, $user->company_id, $this->dir);
        return ApiResponse::JsonResult(Null, false, 'Deleted');
    }
}
