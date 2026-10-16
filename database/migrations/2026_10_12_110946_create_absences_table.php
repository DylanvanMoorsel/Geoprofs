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
        Schema::create('absences', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->string('type');                          // vakantie, ziek, overig
    $table->date('start_date');
    $table->date('end_date');
    $table->string('status')->default('aangevraagd'); // aangevraagd, goedgekeurd, afgewezen
    $table->text('reason')->nullable();
    $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
    $table->timestamp('reviewed_at')->nullable();
    $table->timestamps();

    $table->index(['user_id', 'start_date', 'end_date']);
});
    }
};
