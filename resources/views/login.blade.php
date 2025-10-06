<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="description" content="">
    <meta name="author" content="">

    <title>Oyen PetShop</title>

    <!-- CSS FILES -->
    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;600;700&family=Open+Sans&display=swap"
        rel="stylesheet">

    <link href="css/bootstrap.min.css" rel="stylesheet">

    <link href="css/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">

    <link href="css/templatemo-topic-listing.css" rel="stylesheet">

</head>

<body id="top">
  <main>
    <nav class="navbar navbar-expand-lg">
      <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="/">
          <i class="bi bi-leaf"></i>
          <span>TemulawakSmart</span>
        </a>

        <div class="d-lg-none ms-auto me-4">
          <a href="#top" class="navbar-icon bi-person smoothscroll" data-bs-toggle="modal" data-bs-target="#loginModal"></a>
        </div>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
          aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Modal Login (dipercantik) -->
        <div class="modal fade" id="loginModal" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <form method="POST" action="/" class="modal-content needs-validation" novalidate>
              @csrf
              <div class="modal-header bg-success text-white">
                <div class="d-flex align-items-center gap-2">
                  <i class="bi bi-shield-lock"></i>
                  <h5 class="modal-title mb-0">Login Admin</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>

              <div class="modal-body">
                @if ($errors->has('login'))
                  <div class="alert alert-danger">{{ $errors->first('login') }}</div>
                @endif

                <p class="text-muted small mb-3">
                  Masuk untuk mengelola dataset, kriteria, dan melihat hasil perhitungan kemiripan & prediksi.
                  <br><span class="badge bg-light text-dark">Contoh kredensial • user: <b>admin</b> • pass: <b>123</b></span>
                </p>

                <div class="mb-3">
                  <label class="form-label">Username</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input type="text" name="username" class="form-control" placeholder="admin" required>
                    <div class="invalid-feedback">Username wajib diisi.</div>
                  </div>
                </div>

                <div class="mb-3">
                  <label class="form-label">Password</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-key"></i></span>
                    <input type="password" name="password" class="form-control" placeholder="••••••" required>
                    <button class="btn btn-outline-secondary" type="button" onclick="togglePw(this)">
                      <i class="bi bi-eye"></i>
                    </button>
                    <div class="invalid-feedback">Password wajib diisi.</div>
                  </div>
                </div>

                <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="rememberMe">
                  <label class="form-check-label" for="rememberMe">Ingat saya</label>
                </div>
              </div>

              <div class="modal-footer d-flex justify-content-between">
                <small class="text-muted">Akses hanya untuk Admin.</small>
                <button class="btn btn-success"><i class="bi bi-box-arrow-in-right me-1"></i> Login</button>
              </div>
            </form>
          </div>
        </div>
        <!-- /Modal -->

        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav ms-lg-5 me-lg-auto">
            <li class="nav-item">
              <a class="nav-link click-scroll" href="#section_1">Beranda</a>
            </li>
            <li class="nav-item">
              <a class="nav-link click-scroll" href="#section_2">Tentang Temulawak</a>
            </li>
            <li class="nav-item">
              <a class="nav-link click-scroll" href="#section_3">Cara Kerja</a>
            </li>
            <li class="nav-item">
              <a class="nav-link click-scroll" href="#section_4">Hasil Perhitungan</a>
            </li>
            <li class="nav-item">
              <a class="nav-link click-scroll" href="#section_5">Kontak</a>
            </li>
          </ul>

          <div class="d-none d-lg-block">
            <a href="#top" data-bs-toggle="modal" data-bs-target="#loginModal"
               class="navbar-icon bi-person smoothscroll"></a>
          </div>
        </div>
      </div>
    </nav>

    <!-- Hero -->
    <section class="hero-section d-flex justify-content-center align-items-center" id="section_1"
      style="background: linear-gradient(120deg,#0f5132,#198754);">
      <div class="container">
        <div class="row">
          <div class="col-lg-10 col-12 mx-auto text-center">
            <h1 class="text-white">Sistem Seleksi Bibit Temulawak Terbaik</h1>
            <h6 class="text-white mt-3">
              Item-Based Collaborative Filtering untuk rekomendasi bibit berdasarkan kriteria C1–C6
            </h6>

            <a href="#" data-bs-toggle="modal" data-bs-target="#loginModal" class="btn btn-light btn-lg mt-4">
              Masuk Dashboard
            </a>
          </div>
        </div>
      </div>
    </section>

    <!-- Fitur Utama -->
    <section class="featured-section" id="section_2">
      <div class="container">
        <div class="row justify-content-center g-4">
          <div class="col-lg-4 col-12">
            <div class="custom-block bg-white shadow-lg h-100">
              <div class="d-flex">
                <div>
                  <h5 class="mb-2">Manajemen Dataset Bibit</h5>
                  <p class="mb-0">
                    Input satu <b>Dataset</b> berisi 6 bibit (A1–A6) dengan kriteria C1–C6:
                    Ukuran, Warna, Kondisi Kulit, Usia, Kesehatan, Produktivitas.
                  </p>
                </div>
                <span class="badge bg-success rounded-pill ms-auto">✓</span>
              </div>
              <img src="images/topics/undraw_Educator_re_ju47.png" class="custom-block-image img-fluid" alt="Input Bibit Temulawak">
            </div>
          </div>

          <div class="col-lg-4 col-12">
            <div class="custom-block bg-white shadow-lg h-100">
              <div class="d-flex">
                <div>
                  <h5 class="mb-2">Kemiripan (Cosine)</h5>
                  <p class="mb-0">
                    Sistem menghitung <b>cosine similarity</b> antar kriteria (C1–C6) untuk setiap dataset, meniru hitungan manual (Tabel 4.13).
                  </p>
                </div>
                <span class="badge bg-primary rounded-pill ms-auto">✓</span>
              </div>
              <img src="images/topics/colleagues-working-cozy-office-medium-shot.png"
                   class="custom-block-image img-fluid" alt="Cosine Similarity">
            </div>
          </div>

          <div class="col-lg-4 col-12">
            <div class="custom-block bg-white shadow-lg h-100">
              <div class="d-flex">
                <div>
                  <h5 class="mb-2">Prediksi & Ranking</h5>
                  <p class="mb-0">
                    Prediksi rating item kosong & rangking bibit terbaik dengan formula berbasis kemiripan sesuai naskah perhitungan.
                  </p>
                </div>
                <span class="badge bg-info rounded-pill ms-auto">✓</span>
              </div>
              <img src="images/topics/undraw_Finance_re_gnv2.png" class="custom-block-image img-fluid" alt="Prediksi & Ranking">
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Timeline/Cara Kerja -->
    <section class="timeline-section section-padding mt-5" id="section_3">
      <div class="section-overlay"></div>
      <div class="container">
        <div class="row">
          <div class="col-12 text-center">
            <h2 class="text-white mb-4">Bagaimana Sistem Ini Bekerja?</h2>
          </div>

          <div class="col-lg-10 col-12 mx-auto">
            <div class="timeline-container">
              <ul class="vertical-scrollable-timeline" id="vertical-scrollable-timeline">
                <div class="list-progress"><div class="inner"></div></div>

                <li>
                  <h4 class="text-white mb-3">1. Input Dataset (6 Bibit)</h4>
                  <p class="text-white">
                    Admin mengisi Dataset (A1–A6) dengan bobot subkriteria C1–C6. Nilai 0 menandakan belum diisi (akan diprediksi).
                  </p>
                  <div class="icon-holder"><i class="bi bi-pencil-square"></i></div>
                </li>

                <li>
                  <h4 class="text-white mb-3">2. Hitung Kemiripan</h4>
                  <p class="text-white">
                    Sistem menghitung kemiripan antar kriteria menggunakan <em>cosine similarity</em> sesuai tabel acuan manual.
                  </p>
                  <div class="icon-holder"><i class="bi bi-gear-fill"></i></div>
                </li>

                <li>
                  <h4 class="text-white mb-3">3. Prediksi Rating Item Kosong</h4>
                  <p class="text-white">
                    Nilai pada kriteria yang kosong diprediksi menggunakan bobot kemiripan & rating yang tersedia.
                  </p>
                  <div class="icon-holder"><i class="bi bi-bar-chart-line-fill"></i></div>
                </li>

                <li>
                  <h4 class="text-white mb-3">4. Perangkingan Bibit</h4>
                  <p class="text-white">
                    Hasil prediksi dirangking untuk rekomendasi bibit temulawak terbaik pada dataset terkait.
                  </p>
                  <div class="icon-holder"><i class="bi bi-file-earmark-bar-graph"></i></div>
                </li>
              </ul>
            </div>
          </div>

          <div class="col-12 text-center mt-5">
            <p class="text-white">
              Siap mengelola dataset Temulawak?
              <a href="#" class="btn custom-btn custom-border-btn ms-3" data-bs-toggle="modal" data-bs-target="#loginModal">Masuk Admin</a>
            </p>
          </div>

        </div>
      </div>
    </section>
  </main>

  <footer class="site-footer section-padding" id="section_5">
    <div class="container">
      <div class="row">
        <!-- Brand & Deskripsi -->
        <div class="col-lg-3 col-12 mb-4 pb-2">
          <a class="navbar-brand mb-2 d-flex align-items-center gap-2" href="/">
            <i class="bi bi-leaf"></i>
            <span>TemulawakSmart</span>
          </a>
          <p class="text-white">
            Sistem seleksi bibit temulawak berbasis Item-Based Collaborative Filtering. Membantu rekomendasi bibit terbaik dengan perhitungan akurat & visual.
          </p>
        </div>

        <!-- Navigasi -->
        <div class="col-lg-3 col-md-4 col-6">
          <h6 class="site-footer-title mb-3">Menu</h6>
          <ul class="site-footer-links">
            <li class="site-footer-link-item"><a href="/" class="site-footer-link">Beranda</a></li>
            <li class="site-footer-link-item"><a href="/admin/data-pengecekan" class="site-footer-link">Data Bibit (CRUD)</a></li>
            <li class="site-footer-link-item"><a href="/admin/hasil-prediksi" class="site-footer-link">Matriks Kemiripan</a></li>
            <li class="site-footer-link-item"><a href="/admin/prediksi/rangking" class="site-footer-link">Ranking Prediksi</a></li>
          </ul>
        </div>

        <!-- Kontak -->
        <div class="col-lg-3 col-md-4 col-6 mb-4 mb-lg-0">
          <h6 class="site-footer-title mb-3">Kontak</h6>
          <p class="text-white d-flex mb-1">
            <a href="tel:081234567890" class="site-footer-link">0812-3456-7890</a>
          </p>
          <p class="text-white d-flex">
            <a href="mailto:admin@temulawaksmart.id" class="site-footer-link">admin@temulawaksmart.id</a>
          </p>
        </div>

        <!-- Bahasa & Hak Cipta -->
        <div class="col-lg-3 col-md-4 col-12 mt-4 mt-lg-0 ms-auto">
          <div class="dropdown">
            <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
              Bahasa Indonesia
            </button>
            <ul class="dropdown-menu">
              <li><button class="dropdown-item" type="button">English</button></li>
              <li><button class="dropdown-item" type="button">Melayu</button></li>
            </ul>
          </div>

          <p class="copyright-text mt-lg-5 mt-4">
            © 2025 TemulawakSmart.<br>
            All rights reserved.<br><br>
          </p>
        </div>
      </div>
    </div>
  </footer>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
    integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.min.js"
    integrity="sha384-7qAoOXltbVP82dhxHAUje59V5r2YsVfBafyUDxEdApLPmcdhBPg1DKg1ERo0BZlK" crossorigin="anonymous"></script>

  <!-- Optional extras -->
  <script>
    // Bootstrap client-side validation
    (() => {
      const forms = document.querySelectorAll('.needs-validation');
      Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
          if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
          }
          form.classList.add('was-validated');
        }, false);
      });
    })();

    // Toggle password visibility
    function togglePw(btn) {
      const input = btn.parentElement.querySelector('input[type="password"], input[type="text"]');
      const icon  = btn.querySelector('i');
      if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
      } else {
        input.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
      }
    }
  </script>

  <!-- Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</body>

</html>
