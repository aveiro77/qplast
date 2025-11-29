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
                        <select class="w-full border p-1" x-model="item.product_id" @change="updateItem(index)">
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

    {{-- // tambahan --}}
    <h3 class="mt-6 mb-2 font-semibold text-lg">Pilih Produk</h3>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <template x-for="p in products" :key="p.id">
            <div class="border rounded shadow hover:shadow-lg cursor-pointer"
                @click="addProductToCart(p)">

                <img :src="p.image" class="w-full h-32 object-cover rounded-t">

                <div class="p-2 text-center">
                    <div class="font-semibold" x-text="p.name"></div>
                    <div class="text-sm text-gray-600" x-text="'Stok: ' + p.stock"></div>
                    <div class="mt-1 text-blue-600 font-bold" x-text="formatRupiah(p.unit === 'Ball' ? p.hrg_ball : p.hrg_ecer)""></div>
                </div>
            </div>
        </template>
    </div>

    {{-- // end tambahan --}}

    <button class="mt-3 bg-blue-500 text-white px-3 py-2 rounded" @click="addItem">+ Tambah Item</button>

    {{-- TOTAL --}}
    <div class="mt-5 text-right text-xl font-bold">
        Total: <span x-text="formatRupiah(grandTotal)"></span>
    </div>

    <form method="POST" action="{{ route('pos.store') }}" @submit.prevent="submitForm">
        @csrf
        <input type="hidden" name="customer_id" x-model="customer_id">
        <input type="hidden" name="cart" x-model="JSON.stringify(cart)">

        <button class="mt-4 bg-green-600 text-white px-4 py-2 rounded disabled:opacity-50 disabled:cursor-not-allowed" 
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

<script src="//unpkg.com/alpinejs" defer></script>
<script>
function posApp(productsData) {
    return {
        products: productsData,
        customer_id: 1,
        cart: [],
        isLoading: false,

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

        async submitForm() {
            if (!this.customer_id) {
                this.showToast("Customer belum dipilih!", "error");
                return;
            }
            if (this.cart.length === 0) {
                this.showToast("Cart masih kosong!", "error");
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
                    this.customer_id = "";
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
            const textColor = '#ffffff';
            
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