<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use \App\Jobs\ProcessUserImport;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Facades\Log;


class UsersImport implements ToCollection, WithHeadingRow, WithChunkReading
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */

    public function collection(Collection $rows)
    {

        Log::info('Rows read from Excel:', ['rows' => $rows->toArray()]);

        if ($rows->isEmpty()) {
            Log::error('No rows found in the Excel file.');
            return;
        }
        ProcessUserImport::dispatch($rows->toArray());
    }

    public function chunkSize(): int
    {
        return 100;
    }
    // public function model(array $row)
    // {

    //     // dd($row);
    //     return new User([
    //         'name' => $row['name'],
    //         'email' => $row['email'],
    //         'nisn' => $row['nisn'],
    //         'class' => $row['class'],
    //         'description' => $row['description'],
    //         'password' => Hash::make($row['password']),
    //     ]);
    // }
}
