<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('preview_links', function (Blueprint $table) {
            $table->id();
            $table->string('token', 64)->unique();
            $table->string('collection');
            $table->string('entry_id');
            $table->string('entry_slug');
            $table->string('entry_title')->nullable();
            $table->text('entry_data')->nullable();
            $table->timestamp('expires_at');
            $table->integer('access_count')->default(0);
            $table->timestamp('last_accessed_at')->nullable();
            $table->string('created_by')->nullable();
            $table->timestamps();
            
            $table->index(['token']);
            $table->index(['expires_at']);
            $table->index(['collection', 'entry_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('preview_links');
    }
};