<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CobitItem;
use App\Models\Kategori;
use App\Models\Level;
use App\Models\Quisioner;
use Illuminate\Support\Facades\DB;

class CobitDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Nonaktifkan pengecekan foreign key untuk sementara
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Kosongkan tabel untuk menghindari duplikasi data saat seeder dijalankan ulang
        CobitItem::truncate();
        Kategori::truncate();
        Level::truncate();
        Quisioner::truncate();

        // Data yang akan di-seed
        $data = [
            [
                'nama_item' => 'APO01',
                'deskripsi' => 'Managed I&T Management Framework. This process ensures that the I&T management framework is aligned with the enterprise’s overall governance framework.',
                'is_visible' => true,
                'kategoris' => [
                    [
                        'nama' => 'APO01.01 - Define the Management Framework',
                        'levels' => [
                            ['level_number' => 1, 'nama_level' => 'Level 1: Initial/Ad Hoc', 'pertanyaan' => ['Pertanyaan untuk APO01.01 Level 1.']],
                            ['level_number' => 2, 'nama_level' => 'Level 2: Managed', 'pertanyaan' => ['Pertanyaan untuk APO01.01 Level 2.']],
                            ['level_number' => 3, 'nama_level' => 'Level 3: Established', 'pertanyaan' => ['Pertanyaan untuk APO01.01 Level 3.']],
                            ['level_number' => 4, 'nama_level' => 'Level 4: Predictable', 'pertanyaan' => ['Pertanyaan untuk APO01.01 Level 4.']],
                            ['level_number' => 5, 'nama_level' => 'Level 5: Optimizing', 'pertanyaan' => ['Pertanyaan untuk APO01.01 Level 5.']],
                        ]
                    ],
                    [
                        'nama' => 'APO01.02 - Define Governance Principles',
                        'levels' => [
                            ['level_number' => 1, 'nama_level' => 'Level 1: Initial/Ad Hoc', 'pertanyaan' => ['Pertanyaan untuk APO01.02 Level 1.']],
                            ['level_number' => 2, 'nama_level' => 'Level 2: Managed', 'pertanyaan' => ['Pertanyaan untuk APO01.02 Level 2.']],
                            ['level_number' => 3, 'nama_level' => 'Level 3: Established', 'pertanyaan' => ['Pertanyaan untuk APO01.02 Level 3.']],
                            ['level_number' => 4, 'nama_level' => 'Level 4: Predictable', 'pertanyaan' => ['Pertanyaan untuk APO01.02 Level 4.']],
                            ['level_number' => 5, 'nama_level' => 'Level 5: Optimizing', 'pertanyaan' => ['Pertanyaan untuk APO01.02 Level 5.']],
                        ]
                    ],
                    [
                        'nama' => 'APO01.03 - Define Roles and Structures',
                        'levels' => [
                            ['level_number' => 1, 'nama_level' => 'Level 1: Initial/Ad Hoc', 'pertanyaan' => ['Pertanyaan untuk APO01.03 Level 1.']],
                            ['level_number' => 2, 'nama_level' => 'Level 2: Managed', 'pertanyaan' => ['Pertanyaan untuk APO01.03 Level 2.']],
                            ['level_number' => 3, 'nama_level' => 'Level 3: Established', 'pertanyaan' => ['Pertanyaan untuk APO01.03 Level 3.']],
                            ['level_number' => 4, 'nama_level' => 'Level 4: Predictable', 'pertanyaan' => ['Pertanyaan untuk APO01.03 Level 4.']],
                            ['level_number' => 5, 'nama_level' => 'Level 5: Optimizing', 'pertanyaan' => ['Pertanyaan untuk APO01.03 Level 5.']],
                        ]
                    ],
                ]
            ],
            // Contoh data kedua tetap ada untuk variasi
            [
                'nama_item' => 'BAI03',
                'deskripsi' => 'Managed Solutions Identification and Build. This process ensures that business requirements are translated into effective and efficient solutions.',
                'is_visible' => true,
                'kategoris' => [
                    [
                        'nama' => 'BAI03.01 - Design High-Level Solutions',
                        'levels' => [
                            ['level_number' => 1, 'nama_level' => 'Level 1: Initial', 'pertanyaan' => ['Apakah desain solusi tingkat tinggi sudah dibuat berdasarkan kebutuhan bisnis?']],
                            ['level_number' => 2, 'nama_level' => 'Level 2: Managed', 'pertanyaan' => ['Apakah desain solusi yang detail dikembangkan dan didokumentasikan?']],
                        ]
                    ],
                ]
            ],
        ];

        // Looping untuk memasukkan data
        foreach ($data as $itemData) {
            // Buat CobitItem
            $cobitItem = CobitItem::create([
                'nama_item' => $itemData['nama_item'],
                'deskripsi' => $itemData['deskripsi'],
                'is_visible' => $itemData['is_visible'],
            ]);

            foreach ($itemData['kategoris'] as $kategoriData) {
                // Buat Kategori yang berelasi dengan CobitItem
                $kategori = $cobitItem->kategoris()->create([
                    'nama' => $kategoriData['nama'],
                ]);

                foreach ($kategoriData['levels'] as $levelData) {
                    // Buat Level yang berelasi dengan Kategori
                    $level = $kategori->levels()->create([
                        'level_number' => $levelData['level_number'],
                        'nama_level' => $levelData['nama_level'],
                    ]);

                    foreach ($levelData['pertanyaan'] as $pertanyaan) {
                        // Buat Quisioner yang berelasi dengan Level
                        $level->quisioners()->create([
                            'pertanyaan' => $pertanyaan,
                        ]);
                    }
                }
            }
        }

        // Aktifkan kembali pengecekan foreign key
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
