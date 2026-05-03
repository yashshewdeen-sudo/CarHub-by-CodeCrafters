@extends('layouts.app')
@section('title', 'Login')
@section('content')
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-body">
                    <h3>Login</h3>
                    @if ($errors->any())
                        <div class="alert alert-danger">{{ $errors->first() }}</div>
                    @endif
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="mb-3"><label class="form-label">Email</label>
                            <input name="email" type="email" class="form-control" value="{{ old('email') }}" required></div>
                        <div class="mb-3"><label class="form-label">Password</label>
                            <input name="password" type="password" class="form-control" required></div>
                        <button class="btn btn-primary w-100">Login</button>
                    </form>
                    <div class="text-center mt-3">
                        <a href="{{ route('register') }}">No account? Register</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
