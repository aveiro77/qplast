@extends('layouts.dashboard')

@section('content')

<header class="mb-3">
    <a href="#" class="burger-btn d-block d-xl-none">
        <i class="bi bi-justify fs-3"></i>
    </a>
</header>

<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Products</h3>
                <p class="text-subtitle text-muted">Manage products data.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
                        <li class="breadcrumb-item">Products</li>
                        <li class="breadcrumb-item active" aria-current="page">Index</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    
    <section class="section">
        <div class="card">
            
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="card-body">

                <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label for="category_id" class="form-label">Category</label>
                        <select name="category_id" class="form-control" id="category_id" required>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
        
                    <div class="mb-3">
                        <label for="name" class="form-label">Product Name</label>
                        <input type="text" name="name" class="form-control" id="name" required>
                    </div>

                    <div class="mb-3">
                        <label for="unit" class="form-label">Unit</label>
                        <select name="unit" id="unit" class="form-control" required>
                            <option value="Ball">Ball</option>
                            <option value="Pcs">Pcs</option>
                        </select>
                    </div>


                    <div class="mb-3">
                        <label for="stock" class="form-label">Stock</label>
                        <input type="number" name="stock" class="form-control" id="stock" required>
                    </div>

                    <div class="mb-3">
                        <label for="hpp" class="form-label">HPP</label>
                        <input type="number" name="hpp" class="form-control" id="hpp" required>
                    </div>

                    <div class="mb-3">
                        <label for="hrg_grosir" class="form-label">Grosir</label>
                        <input type="number" name="hrg_grosir" class="form-control" id="hrg_grosir" required>
                    </div>

                    <div class="mb-3">
                        <label for="hrg_ball" class="form-label">Ball</label>
                        <input type="number" name="hrg_ball" class="form-control" id="hrg_ball" required>
                    </div>

                    <div class="mb-3">
                        <label for="hrg_ecer" class="form-label">Ecer</label>
                        <input type="number" name="hrg_ecer" class="form-control" id="hrg_ecer" required>
                    </div>
        
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label">Upload Image</label>
                        <input type="file" name="image" id="image" class="image-preview-filepond">
                    </div>
        
                    <button type="submit" class="btn btn-primary">Save</button>
                </form>
              
            </div>
        </div>
    </section>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Ambil elemen-elemen yang dibutuhkan
        const unitSelect = document.getElementById('unit');
        const hrgEcer = document.getElementById('hrg_ecer');
        const hrgBall = document.getElementById('hrg_ball');
        const hrgGrosir = document.getElementById('hrg_grosir');

        // 2. Fungsi untuk mengatur Read Only
        function togglePriceInputs() {
            const unit = unitSelect.value;

            if (unit === 'Ball') {
                // KONDISI: BALL
                // Harga Ecer -> Read Only & di-nol-kan
                hrgEcer.setAttribute('readonly', true);
                hrgEcer.value = 0; 
                // hrgEcer.style.backgroundColor = '#fd0505ff'; // Bikin abu-abu biar jelas

                // Harga Ball & Grosir -> Bisa diedit
                hrgBall.removeAttribute('readonly');
                hrgGrosir.removeAttribute('readonly');
            } else if (unit === 'Pcs') {
                // KONDISI: PCS
                // Harga Ecer -> Bisa diedit
                hrgEcer.removeAttribute('readonly');

                // Harga Ball & Grosir -> Read Only & di-nol-kan
                hrgBall.setAttribute('readonly', true);
                hrgBall.value = 0;
                hrgGrosir.setAttribute('readonly', true);
                hrgGrosir.value = 0;
            }
        }

        // 3. Pasang Event Listener (Agar jalan saat user ganti pilihan)
        unitSelect.addEventListener('change', togglePriceInputs);

        // 4. Panggil fungsi sekali saat halaman dimuat 
        // (berguna jika terjadi error validasi dan halaman reload, agar status input tetap benar)
        togglePriceInputs();
    });
</script>

@endsection