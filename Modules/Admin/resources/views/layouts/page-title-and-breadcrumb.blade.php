<div class="row">
    <div class="col-12 d-sm-flex align-items-center justify-content-between">
        <div class="page-title-box">
            <h4 class="font-size-18">
                @yield('title')
            </h4>
            @if (Route::currentRouteName() !== 'dashboard.index')
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard.index') }}">
                                Dashboard
                            </a>
                        </li>
                        @yield('breadcrumb')
                        <li class="breadcrumb-item active">
                            @yield('title')
                        </li>
                    </ol>
                </div>
            @endif
        </div>
        <div class="d-flex flex-wrap align-items-center justify-content-end gap-2 mb-3">
            @yield('buttons')
        </div>
    </div>
</div>
