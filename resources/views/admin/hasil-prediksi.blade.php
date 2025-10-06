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
        <span class="text">Matriks Kemiripan (Cosine) – Anchor {{ $anchor ?? 'C1' }}</span>
      </div>

      @if (!empty($note))
        <div class="alert alert-warning mt-2">{{ $note }}</div>
      @endif

      <div class="row justify-content-end mb-3">
        <div class="col-lg-8 text-end">
          <form method="get" action="{{ route('hasil.prediksi') }}" class="d-inline-flex gap-2">
            <select name="dataset" class="form-select form-select-sm" onchange="this.form.submit()">
              @if (empty($useDataset))
                <option value="">(Semua / Terbaru)</option>
              @endif
              @foreach ($datasets ?? [] as $ds)
                <option value="{{ $ds }}" {{ $useDataset === $ds ? 'selected' : '' }}>{{ $ds }}</option>
              @endforeach
            </select>
            {{-- <select name="anchor" class="form-select form-select-sm" onchange="this.form.submit()">
              @foreach ($header as $code)
                <option value="{{ $code }}" {{ ($anchor ?? 'C1') === $code ? 'selected' : '' }}>{{ $code }}</option>
              @endforeach
            </select> --}}
          </form>
        </div>
      </div>

      @if (empty($tableRows))
        <div class="text-muted">Belum ada data untuk dihitung.</div>
      @else
        <table id="datatable" class="table table-hover table-striped">
          <thead>
            <tr>
              <th>Nama Dataset</th>
              <th>User</th>
              @foreach ($header as $h)
                <th>{{ $h }}</th>
              @endforeach
            </tr>
          </thead>
          <tbody>
            @foreach ($tableRows as $tr)
              <tr>
                <td>{{ $tr['dataset'] }}</td>
                <td><strong>{{ $tr['label'] }}</strong></td>
                @foreach ($header as $h)
                  <td>{{ number_format($tr[$h] ?? 0, 3) }}</td>
                @endforeach
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
