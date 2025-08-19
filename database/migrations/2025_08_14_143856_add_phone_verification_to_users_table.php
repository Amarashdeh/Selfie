<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'pending_phone')) {
                $table->string('pending_phone')->nullable()->after('phone');
            }
            if (! Schema::hasColumn('users', 'phone_verification_code')) {
                // store hashed code
                $table->string('phone_verification_code')->nullable()->after('pending_phone');
            }
            if (! Schema::hasColumn('users', 'phone_verification_expires_at')) {
                $table->timestamp('phone_verification_expires_at')->nullable()->after('phone_verification_code');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['pending_phone','phone_verification_code','phone_verification_expires_at']);
        });
    }
};
