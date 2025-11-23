@extends('layouts.dashboard')

@section('content')

<div x-data='posApp(@json($products))'>

    <header class="mb-3">
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
                        <div class="card-content">
                            <div class="card-body">
                                <form method="POST" action="{{ route('pos.store') }}" @submit.prevent="submitForm">
                                    @csrf
                                    <input type="hidden" name="customer_id" x-model="customer_id">
                                    <input type="hidden" name="cart" x-model="cartJson">

                                    <button class="mt-3 bg-green-600 text-white px-4 py-2 rounded">
                                        Simpan Transaksi
                                    </button>
                                </form>
                            </div>
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
                                <table class="table table-dark mb-0">
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

    <section id="content-types">
        <div class="row mt-3">
            <template x-for="p in products" :key="p.id">
                <div class="col-xl-4 col-md-6 col-sm-12 mb-4">
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
                                <h5 class="card-title" x-text="p.name"></h5>
                                
                                <p class="card-text mt-3">
                                    <span class="d-block text-primary fw-bold" x-text="formatRupiah(p.hrg_ecer)"></span>
                                    <small class="text-muted" x-text="'Stok: ' + p.stock"></small>
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
            customer_id: "",
            cart: [],

            // addItem() {
            //     this.cart.push({
            //         product_id: "",
            //         name: "",
            //         qty: 1,
            //         unit: "",
            //         harga: 0,
            //         subtotal: 0
            //     });
            // },

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

            submitForm() {
                if (!this.customer_id) {
                    alert("Customer belum dipilih!");
                    return;
                }
                if (this.cart.length === 0) {
                    alert("Cart masih kosong!");
                    return;
                }

                // submit form
                document.querySelector("form").submit();
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