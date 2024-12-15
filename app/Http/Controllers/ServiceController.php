<?php

namespace App\Http\Controllers;

use ApiResponse;
use App\Models\Service;
use App\Services\UserService;
use Carbon\Carbon;
use Helper;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    protected $dir = 'Services';
    public function ServiceValidation(Request $req)
    {
        return validator($req->all(), [
            'name' => 'required|string',
            'description' => 'nullable|string',
            'date' => 'required|string',
            'photo' => 'nullable|string',
        ]);
    }
    public function saveService(Request $req){
        $validator = $this->ServiceValidation($req);
        if ($validator->fails()) return ApiResponse::ValidateFail($validator->errors()->first(), $validator->errors());
        $input = $validator->validated();
        $user = UserService::getAuthUser();
        if (!$user) return ApiResponse::Unauthorized('User not found');
        if (!Carbon::hasFormat($input['date'], 'Y-m-d')) return ApiResponse::JsonResult(null, true, 'Invalid date format. Expected format: Y-m-d');
        $input['date'] = Carbon::parse($input['date'])->format('Y-m-d');
        $input['create_uid'] = $user->user_id;
        $input['update_uid'] = $user->user_id;
        $input['company_id'] = $user->company_id;
        $base64Photo = $input['photo'] ?? null;
        unset($input['photo']);
        $photo = Helper::base64ToImageFile($base64Photo, $user->company_id, $this->dir);
        $input['photo_file_name'] = $photo;
        $create = Service::create($input);
        if (!$create) {
            Helper::deleteImageFile($photo, $user->company_id, $this->dir);
            return ApiResponse::Error('Fail to create');
        }
        return ApiResponse::JsonResult($create, false, 'Created');
    }

    public function getListServices(Request $req) {
        $user = UserService::getAuthUser();
        $services = Service::with('creator')
            ->orderBy('id', 'desc')
            ->get();
        foreach ($services as $s) {
            $s->image_url = Helper::getImageUrl($s->photo_file_name, $user->company_id, $this->dir);
            $s->create_by = $s->creator->username ?? 'Unknown';
            unset($s->photo_file_name, $s->creator);
        }
        return ApiResponse::Pagination($services, $req, "Success");
    }

    public function getOneService($id) {
        $user = UserService::getAuthUser();
        $service = Service::with('creator')->find($id);
        if (!$service) return ApiResponse::NotFound('Service not found');
        $service->image_url = Helper::getImageUrl($service->photo_file_name, $user->company_id, $this->dir);
        $service->create_by = $service->creator->username ?? 'Unknown';
        unset($service->photo_file_name, $service->creator);
        return ApiResponse::JsonResult($service, false, 'Success');
    }


    public function updateService($id, Request $req) {
        $validator = $this->ServiceValidation($req);
        if ($validator->fails()) return ApiResponse::ValidateFail($validator->errors()->first(), $validator->errors());
        $input = $validator->validated();
        if (!Carbon::hasFormat($input['date'], 'Y-m-d')) return ApiResponse::JsonResult(null, true, 'Invalid date format. Expected format: Y-m-d');
        $input['date'] = Carbon::parse($input['date'])->format('Y-m-d');
        $user = UserService::getAuthUser();
        if (!$user) return ApiResponse::Unauthorized('Not found');
        $service = Service::find($id);
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


    public function  deleteService($id){
        $user = UserService::getAuthUser();
        $service = Service::find($id);
        if (!$service) return ApiResponse::NotFound('Service not found');
        $delete = $service->delete();
        if (!$delete) return ApiResponse::Error('Fail to delete');
        Helper::deleteImageFile($service->photo_file_name, $user->company_id, $this->dir);
        return ApiResponse::JsonResult($service, false, 'Deleted');
    }
}
