@extends('layouts.main')
@section('content')
<section class="dashboard">
  <div class="top">
    <i class="uil uil-bars sidebar-toggle"></i>
    <div class="search-box">
      <i class="uil uil-search"></i>
      <input type="text" placeholder="Search here...">
    </div>
    <img src="/images/profil.png" alt="">
  </div>

  <div class="dash-content">
    <div class="overview">
      <div class="title">
        <i class="uil uil-chart-line"></i>
        <span class="text">Dashboard Penjualan</span>
      </div>
    </div>
    <div class="boxes">
      <div class="box box1">
        <i class="uil uil-thumbs-up"></i>
        <span class="text">Total Dataset</span>
        <span class="number">{{ $boxTotalProduk ?? 0 }}</span>
      </div>
      <div class="box box2">
        <i class="uil uil-comments"></i>
        <span class="text">Total Pegawai</span>
        <span class="number">6</span>
      </div>
      <div class="box box3">
        <i class="uil uil-share"></i>
        <span class="text">Total Bibit (Baris)</span>
        <span class="number">{{ $boxTotalPenjualan ?? 0 }}</span>
      </div>
    </div>

    {{-- Grafik Chart Apex --}}
    <div class="activity">
      <div class="title mb-3">
        <i class="uil uil-chart-bar"></i>
        <span class="text">Analisis dan Prediksi</span>
      </div>

      <div class="row">
        {{-- Chart 1: Distribusi Dataset (Donut) --}}
        <div class="col-md-6 mb-4">
          <div id="chartTotalProduk"></div>
        </div>

        {{-- Chart 2: Heatmap Kemiripan (Cosine) dataset terbaru --}}
        <div class="col-md-6 mb-4">
          <div id="chartMape"></div>
        </div>

        {{-- Chart 3: Rata-rata Prediksi per Dataset --}}
        <div class="col-md-6 mb-4">
          <div id="chartTotalBulan"></div>
        </div>

        {{-- Chart 4: Top 5 Prediksi Tertinggi (dataset terbaru) --}}
        <div class="col-md-6 mb-4">
          <div id="chartTopProduk"></div>
        </div>

        {{-- Chart 5: Data Aktual vs Prediksi (contoh 1 user) --}}
        <div class="col-md-12 mb-4">
          <div id="chartBandingAktual"></div>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- ApexCharts CDN --}}
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>
  // Data dari controller (PHP -> JS)
  const donutLabels   = @json($donutLabels);
  const donutSeries   = @json($donutSeries);

  const heatmapCats   = @json($heatmapCats);
  const heatmapSeries = @json($heatmapSeries);
  const latestDataset = @json($latestDataset);

  const avgPredLabels = @json($avgPredLabels);
  const avgPredSeries = @json($avgPredSeries);

  const topCats       = @json($topCats);
  const topSeries     = @json($topSeries);

  const trendCats     = @json($trendCats);
  const trendSeries   = @json($trendSeries);

  const banding       = @json($banding);

  // 1) Donut: Distribusi Dataset (jumlah baris/bibit per dataset)
  new ApexCharts(document.querySelector("#chartTotalProduk"), {
    chart: { type: 'donut', height: 320 },
    series: donutSeries,
    labels: donutLabels.length ? donutLabels : ['(kosong)'],
    title: { text: 'Distribusi Dataset Bibit Temulawak' },
    noData: { text: 'Belum ada data' }
  }).render();

  // 2) Heatmap Kemiripan (Cosine) untuk dataset terbaru
  new ApexCharts(document.querySelector("#chartMape"), {
    chart: { type: 'heatmap', height: 320 },
    series: heatmapSeries.length ? heatmapSeries : [],
    xaxis: { categories: heatmapCats },
    dataLabels: { enabled: false },
    title: { text: 'Matriks Kemiripan (Cosine) ' + (latestDataset ? `– ${latestDataset}` : '') },
    noData: { text: 'Belum ada data' },
    plotOptions: { heatmap: { shadeIntensity: 0.5 } }
  }).render();

  // 3) Column: Rata-rata Nilai Prediksi per Dataset
  new ApexCharts(document.querySelector("#chartTotalBulan"), {
    chart: { type: 'bar', height: 320 },
    series: [{ name: 'Rata-rata Prediksi', data: avgPredSeries }],
    xaxis: { categories: avgPredLabels },
    title: { text: 'Rata-rata Nilai Prediksi per Dataset' },
    noData: { text: 'Belum ada data' }
  }).render();

  // 4) Horizontal Bar: Top 5 Prediksi Tertinggi (dataset terbaru)
  new ApexCharts(document.querySelector("#chartTopProduk"), {
    chart: { type: 'bar', height: 320 },
    plotOptions: { bar: { horizontal: true } },
    series: [{ name: 'Nilai Prediksi', data: topSeries }],
    xaxis: { categories: topCats },
    title: { text: 'Top 5 Prediksi Tertinggi' },
    noData: { text: 'Belum ada data' }
  }).render();

  // 5) Banding Aktual vs Prediksi (1 contoh)
  new ApexCharts(document.querySelector("#chartBandingAktual"), {
    chart: { type: 'bar', height: 360, stacked: false },
    series: [
      { name: 'Actual', data: banding.actual || [] },
      { name: 'Prediksi (Target)', data: banding.predOnly || [] },
      { name: 'Actual + Prediksi', data: banding.completed || [] },
    ],
    xaxis: { categories: banding.categories || [] },
    title: { text: banding.title || 'Actual vs Prediksi' },
    noData: { text: 'Belum ada data' }
  }).render();
</script>
@endsection
