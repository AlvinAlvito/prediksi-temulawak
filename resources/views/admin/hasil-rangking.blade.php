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
      <div class="title">
        <i class="uil uil-clipboard-notes"></i>
        <span class="text">Hasil Prediksi & Perangkingan</span>
      </div>

      @if (session('success'))
        <div class="alert alert-success mt-2">{{ session('success') }}</div>
      @endif
      @if (session('warning'))
        <div class="alert alert-warning mt-2">{{ session('warning') }}</div>
      @endif

      <div class="row justify-content-between mb-3">
        <div class="col-md-8">
          <form action="{{ route('prediksi.hitung') }}" method="POST" class="d-inline-flex gap-2">
            @csrf
            <select name="dataset" class="form-select form-select-sm">
              @foreach ($datasets as $ds)
                <option value="{{ $ds }}" {{ $useDataset===$ds?'selected':'' }}>{{ $ds }}</option>
              @endforeach
            </select>
            <button class="btn btn-sm btn-primary">
              <i class="uil uil-calculator"></i> Hitung Prediksi
            </button>
          </form>
        </div>
        <div class="col-md-4 text-end">
          <form method="get" action="{{ route('prediksi.rangking') }}" class="d-inline-flex gap-2">
            <select name="dataset" class="form-select form-select-sm" onchange="this.form.submit()">
              @foreach ($datasets as $ds)
                <option value="{{ $ds }}" {{ $useDataset===$ds?'selected':'' }}>{{ $ds }}</option>
              @endforeach
            </select>
          </form>
        </div>
      </div>

      @if ($rows->isEmpty())
        <div class="text-muted">Belum ada hasil prediksi untuk dataset terpilih. Klik <b>Hitung Prediksi</b> untuk memproses.</div>
      @else
        <table id="datatable" class="table table-hover table-striped">
          <thead>
            <tr>
              <th>Rank</th>
              <th>Nama Dataset</th>
              <th>User</th>
              <th>Kriteria (Item)</th>
              <th>Nilai Prediksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($rows as $r)
              <tr>
                <td>{{ $r['rank'] }}</td>
                <td>{{ $r['dataset'] }}</td>
                <td>{{ $r['user'] }}</td>
                <td>{{ $r['item'] }}</td>
                <td>{{ number_format($r['pred'], 3) }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      @endif
    </div>
  </div>
</section>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
  $(function() {
    var $t = $('#datatable');
    if ($t.length) {
      $t.DataTable({ paging:false, searching:false, info:false });
    }
  });
</script>
@endsection
