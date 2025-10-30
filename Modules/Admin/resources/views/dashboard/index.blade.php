@extends('admin::layouts.app')
@section('title', 'Dashboard')

@push('css')
<style>
    .metric-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: linear-gradient(135deg, #4e73df, #224abe);
        color: #fff;
        padding: 20px;
        border-radius: 12px;
        transition: 0.3s ease-in-out;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    .metric-card:hover {
        transform: translateY(-5px);
    }

    .metric-icon i {
        font-size: 28px;
    }

    .metric-number {
        font-size: 28px;
        font-weight: 700;
    }

    .metric-label {
        font-size: 14px;
        text-transform: uppercase;
        opacity: 0.9;
    }

    .metric-card-link {
        text-decoration: none;
        color: inherit;
    }

    .section-title i {
        color: #4e73df;
    }
</style>
@endpush

@section('content')
<div class="dashboard-container">
    <!-- Dashboard Header -->
    <div class="dashboard-header mb-4">
        <h6 class="dashboard-title">Dashboard Overview</h6>
        <p class="dashboard-subtitle">Monitor your business metrics</p>
    </div>

    <!-- Main Metrics Section -->
    <div class="metrics-section mb-5">
        <h5 class="section-title">
            <i class="fas fa-chart-line me-2"></i>Business Metrics
        </h5>
        <div class="row g-4">

            <!-- Super Admin -->
            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="metric-card gradient-primary">
                    <div class="metric-icon">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <div class="metric-content">
                        <div class="metric-number">{{ $superAdminCount }}</div>
                        <div class="metric-label">Super Admins</div>
                    </div>
                </div>
            </div>

            <!-- Agent -->
            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="metric-card gradient-primary">
                    <div class="metric-icon">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <div class="metric-content">
                        <div class="metric-number">{{ $agentCount }}</div>
                        <div class="metric-label">Agents</div>
                    </div>
                </div>
            </div>

            <!-- Sub Agent -->
            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="metric-card gradient-primary">
                    <div class="metric-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="metric-content">
                        <div class="metric-number">{{ $subAgentCount }}</div>
                        <div class="metric-label">Sub Agents</div>
                    </div>
                </div>
            </div>

            <!-- Games -->
            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="metric-card gradient-primary">
                    <div class="metric-icon">
                        <i class="fas fa-gamepad"></i>
                    </div>
                    <div class="metric-content">
                        <div class="metric-number">{{ $gameCount }}</div>
                        <div class="metric-label">Games</div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    // optional: you can animate the numbers later if you like
</script>
@endpush
