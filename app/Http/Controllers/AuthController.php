<?php

namespace App\Http\Controllers;

use ApiResponse;
use App\Models\Role;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function userValidation(Request $req)
    {
        return validator($req->all(), [
            'username' => 'required|min:4|max:32',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:4',
            'phone' => 'required',
            'account_type' => 'required',
            'roles' => 'required|array', // Ensure roles is provided as an array
            'roles.*' => 'exists:roles,id', // Validate that each role ID exists in the roles table
        ]);
    }
    public function register(Request $req): JsonResponse
    {
        $validator = $this->userValidation($req);
        if ($validator->fails()) return ApiResponse::ValidateFail($validator->errors()->first(), $validator->errors());
        $input = $validator->validated();
        $currentUser = Auth::user();
        if (!$currentUser) return ApiResponse::NotFound("User does not exist!!");
        $roles = Role::findMany($input['roles']);
        if ($roles->isEmpty()) return ApiResponse::Error("Role does not exist!!");
        $input['create_uid'] = $currentUser->id;
        $input['update_uid'] = $currentUser->id;
        $input['branch_id'] = $currentUser->branch_id;
        $input['company_id'] = $currentUser->company_id;
        if (Auth::check()) {
            $accountType = $currentUser->account_type;
            if (($accountType === 'superadmin' && in_array($input['account_type'], ['admin', 'teacher', 'student'])) ||
                ($accountType === 'admin' && in_array($input['account_type'], ['teacher', 'student']))) {
                $newUser = User::create($input);
                if ($newUser) {
                    $newUser->roles()->attach($roles->pluck('id'));
                    return ApiResponse::JsonResult($newUser, "User created successfully!");
                }
                return ApiResponse::Error("Failed to create user!!");
            } else {
                return ApiResponse::Error("Invalid account type!!");
            }
        }
        return ApiResponse::Error("unauthorized access");
    }

    public function deleteUser(Request $req, $userId): JsonResponse
    {
        $currentUser = Auth::user();
        if (!$currentUser)  return ApiResponse::NotFound("Current User does not exist!!");
        $userToDelete = User::find($userId);
        if (!$userToDelete)  return ApiResponse::NotFound("User does not exist!!");
        $accountType = $currentUser->account_type;
        if ($accountType === 'superadmin') {
            if ($userToDelete->account_type === 'admin') {
                $userToDelete->delete();
                return ApiResponse::JsonResult(null, "Admin deleted successfully!");
            } else {
                return ApiResponse::Error("SuperAdmin can only delete admin.");
            }
        } elseif ($accountType === 'admin') {
            if ($userToDelete->account_type === 'teacher') {
                $userToDelete->delete();
                return ApiResponse::JsonResult(null, "Teacher deleted successfully!");
            } else {
                return ApiResponse::Error("Admin can only delete teacher.");
            }
        }
        return ApiResponse::Error("unauthorized");
    }

    public function getListUser(): JsonResponse
    {
        $user = UserService::getAuthUser();
        if (!$user) {
            return ApiResponse::NotFound("User does not exist");
        }
        $users = User::with('roles')
            ->where('id', '!=', $user->id)
            ->get();
        return ApiResponse::JsonResult($users, 'User list retrieved successfully.');
    }

    public function login(Request $req): JsonResponse
    {
        $validator = Validator::make($req->all(), [
            'username' => 'required|string',
            'password' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $credentials = $req->only('username', 'password');
        // Set the token TTL dynamically (e.g., 2 hours)
        JWTAuth::factory()->setTTL(300);
        if (!$token = JWTAuth::attempt($credentials)) {
            return ApiResponse::Unauthorized('Invalid credentials');
        }

        $user = Auth::user();
        return ApiResponse::JsonResult([
            'access_token' => $token,
            'token_expires_in' => JWTAuth::factory()->getTTL() * 60, // Return expiration in seconds
            'account_type' => $user->account_type,
            'username' => $user->username,
        ]);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile(): JsonResponse
    {
        $user = null; //auth()->user();
        return response()->json([
            'status_code' => 200,
            'data' => $user,
        ], 200);
    }

    public function logout(): JsonResponse
    {
        // auth()->invalid;
        return response()->json([
            'status_code' => 202,
            'message' => 'Successfully logged out',
        ]);
    }

    protected function responseWithToken($token): JsonResponse
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl'),
        ], 200);
    }
}
