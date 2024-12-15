<?php

namespace App\Http\Controllers;

use ApiResponse;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    public function categoryValidation(Request $req)
    {
        return validator($req->all(), [
            'cate_name' => 'required|string',
            'cate_description' => 'nullable',
        ]);
    }
    public function saveCategory(Request $req){
        $validator = $this->categoryValidation($req);
        if ($validator->fails()) return response()->json($validator->errors(), 400);
        $input = $validator->validated();
        $exit = $input['cate_name'];
        $exit = Category::where('cate_name', $exit)->first();
        if ($exit) return ApiResponse::Error('Author name already exists');
        $authors = Category::create($input);
        if(!$authors) return ApiResponse::Error('Fail to create');
        return ApiResponse::JsonResult($authors, false, 'Created');
    }

    public function getListCategories(Request $req)
    {
        $data = Category::orderBy('cate_id', 'desc')->get();
        return ApiResponse::Pagination($data, false, 'Success');
    }

    public function getOneCategory($id)
    {
        $data = Category::find($id);
        if (!$data) return ApiResponse::NotFound('Category not found');
        return ApiResponse::JsonResult($data, false, 'Success');
    }

    public function updateCategory(Request $req, $id){
        $validator = $this->categoryValidation($req);
        if ($validator->fails()) return response()->json($validator->errors(), 400);
        $input = $validator->validated();
        $authors = Category::find($id);
        if (!$authors) return ApiResponse::NotFound('Categories not found');
        $authors->update($input);
        return ApiResponse::JsonResult($authors, false, 'Updated');
    }

    public function deleteCategory($id){
        $authors = Category::find($id);
        if (!$authors) return ApiResponse::NotFound('Category not found');
        $authors->delete();
        return ApiResponse::JsonResult($authors, false, 'Deleted');
    }
}
