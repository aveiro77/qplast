@extends('layouts.dashboard')

@section('content')

<div x-data='posApp(@json($products))' class="p-6 max-w-4xl mx-auto bg-white rounded shadow">

    <h2 class="text-xl font-semibold mb-4">Form Kasir (POS)</h2>

    {{-- PILIH CUSTOMER --}}
    <label class="block mb-3">
        <span class="font-semibold">Customer</span>
        <select class="w-full border p-2" x-model="customer_id">
            @foreach ($customers as $c)
            <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->type }})</option>
            @endforeach
        </select>
    </label>

    {{-- TABEL INPUT ITEM --}}
    <table class="w-full border mt-4">
        <thead class="bg-gray-100">
            <tr>
                <th class="border p-2">Produk</th>
                <th class="border p-2 w-24">Qty</th>
                <th class="border p-2">Unit</th>
                <th class="border p-2">Harga</th>
                <th class="border p-2">Subtotal</th>
                <th class="border p-2">#</th>
            </tr>
        </thead>

        <tbody>
            <template x-for="(item, index) in cart" :key="index">
                <tr>
                    <td class="border p-2">
                        <select class="w-full border p-1" 
                                x-model="item.product_id"
                                @change="updateItem(index)">
                            <option value="">-- pilih --</option>
                            <template x-for="p in products">
                                <option :value="p.id" x-text="p.name"></option>
                            </template>
                        </select>
                    </td>

                    <td class="border p-2">
                        <input type="number" min="1" class="w-full border p-1"
                               x-model.number="item.qty"
                               @input="updateItem(index)">
                    </td>

                    <td class="border p-2" x-text="item.unit"></td>

                    <td class="border p-2" x-text="formatRupiah(item.harga)"></td>

                    <td class="border p-2" x-text="formatRupiah(item.subtotal)"></td>

                    <td class="border p-2 text-center">
                        <button class="text-red-500" @click="cart.splice(index,1)">X</button>
                    </td>
                </tr>
            </template>
        </tbody>
    </table>

    <button class="mt-3 bg-blue-500 text-white px-3 py-2 rounded"
            @click="addItem">+ Tambah Item</button>

    {{-- TOTAL --}}
    <div class="mt-5 text-right text-xl font-bold">
        Total: <span x-text="formatRupiah(grandTotal)"></span>
    </div>

    <form method="POST" action="{{ route('pos.store') }}"
      @submit.prevent="submitForm">
    @csrf
    <input type="hidden" name="customer_id" x-model="customer_id">
    <input type="hidden" name="cart" x-model="cartJson">

    <button class="mt-4 bg-green-600 text-white px-4 py-2 rounded">
        Simpan Transaksi
    </button>
</form>


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
        }
    }
}

</script>

@endsection