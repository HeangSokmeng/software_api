<?php

namespace App\Http\Controllers;

use ApiResponse;
use App\Models\Genre;
use Illuminate\Http\Request;

class GenresController extends Controller
{
    public function genreValidation(Request $req)
    {
        return validator($req->all(), [
            'genr_name' => 'required|string',
            'genr_description' => 'nullable'
        ]);
    }
    public function saveGenre(Request $req){
        $validator = $this->genreValidation($req);
        if ($validator->fails()) return response()->json($validator->errors(), 400);
        $input = $validator->validated();
        $exit = $input['genr_name'];
        $exit = Genre::where('genr_name', $exit)->first();
        if ($exit) return ApiResponse::Error('Genre name already exists');
        $genres = Genre::create($input);
        if(!$genres) return ApiResponse::Error('Fail to create');
        return ApiResponse::JsonResult($genres, false, 'Created');
    }

    public function getListGenres(Request $req)
    {
        $authors = Genre::orderBy('genr_id', 'desc')->get();
        return ApiResponse::Pagination($authors, false, 'Success');
    }

    public function getOneGenre($id)
    {
        $author = Genre::find($id);
        if (!$author) return ApiResponse::NotFound('Genre not found');
        return ApiResponse::JsonResult($author, false, 'Success');
    }

    public function updateGenre(Request $req, $id){
        $validator = $this->genreValidation($req);
        if ($validator->fails()) return response()->json($validator->errors(), 400);
        $input = $validator->validated();
        $authors = Genre::find($id);
        if (!$authors) return ApiResponse::NotFound('Genre not found');
        $authors->update($input);
        return ApiResponse::JsonResult($authors, false, 'Updated');
    }

    public function deleteGenre($id){
        $authors = Genre::find($id);
        if (!$authors) return ApiResponse::NotFound('Genre not found');
        $authors->delete();
        return ApiResponse::JsonResult($authors, false, 'Deleted');
    }
}
