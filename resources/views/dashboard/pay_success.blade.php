@extends('layouts.main')

@section('content')
    <div class="content page-login page-reciept box-center">
        <div class="box reciept">
            <h3>Payment Success</h3>
            <div class="inner">
                <div class="form-group">
                    <label>Membership</label>
                    <h3>{{ ucfirst($membership) }}</h3>
                </div>
                @if($membership == \App\Models\User::MEMBERSHIP_BASIC)
                    <div class="form-group">
                        <label>Theme</label>
                        <h3>{{ $themeName }}</h3>
                    </div>
                @endif
                <div class="form-group">
                    <label>Period</label>
                    <h3>{{ $period }}</h3>
                </div>
                <div class="form-group">
                    <label>Account</label>
                    <h3>{{ $account }}</h3>
                </div>
                <div class="form-group ammout">
                    <label>Total</label>
                    <h3>${{ $total }}</h3>
                </div>
                <div class="form-group link">
                    <a class="button" href="{{ url('/dashboard') }}">Dashboard</a>
                </div>
            </div>
        </div>
    </div>
@endsection
