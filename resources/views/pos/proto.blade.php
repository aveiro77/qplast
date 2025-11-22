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
            <div class="col-md-6 col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Point of sale</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form class="form form-horizontal">
                                <div class="form-body">
                                    <div class="row">
                                            <div class="col-md-4">
                                            <label for="first-name-horizontal">Customer</label>
                                        </div>
                                        <div class="col-md-8 form-group">
                                            <fieldset class="form-group">
                                                <select class="form-select" x-model="customer_id">
                                                    @foreach ($customers as $c)
                                                        <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->type }})</option>
                                                    @endforeach
                                                </select>
                                            </fieldset>                        
                                        </div>
                                    </div>
                                </div>
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
                                                <td class="text-bold-500">
                                                    <select class="w-full border p-1" x-model="item.product_id" @change="updateItem(index)">
                                                        <option value="">-- pilih --</option>
                                                        <template x-for="p in products">
                                                            <option :value="p.id" x-text="p.name"></option>
                                                        </template>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="number" min="1" class="w-full border p-1" x-model.number="item.qty" @input="updateItem(index)">
                                                </td>
                                                <td x-text="item.unit"></td>
                                                <td x-text="formatRupiah(item.harga)">Remote</td>
                                                <td x-text="formatRupiah(item.subtotal)">Austin,Taxes</td>
                                                <td>
                                                    <button class="text-red-500" @click="cart.splice(index,1)">X</button>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <button class="mt-3 bg-blue-500 text-white px-3 py-2 rounded" @click="addItem">+ Tambah Item</button>
                    </div>
                </div>
            </div>
        </section>
    </div>

    {{-- TOTAL --}}
    <div class="mt-5 text-right text-xl font-bold">
        Total: <span x-text="formatRupiah(grandTotal)"></span>
    </div>

    <form method="POST" action="{{ route('pos.store') }}" @submit.prevent="submitForm">
        @csrf
        <input type="hidden" name="customer_id" x-model="customer_id">
        <input type="hidden" name="cart" x-model="cartJson">

        <button class="mt-4 bg-green-600 text-white px-4 py-2 rounded">
            Simpan Transaksi
        </button>
    </form>

    <section id="content-types">
        <div class="row">
            <div class="col-xl-4 col-md-6 col-sm-12">
                <div class="card">
                    <div class="card-content">
                        <div class="card-body">
                            <p class="card-text">
                                Introducing our beautifully designed cards, thoughtfully crafted to enhance your
                                browsing experience. These versatile elements are the perfect way to present
                                information, products, or services on our website.
                            </p>
                        </div>
                        <img class="img-fluid w-100" src="./assets/compiled/jpg/banana.jpg" alt="Card image cap">
                    </div>
                    <div class="card-footer d-flex justify-content-between">
                        <span>Card Footer</span>
                        <button class="btn btn-light-primary">Read More</button>
                    </div>
                </div>
            </div>
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

        addItem() {
            this.cart.push({
                product_id: "",
                qty: 1,
                unit: "",
                harga: 0,
                subtotal: 0
            });
        },

        updateItem(index) {
            let item = this.cart[index];
            let p = this.products.find(x => x.id == item.product_id);
            if (!p) return;

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