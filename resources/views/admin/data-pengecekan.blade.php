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
                    <span class="text">Data Pengecekan Bibit Temulawak</span>
                </div>

                @if (session('success'))
                    <div class="alert alert-success mt-2">{{ session('success') }}</div>
                @endif

                <div class="row justify-content-end mb-3">
                    <div class="col-lg-3 text-end">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                            <i class="uil uil-plus"></i> Tambah Data
                        </button>
                    </div>
                </div>

                <table id="datatable" class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Dataset</th>
                            <th>User</th>
                            <th>C1</th>
                            <th>C2</th>
                            <th>C3</th>
                            <th>C4</th>
                            <th>C5</th>
                            <th>C6</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pengecekan as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->dataset_name ?? '-' }}</td>
                                <td>{{ $item->nama_pembeli }}</td> {{-- sekarang ditampilkan sebagai "User" --}}
                                <td>{{ $item->c1 }}</td>
                                <td>{{ $item->c2 }}</td>
                                <td>{{ $item->c3 }}</td>
                                <td>{{ $item->c4 }}</td>
                                <td>{{ $item->c5 }}</td>
                                <td>{{ $item->c6 }}</td>
                                <td class="d-flex gap-2">
                                    <!-- Tombol Edit -->
                                    <button class="btn btn-link text-primary p-0 m-0" data-bs-toggle="modal"
                                        data-bs-target="#modalEdit{{ $item->id }}">
                                        <i class="uil uil-edit"></i>
                                    </button>

                                    <!-- Tombol Hapus -->
                                    <form action="{{ route('pengecekan.destroy', $item->id) }}" method="POST"
                                        onsubmit="return confirm('Yakin hapus?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-link text-danger p-0 m-0">
                                            <i class="uil uil-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center">Belum ada data pengecekan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>
        </div>
    </section>

    @php
        // helper untuk render select dari master kriteria
        $byKode = $kriterias->keyBy('kode');
        $renderSelect = function ($kode, $name, $selected = 0) use ($byKode) {
            $k = $byKode[$kode] ?? null;
            if (!$k) {
                return '';
            }
            $html = '<label class="form-label">' . $k->kode . ' - ' . $k->nama . '</label>';
            $html .= '<select name="' . $name . '" class="form-select">';
            $html .=
                '<option value="0"' . ((int) $selected === 0 ? ' selected' : '') . '>Tidak Diisi / Kosong (0)</option>';
            foreach ($k->subkriterias as $s) {
                $sel = (int) $selected === (int) $s->bobot ? ' selected' : '';
                $html .=
                    '<option value="' .
                    $s->bobot .
                    '"' .
                    $sel .
                    '>' .
                    $s->label .
                    ' — Bobot ' .
                    $s->bobot .
                    '</option>';
            }
            $html .= '</select>';
            return $html;
        };
    @endphp

    {{-- Modal Edit (per item) --}}
    @foreach ($pengecekan as $item)
        <div class="modal fade" id="modalEdit{{ $item->id }}" tabindex="-1"
            aria-labelledby="modalEditLabel{{ $item->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <form action="{{ route('pengecekan.update', $item->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Data Pengecekan</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label>Nama Pembeli</label>
                                <input type="text" name="nama_pembeli" class="form-control"
                                    value="{{ $item->nama_pembeli }}" required>
                            </div>

                            <div class="row g-3">
                                <div class="col-12 col-md-6">{!! $renderSelect('C1', 'c1', $item->c1) !!}</div>
                                <div class="col-12 col-md-6">{!! $renderSelect('C2', 'c2', $item->c2) !!}</div>
                                <div class="col-12 col-md-6">{!! $renderSelect('C3', 'c3', $item->c3) !!}</div>
                                <div class="col-12 col-md-6">{!! $renderSelect('C4', 'c4', $item->c4) !!}</div>
                                <div class="col-12 col-md-6">{!! $renderSelect('C5', 'c5', $item->c5) !!}</div>
                                <div class="col-12 col-md-6">{!! $renderSelect('C6', 'c6', $item->c6) !!}</div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary">Update</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endforeach


    <!-- Modal Tambah (Input 1 Dataset = 6 Bibit) -->
    <div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <form action="{{ route('pengecekan.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Dataset Bibit Temulawak (6 Bibit Sekaligus)</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <p class="mb-3">
                            Silahkan isi <strong>Nama Dataset</strong>, lalu masukkan <strong>6 Bibit</strong> sekaligus.
                            Nilai default sudah disesuaikan dengan perhitungan manual (A1..A6).
                        </p>

                        {{-- Nama Dataset --}}
                        <div class="mb-4">
                            <label class="form-label">Nama Dataset</label>
                            <input type="text" name="dataset_name" class="form-control" value="Dataset Pertama" required>
                        </div>

                        @php
                            // Default A1..A6 sesuai manual:
                            $defaults = [
                                ['nama' => 'A1', 'c1' => 5, 'c2' => 3, 'c3' => 5, 'c4' => 4, 'c5' => 0, 'c6' => 5],
                                ['nama' => 'A2', 'c1' => 3, 'c2' => 3, 'c3' => 0, 'c4' => 4, 'c5' => 4, 'c6' => 4], // ✅ perbaikan disini
                                ['nama' => 'A3', 'c1' => 2, 'c2' => 5, 'c3' => 3, 'c4' => 0, 'c5' => 2, 'c6' => 2],
                                ['nama' => 'A4', 'c1' => 0, 'c2' => 1, 'c3' => 2, 'c4' => 1, 'c5' => 3, 'c6' => 1],
                                ['nama' => 'A5', 'c1' => 5, 'c2' => 0, 'c3' => 2, 'c4' => 4, 'c5' => 3, 'c6' => 2],
                                ['nama' => 'A6', 'c1' => 1, 'c2' => 2, 'c3' => 1, 'c4' => 3, 'c5' => 2, 'c6' => 0],
                            ];

                            $byKode = $kriterias->keyBy('kode');
                            $renderSelectVal = function ($kode, $name, $selected) use ($byKode) {
                                $k = $byKode[$kode] ?? null;
                                if (!$k) {
                                    return '';
                                }
                                $html = '<select name="' . $name . '" class="form-select" required>';
                                $html .=
                                    '<option value="0"' .
                                    ((int) $selected === 0 ? ' selected' : '') .
                                    '>Tidak Diisi / Kosong (0)</option>';
                                foreach ($k->subkriterias as $s) {
                                    $sel = (int) $selected === (int) $s->bobot ? ' selected' : '';
                                    $html .=
                                        '<option value="' .
                                        $s->bobot .
                                        '"' .
                                        $sel .
                                        '>' .
                                        $s->label .
                                        ' — Bobot ' .
                                        $s->bobot .
                                        '</option>';
                                }
                                $html .= '</select>';
                                return $html;
                            };
                        @endphp

                        <div class="row g-3">
                            @foreach ($defaults as $idx => $d)
                                <div class="col-12">
                                    <div class="card border-1">
                                        <div class="card-body">
                                            <h6 class="mb-3">Nama Bibit {{ $idx + 1 }}</h6>
                                            <div class="row g-3 align-items-end">
                                                <div class="col-md-3">
                                                    <label class="form-label">Nama Bibit {{ $idx + 1 }}</label>
                                                    <input type="text" name="items[{{ $idx }}][nama]"
                                                        class="form-control" value="{{ $d['nama'] }}" required>
                                                </div>

                                                <div class="col-md-1">
                                                    <label class="form-label">C1</label>
                                                    {!! $renderSelectVal('C1', "items[$idx][c1]", $d['c1']) !!}
                                                </div>
                                                <div class="col-md-1">
                                                    <label class="form-label">C2</label>
                                                    {!! $renderSelectVal('C2', "items[$idx][c2]", $d['c2']) !!}
                                                </div>
                                                <div class="col-md-1">
                                                    <label class="form-label">C3</label>
                                                    {!! $renderSelectVal('C3', "items[$idx][c3]", $d['c3']) !!}
                                                </div>
                                                <div class="col-md-1">
                                                    <label class="form-label">C4</label>
                                                    {!! $renderSelectVal('C4', "items[$idx][c4]", $d['c4']) !!}
                                                </div>
                                                <div class="col-md-1">
                                                    <label class="form-label">C5</label>
                                                    {!! $renderSelectVal('C5', "items[$idx][c5]", $d['c5']) !!}
                                                </div>
                                                <div class="col-md-1">
                                                    <label class="form-label">C6</label>
                                                    {!! $renderSelectVal('C6', "items[$idx][c6]", $d['c6']) !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                    </div> <!-- /modal-body -->

                    <div class="modal-footer">
                        <button class="btn btn-primary">Simpan 6 Bibit</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    </div>

                </div>
            </form>
        </div>
    </div>



    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script>
        $(function() {
            $('#datatable').DataTable();
        });
    </script>
@endsection
