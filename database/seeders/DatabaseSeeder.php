<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as FakerFactory;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // ...

        $faker = FakerFactory::create();

        $publicPdfPath = public_path('pdfs');
        $storagePdfPath = storage_path('pdfs');
        if (!File::exists($publicPdfPath)) {
            File::makeDirectory($publicPdfPath, 0755, true);
        }
        if (!File::exists($storagePdfPath)) {
            File::makeDirectory($storagePdfPath, 0755, true);
        }

        // Generate a dummy image
        $imagePath = 'public/dummy/dummy-image.png';
        $imageContents = file_get_contents($faker->imageUrl());
        Storage::put($imagePath, $imageContents);

        // Generate a dummy PDF
        $pdfPath = 'public/dummy/dummy-book.pdf';
        $pdfSourcePath = $publicPdfPath . '/dummy.pdf';
        File::put($pdfSourcePath, 'Dummy PDF content');  // Add actual content as needed
        $pdfContents = file_get_contents($pdfSourcePath);
        Storage::put($pdfPath, $pdfContents);

        // generate a dummy book 100 data
        for ($i = 0; $i < 100; $i++) {
            // Generate unique names for image and PDF
            $uniqueImageName = 'books/images/' . $faker->unique()->md5 . '.jpg';
            $uniquePdfName = 'books/pdfs/' . $faker->unique()->md5 . '.pdf';

            // Copy dummy image and PDF with unique names
            Storage::copy($imagePath, $uniqueImageName);
            Storage::copy($pdfPath, $uniquePdfName);

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
