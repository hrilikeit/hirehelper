<?php

namespace App\Http\Controllers;

use App\Models\EmailLog;
use Illuminate\Http\Response;

class EmailTrackingController extends Controller
{
    /**
     * 1×1 transparent pixel served when the email is opened.
     */
    public function track(int $id, string $hash): Response
    {
        // Verify the hash to prevent abuse
        $expected = hash('sha256', $id . config('app.key'));

        if (hash_equals($expected, $hash)) {
            $log = EmailLog::find($id);

            if ($log && ! $log->opened_at) {
                $log->update([
                    'status'    => 'opened',
                    'opened_at' => now(),
                ]);
            }
        }

        // Return a 1×1 transparent GIF
        $pixel = base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');

        return response($pixel, 200, [
            'Content-Type'  => 'image/gif',
            'Content-Length' => strlen($pixel),
            'Cache-Control'  => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma'         => 'no-cache',
        ]);
    }
}
