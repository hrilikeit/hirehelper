<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 60)->unique();
            $table->string('name', 120);
            $table->string('description', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Seed the default email types
        $now = now();
        $emails = [
            ['key' => 'verify_email',      'name' => 'Email Verification',     'description' => 'Sent when a user requests email verification'],
            ['key' => 'get_started',        'name' => 'Get Started',            'description' => 'Welcome email sent after registration'],
            ['key' => 'unread_message',     'name' => 'Unread Message',         'description' => 'Notifies client about a new unread message'],
            ['key' => 'payment_method_added','name' => 'Payment Method Added', 'description' => 'Sent when a billing method is connected'],
            ['key' => 'payment_failed',     'name' => 'Payment Failed',         'description' => 'Manually triggered when a payment fails'],
            ['key' => 'weekly_tracked_hours','name' => 'Weekly Tracked Hours', 'description' => 'Sent from admin with weekly hour totals'],
            ['key' => 'contract_active',    'name' => 'Contract Active',        'description' => 'Sent when an offer status changes to active'],
            ['key' => 'password_reset',     'name' => 'Password Reset',         'description' => 'Sent when a user requests a password reset'],
        ];

        foreach ($emails as $email) {
            DB::table('email_settings')->insert(array_merge($email, [
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('email_settings');
    }
};
