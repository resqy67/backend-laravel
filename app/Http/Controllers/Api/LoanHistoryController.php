<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DataResource;
use App\Models\LoanHistory;
use Illuminate\Http\Request;

class LoanHistoryController extends Controller
{
    /**
     * Loan History
     *
     * Get all loan history
     *
     */
    public function index()
    {
        $loans = LoanHistory::with('user', 'book')->latest()->simplePaginate(20);
        return new DataResource('success', 'Data retrieved successfully', $loans);
    }

    /**
     * Search by User
     *
     * Get loan history by user
     *
     */
    public function searchByUser(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return new DataResource('error', 'User not authenticated', null);
        }

        // Get loans by authenticated user's ID
        $loans = LoanHistory::where('user_id', $user->id)->with('book')->latest()->simplePaginate(20);

        return new DataResource('success', 'Data retrieved successfully', $loans);
    }
}
