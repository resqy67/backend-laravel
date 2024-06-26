<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\dataResource;
use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LoanController extends Controller
{
    //

    public function index()
    {
        $loans = Loan::latest()->paginate(5);
        return new dataResource('success', 'Data retrieved successfully', $loans);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'loan_date' => 'required|date',
            'return_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return new dataResource('error', $validator->errors(), null);
        }

        $loan = Loan::create([
            'loan_date' => $request->loan_date,
            'return_date' => $request->return_date,
        ]);

        return new dataResource('success', 'Data stored successfully', $loan);
    }
}
