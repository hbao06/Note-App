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
        // Schema::create('notes', function (Blueprint $table) {
        //     $table->id();
        //     $table->timestamps();
        // });
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Liên kết với User [cite: 16]
            $table->string('title'); // Tiêu đề ghi chú 
            $table->text('content'); // Nội dung ghi chú 
            
            // Tính năng nâng cao
            $table->string('password')->nullable(); // Để khóa ghi chú bằng mật khẩu [cite: 53, 54]
            $table->boolean('is_pinned')->default(false); // Để ghim ghi chú lên đầu [cite: 41]
            $table->timestamp('pinned_at')->nullable(); // Thứ tự ưu tiên khi ghim nhiều ghi chú [cite: 42]
            
            // Tùy chỉnh hiển thị (User Preferences cho từng note nếu cần)
            $table->string('color')->default('#ffffff'); // Màu sắc ghi chú [cite: 24]
            
            $table->timestamps(); // Lưu thời gian tạo và cập nhật để sắp xếp 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};
