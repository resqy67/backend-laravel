<?php

namespace App\Http\Controllers\api;

use App\Events\LoanCreated;
use App\Http\Controllers\Controller;
use App\Http\Resources\DataResource;
use App\Models\Book;
use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Laravel\Firebase\Facades\Firebase;

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
        $loans = Loan::with('user', 'book')->latest()->paginate(20);
        return new DataResource('success', 'Data retrieved successfully', $loans);
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
            'user_id' => 'required|exists:users,id',
            'book_uuid' => 'required|exists:books,uuid',
        ]);

        if ($validator->fails()) {
            return (new DataResource('error', 'Data not valid', $validator->errors()))->response()->setStatusCode(422);
        }

        $book = Book::where('uuid', $request->book_uuid)->first();
        if (!$book->availability) {
            return (new DataResource('error', 'Book is not available', null))->response()->setStatusCode(422);
        }

        // Set loan_date to current date
        $loanDate = now()->addHours(8);

        // Set return_date to 7 days from loan_date
        $returnDate = $loanDate->copy()->addDays(7);

        $loan = Loan::create([
            'user_id' => $request->user_id,
            'book_uuid' => $request->book_uuid,
            'loan_date' => $loanDate,
            'return_date' => $returnDate,
        ]);

        $book->availability = false;
        $book->incrementLoanCount();
        $book->save();

        event(new LoanCreated($loan));

        return new DataResource('success', "Data stored successfully. loan date is {$loanDate}", $loan);
    }

    /**
     * Search by User
     *
     * Get loans by user
     *
     */
    public function searchByUserId(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return (new DataResource('error', 'User not found', null))->response()->setStatusCode(404);
        }

        // Get loans by authenticated user's ID
        $loans = Loan::where('user_id', $user->id)->with('book')->latest()->paginate(20);

        return new DataResource('success', 'Data retrieved successfully', $loans);
    }

    /**
     * Return Book
     *
     * Return book
     *
     */
    public function returnBook(Request $request)
    {
        /**
         * Validate request
         */
        $validator = Validator::make($request->all(), [
            'loan_uuid' => 'required|exists:loans,uuid',
        ]);

        if ($validator->fails()) {
            return (new DataResource('error', 'Data not valid', $validator->errors()))->response()->setStatusCode(422);
        }

        $loan = Loan::where('uuid', $request->loan_uuid)->first();
        if ($loan) {
            $this->sendNotification($loan);
            $loan->delete(); // Menghapus record pinjaman setelah buku dikembalikan
            // Opsional: Update status buku menjadi tersedia
            $book = Book::where('uuid', $loan->book_uuid)->first();
            if ($book) {
                $book->availability = true;
                $book->save();
            }
            return new DataResource('success', 'Book returned successfully', null);
        } else {
            return (new DataResource('error', 'Loan not found', null))->response()->setStatusCode(404);
        }
    }

    /**
     * Check Loan
     *
     * Check if book is available or borrowed by user
     */
    public function checkLoan($book_uuid, $user_id)
    {
        $book = Book::where('uuid', $book_uuid)->first();
        $loan = Loan::where('book_uuid', $book_uuid)->where('user_id', $user_id)->first();

        if (!$book && !$loan) {
            return (new DataResource('error', 'Book not found', null))->response()->setStatusCode(404);
        }

        if ($book->availability) {
            return new DataResource('success', 'Book is available', null);
        } else if ($loan) {
            return new DataResource('info', 'Book is borrowed by you', null);
        } else {
            return new DataResource('error', 'Book is borrowed by another user', null);
        }
    }


    /**
     * Send Notification
     *
     * Send notification to user
     */
    public function sendNotification($loan)
    {
        $messaging = Firebase::messaging();
        $user = $loan->user;
        if ($user->token_fcm) {
            Log::info("Sending FCM notification to user {$user->id} with token {$user->token_fcm}");
            try {
                $message = CloudMessage::withTarget('token', $user->token_fcm)
                    ->withNotification([
                        'title' => 'Book Returned',
                        'body' => 'Your book has been returned successfully'
                    ]);
                $messaging->send($message);
            } catch (\Kreait\Firebase\Exception\MessagingException $e) {
                // Handle exception
                Log::error("FCM token for user {$user->id} is invalid: {$e->getMessage()}");
                $user->token_fcm = null;
                $user->save();
            } catch (\Kreait\Firebase\Exception\FirebaseException $e) {
                // Handle exception
                Log::error("An error occurred while sending FCM notification to user {$user->id}: {$e->getMessage()}");
            }
        } else {
            Log::info("FCM token for user {$user->id} is not set");
        }
    }
}
