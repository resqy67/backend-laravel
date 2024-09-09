<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UsersImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {

        // dd($row);
        return new User([
            'name' => $row['name'],
            'email' => $row['email'],
            'nisn' => $row['nisn'],
            'class' => $row['class'],
            'description' => $row['description'],
            'password' => Hash::make($row['password']),
        ]);
    }
}
