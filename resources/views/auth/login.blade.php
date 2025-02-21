@extends('layouts.app')

@section('title', 'Login Presensi')

@section('content')
<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="row w-100">
        <div class="col-md-4 offset-md-4">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white text-center">
                    <h4>Login Presensi</h4>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form action="{{ route('login.post') }}" method="POST">
                        @csrf
                        
                        <!-- NIK -->
                        <div class="mb-3">
                            <label for="nik" class="form-label">NIK</label>
                            <input type="text" id="nik" name="nik" class="form-control @error('nik') is-invalid @enderror" 
                                   value="{{ old('nik') }}" required autofocus>
                            @error('nik')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- PIN -->
                        <div class="mb-3">
                            <label for="pin" class="form-label">PIN</label>
                            <input type="password" id="pin" name="pin" class="form-control @error('pin') is-invalid @enderror" required>
                            @error('pin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit -->
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Login</button>
                        </div>
                    </form>
                </div>

                <div class="card-footer text-center">
                    <small>&copy; {{ date('Y') }} Aplikasi Presensi</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection