@extends('layouts.app')
@section('title', 'Register')
@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body">
                    <h3>Register</h3>
                    @if ($errors->any())
                        <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
                    @endif
                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        <div class="mb-3"><label class="form-label">Full Name</label>
                            <input name="name" class="form-control" value="{{ old('name') }}" required></div>
                        <div class="mb-3"><label class="form-label">Email</label>
                            <input name="email" type="email" class="form-control" value="{{ old('email') }}" required></div>
                        <div class="mb-3"><label class="form-label">Phone</label>
                            <input name="phone" class="form-control" value="{{ old('phone') }}" required></div>
                        <div class="mb-3"><label class="form-label">Password</label>
                            <input name="password" type="password" class="form-control" required minlength="8"></div>
                        <div class="mb-3"><label class="form-label">Confirm Password</label>
                            <input name="password_confirmation" type="password" class="form-control" required minlength="8"></div>
                        <div class="mb-3"><label class="form-label">Role</label>
                            <select name="role" class="form-select">
                                <option value="Buyer">Buyer</option>
                                <option value="Seller">Seller</option>
                            </select></div>
                        <button class="btn btn-success w-100">Create Account</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
