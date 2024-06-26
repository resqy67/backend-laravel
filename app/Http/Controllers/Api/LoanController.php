<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\dataResource;
use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


/**
 * @group Loan
 *
 * APIs for managing loans
 */
class LoanController extends Controller
{
    /**
     * Loan
     *
     * Get all loans
     *
     */
    public function index()
    {
        $loans = Loan::latest()->paginate(5);
        return new dataResource('success', 'Data retrieved successfully', $loans);
    }

    /**
     * Store
     *
     * Store new loan
     *
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_uuid' => 'required|string',
            'book_copy_uuid' => 'required|string',
            'loan_date' => 'required|date',
            'return_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return new dataResource('error', $validator->errors(), null);
        }

        $loan = Loan::create([
            'user_uuid' => $request->user_uuid,
            'book_copy_uuid' => $request->book_copy_uuid,
            'loan_date' => $request->loan_date,
            'return_date' => $request->return_date,
        ]);

        return new dataResource('success', 'Data stored successfully', $loan);
    }
}
