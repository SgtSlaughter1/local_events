<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop the unique constraint if it exists
        try {
            Schema::table('event_registrations', function (Blueprint $table) {
                $table->dropUnique(['ticket_reference']);
            });
        } catch (\Exception $e) {
            // Ignore if constraint doesn't exist
        }

        // Update any empty ticket references with a unique value
        $registrations = DB::table('event_registrations')
            ->whereNull('ticket_reference')
            ->orWhere('ticket_reference', '')
            ->get();

        foreach ($registrations as $registration) {
            $ticketRef = 'TICKET-' . strtoupper(substr(md5(uniqid()), 0, 8));
            DB::table('event_registrations')
                ->where('id', $registration->id)
                ->update(['ticket_reference' => $ticketRef]);
        }

        // Now add the unique constraint
        Schema::table('event_registrations', function (Blueprint $table) {
            $table->unique('ticket_reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_registrations', function (Blueprint $table) {
            $table->dropUnique(['ticket_reference']);
        });
    }
};
