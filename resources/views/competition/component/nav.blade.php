<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<style>
    .navigate {
        padding: 0.75rem 1.5rem;
        border-bottom: 1px solid #e2e8f0;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
    }

    .link {
        display: flex;
        gap: 2rem;
        align-items: center;
    }

    .link-custom {
        text-decoration: none;
        font-weight: 500;
        color: #475569;
        padding: 0.5rem 0.85rem;
        border-radius: 0.37rem;
        font-size: 0.92rem;
        transition: color 0.2s ease;
    }

    .link-custom:hover {
        color: #4f46e5;
        background-color: #f1f5f9;
    }

    .link-custom.active {
        color: #4f46e5;
        background-color: #eef2ff;
    }

    .user-box {
        display: inline-flex;
        gap: 0.5rem;
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        padding: 0.35rem 0.75rem;
        border-radius: 30px;
        font-weight: 600;
        font-size: 0.87rem;
        align-items: center;
        color: #334155;
    }

    .user-icon {
        width: 28px;
        height: 28px;
        background-color: #6366f1;
        color: white;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 0.75rem;
    }

    @media (max-width: 992px) {
        .navigate {
            padding: 1.2rem 0rem;
        }

        .link {
            flex-direction: column;
            gap: 1rem;
        }

        .user-box {
            font-size: 0.75rem;
        }

        .user-icon {
            width: 24px;
            height: 24px;
            font-size: 0.63rem;
        }

        .navbar-collapse {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #f1f5f9;
        }

        .link-custom {
            width: 100%;
        }
    }

    @media (min-width: 992px) {
        .link-wrap {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
        }
    }
</style>

<div class="navigate navbar navbar-expand-lg bg-white sticky-top">
    <div class="container-fluid d-flex justify-content-between">
        
            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMobile" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- <span class="logo"></span> -->

            <div class="user-box ms-lg-auto d-lg-none">
                <div class="user-icon">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <span>{{ Auth::user()->name }}</span>
            </div>

        <div class="collapse navbar-collapse" id="navbarMobile">
            <div class="link-wrap mx-auto">
                @if(request()->get('mode') === 'user' || request()->routeIs('competition.user*'))
                    <div class="link">
                        <a href="{{ route('competition.index') }}" class="link-custom {{ request()->routeIs('competition.index') ? 'active' : '' }}">Dashboard</a>
                        <a href="{{ route('competition.user') }} " class="link-custom {{ request()->routeIs('competition.user') ? 'active' : '' }}">Senarai Pertandingan</a>
                        <a href="{{ route('competition.userrecord') }}" class="link-custom {{ request()->routeIs('competition.userrecord') ? 'active' : '' }}">Rekod Pendaftaran</a>
                    </div>
                @else

                    <div class="link">
                        <a href="{{ route('competition.index') }}" class="link-custom {{ request()->routeIs('competition.index') ? 'active' : '' }}">Dashboard</a>
                        <a href="{{ route('competition.host') }}" class="link-custom {{ request()->routeIs('competition.host') ? 'active' : '' }}">Pengurusan Pertandingan</a>
                        <a href="{{ route('competition.hostrecord') }}" class="link-custom {{ request()->routeIs('competition.hostrecord') ? 'active' : '' }}">Urus Pendaftaran</a>
                    </div>
                @endif
            </div>
        </div>
            
        <div class="user-box ms-lg-auto d-none d-lg-inline-flex">
            <div class="user-icon">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
            <span>{{ Auth::user()->name }}</span>
        </div>
    </div>
</div>