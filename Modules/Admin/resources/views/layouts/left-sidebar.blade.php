<div class="vertical-menu">
    <div data-simplebar class="h-100">

        <!-- Sidemenu -->
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

                {{-- Game --}}
                <li class="{{ Nav::isRoute('game.index', 'mm-active') }}">
                    <a href="{{ route('game.index') }}" class="{{ Nav::isRoute('game.index') }}">
                        <i data-feather="clock"></i>
                        <span>Game</span>
                    </a>
                </li>

                {{-- Prize Entry --}}
                <li class="{{ Nav::isRoute('prize-entry.index', 'mm-active') }}">
                    <a href="{{ route('prize-entry.index') }}" class="{{ Nav::isRoute('prize-entry.index') }}">
                        <i data-feather="clock"></i>
                        <span>Prize Entry</span>
                    </a>
                </li>

                {{-- Result Entry --}}
                <li class="{{ Nav::isRoute('result-entry.index', 'mm-active') }}">
                    <a href="{{ route('result-entry.index') }}" class="{{ Nav::isRoute('result-entry.index') }}">
                        <i data-feather="file-text"></i>
                        <span>Result Entry</span>
                    </a>
                </li>

                {{-- Super Agent --}}
                <li class="{{ Nav::isRoute('super-agent.index', 'mm-active') }}">
                    <a href="{{ route('super-agent.index') }}" class="{{ Nav::isRoute('super-agent.index') }}">
                        <i data-feather="shield"></i>
                        <span>Super Agent</span>
                    </a>
                </li>

                {{-- Agent --}}
                <li class="{{ Nav::isRoute('agent.index', 'mm-active') }}">
                    <a href="{{ route('agent.index') }}" class="{{ Nav::isRoute('agent.index') }}">
                        <i data-feather="user-check"></i>
                        <span>Agent</span>
                    </a>
                </li>

                {{-- Sub Agent --}}
                <li class="{{ Nav::isRoute('sub-agent.index', 'mm-active') }}">
                    <a href="{{ route('sub-agent.index') }}" class="{{ Nav::isRoute('sub-agent.index') }}">
                        <i data-feather="user"></i>
                        <span>Sub Agent</span>
                    </a>
                </li>

                {{-- Bills --}}
                <li class="{{ Nav::isRoute('result-entry.index', 'mm-active') }}">
                    <a href="{{ route('result-entry.index') }}" class="{{ Nav::isRoute('result-entry.index') }}">
                        <i data-feather="file-text"></i>
                        <span>Bills</span>
                    </a>
                </li>

                {{-- App Settings --}}
                <li>
                    <a href="">
                        <i data-feather="smartphone"></i>
                        <span>App Settings</span>
                    </a>
                </li>

            </ul>

        </div>
        <!-- Sidebar -->

    </div>
</div>