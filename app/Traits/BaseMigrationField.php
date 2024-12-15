<?php
namespace App\Traits;
use Illuminate\Database\Schema\Blueprint;
trait BaseMigrationField{
    public function AddBaseFields(Blueprint $table,$useVoid=false){
        $table->id();
        $table->timestampTz("created_at")->useCurrent();
        $table->timestampTz("updated_at")->useCurrent()->useCurrentOnUpdate();
        $table->unsignedBigInteger('create_uid');
        $table->unsignedBigInteger('update_uid');
        $table->unsignedBigInteger('company_id');

        /**
         * relationship
        */

        $table->foreign('create_uid')->references('id')->on('users')->onDelete('cascade');
        $table->foreign('update_uid')->references('id')->on('users')->onDelete('cascade');
        $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');


        /**
         * add soft delete if needed
         */

        if($useVoid){
            $table->boolean('is_deleted')->default(0);
            $table->unsignedBigInteger('deleted_uid')->nullable();
            $table->dateTime('deleted_datetime')->nullable();
            $table->foreign('deleted_uid')->references('id')->on('users')->onDelete('cascade');
        }
    }
}
