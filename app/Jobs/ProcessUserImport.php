<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;


class ProcessUserImport implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels;

    protected $rows;

    public function __construct(array $rows)
    {
        $this->rows = $rows;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $validRows = array_filter($this->rows, function ($row) {
            return !empty($row['name']) && !empty($row['email']) && !empty($row['nisn']);
        });
        try {
            Log::info('Processing rows:', $this->rows);

            $data = array_map(function ($row) {
                if (
                    empty($row['name']) ||
                    empty($row['email']) ||
                    empty($row['nisn']) ||
                    empty($row['class']) ||
                    empty($row['description']) ||
                    empty($row['password'])
                ) {
                    throw new \Exception("Invalid data in row: " . json_encode($row));
                }

                return [
                    'name' => $row['name'],
                    'email' => $row['email'],
                    'nisn' => $row['nisn'],
                    'class' => $row['class'],
                    'description' => $row['description'],
                    'password' => Hash::make($row['password']),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }, $validRows);

            User::insert($data);
        } catch (\Exception $e) {
            Log::error('Error processing rows: ' . $e->getMessage());
            throw $e;
        }
    }
}
