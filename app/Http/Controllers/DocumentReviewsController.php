<?php

namespace App\Http\Controllers;

use ApiResponse;
use App\Models\Category;
use App\Models\DocumentReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DocumentReviewsController extends Controller
{
    public function validation(Request $req)
    {
        return validator($req->all(), [
            'document_id' => 'required',
            'docr_comment' => 'required',
            'docr_rating' => 'nullable',
        ]);
    }
    public function saveCategory(Request $req){
        $validator = $this->validation($req);
        if ($validator->fails()) return response()->json($validator->errors(), 400);
        $input = $validator->validated();
        // $doc = $input['document_id'];
        // $exit = DocumentReview::find($doc);
        // Log::info($exit);
        // if (!$exit) return ApiResponse::NotFound('Document not found');
        $authors = DocumentReview::create($input);
        if(!$authors) return ApiResponse::Error('Fail to create');
        return ApiResponse::JsonResult($authors, false, 'Created');
    }
    // public function getListDoc(Request $req)
    // {
    //     Log::info('Request data:', $req->all());

    //     try {
    //         $data = DocumentReview::with('document')->get();
    //         Log::info('Fetched data:', $data->toArray());
    //         return ApiResponse::Pagination($data, 'Success');
    //     } catch (\Exception $e) {
    //         Log::error('Error fetching documents:', ['error' => $e->getMessage()]);
    //         return response()->json(['error' => 'Something went wrong.'], 500);
    //     }
    // }

    public function getListDoc(Request $req){
        Log::info('Request data:', $req->all());

        try {
            $data = DocumentReview::with('document')->get();
            Log::info('Fetched data:', $data->toArray());
            return ApiResponse::Pagination($data, 'Success');
        } catch (\Exception $e) {
            Log::error('Error fetching documents:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Something went wrong.'], 500);
        }
    }

    public function getOneCategory($id)
    {
        $data = Category::find($id);
        if (!$data) return ApiResponse::NotFound('Category not found');
        return ApiResponse::JsonResult($data, false, 'Success');
    }

    public function updateCategory(Request $req, $id){
        $validator = $this->validation($req);
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
