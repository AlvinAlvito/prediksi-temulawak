<?php

namespace App\Http\Controllers;

use App\Models\Kriteria;
use App\Models\Pengecekan;
use App\Models\PrediksiRating;
use Illuminate\Http\Request;

class PengecekanController extends Controller
{
    // ====== CRUD (tetap) ======
    public function index()
    {
        $kriterias = Kriteria::with(['subkriterias' => fn($q) => $q->orderBy('urutan')])
            ->orderBy('kode')->get();
        $pengecekan = Pengecekan::orderByDesc('id')->get();

        // view CRUD kamu di /resources/views/admin/data-pengecekan.blade.php
        return view('admin.data-pengecekan', compact('kriterias', 'pengecekan'));
    }

    public function store(\Illuminate\Http\Request $request)
    {
        // Validasi: wajib ada nama dataset + tepat 6 item
        $validated = $request->validate([
            'dataset_name' => 'required|string|max:100',
            'items' => 'required|array|size:6', // harus 6
            'items.*.nama' => 'required|string|max:255',
            'items.*.c1' => 'required|integer|min:0|max:5',
            'items.*.c2' => 'required|integer|min:0|max:5',
            'items.*.c3' => 'required|integer|min:0|max:5',
            'items.*.c4' => 'required|integer|min:0|max:5',
            'items.*.c5' => 'required|integer|min:0|max:5',
            'items.*.c6' => 'required|integer|min:0|max:5',
        ]);

        // Simpan 6 baris sekaligus
        foreach ($validated['items'] as $it) {
            \App\Models\Pengecekan::create([
                'dataset_name' => $validated['dataset_name'],
                'nama_pembeli' => $it['nama'], // di konteksmu: Nama Bibit
                'c1' => $it['c1'],
                'c2' => $it['c2'],
                'c3' => $it['c3'],
                'c4' => $it['c4'],
                'c5' => $it['c5'],
                'c6' => $it['c6'],
            ]);
        }

        return back()->with('success', 'Dataset "' . $validated['dataset_name'] . '" berhasil disimpan (6 bibit).');
    }



