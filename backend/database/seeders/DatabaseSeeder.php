<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $roles = [
            'admin' => 'Admin',
            'leader' => 'Leader',
            'santri' => 'Santri Karya',
        ];

        foreach ($roles as $code => $name) {
            $role = DB::table('roles')->where('code', $code);

            if ($role->exists()) {
                $role->update(['name' => $name]);

                continue;
            }

            DB::table('roles')->insert([
                'id' => (string) Str::uuid(),
                'code' => $code,
                'name' => $name,
            ]);
        }

        $activities = [
            ['code' => 'absen_subuh', 'name' => 'Absen Subuh'],
            ['code' => 'shalawat_munjiyat', 'name' => 'Shalawat Munjiyat'],
            ['code' => 'shalat_tepat_waktu_jam_kerja', 'name' => 'Shalat Tepat Waktu di Jam Kerja'],
            ['code' => 'dzikir_pagi_petang', 'name' => 'Dzikir Pagi Petang'],
            ['code' => 'tilawah_harian', 'name' => 'Tilawah Harian'],
            ['code' => 'shalat_dhuha', 'name' => 'Shalat Dhuha'],
            ['code' => 'tahajud', 'name' => 'Tahajud'],
            ['code' => 'tilawati', 'name' => 'Tilawati'],
            ['code' => 'subuhan_di_masjid', 'name' => 'Subuhan di Masjid'],
            ['code' => 'puasa_kamis', 'name' => 'Puasa Kamis'],
        ];

        foreach ($activities as $sortOrder => $activity) {
            $query = DB::table('lks_activities')->where('code', $activity['code']);

            if ($query->exists()) {
                $query->update([
                    'name' => $activity['name'],
                    'is_active' => true,
                    'sort_order' => $sortOrder + 1,
                    'updated_at' => now(),
                ]);

                continue;
            }

            DB::table('lks_activities')->insert([
                'id' => (string) Str::uuid(),
                'code' => $activity['code'],
                'name' => $activity['name'],
                'is_active' => true,
                'sort_order' => $sortOrder + 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
