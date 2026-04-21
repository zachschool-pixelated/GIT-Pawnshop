<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CPB-NGI Pawnshop Management System')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #1a5276;
            --secondary-color: #c39bd3;
            --danger-color: #c0392b;
        }
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }
        .sidebar {
            background-color: var(--primary-color);
            color: white;
            height: 100vh;
            min-height: 100vh;
            padding: 20px;
            position: fixed;
            width: 260px;
            left: 0;
            top: 0;
            overflow-y: auto;
            overscroll-behavior: contain;
            z-index: 1000;
        }
        .sidebar-offcanvas {
            background-color: var(--primary-color);
            color: white;
            width: 280px;
            height: 100vh;
            max-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .sidebar-offcanvas .offcanvas-header {
            border-bottom: 1px solid rgba(255, 255, 255, 0.15);
        }
        .sidebar-offcanvas .offcanvas-body {
            overflow-y: auto;
            flex: 1 1 auto;
        }
        .sidebar-offcanvas .btn-close {
            filter: invert(1) grayscale(100%);
        }
        .sidebar h3 {
            color: var(--secondary-color);
            margin-bottom: 30px;
            font-weight: bold;
            font-size: 18px;
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 12px 15px;
            margin-bottom: 8px;
            border-radius: 5px;
            transition: all 0.3s;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: var(--secondary-color);
            color: white;
        }
        .sidebar-offcanvas .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 12px 15px;
            margin-bottom: 8px;
            border-radius: 5px;
            transition: all 0.3s;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .sidebar-offcanvas .nav-link:hover,
        .sidebar-offcanvas .nav-link.active {
            background-color: var(--secondary-color);
            color: white;
        }
        .sidebar i {
            width: 20px;
        }
        .sidebar-offcanvas i {
            width: 20px;
        }
        .main-content {
            margin-left: 260px;
            padding: 30px;
        }
        .navbar {
            background-color: white;
            border-bottom: 2px solid var(--primary-color);
            margin-bottom: 30px;
        }
        .navbar-brand {
            color: var(--primary-color) !important;
            font-weight: bold;
        }
        .page-title {
            color: var(--primary-color);
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--secondary-color);
        }
        .card {
            border: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .card-header {
            background-color: var(--primary-color);
            color: white;
            border-bottom: none;
            font-weight: bold;
        }
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        .btn-primary:hover {
            background-color: #0d3d56;
            border-color: #0d3d56;
        }
        .btn-secondary {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        .btn-secondary:hover {
            background-color: #a89bb0;
            border-color: #a89bb0;
        }
        .badge {
            padding: 6px 12px;
            border-radius: 20px;
        }
        .alert {
            border: none;
            border-radius: 5px;
        }
        .table-hover tbody tr:hover {
            background-color: #f5f5f5;
        }
        .stat-card {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 15px;
        }
        .stat-card h5 {
            font-size: 12px;
            opacity: 0.9;
            margin-bottom: 10px;
        }
        .stat-card .value {
            font-size: 28px;
            font-weight: bold;
        }
        .form-label {
            font-weight: 600;
            color: var(--primary-color);
        }
        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(26, 82, 118, 0.25);
        }
        @media (max-width: 1199.98px) {
            .sidebar {
                display: none;
            }
            .main-content {
                margin-left: 0;
                padding: 20px 15px;
            }
        }
    </style>
    @yield('styles')
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="sidebar">
                <h3><i class="bi bi-building"></i> Pawnshop</h3>
                <ul class="nav flex-column">
                    @include('layouts.partials.sidebar-links')
                </ul>
            </nav>

            <div class="offcanvas offcanvas-start sidebar-offcanvas d-xl-none" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title" id="mobileSidebarLabel">
                        <i class="bi bi-building"></i> Pawnshop
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">
                    <div class="mb-4">
                        <div class="fw-semibold">{{ Auth::user()->name }}</div>
                        <div class="small text-white-50">{{ Auth::user()->email }}</div>
                        <span class="badge bg-info text-dark mt-2">{{ Auth::user()->role }}</span>
                    </div>
                    <ul class="nav flex-column">
                        @include('layouts.partials.sidebar-links')
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <main class="main-content col">
                <!-- Navbar -->
                <nav class="navbar navbar-expand-lg navbar-light mb-4">
                    <div class="container-fluid">
                        <div class="d-flex align-items-center gap-2">
                            <button class="btn btn-outline-primary d-xl-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar" aria-controls="mobileSidebar" aria-label="Open navigation menu">
                                <i class="bi bi-list fs-4"></i>
                            </button>
                            <a class="navbar-brand mb-0" href="{{ route('dashboard') }}">
                                <i class="bi bi-building"></i> CPB-NGI Pawnshop
                            </a>
                        </div>
                        <div class="d-flex align-items-center gap-2 ms-auto">
                            <span class="navbar-text d-none d-sm-inline">
                                <i class="bi bi-person-circle"></i> {{ Auth::user()->name }}
                            </span>
                            <span class="badge bg-info text-dark">{{ Auth::user()->role }}</span>
                        </div>
                    </div>
                </nav>

                <!-- Flash Messages -->
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Validation Error!</strong>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        {{ session('warning') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Page Content -->
                @yield('content')
            </main>
        </div>
    </div>

    <div class="modal fade" id="confirmActionModal" tabindex="-1" aria-labelledby="confirmActionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmActionModalLabel">Please Confirm</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="confirmActionModalMessage">
                    Are you sure you want to continue?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmActionButton">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modalElement = document.getElementById('confirmActionModal');
            if (!modalElement || typeof bootstrap === 'undefined') {
                return;
            }

            const modalTitle = document.getElementById('confirmActionModalLabel');
            const modalMessage = document.getElementById('confirmActionModalMessage');
            const confirmButton = document.getElementById('confirmActionButton');
            const confirmModal = new bootstrap.Modal(modalElement);
            let pendingForm = null;

            document.addEventListener('submit', function (event) {
                const form = event.target;
                if (!(form instanceof HTMLFormElement)) {
                    return;
                }

                const message = form.dataset.confirmMessage;
                if (!message) {
                    return;
                }

                if (form.dataset.confirmed === 'true') {
                    delete form.dataset.confirmed;
                    return;
                }

                event.preventDefault();
                pendingForm = form;

                modalTitle.textContent = form.dataset.confirmTitle || 'Please Confirm';
                modalMessage.textContent = message;
                confirmButton.textContent = form.dataset.confirmButton || 'Confirm';
                confirmButton.className = 'btn ' + (form.dataset.confirmButtonClass || 'btn-primary');

                confirmModal.show();
            }, true);

            confirmButton.addEventListener('click', function () {
                if (!pendingForm) {
                    return;
                }

                pendingForm.dataset.confirmed = 'true';
                confirmModal.hide();

                if (typeof pendingForm.requestSubmit === 'function') {
                    pendingForm.requestSubmit();
                } else {
                    pendingForm.submit();
                }
            });

            modalElement.addEventListener('hidden.bs.modal', function () {
                pendingForm = null;
            });
        });
    </script>
    @yield('scripts')
</body>
</html>
