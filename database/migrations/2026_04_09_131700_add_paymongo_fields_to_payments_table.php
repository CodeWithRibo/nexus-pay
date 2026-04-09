<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('payment_channel')->default('kiosk_cash')->after('status');
            $table->string('payment_context')->nullable()->after('payment_channel');
            $table->boolean('pay_all')->default(false)->after('payment_context');
            $table->boolean('use_overpayment')->default(false)->after('pay_all');
            $table->string('gateway_reference_id')->nullable()->after('reference_no');
            $table->string('gateway_payment_id')->nullable()->after('gateway_reference_id');
            $table->string('gateway_status')->nullable()->after('gateway_payment_id');
            $table->string('gateway_method')->nullable()->after('gateway_status');
            $table->longText('gateway_qr_code')->nullable()->after('gateway_method');
            $table->json('gateway_response')->nullable()->after('gateway_qr_code');
            $table->text('failed_reason')->nullable()->after('gateway_response');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'payment_channel',
                'payment_context',
                'pay_all',
                'use_overpayment',
                'gateway_reference_id',
                'gateway_payment_id',
                'gateway_status',
                'gateway_method',
                'gateway_qr_code',
                'gateway_response',
                'failed_reason',
            ]);
        });
    }
};
