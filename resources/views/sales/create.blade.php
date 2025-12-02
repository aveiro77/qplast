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
                <h3>Create New Sale</h3>
                <p class="text-subtitle text-muted">Create a manual sale with line items and adjustable prices.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('sales.index') }}">Sales</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Create</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-body">

                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('sales.store') }}" x-data
                      x-ref="saleForm" @submit.prevent="submitForm">
                    @csrf

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Customer</label>
                            <select name="customer_id" class="form-select" required>
                                <option value="">-- Select Customer --</option>
                                @foreach($customers as $c)
                                    <option value="{{ $c->id }}" {{ old('customer_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Sale Type</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', 'B2B') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Note (optional)</label>
                            <input type="text" name="note" class="form-control" value="{{ old('note') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Payment Method</label>
                            <select name="payment_method" class="form-select" required>
                                <option value="Cash">Cash</option>
                                <option value="Transfer">Transfer</option>
                            </select>
                        </div>
                    </div>

                    <div x-data="itemsManager()" @submit.prevent="submitForm" class="card-section">
                        <input type="hidden" name="items" x-model="itemsJson">

                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th style="width:40%">Product</th>
                                        <th style="width:10%">Unit</th>
                                        <th style="width:15%">Qty</th>
                                        <th style="width:15%">Price</th>
                                        <th style="width:15%">Subtotal</th>
                                        <th style="width:5%"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(row, idx) in items" :key="idx">
                                        <tr>
                                            <td>
                                                <select class="form-select" x-model="row.product_id" @change="onProductChange(idx)" required>
                                                    <option value="">-- choose product --</option>
                                                    <template x-for="p in products" :key="p.id">
                                                        <option :value="p.id" x-text="p.name" :selected="row.product_id == p.id"></option>
                                                    </template>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" x-model="row.unit" readonly>
                                            </td>
                                            <td>
                                                <input type="number" min="1" step="1" class="form-control" x-model.number="row.quantity" @input="recalcRow(idx)">
                                            </td>
                                            <td>
                                                <input type="number" min="0" step="0.01" class="form-control" x-model.number="row.price" @input="recalcRow(idx)">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" :value="formatCurrency(row.subtotal)" readonly>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-danger" @click="removeRow(idx)">×</button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="6">
                                            <button type="button" class="btn btn-sm btn-secondary" @click="addRow">Add Item</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="text-end"><strong>Total:</strong></td>
                                        <td><strong x-text="formatCurrency(total)"></strong></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('sales.index') }}" class="btn btn-secondary me-2">Cancel</a>
                            <button type="button" class="btn btn-primary" @click="submitForm">Save Sale</button>
                        </div>

                    </div>
                </form>

            </div>
        </div>
    </section>
</div>

<script>
window.itemsManager = function() {
    const products = @json($products);
    const oldItems = @json(old('items') ? json_decode(old('items'), true) : null);
    
    return {
        products: products || [],
        items: (oldItems && Array.isArray(oldItems) && oldItems.length > 0) ? oldItems : [ { product_id: null, unit: '', quantity: 1, price: 0, subtotal: 0 } ],
        itemsJson: '',
        get total() {
            return this.items.reduce((s, r) => s + (Number(r.subtotal) || 0), 0);
        },
        formatCurrency(value) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value);
        },
        addRow() {
            this.items.push({ product_id: null, unit: '', quantity: 1, price: 0, subtotal: 0 });
        },
        removeRow(idx) {
            if (this.items.length > 1) {
                this.items.splice(idx, 1);
            } else {
                alert('Must have at least one item');
            }
        },
        onProductChange(idx) {
            const id = this.items[idx].product_id;
            const p = this.products.find(x => x.id == id);
            if (p) {
                this.items[idx].unit = p.unit ?? '';
                this.items[idx].price = Number(p.hrg_ecer ?? p.hrg_grosir ?? p.hrg_ball ?? 0) || 0;
            } else {
                this.items[idx].unit = '';
                this.items[idx].price = 0;
            }
            this.recalcRow(idx);
        },
        recalcRow(idx) {
            const r = this.items[idx];
            r.subtotal = (Number(r.quantity) || 0) * (Number(r.price) || 0);
        },
        prepareSubmit() {
            const filtered = this.items.filter(r => r.product_id && Number(r.quantity) > 0);
            this.itemsJson = JSON.stringify(filtered);
        },
        submitForm() {
            this.prepareSubmit();
            if (!this.itemsJson || this.itemsJson === '[]') {
                alert('Please add at least one item.');
                return;
            }
            document.querySelector('input[name="items"]').value = this.itemsJson;
            document.querySelector('form').submit();
        }
    };
};
</script>

@endsection
