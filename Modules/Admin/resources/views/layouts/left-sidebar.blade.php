<div class="vertical-menu">
    <div data-simplebar class="h-100">
        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">

                {{-- Dashboard --}}
                <li class="{{ Nav::isRoute('dashboard.index', 'mm-active') }}">
                    <a href="{{ route('dashboard.index') }}" class="{{ Nav::isRoute('dashboard.index') }}">
                        <i data-feather="grid"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li class="{{ Nav::isRoute('game.index', 'mm-active') }}">
                    <a href="{{ route('game.index') }}" class="{{ Nav::isRoute('game.index') }}">
                        <i data-feather="clock"></i>
                        <span>Game</span>
                    </a>
                </li>
                <li
                    class="{{ Nav::isRoute('year.edit', 'mm-active') }} {{ Nav::isRoute('year.index', 'mm-active') }} {{ Nav::isRoute('year.create', 'mm-active') }}">
                    <a href="javascript:void(0);"
                        class="has-arrow {{ Nav::isRoute('year.edit', 'mm-active') }} {{ Nav::isRoute('year.index', 'mm-active') }} {{ Nav::isRoute('year.create', 'mm-active') }}">
                        <i data-feather="users"></i> {{-- Changed from calendar to users --}}
                        <span>User</span>
                    </a>
                    <ul class="sub-menu {{ Nav::isRoute('year.edit', 'mm-show') }} {{ Nav::isRoute('year.index', 'mm-show') }} {{ Nav::isRoute('year.create', 'mm-show') }}"
                        aria-expanded="false">

                        <li class="{{ Nav::isRoute('super-agent.index', 'mm-active') }}">
                            <a href="{{ route('super-agent.index') }}"
                                class="{{ Nav::isRoute('super-agent.index') }}">
                                <i data-feather="shield"></i> {{-- Super Agent: authority / top-level --}}
                                <span>Super Agent</span>
                            </a>
                        </li>

                        <li class="{{ Nav::isRoute('agent.index', 'mm-active') }}">
                            <a href="{{ route('agent.index') }}" class="{{ Nav::isRoute('agent.index') }}">
                                <i data-feather="user-check"></i> {{-- Agent: verified / standard user --}}
                                <span>Agent</span>
                            </a>
                        </li>

                        <li class="{{ Nav::isRoute('sub-agent.index', 'mm-active') }}">
                            <a href="{{ route('sub-agent.index') }}" class="{{ Nav::isRoute('sub-agent.index') }}">
                                <i data-feather="user"></i> {{-- Sub Agent: part of a team --}}
                                <span>Sub Agent</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="{{ Nav::isRoute('result-entry.index', 'mm-active') }}">
                    <a href="{{ route('result-entry.index') }}" class="{{ Nav::isRoute('result-entry.index') }}">
                        <i data-feather="file-text"></i>
                        <span>Result Entry</span>
                    </a>
                </li>


                <li class="">
                    <a href="javascript:void(0);" class="has-arrow">
                        <i data-feather="bar-chart-2"></i> {{-- Reports main icon --}}
                        <span>Reports</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">

                        <li class="">
                            <a href="" class="">
                                <i data-feather="calendar"></i> {{-- Daily Report --}}
                                <span>Daily Report</span>
                            </a>
                        </li>

                        <li class="">
                            <a href="" class="">
                                <i data-feather="pie-chart"></i> {{-- Monthly Report --}}
                                <span>Monthly Report</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <li>
    <a href="">
        <i data-feather="user"></i>
        <span>Agent Settings</span>
    </a>
</li>

<li>
    <a href="">
        <i data-feather="calendar"></i>
        <span>Daily Reports</span>
    </a>
</li>

<li>
    <a href="">
        <i data-feather="calendar"></i>
        <span>Weekly Reports</span>
    </a>
</li>

<li>
    <a href="">
        <i data-feather="bar-chart-2"></i>
        <span>Result View</span>
    </a>
</li>

<li>
    <a href="">
        <i data-feather="trending-up"></i>
        <span>Total Revenue</span>
    </a>
</li>

<li>
    <a href="">
        <i data-feather="smartphone"></i>
        <span>App Settings</span>
    </a>
</li>

<li>
    <a href="">
        <i data-feather="settings"></i>
        <span>Settings</span>
    </a>
</li>



            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>
