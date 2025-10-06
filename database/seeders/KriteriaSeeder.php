<?php

namespace Database\Seeders;

use App\Models\Kriteria;
use App\Models\Subkriteria;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KriteriaSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $data = [
                // C1 Ukuran Rimpang
                'C1' => [
                    'nama' => 'Ukuran Rimpang',
                    'subs' => [
                        ['label' => 'Kecil (≤20 gram)', 'bobot' => 1],
                        ['label' => 'Sedang (21–40 gram)', 'bobot' => 2],
                        ['label' => 'Agak Besar (41–60 gram)', 'bobot' => 3],
                        ['label' => 'Besar (61–80 gram)', 'bobot' => 4],
                        ['label' => 'Sangat Besar (≥80 gram)', 'bobot' => 5],
                    ],
                ],
                // C2 Warna Rimpang
                'C2' => [
                    'nama' => 'Warna Rimpang',
                    'subs' => [
                        ['label' => 'Pucat / Kusam', 'bobot' => 1],
                        ['label' => 'Kuning Muda', 'bobot' => 2],
                        ['label' => 'Kuning Emas', 'bobot' => 3],
                        ['label' => 'Oranye Kekuningan', 'bobot' => 4],
                        ['label' => 'Oranye Cerah / Tajam', 'bobot' => 5],
                    ],
                ],
                // C3 Kondisi Kulit Rimpang
                'C3' => [
                    'nama' => 'Kondisi Kulit Rimpang',
                    'subs' => [
                        ['label' => 'Banyak Luka / Cacat', 'bobot' => 1],
                        ['label' => 'Agak Rusak / Bercak', 'bobot' => 2],
                        ['label' => 'Cukup Mulus dengan sedikit noda', 'bobot' => 3],
                        ['label' => 'Hampir Mulus, sedikit keriput', 'bobot' => 4],
                        ['label' => 'Sangat Mulus, Bersih, Sehat', 'bobot' => 5],
                    ],
                ],
                // C4 Usia Bibit
                'C4' => [
                    'nama' => 'Usia Bibit',
                    'subs' => [
                        ['label' => '1 Bulan', 'bobot' => 1],
                        ['label' => '1–2 Bulan', 'bobot' => 2],
                        ['label' => '3–4 Bulan', 'bobot' => 3],
                        ['label' => '5–6 Bulan', 'bobot' => 4],
                        ['label' => '≥6 Bulan', 'bobot' => 5],
                    ],
                ],
                // C5 Kesehatan Bibit
                'C5' => [
                    'nama' => 'Kesehatan Bibit',
                    'subs' => [
                        ['label' => 'Sangat Rentan Penyakit', 'bobot' => 1],
                        ['label' => 'Sering Terserang Penyakit', 'bobot' => 2],
                        ['label' => 'Cukup Sehat, Kadang Sakit', 'bobot' => 3],
                        ['label' => 'Sehat, Jarang Sakit', 'bobot' => 4],
                        ['label' => 'Sangat Sehat, Tahan Penyakit', 'bobot' => 5],
                    ],
                ],
                // C6 Produktivitas Potensial
                'C6' => [
                    'nama' => 'Produktivitas Potensial',
                    'subs' => [
                        ['label' => 'Sangat Rendah (<1 kg/rumpun)', 'bobot' => 1],
                        ['label' => 'Rendah (1–2 kg/rumpun)', 'bobot' => 2],
                        ['label' => 'Sedang (2–3 kg/rumpun)', 'bobot' => 3],
                        ['label' => 'Tinggi (3–4 kg/rumpun)', 'bobot' => 4],
                        ['label' => 'Sangat Tinggi (>4 kg/rumpun)', 'bobot' => 5],
                    ],
                ],
            ];

            $urutan = 1;
            foreach ($data as $kode => $def) {
                $k = Kriteria::updateOrCreate(
                    ['kode' => $kode],
                    ['nama' => $def['nama']]
                );

                // hapus sub lama biar rapi kalau re-seed
                $k->subkriterias()->delete();

                $i = 1;
                foreach ($def['subs'] as $sub) {
                    Subkriteria::create([
                        'kriteria_id' => $k->id,
                        'label'       => $sub['label'],
                        'bobot'       => $sub['bobot'],
                        'urutan'      => $i++,
                        'keterangan'  => null,
                    ]);
                }
                $urutan++;
            }
        });
    }
}
