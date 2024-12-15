<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InitialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $userId  = DB::table('users')->insertGetId([
            'first_name' => 'Super',
            'last_name' => 'ADMIN',
            'username' => 'superadmin',
            'phone' => '092335554',
            'email' => 'superadmin@gmail.com',
            'account_type' => 'superadmin',
            'gender' => 'M',
            'password' => \Hash::make('1234'),
            'system_admin' => true,
            'create_uid' => 1, //* just default val
            'update_uid' => 1, //* just default val
            'branch_id'=>1, //* just default val
            'company_id' => 1 //* just default val
        ]);

        $comapanyId  = DB::table('companies')->insertGetId([
            'name' => 'School Root',
            'name_kh' => 'ក្រុមហ៊ុន',
            'address' => 'some where',
            'email' => 'school@gmail.com',
            'phone_number' => '092335554',
            'description' => 'This is Root, Root represent to all branches',
            'create_uid' => $userId,
            'company_type' => 'digital marketing',
            'update_uid' => $userId
        ]);
        $branchId =  DB::table('branches')->insertGetId([
            'name' => 'First Branch',
            'name_kh' => 'សាខា',
            'address' => 'address',
            'company_id' => $comapanyId,
            'phone' => '092335554',
            'description' => 'The initail branch',
            'create_uid' => $userId,
            'update_uid' => $userId
        ]);

        $roleId = DB::table('roles')->insertGetId([
            'name' => 'Admin',
            'description' => '',
            'create_uid' => $userId,
            'update_uid' => $userId,
            'company_id' => $comapanyId,
        ]);

        $userRoles = DB::table('user_roles')->insert([
            'user_id' => $userId,
            'role_id' => $roleId
        ]);
    }
}
