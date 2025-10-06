<?php

namespace App\Http\Controllers;

use App\Models\Pengecekan;
use App\Models\PrediksiRating;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        if (!session('is_admin'))
            return redirect('/');

        $itemCodes = ['C1', 'C2', 'C3', 'C4', 'C5', 'C6'];

        // --- Ringkasan dataset: jumlah baris per dataset (tiap dataset = 6 bibit)
        // --- Ringkasan dataset: jumlah baris per dataset (tiap dataset = 6 bibit)
        $datasetsSummary = \App\Models\Pengecekan::select('dataset_name', \DB::raw('COUNT(*) as total_rows'))
            ->whereNotNull('dataset_name')
            ->groupBy('dataset_name')
            ->orderBy('dataset_name')
            ->get();

        $donutLabels = $datasetsSummary->pluck('dataset_name')->map(fn($v) => $v ?? '(tanpa nama)')->values()->all();
        $donutSeries = $datasetsSummary->pluck('total_rows')->values()->all(); // <-- ganti 'rows' -> 'total_rows'


        // --- Dataset terbaru (untuk heatmap & contoh banding)
        $latestDataset = Pengecekan::whereNotNull('dataset_name')
            ->orderByDesc('id')
            ->value('dataset_name');

        // --- Vektor & Cosine (dataset terbaru)
        $matrixSeries = [];
        if ($latestDataset) {
            $rows = Pengecekan::where('dataset_name', $latestDataset)
                ->orderBy('id')
                ->get(['c1', 'c2', 'c3', 'c4', 'c5', 'c6']);

            $vectors = array_fill_keys($itemCodes, []);
            foreach ($rows as $r) {
                $vectors['C1'][] = (int) $r->c1;
                $vectors['C2'][] = (int) $r->c2;
                $vectors['C3'][] = (int) $r->c3;
                $vectors['C4'][] = (int) $r->c4;
                $vectors['C5'][] = (int) $r->c5;
                $vectors['C6'][] = (int) $r->c6;
            }

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

            $matrix = [];
            foreach ($itemCodes as $i) {
                foreach ($itemCodes as $j) {
                    $matrix[$i][$j] = ($i === $j) ? 1.000 : round($cosine($vectors[$i], $vectors[$j]), 3);
                }
            }

            // Apex heatmap format: [{name:'C1', data:[..] }, ...]
            foreach ($itemCodes as $row) {
                $matrixSeries[] = [
                    'name' => $row,
                    'data' => array_values(array_map(fn($c) => $matrix[$row][$c] ?? 0, $itemCodes))
                ];
            }
        }

        // --- Rata-rata prediksi per dataset (bar/column)
        $avgPred = PrediksiRating::select('dataset_name', DB::raw('AVG(prediksi) as avg_pred'))
            ->groupBy('dataset_name')
            ->orderBy('dataset_name')
            ->get();

        $avgPredLabels = $avgPred->pluck('dataset_name')->map(fn($v) => $v ?? '(tanpa nama)')->values()->all();
        $avgPredSeries = $avgPred->pluck('avg_pred')->map(fn($v) => round((float) $v, 3))->values()->all();

        // --- Top 5 prediksi (dataset terbaru bila ada)
        $topQ = PrediksiRating::query();
        if ($latestDataset)
            $topQ->where('dataset_name', $latestDataset);
        $top5 = $topQ->orderByDesc('prediksi')->limit(5)->get(['user_label', 'item_kode', 'prediksi', 'dataset_name']);

        $topCats = $top5->map(fn($r) => "{$r->user_label} ({$r->item_kode})")->values()->all();
        $topSeries = $top5->pluck('prediksi')->map(fn($v) => round((float) $v, 3))->values()->all();

        // --- Tren input dataset per bulan (pakai tanggal pertama tiap dataset)
        $firstDates = Pengecekan::select('dataset_name', DB::raw('MIN(created_at) as first_at'))
            ->whereNotNull('dataset_name')
            ->groupBy('dataset_name')
            ->get();

        $byMonth = [];
        foreach ($firstDates as $d) {
            if (!$d->first_at)
                continue;
            $key = date('Y-m', strtotime($d->first_at));
            $byMonth[$key] = ($byMonth[$key] ?? 0) + 1;
        }
        ksort($byMonth);
        $trendCats = array_keys($byMonth);
        $trendSeries = array_values($byMonth);

        // --- Banding Aktual vs Prediksi (ambil satu contoh dari top5 atau baris prediksi pertama)
        $sample = $top5->first() ?: PrediksiRating::first();
        $banding = [
            'categories' => $itemCodes,
            'actual' => [],
            'predOnly' => [],
            'completed' => [],
            'title' => '',
        ];
        if ($sample) {
            $u = $sample->user_label;
            $ds = $sample->dataset_name;
            $target = $sample->item_kode;

            $uRow = Pengecekan::where('dataset_name', $ds)->where('nama_pembeli', $u)->first();
            if ($uRow) {
                $ratings = [
                    'C1' => (int) $uRow->c1,
                    'C2' => (int) $uRow->c2,
                    'C3' => (int) $uRow->c3,
                    'C4' => (int) $uRow->c4,
                    'C5' => (int) $uRow->c5,
                    'C6' => (int) $uRow->c6,
                ];
                $actual = [];
                $predOnly = [];
                $completed = [];
                foreach ($itemCodes as $c) {
                    $actual[] = $ratings[$c];
                    $predOnly[] = ($c === $target) ? round((float) $sample->prediksi, 3) : null;
                    $completed[] = ($ratings[$c] > 0) ? $ratings[$c] : (($c === $target) ? round((float) $sample->prediksi, 3) : null);
                }
                $banding['actual'] = $actual;
                $banding['predOnly'] = $predOnly;
                $banding['completed'] = $completed;
                $banding['title'] = "Actual vs Prediksi – {$u} / {$ds} (target {$target})";
            }
        }

        // --- (opsional) angka ringkas untuk box atas:
        $totalBibit = (int) Pengecekan::count();
        $totalDataset = (int) Pengecekan::whereNotNull('dataset_name')->distinct()->count('dataset_name');
        $totalPrediksi = (int) PrediksiRating::count();

        return view('admin.index', [
            // donut dataset
            'donutLabels' => $donutLabels,
            'donutSeries' => $donutSeries,

            // heatmap cosine
            'heatmapCats' => $itemCodes,
            'heatmapSeries' => $matrixSeries,
            'latestDataset' => $latestDataset,

            // avg pred per dataset
            'avgPredLabels' => $avgPredLabels,
            'avgPredSeries' => $avgPredSeries,

            // top 5 pred
            'topCats' => $topCats,
            'topSeries' => $topSeries,

            // trend
            'trendCats' => $trendCats,
            'trendSeries' => $trendSeries,

            // banding
            'banding' => $banding,

            // boxes
            'boxTotalProduk' => $totalDataset,   // kamu bisa ganti sesuai kebutuhan
            'boxTotalPegawai' => 6,               // placeholder
            'boxTotalPenjualan' => $totalBibit,     // total baris
            'boxTotalPrediksi' => $totalPrediksi,  // kalau mau ditampilkan
        ]);
    }
}
