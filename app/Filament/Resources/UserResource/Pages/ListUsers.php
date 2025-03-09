<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            \EightyNine\ExcelImport\ExcelImportAction::make()
                ->use(\App\Imports\UsersImport::class)
                ->after(function () {
                    try {
                        // Proses import
                        Notification::make()
                            ->title('Import Berhasil')
                            ->body('Data berhasil diimpor ke sistem.')
                            ->success()
                            ->send();

                        redirect()->back();
                    } catch (\Exception $e) {
                        // Tampilkan notifikasi error jika gagal
                        Notification::make()
                            ->title('Import Gagal')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }
}
