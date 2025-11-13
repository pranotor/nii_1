<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class ClosingController extends Controller
{
    /**
     * Update closing month (set to first day of given month)
     * Expected payload: { "bulan": "YYYY-MM" }
     */
    public function closing_month(Request $request): JsonResponse
    {
        // Validate input format YYYY-MM
        $validator = Validator::make($request->all(), [
            'bulan' => ['required','regex:/^(19|20|21|22|23|24|25|26|27|28|29|30)\\d{2}-(0[1-9]|1[0-2])$/'],
        ], [
            'bulan.required' => 'Field bulan is required',
            'bulan.regex' => 'Format bulan harus YYYY-MM',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first('bulan'),
            ], 422);
        }

        $bulan = $request->input('bulan'); // e.g. 2025-01
        $tanggal = $bulan . '-01'; // first day of month

        // Update t_closing.closing_month (assumes singleton row)
        // Minimal change: update all rows as existing code treats t_closing as config table with first() reads
        DB::table('t_closing')->update(['closing_month' => $tanggal]);

        return response()->json([
            'status' => true,
            'message' => 'Closing month updated',
            'closing_month' => $tanggal,
        ]);
    }
}
