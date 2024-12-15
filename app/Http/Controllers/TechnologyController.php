<?php

namespace App\Http\Controllers;

use ApiResponse;
use App\Models\Technology;
use App\Services\UserService;
use Helper;
use Illuminate\Http\Request;

class TechnologyController extends Controller
{
    protected $dir = 'Technologies';
    public function technologyValidation(Request $req)
    {
        return validator($req->all(), [
            'name' => 'required|string',
            'version' => 'nullable|string',
            'description' => 'nullable|string',
            'is_active' => 'required|string',
            'photo' => 'nullable|string',
        ]);
    }

    public function saveTechnology(Request $req){
        $validator = $this->technologyValidation($req);
        if ($validator->fails()) return ApiResponse::ValidateFail($validator->errors()->first(), $validator->errors());
        $input = $validator->validated();
        $user = UserService::getAuthUser();
        if (!$user) return ApiResponse::NotFound('User not found');
        $input['create_uid'] = $user->user_id;
        $input['update_uid'] = $user->user_id;
        $input['company_id'] = $user->company_id;
        $base64Photo = $input['photo'] ?? null;
        unset($input['photo']);
        $photo = Helper::base64ToImageFile($base64Photo, $user->company_id, $this->dir);
        $input['photo_file_name'] = $photo;
        $create = Technology::create($input);
        if (!$create) {
            Helper::deleteImageFile($photo, $user->company_id, $this->dir);
            return ApiResponse::Error('Fail to create');
        }
        return ApiResponse::JsonResult($create, false, 'Created');
    }

    public function getListTechnologies(Request $req) {
        $user = UserService::getAuthUser();
        $services = Technology::with('creator')
            ->orderBy('id', 'desc')
            ->get();
        foreach ($services as $s) {
            $s->image_url = Helper::getImageUrl($s->photo_file_name, $user->company_id, $this->dir);
            $s->create_by = $s->creator->username ?? 'Unknown';
            unset($s->photo_file_name, $s->creator);
        }
        return ApiResponse::Pagination($services, $req, "Success");
    }

    public function getOneTechnology($id) {
        $user = UserService::getAuthUser();
        $service = Technology::with('creator')->find($id);
        if (!$service) return ApiResponse::NotFound('Service not found');
        $service->image_url = Helper::getImageUrl($service->photo_file_name, $user->company_id, $this->dir);
        $service->create_by = $service->creator->username ?? 'Unknown';
        unset($service->photo_file_name, $service->creator);
        return ApiResponse::JsonResult($service, false, 'Success');
    }

    public function updateTechnology($id, Request $req) {
        $validator = $this->technologyValidation($req);
        if ($validator->fails()) return ApiResponse::ValidateFail($validator->errors()->first(), $validator->errors());
        $input = $validator->validated();
        $user = UserService::getAuthUser();
        if (!$user) return ApiResponse::NotFound('Not found');
        $service = Technology::find($id);
        if (!$service) return ApiResponse::NotFound('Not found');
        $photo = $input['photo'] ?? null;
        unset($input['photo']);
        if (Helper::isValidBase64Image($photo) || !$photo) {
            $photo = Helper::base64ToImageFile($photo, $user->company_id, $this->dir);
            Helper::deleteImageFile($service->photo_file_name, $user->company_id, $this->dir);
            $input['photo_file_name'] = $photo;
        }
        $input['update_uid'] = $user->user_id;
        return $service->update($input)
        ? ApiResponse::JsonResult($service, false, 'Updated')
        : ApiResponse::Error('Failed to update');
    }

    public function deleteTechnology($id) {
        $user = UserService::getAuthUser();
        $service = Technology::find($id);
        if (!$service) return ApiResponse::NotFound('Service not found');
        $delete = $service->delete();
        if (!$delete) return ApiResponse::Error('Fail to delete');
        Helper::deleteImageFile($service->photo_file_name, $user->company_id, $this->dir);
        return ApiResponse::JsonResult(null, false, 'Deleted');
    }
}
