@extends('admin::layouts.app')
@section('title', 'Dashboard')
@section('content')
    <div class="d-flex align-items-center justify-content-center">
        <div class="card p-4 shadow-sm d-flex flex-column align-items-center justify-content-center responsive-card"
            style="width: 600px; height: 300px; border-radius: 10px;">
            <img src="@settings('website-dashboard-logo')" alt="@settings('website-name')" class="img-fluid mb-3 logo-img"
                style="max-width: 150px; height: auto;">
            <h5 class="fw-bold text-primary">Welcome to Dashboard</h5>
            <p class="text-muted">Seamlessly organize and control your content.</p>
        </div>
    </div>
@endsection
