@extends('layouts.dashboard')

@section('content')

<div x-data='posApp(@json($products))'>

    <header class="mb-1">
        <a href="#" class="burger-btn d-block d-xl-none">
            <i class="bi bi-justify fs-3"></i>
        </a>
    </header>

    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <!-- <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>POS</h3>
                </div> -->
            </div>
        </div>
        
        <section class="section">
            <div class="row">
                <div class="col-md-6 col-12">
                    <div class="card">
                        {{-- <div class="card-header">
                            <h4 class="card-title">Point of sale</h4>
                        </div> --}}
                        <div class="card-content">
                            <div class="card-body">
                                {{-- <form class="form form-horizontal"> --}}
                                    {{-- <div class="form-body"> --}}
                                        <h6>Customer</h6>
                                        <fieldset class="form-group">
                                            <select class="form-select" x-model="customer_id">
                                                @foreach ($customers as $c)
                                                    <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->type }})</option>
                                                @endforeach
                                            </select>
                                        </fieldset>           
                                    {{-- </div> --}}
                                {{-- </form> --}}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">
                                Total: <span x-text="formatRupiah(grandTotal)"></span>
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label class="form-label">Pembayaran</label>
                                <input type="number" class="form-control" x-model.number="pembayaran" @input="updateKembalian" placeholder="0">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Kembalian: <span x-text="formatRupiah(kembalian)"></span></label>
                                <div class="alert" :class="kembalian < 0 ? 'alert-danger' : 'alert-success'" role="alert" x-show="pembayaran > 0">
                                    <span x-show="kembalian < 0">Pembayaran kurang sebesar <strong x-text="formatRupiah(Math.abs(kembalian))"></strong></span>
                                    <span x-show="kembalian >= 0">Kembalian: <strong x-text="formatRupiah(kembalian)"></strong></span>
                                </div>
                            </div>

                            <form method="POST" action="{{ route('pos.store') }}" @submit.prevent="submitForm">
                                @csrf
                                <input type="hidden" name="customer_id" x-model="customer_id">
                                <input type="hidden" name="cart" x-model="cartJson">

                                <button class="mt-1 btn btn-primary disabled:opacity-50 disabled:cursor-not-allowed" 
                                        :disabled="isLoading"
                                        type="button" @click="submitForm">
                                    <span x-show="!isLoading">Simpan Transaksi</span>
                                    <span x-show="isLoading" class="inline-flex items-center gap-2">
                                        <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                            Menyimpan...
                                    </span>
                                </button>
                            </form>
                            </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="section">
            <div class="row" id="table-inverse">
                <div class="col-12">
                    <div class="card">
                        <div class="card-content">
                            <!-- table with dark -->
                            <div class="table-responsive">
                                <table class="table table-dark">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Qty</th>
                                            <th>Unit</th>
                                            <th>Price</th>
                                            <th>Subtotal</th>
                                            <th>#</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(item, index) in cart" :key="index">
                                            <tr>
                                                {{-- <td class="text-bold-500">
                                                    <select class="w-full border p-1" x-model="item.product_id" @change="updateItem(index)">
                                                        <option value="">-- pilih --</option>
                                                        <template x-for="p in products">
                                                            <option :value="p.id" x-text="p.name"></option>
                                                        </template>
                                                    </select>
                                                </td> --}}
                                                <td x-text="item.name"></td>
                                                <td>
                                                    <input type="number" min="1" class="w-full border p-1" x-model.number="item.qty" @input="updateItem(index)">
                                                </td>
                                                <td align="rigt" x-text="item.unit"></td>
                                                <td align="rigt" x-text="formatRupiah(item.harga)">Remote</td>
                                                <td class="text-align-right" x-text="formatRupiah(item.subtotal)">Austin,Taxes</td>
                                                <td>
                                                    <button class="text-red-500" @click="cart.splice(index,1)">X</button>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        {{-- <button class="mt-3 bg-blue-500 text-white px-3 py-2 rounded" @click="addItem">+ Tambah Item</button> --}}
                    </div>
                </div>
            </div>
        </section>
    </div>

    <section id="filter-category" class="section">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <label class="form-label fw-bold">Filter Kategori</label>
                        <select class="form-select" x-model="selected_category_id">
                            <option value="0">ALL CATEGORY</option>
                            <template x-for="cat in categoriesData" :key="cat.id">
                                <option :value="cat.id" x-text="cat.name"></option>
                            </template>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="content-types">
        <div class="row mt-3">
            <template x-for="p in filteredProducts" :key="p.id">
                <div class="col-xl-3 col-md-4 col-sm-6 mb-2">
                    <!-- 
                        1. @click ditaruh di sini agar seluruh card bisa diklik.
                        2. cursor: pointer agar user tahu ini bisa diklik.
                        3. h-100 agar tinggi card rata.
                    -->
                    <div class="card h-60" 
                        @click="addProductToCart(p)" 
                        style="cursor: pointer; transition: transform 0.2s;"
                        onmouseover="this.style.transform='scale(1.02)'"
                        onmouseout="this.style.transform='scale(1)'">
                        
                        <!-- Area Gambar dengan tinggi tetap agar rapi -->
                        <div style="height: 200px; overflow: hidden;" class="bg-light w-100">
                            
                            <!-- OPSI 1: Tampil jika gambar ADA -->
                            <img
                                :src="p.image" 
                                class="card-img-top w-100 h-100" 
                                style="object-fit: cover;" 
                                :alt="p.name">
                        </div>

                        <div class="card-content d-flex flex-column flex-grow-1">
                            <div class="card-body">
                                <!-- Menggunakan x-text langsung pada h5 untuk judul -->
                                <h6 class="card-title" x-text="p.name"></h6>
                                <h6 class="card-title" x-text="'Satuan:' +' '+ p.unit"></h6>
                                <p class="card-text mt-3">
                                    <span class="d-block text-primary fw-bold" x-text="formatRupiah(p.unit === 'Ball' ? p.hrg_ball : p.hrg_ecer)"></span>
                                    <small class="text-muted" x-text="'Stok: ' + p.stock +' '+ p.unit"></small>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </section>

