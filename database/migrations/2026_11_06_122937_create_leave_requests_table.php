<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('type'); 
            $table->string('reason')->nullable();
            $table->decimal('hours', 6, 2);
            $table->string('status')->default('pending'); 
            $table->timestamps();
        });

        DB::statement('ALTER TABLE leave_requests ENABLE ROW LEVEL SECURITY');
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_requests');
    }
};