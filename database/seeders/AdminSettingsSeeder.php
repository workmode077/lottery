<?php

namespace Database\Seeders;

use App\Models\AdminSettings;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class AdminSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing media collections
        AdminSettings::query()->each(function ($record) {
            $record->clearMediaCollection('value');
        });
        AdminSettings::truncate();

        $adminSettings = [
            [
                'key'   => 'website-name',
                'value' => 'Game',
                'type'  => 1
            ],
            [
                'key'   => 'website-auth-background',
                'value' => 'backend/images/auth-bg.jpg',
                'type'  => 2
            ],
            [
                'key'   => 'website-logo',
                'value' => 'backend/images/logo-sm.svg',
                'type'  => 2
            ],
            [
                'key'   => 'website-favicon',
                'value' => 'backend/images/favicon.ico',
                'type'  => 2
            ],
            [
                'key'   => 'website-dashboard-logo',
                'value' => 'backend/images/logo.svg',
                'type'  => 2
            ],
            [
                'key'   => 'backend-prefix',
                'value' => 'admin-portal',
                'type'  => 1
            ],
        ];

        foreach ($adminSettings as $data) {
            $adminSetting = AdminSettings::create($data);

            if ($data['type'] == 2) {
                $this->copyMediaToModel($adminSetting, $data['value'], 'value');
            }
        }
    }

    /**
     * Copy the media file to the specified collection if the file exists.
     */
    private function copyMediaToModel($model, string $filePath, string $collection): void
    {
        if (File::exists($fullPath = public_path($filePath))) {
            $model->copyMedia($fullPath)->toMediaCollection($collection);
        }
    }
}
