<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as FakerFactory;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Book;

class BooksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $faker = FakerFactory::create();

        // Path to dummy files
        $dummyImagePath = 'public/dummy/image.png';
        $dummyPdfPath = 'public/dummy/file.pdf';

        // Ensure dummy files exist
        if (!Storage::exists($dummyImagePath) || !Storage::exists($dummyPdfPath)) {
            throw new \Exception('Dummy files do not exist in storage/dummy directory.');
        }

        // Generate 100 dummy books
        for ($i = 0; $i < 5; $i++) {
            // Generate unique names for image and PDF
            $uniqueImageName = Str::random(40) . '.png';
            $uniquePdfName = Str::random(40) . '.pdf';

            // Copy dummy image and PDF with unique names
            Storage::copy($dummyImagePath, 'public/books/images/' . $uniqueImageName);
            Storage::copy($dummyPdfPath, 'public/books/pdfs/' . $uniquePdfName);

            Book::create([
                'title' => $faker->sentence(10),
                'author' => $faker->name,
                'description' => $faker->paragraph(1),
                'isbn' => $faker->isbn13,
                'year' => $faker->year,
                'pages' => $faker->numberBetween(100, 1000),
                'publisher' => $faker->company,
                'image' => $uniqueImageName,
                'filePdf' => $uniquePdfName,
            ]);
        }
    }
}
