<?php

namespace App\Http\Controllers;

use ApiResponse;
use App\Models\Authors;
use App\Http\Requests\StoreAuthorsRequest;
use App\Http\Requests\UpdateAuthorsRequest;
use App\Models\Author;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthorsController extends Controller
{
    public function authorValidation(Request $req)
    {
        return validator($req->all(), [
            'auth_name' => 'required|string',
            'auth_bio' => 'nullable',
            'auth_email' => 'nullable|string',
            'auth_gender' => 'nullable|string',
            'auth_phone' => 'nullable',
            'auth_address' => 'nullable',
        ]);
    }
    public function saveAuthors(Request $req){
        $validator = $this->authorValidation($req);
        if ($validator->fails()) return response()->json($validator->errors(), 400);
        $input = $validator->validated();
        $exitAuth = $input['auth_name'];
        $exitAuth = Author::where('auth_name', $exitAuth)->first();
        if ($exitAuth) return ApiResponse::Error('Author name already exists');
        $authors = Author::create($input);
        if(!$authors) return ApiResponse::Error('Fail to create');
        return ApiResponse::JsonResult($authors, false, 'Created');
    }

    public function getListAuthors(Request $req)
    {
        $authors = Author::orderBy('auth_id', 'desc')->get();
        return ApiResponse::Pagination($authors, false, 'Success');
    }

    public function getOneAuthor($id)
    {
        $author = Author::find($id);
        if (!$author) return ApiResponse::NotFound('Author not found');
        return ApiResponse::JsonResult($author, false, 'Success');
    }

    public function updateAuthor(Request $req, $id){
        $validator = $this->authorValidation($req);
        if ($validator->fails()) return response()->json($validator->errors(), 400);
        $input = $validator->validated();
        $authors = Author::find($id);
        if (!$authors) return ApiResponse::NotFound('Author not found');
        $authors->update($input);
        return ApiResponse::JsonResult($authors, false, 'Updated');
    }

    public function deleteAuthor($id){
        $authors = Author::find($id);
        if (!$authors) return ApiResponse::NotFound('Author not found');
        $authors->delete();
        return ApiResponse::JsonResult($authors, false, 'Deleted');
    }
}
