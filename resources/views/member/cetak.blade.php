@extends('Layouts.Admin')
@section('header', 'Kartu Member')
@section('content')

<div class="container">
    <div class="row">
        @foreach ($kartuMembers as $kartuMember)
        <div class="col-md-4 mb-4">
            <div class="card" style="position: relative; width: 100%; height: 200px;">
                <!-- Gambar Background -->
                <img src="{{ asset('template-kartu-nama-44.png') }}" class="card-img" alt="Background Image" style="position: absolute; top: 0; left: 0; z-index: 1; width: 100%; height: 100%; object-fit: cover;">
                <h5 style="position: absolute; top: 85px; left: 20px; z-index: 2; color: white;">Member Card</h5>
                <!-- Konten Kartu -->
                <div class="card-img-overlay" style="z-index: 2; text-align: right; padding: 10px;">
                    <h5 class="card-text mb-6">{{ $kartuMember['member']->name }}</h5>
                    <p class="card-text">Member ID: {{ $kartuMember['member']->member_code }}</p>
                    <!-- QR Code -->
                    <div class="qrcode" style="position: absolute; bottom: 10px; right: 10px;">
                        {!! $kartuMember['qrcode'] !!}
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

@endsection
