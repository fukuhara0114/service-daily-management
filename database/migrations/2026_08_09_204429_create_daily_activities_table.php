<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('daily_activities', function (Blueprint $table) {
            $table->id();
    
            // 担当者
            $table->foreignId('user_id');
    
            // 作業日
            $table->date('work_date');
    
            // 新規保守案件登録件数
            $table->integer('new_case_count')->default(0);
    
            // 保守案件メール案内件数
            $table->integer('mail_notice_count')->default(0);
    
            // 備考
            $table->text('remarks')->nullable();
    
            $table->timestamps();
    
            // 同一ユーザー・同一日の重複防止
            $table->unique(['user_id', 'work_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_activities');
    }
};