    public function update(Request $request, $id)
    {
        $row = Pengecekan::findOrFail($id);
        $data = $request->validate([
            'nama_pembeli' => 'required|string|max:255',
            'c1' => 'nullable|integer|min:0|max:5',
            'c2' => 'nullable|integer|min:0|max:5',
            'c3' => 'nullable|integer|min:0|max:5',
            'c4' => 'nullable|integer|min:0|max:5',
            'c5' => 'nullable|integer|min:0|max:5',
            'c6' => 'nullable|integer|min:0|max:5',
        ]);
        foreach (['c1', 'c2', 'c3', 'c4', 'c5', 'c6'] as $c)
            $data[$c] = $data[$c] ?? 0;

        $row->update($data);
        return back()->with('success', 'Data pengecekan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $row = Pengecekan::findOrFail($id);
        $row->delete();
        return back()->with('success', 'Data pengecekan berhasil dihapus.');
    }

    // ====== PROSES: Cosine Similarity (item-based) ======
    public function hasilPrediksi(\Illuminate\Http\Request $req)
    {
        $anchorTitle = strtoupper($req->query('anchor', 'C1')); // hanya untuk judul
        $itemCodes = ['C1', 'C2', 'C3', 'C4', 'C5', 'C6'];

        // daftar dataset utk dropdown
        $datasets = \App\Models\Pengecekan::select('dataset_name')
            ->whereNotNull('dataset_name')
            ->distinct()
            ->orderBy('dataset_name')
            ->pluck('dataset_name')
            ->toArray();

        // pilih dataset (query ?dataset=..., atau paling baru)
        $useDataset = $req->query('dataset');
        if (!$useDataset) {
            $useDataset = \App\Models\Pengecekan::whereNotNull('dataset_name')
                ->orderByDesc('id')->value('dataset_name');
        }

        $query = \App\Models\Pengecekan::query();
        if ($useDataset) {
            $query->where('dataset_name', $useDataset);
        }

        // sertakan dataset_name di select
        $rows = $query->orderBy('id')->get([
            'id',
            'dataset_name',
            'nama_pembeli',
            'c1',
            'c2',
            'c3',
            'c4',
            'c5',
            'c6'
        ]);

        // jika masih kosong
        if ($rows->count() < 1) {
            return view('admin.hasil-prediksi', [
                'header' => $itemCodes,
                'datasets' => $datasets,
                'useDataset' => $useDataset,
                'anchor' => $anchorTitle,
                'note' => 'Belum ada data pada dataset terpilih.',
                'tableRows' => [],
            ]);
        }

        // REINDEX -> 0..n
        $rowsIndexed = $rows->values();

        // bentuk vektor item dari dataset terpilih
        $vectors = array_fill_keys($itemCodes, []);
        foreach ($rowsIndexed as $r) {
            $vectors['C1'][] = (int) $r->c1;
            $vectors['C2'][] = (int) $r->c2;
            $vectors['C3'][] = (int) $r->c3;
            $vectors['C4'][] = (int) $r->c4;
            $vectors['C5'][] = (int) $r->c5;
            $vectors['C6'][] = (int) $r->c6;
        }

        // COSINE TANPA FILTER 0 (sesuai manual)
        $cosine = function (array $a, array $b): float {
            $dot = 0;
            $na = 0;
            $nb = 0;
            $n = max(count($a), count($b));
            for ($i = 0; $i < $n; $i++) {
                $ai = $a[$i] ?? 0;
                $bi = $b[$i] ?? 0;

                // Abaikan nilai 0 pada dot product, tapi tetap dihitung di akar
                if ($ai > 0 && $bi > 0) {
                    $dot += $ai * $bi;
                }

                $na += $ai * $ai;
                $nb += $bi * $bi;
            }

            if ($na == 0 || $nb == 0)
                return 0.0;
            return $dot / (sqrt($na) * sqrt($nb));
        };


        // hitung full 6x6
        $matrix = [];
        foreach ($itemCodes as $i) {
            foreach ($itemCodes as $j) {
                if ($i === $j) {
                    $hasAny = collect($vectors[$i])->contains(fn($v) => $v > 0);
                    $matrix[$i][$j] = $hasAny ? 1.000 : 0.000;
                } else {
                    $matrix[$i][$j] = round($cosine($vectors[$i], $vectors[$j]), 3);
                }
            }
        }

        // label baris: ambil dari rowsIndexed (A1..A6) atau fallback
        $labels = $rowsIndexed->pluck('nama_pembeli')->take(6)->values()->all();
        $fallback = ['A1', 'A2', 'A3', 'A4', 'A5', 'A6'];
        for ($i = 0; $i < 6; $i++) {
            if (empty($labels[$i]))
                $labels[$i] = $fallback[$i];
        }

        // siapkan baris tabel (maks 6 baris / sesuai jumlah yang ada)
        $rowCountToRender = min(6, $rowsIndexed->count());
        $tableRows = [];
        for ($i = 0; $i < $rowCountToRender; $i++) {
            $rowCode = $itemCodes[$i]; // C1..C6
            $rowModel = $rowsIndexed->get($i); // model ke-i (sudah 0..n)
            $datasetPerRow = (!empty($rowModel?->dataset_name)) ? $rowModel->dataset_name : '-';

            $rowData = [
                'dataset' => $datasetPerRow,
                'label' => $labels[$i],
            ];
            foreach ($itemCodes as $colCode) {
                $rowData[$colCode] = $matrix[$rowCode][$colCode] ?? 0.0;
            }
            $tableRows[] = $rowData;
        }

        // catatan jika semua off-diagonal 0
        $offAllZero = true;
        foreach ($itemCodes as $i) {
            foreach ($itemCodes as $j) {
                if ($i !== $j && ($matrix[$i][$j] ?? 0) > 0) {
                    $offAllZero = false;
                    break 2;
                }
            }
        }
        $note = $offAllZero
            ? 'Semua similarity selain diagonal = 0. Tambahkan data A1..A6 (atau dataset dengan overlap penilaian) agar hasil bermakna.'
            : null;

        return view('admin.hasil-prediksi', [
            'header' => $itemCodes,
            'datasets' => $datasets,
            'useDataset' => $useDataset,
            'anchor' => $anchorTitle,
            'note' => $note,
            'tableRows' => $tableRows,
        ]);
    }
    public function hitungPrediksi(\Illuminate\Http\Request $req)
    {
        if (!session('is_admin'))
            return redirect('/');

        $itemCodes = ['C1', 'C2', 'C3', 'C4', 'C5', 'C6'];

        // pilih dataset
        $useDataset = $req->input('dataset');
        if (!$useDataset) {
            $useDataset = \App\Models\Pengecekan::whereNotNull('dataset_name')
                ->orderByDesc('id')->value('dataset_name');
        }

        // ambil data dataset
        $rows = \App\Models\Pengecekan::where('dataset_name', $useDataset)
            ->orderBy('id')
            ->get(['id', 'dataset_name', 'nama_pembeli', 'c1', 'c2', 'c3', 'c4', 'c5', 'c6']);

        if ($rows->isEmpty()) {
            return redirect()->route('prediksi.rangking')
                ->with('warning', 'Dataset belum ada.');
        }

        // bentuk vektor item
        $rowsIndexed = $rows->values();
        $vectors = array_fill_keys($itemCodes, []);
        foreach ($rowsIndexed as $r) {
            $vectors['C1'][] = (int) $r->c1;
            $vectors['C2'][] = (int) $r->c2;
            $vectors['C3'][] = (int) $r->c3;
            $vectors['C4'][] = (int) $r->c4;
            $vectors['C5'][] = (int) $r->c5;
            $vectors['C6'][] = (int) $r->c6;
        }

        // cosine standar (semua elemen)
        $cosine = function (array $a, array $b): float {
            $dot = 0;
            $na = 0;
            $nb = 0;
            $n = max(count($a), count($b));
            for ($i = 0; $i < $n; $i++) {
                $ai = $a[$i] ?? 0;
                $bi = $b[$i] ?? 0;
                $dot += $ai * $bi;
                $na += $ai * $ai;
                $nb += $bi * $bi;
            }
            if ($na == 0 || $nb == 0)
                return 0.0;
            return $dot / (sqrt($na) * sqrt($nb));
        };

        // matriks similarity sim(i,j)
        $sim = [];
        foreach ($itemCodes as $i) {
            foreach ($itemCodes as $j) {
                if ($i === $j) {
                    $sim[$i][$j] = 1.0;
                    continue;
                }
                $sim[$i][$j] = $cosine($vectors[$i], $vectors[$j]);
            }
        }

        // bersihkan hasil lama dataset ini
        PrediksiRating::where('dataset_name', $useDataset)->delete();

        // untuk setiap user, cari item yang 0 → prediksi r_hat
        foreach ($rowsIndexed as $uIdx => $userRow) {
            $userLabel = $userRow->nama_pembeli;
            $ratings = [
                'C1' => (int) $userRow->c1,
                'C2' => (int) $userRow->c2,
                'C3' => (int) $userRow->c3,
                'C4' => (int) $userRow->c4,
                'C5' => (int) $userRow->c5,
                'C6' => (int) $userRow->c6,
            ];

            // cari item kosong (0). Kalau ada banyak, kita prediksi semuanya.
            $missingItems = array_keys(array_filter($ratings, fn($v) => $v === 0));

            foreach ($missingItems as $target) {
                $num = 0.0;
                $den = 0.0;
                $used = [];

                // tetangga j = item yang user beri rating > 0
                foreach ($itemCodes as $j) {
                    if ($j === $target)
                        continue;
                    $r_uj = $ratings[$j];
                    if ($r_uj > 0) {
                        $w = $sim[$target][$j] ?? 0.0; // sim(i,j)
                        if ($w != 0.0) {
                            $num += $w * $r_uj;
                            $den += abs($w);
                            $used[$j] = ['sim' => $w, 'rating' => $r_uj];
                        }
                    }
                }

                $pred = ($den > 0) ? ($num / $den) : 0.0;

                PrediksiRating::create([
                    'dataset_name' => $useDataset,
                    'user_label' => $userLabel,
                    'item_kode' => $target,
                    'prediksi' => round($pred, 3),
                    'neighbors_used' => json_encode($used),
                    'denominator' => $den,
                ]);
            }
        }

        return redirect()->route('prediksi.rangking', ['dataset' => $useDataset])
            ->with('success', 'Perhitungan prediksi & rangking selesai untuk dataset: ' . $useDataset);
    }

    public function rangkingPrediksi(\Illuminate\Http\Request $req)
    {
        if (!session('is_admin'))
            return redirect('/');

        $datasets = \App\Models\Pengecekan::select('dataset_name')
            ->whereNotNull('dataset_name')->distinct()->orderBy('dataset_name')
            ->pluck('dataset_name')->toArray();

        $useDataset = $req->query('dataset');
        if (!$useDataset) {
            $useDataset = \App\Models\Pengecekan::whereNotNull('dataset_name')
                ->orderByDesc('id')->value('dataset_name');
        }

        // ambil prediksi dataset (biasanya 1 target per user → 6 baris)
        $prediksi = PrediksiRating::when($useDataset, fn($q) => $q->where('dataset_name', $useDataset))
            ->orderByDesc('prediksi')
            ->get(['dataset_name', 'user_label', 'item_kode', 'prediksi']);

        // buat rank
        $ranked = $prediksi->values()->map(function ($p, $idx) {
            return [
                'dataset' => $p->dataset_name,
                'user' => $p->user_label,
                'item' => $p->item_kode,
                'pred' => (float) $p->prediksi,
                'rank' => $idx + 1,
            ];
        });

        return view('admin.hasil-rangking', [
            'datasets' => $datasets,
            'useDataset' => $useDataset,
            'rows' => $ranked,
        ]);
    }





}
