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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id()->primary();
            $table->morphs('notificable'); // relations
            $table->enum('status', ['pending', 'sent', 'failed'])->default('pending')->index(); // status
            $table->string('from', 255)->nullable(false)->index(); // email_from
            $table->jsonb('recipients')->nullable(false)->index(); // array recipients
            $table->string('reply_to', 255)->nullable()->index(); // email reply_to
            $table->string('subject', 255)->nullable(false)->index(); // subject
            $table->text('error')->nullable()->index(); // error message
            $table->string('type', 255)->nullable(false)->index(); // type of notification
            $table->string('template', 255)->nullable()->index(); // template name
            $table->longText('body')->nullable()->index(); // html of the email
            $table->jsonb('metadata')->nullable()->index(); // metadata of email
            $table->unsignedInteger('priority'); // priotiry
            $table->unsignedInteger('attempts')->default(0); // attempts count
            $table->timestamp('sent_at')->nullable()->index(); // sent_at timestamp
            $table->timestamp('failed_at')->nullable()->index(); // failed_at timestamp
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