</div>

<script src="//unpkg.com/alpinejs" defer></script>
<script>
    function posApp(productsData) {
        return {
            products: productsData,
            categoriesData: @json($categories),
            customer_id: 1,
            cart: [],
            isLoading: false,
            pembayaran: 0,
            kembalian: 0,
            selected_category_id: 0,

            updateKembalian() {
                this.kembalian = this.pembayaran - this.grandTotal;
            },

            updateItem(index) {
                let item = this.cart[index];
                let p = this.products.find(x => x.id == item.product_id);
                if (!p) return;

                item.name = p.name;
                item.unit = p.unit;
                item.harga = p.hrg_ecer;
                item.subtotal = item.qty * item.harga;

                this.updateBallPrices();
            },

            updateBallPrices() {
                let totalBall = this.cart
                    .filter(i => i.unit === 'Ball')
                    .reduce((acc, i) => acc + (i.qty || 0), 0);

                this.cart.forEach(i => {
                    let p = this.products.find(x => x.id == i.product_id);
                    if (!p) return;

                    if (i.unit === 'Ball') {
                        i.harga = totalBall <= 10 ? p.hrg_ball : p.hrg_grosir;
                    } else {
                        i.harga = p.hrg_ecer;
                    }

                    i.subtotal = i.qty * i.harga;
                });
            },

            get grandTotal() {
                return this.cart.reduce((a, i) => a + i.subtotal, 0);
            },

            get cartJson() {
                return JSON.stringify(this.cart);
            },

            get filteredProducts() {
                if (this.selected_category_id === 0 || this.selected_category_id === '0') {
                    return this.products;
                }
                return this.products.filter(p => p.category_id == this.selected_category_id);
            },

            async submitForm() {
                if (!this.customer_id) {
                    this.showToast("Customer belum dipilih!", "error");
                    return;
                }
                if (this.cart.length === 0) {
                    this.showToast("Cart masih kosong!", "error");
                    return;
                }
                if (this.pembayaran === 0) {
                    this.showToast("Pembayaran belum diisi!", "error");
                    return;
                }
                if (this.pembayaran < this.grandTotal) {
                    this.showToast("Pembayaran kurang sebesar " + this.formatRupiah(this.grandTotal - this.pembayaran), "error");
                    return;
                }

                this.isLoading = true;

                try {
                    const formData = new FormData();
                    formData.append('_token', document.querySelector('input[name="_token"]').value);
                    formData.append('customer_id', this.customer_id);
                    formData.append('cart', JSON.stringify(this.cart));

                    const response = await fetch("{{ route('pos.store') }}", {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (data.success) {
                        this.showToast(data.message, "success");
                        // Buka halaman cetak struk di tab baru
                        if (data.receipt_url) {
                            window.open(data.receipt_url, '_blank');
                        }
                        // Reset form setelah sukses
                         this.cart = [];
                        this.customer_id = 1;
                        this.pembayaran = 0;
                        this.kembalian = 0;
                        this.selected_category_id = 0;
                    } else {
                        this.showToast(data.message || "Terjadi kesalahan", "error");
                    }
                } catch (error) {
                    console.error('Error:', error);
                    this.showToast("Terjadi kesalahan jaringan", "error");
                } finally {
                    this.isLoading = false;
                }
            },

            showToast(message, type = 'success') {
                const bgColor = type === 'success' ? '#22c55e' : '#ef4444';
                
                if (typeof Toastify !== 'undefined') {
                    Toastify({
                        text: message,
                        duration: 3000,
                        close: true,
                        gravity: "top",
                        position: "right",
                        backgroundColor: bgColor,
                        stopOnFocus: true
                    }).showToast();
                } else {
                    // Fallback jika Toastify belum loaded
                    alert(message);
                }
            },

            formatRupiah(value) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR'
                }).format(value);
            },

            addProductToCart(p) {
                // jika sudah ada di cart → tambah qty
                let existing = this.cart.find(i => i.product_id == p.id);

                if (existing) {
                    existing.qty++;
                    this.updateBallPrices();
                    return;
                }

                // jika produk baru → masukkan ke cart
                this.cart.push({
                    product_id: p.id,
                    name: p.name,
                    qty: 1,
                    unit: p.unit,
                    harga: p.hrg_ecer,
                    subtotal: p.hrg_ecer,
                });

                this.updateBallPrices();
            },

        }
    }
</script>
@endsection