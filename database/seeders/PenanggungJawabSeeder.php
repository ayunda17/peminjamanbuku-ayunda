<?php

namespace Database\Seeders;

use App\Models\PenanggungJawab;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PenanggungJawabSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $penanggungJawabs = [
            [
                'nama' => 'Dr. Ahmad Santoso',
                'nip' => '198001012010011001',
                'jabatan' => 'Kepala Sekolah',
                'no_hp' => '081234567890',
                'is_active' => true,
            ],
            [
                'nama' => 'Siti Nurhaliza, S.Pd.',
                'nip' => '198512152015032002',
                'jabatan' => 'Wakil Kepala Sekolah',
                'no_hp' => '081345678901',
                'is_active' => true,
            ],
            [
                'nama' => 'Budi Prasetyo, M.Pd.',
                'nip' => '198703221999031003',
                'jabatan' => 'Kepala Perpustakaan',
                'no_hp' => '081456789012',
                'is_active' => true,
            ],
            [
                'nama' => 'Maya Sari, S.Pd.',
                'nip' => '199001152020122003',
                'jabatan' => 'Guru Bahasa Indonesia',
                'no_hp' => '081567890123',
                'is_active' => true,
            ],
            [
                'nama' => 'Rudi Hartono',
                'nip' => '197812051998011004',
                'jabatan' => 'Bendahara Sekolah',
                'no_hp' => '081678901234',
                'is_active' => false,
            ],
        ];

        foreach ($penanggungJawabs as $pj) {
            PenanggungJawab::create($pj);
        }
    }
}
