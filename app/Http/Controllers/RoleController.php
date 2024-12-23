<?php

namespace App\Http\Controllers;

use ApiResponse;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Services\UserService;
class RoleController extends Controller
{
    public function roleValidation(Request $req)
    {
        return validator($req->all(), [
            'name' => 'required|string',
            'description' => 'nullable|string',
        ]);
    }
    public function saveRole(Request $req){
        $validator = $this->roleValidation($req);
        if ($validator->fails()) return response()->json($validator->errors(), 400);
        $input = $validator->validated();
        $user = UserService::getAuthUser();
        $nameRole = $input['name'];
        $input['name'] = strtolower($nameRole);
        $input['create_uid'] = $user->user_id;
        $input['update_uid'] = $user->user_id;
        $input['company_id'] = $user->company_id;
        $input['branch_id'] = $user->branch_id;
        $role = Role::create($input);
        if(!$role) return ApiResponse::Error('Fail to create');
        return ApiResponse::JsonResult($role, false, 'Created');
    }

    public function getListRoles(Request $req)
    {
        $search = $req->input('search');
        $rolesQuery = Role::with('creator')
            ->when($search, function ($query, $search) {
                return $query->where(function ($query) use ($search) {
                    $query->where('name', 'ilike', '%' . $search . '%')
                        ->orWhere('description', 'ilike', '%' . $search . '%');
                });
            })
            ->orderBy('id', 'desc');
        $roles = $rolesQuery->get();
        foreach ($roles as $role) {
            $role->create_by_username = $role->creator->username ?? null;
            unset($role->creator, $role->create_uid, $role->update_uid, $role->company_id, $role->branch_id);
        }
        return ApiResponse::Pagination($roles, $req, "Success");
    }



    public function getOneRole($id)
    {
        $user = UserService::getAuthUser();
        $role = Role::with('creator')
            ->where('id', $id)
            ->first();
        if (!$role) return ApiResponse::NotFound('Role not found');
        $role->create_by_username = $role->creator->username ?? null;
        unset($role->creator);
        return ApiResponse::JsonResult($role);
    }


    public function updateRoles(Request $req, $id){
        $validator = $this->roleValidation($req);
        if ($validator->fails()) return ApiResponse::ValidateFail($validator->errors()->first(), $validator->errors());
        $input = $validator->validated();
        $user = UserService::getAuthUser();
        $input['update_uid'] = $user->user_id;
        $role = Role::find($id);
        if (!$role) return ApiResponse::NotFound('Role not found');
        $role->update($input);
        return ApiResponse::JsonResult($role, false, 'Updated');
    }

    public function deleteRole($id){
        $role = Role::find($id);
        if (!$role) return ApiResponse::NotFound('Role not found');
        $role->delete();
        return ApiResponse::JsonResult(null, false, 'Deleted');
    }

}
