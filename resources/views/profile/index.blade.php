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
                <h3>Account Settings</h3>
                <p class="text-subtitle text-muted">Pengaturan akun login aplikasi.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('affals.index') }}">Account</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Index</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">

                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <div class="row g-3">
                            <div class="col-12">
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h5 class="mb-0">Update Profile Information</h5>
                                    </div>
                                    <div class="card-body">
                                        @include('profile.partials.update-profile-information-form')
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h5 class="mb-0">Change Password</h5>
                                    </div>
                                    <div class="card-body">
                                        @include('profile.partials.update-password-form')
                                    </div>
                                </div>
                            </div>

                                    <!-- <div class="col-12">
                                        <div class="card mb-3 border-danger">
                                            <div class="card-header bg-danger text-white">
                                                <h5 class="mb-0">Delete Account</h5>
                                            </div>
                                            <div class="card-body">
                                                @include('profile.partials.delete-user-form')
                                            </div>
                                        </div>
                                    </div> -->
                        </div>

                    </div>
                </div>
            </div>

            
        </div>
    </section>
</div>

@endsection
