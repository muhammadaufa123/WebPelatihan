@extends('layout.template.mainTemplate')

@section('title', $title ?? 'Dashboard')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    .hover-lift {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .hover-lift:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }
</style>
@endpush

@section('container')
<div class="min-h-screen bg-gray-50 p-6">
    @yield('dashboard-content')
</div>
@endsection
