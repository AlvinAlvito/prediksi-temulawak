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
            <div class="activity">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="title">
                        <i class="bi bi-box-seam me-2"></i>
                        <span class="text fs-5">Detail Produk: <strong>{{ $produk->nama_produk }}</strong></span>
                    </div>
                    <a href="{{ route('hasil.prediksi') }}" class="btn btn-primary">
                        Kembali <i class="bi bi-arrow-right"></i>
                    </a>
                </div>

                <table class="table table-hover table-striped border">
                    <thead>
                        <tr>
                            <th>Harga Satuan </th>
                            <th>Persamaan Regresi</th>
                            <th>Intercept (a)</th>
                            <th>Koefisien (b)</th>
                            <th>MAPE</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Rp {{ number_format($produk->harga_satuan) }}</td>
                            <td>{{ $regresi->persamaan }}</td>
                            <td>{{ round($regresi->a, 2) }}</td>
                            <td>{{ round($regresi->b, 2) }}</td>
                            <td>{{ $regresi->mape }}%</td>

                        </tr>

                    </tbody>
                </table>

                <div class="row">
                    <div class="col-6">
                        {{-- Tabel Data Aktual --}}
                        <div class="card mb-4">
                            <div class="card-header bg-info text-white">
                                <i class="bi bi-bar-chart-fill me-2"></i> Data Penjualan Aktual (Jan–Jun)
                            </div>
                            <div class="card-body p-0">

                                <table class="table table-hover table-striped border">
                                    <thead>
                                        <tr>
                                            <th>Bulan</th>
                                            <th>Penjualan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($aktual as $bulan => $jumlah)
                                            <tr>
                                                <td>{{ $bulan }}</td>
                                                <td>{{ $jumlah }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="card mb-4">
    <div class="card-header bg-warning text-dark">
        <i class="bi bi-calculator me-2"></i> Tabel Evaluasi MAPE (Manual)
    </div>
    <div class="card-body p-0">
        <table class="table table-bordered table-striped text-center">
            <thead class="table-light">
                <tr>
                    <th>X</th>
                    <th>Bulan</th>
                    <th>Aktual</th>
                    <th>Prediksi</th>
                    <th>Selisih</th>
                    <th>%Error</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($evaluasi as $row)
                    <tr>
                        <td>{{ $row['x'] }}</td>
                        <td>{{ $row['bulan'] }}</td>
                        <td>{{ $row['aktual'] }}</td>
                        <td>{{ number_format($row['prediksi'], 2) }}</td>
                        <td>{{ number_format($row['selisih'], 2) }}</td>
                        <td>{{ number_format($row['error_percent'], 2) }}%</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="fw-bold bg-light">
                    <td colspan="5" class="text-end">MAPE:</td>
                    <td>{{ number_format($mape_manual, 2) }}%</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

                    </div>
                    <div class="col-6">
                        <div class="card mb-4">
                            <div class="card-header bg-success text-white">
                                <i class="bi bi-graph-up-arrow me-2"></i> Prediksi Penjualan (Jul–Des)
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-hover table-striped border">
                                    <thead>
                                        <tr>
                                            <th>Bulan</th>
                                            <th>Prediksi Penjualan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($prediksi as $bulan => $jumlah)
                                            <tr>
                                                <td>{{ $bulan }}</td>
                                                <td>{{ $jumlah }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>


                {{-- Tabel Prediksi --}}


                {{-- Grafik Chart.js --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-light">
                        <i class="bi bi-bar-chart-line-fill me-2"></i> Grafik Penjualan
                    </div>
                    <div class="card-body">
                        <div id="grafikPrediksi" style="height: 350px;"></div>
                    </div>
                </div>


            </div>
        </div>
    </section>


    {{-- Bootstrap Icons CDN --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        const dataAktual = {!! json_encode(array_values($aktual)) !!}; // Jan–Jun
        const dataPrediksi = {!! json_encode(array_values($prediksi)) !!}; // Jul–Des
        const namaProduk = {!! json_encode($produk->nama_produk) !!};

        new ApexCharts(document.querySelector("#grafikPrediksi"), {
            chart: {
                type: 'line',
                height: 350
            },
            series: [{
                    name: 'Aktual Jan–Jun',
                    data: dataAktual
                },
                {
                    name: 'Prediksi Jul–Des',
                    data: dataPrediksi
                }
            ],
            xaxis: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des']
            },
            stroke: {
                curve: 'smooth'
            },
            title: {
                text: `Perbandingan Data Aktual & Prediksi - ${namaProduk}`,
                align: 'center'
            },
            colors: ['#1E90FF', '#FF5733'],
            markers: {
                size: 4
            }
        }).render();
    </script>
@endsection
