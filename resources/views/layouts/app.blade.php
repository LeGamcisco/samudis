<!doctype html>
{{-- <html lang="en" class="semi-dark"> --}}
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="{{ asset('assets/images/samu-logo.png') }}" type="image/png">
    <!-- loader-->
    @if (!request()->is('/') || request()->is('/realtime/*'))
        <link href="{{ asset('assets/css/pace.min.css') }}" rel="stylesheet" />
        <script src="{{ asset('assets/js/pace.min.js') }}"></script>
    @endif

    <!--plugins-->
    <link href="{{ asset('assets/plugins/simplebar/css/simplebar.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/metismenu/css/metisMenu.min.css') }}" rel="stylesheet" />

    <!-- CSS Files -->
    {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"> --}}
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/bootstrap-extended.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/icons.css') }}" rel="stylesheet">
    {{-- <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet"> --}}
    <link rel="stylesheet" href="{{ asset('assets/plugins/toastr/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.css') }}">
    <!--Theme Styles-->
    <link href="{{ asset('assets/css/dark-theme.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/semi-dark.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/header-colors.css') }}" rel="stylesheet" />
    @yield('css')


    <title>Samu DIS - @yield('subtitle')</title>
</head>

<body>


    <!--start wrapper-->
    <div class="wrapper">

        <!--start sidebar -->
        <aside class="sidebar-wrapper text-white" style="background: #2A97C7;" data-simplebar="true">
            <div class="sidebar-header">
                <div>
                    <img src="{{ asset('assets/images/samu-logo.png') }}" class="logo-icon " alt="logo icon">
                </div>
                <div>
                    <h4 class="logo-text text-bold" style="letter-spacing: 10px;">DIS</h4>

                </div>
                <div class="toggle-icon ms-auto">
                    <ion-icon name="menu-sharp"></ion-icon>
                </div>
            </div>
            <!--navigation-->
            @include('layouts.sidebar')
            <!--end navigation-->
        </aside>
        <!--end sidebar -->

        <!--start top header-->
        @include('layouts.header')
        <!--end top header-->


        <!-- start page content wrapper-->
        <div class="page-content-wrapper" style="height: 100vh">
            <!-- start page content-->
            <div class="page-content">

                <!--start breadcrumb-->
                <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                    <div class="breadcrumb-title pe-3">@yield('title')</div>
                    <div class="ps-3">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0 p-0 align-items-center">
                                <li class="breadcrumb-item">
                                    <a href="javascript:;">
                                        <ion-icon name="home-outline"></ion-icon>
                                    </a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">@yield('subtitle')</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="ms-auto">
                        @yield('header-option')
                    </div>
                </div>
                <!--end breadcrumb-->

                <!-- start content section-->
                @yield('content')
                <!-- end content section-->

            </div>
            <!-- end page content-->
        </div>
        <!--end page content wrapper-->


        <!--start footer-->
        <footer class="footer fixed-bottom">
            <div class="footer-text">
                Copyright © 2025. All right reserved. <small title="EWS Edition" class="text-secondary">v 3.1.0</small>
            </div>
        </footer>
        <!--end footer-->


        <!--Start Back To Top Button-->
        <a href="javaScript:;" class="back-to-top">
            <ion-icon name="arrow-up-outline"></ion-icon>
        </a>
        <!--End Back To Top Button-->

        <!--start switcher-->
        {{-- <div class="switcher-body">
      <button class="btn btn-primary btn-switcher shadow-sm" type="button" data-bs-toggle="offcanvas"
        data-bs-target="#offcanvasScrolling" aria-controls="offcanvasScrolling">
        <ion-icon name="color-palette-sharp" class="me-0"></ion-icon>
      </button>
      <div class="offcanvas offcanvas-end shadow border-start-0 p-2" data-bs-scroll="true" data-bs-backdrop="false"
        tabindex="-1" id="offcanvasScrolling">
        <div class="offcanvas-header border-bottom">
          <h5 class="offcanvas-title" id="offcanvasScrollingLabel">Theme Customizer</h5>
          <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
          <h6 class="mb-0">Theme Variation</h6>
          <hr>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="inlineRadioOptions" id="LightTheme" value="option1">
            <label class="form-check-label" for="LightTheme">Light</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="inlineRadioOptions" id="DarkTheme" value="option2">
            <label class="form-check-label" for="DarkTheme">Dark</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="inlineRadioOptions" id="SemiDark" value="option3"
              checked>
            <label class="form-check-label" for="SemiDark">Semi Dark</label>
          </div>
          <hr />
          <h6 class="mb-0">Header Colors</h6>
          <hr />
          <div class="header-colors-indigators">
            <div class="row row-cols-auto g-3">
              <div class="col">
                <div class="indigator headercolor1" id="headercolor1"></div>
              </div>
              <div class="col">
                <div class="indigator headercolor2" id="headercolor2"></div>
              </div>
              <div class="col">
                <div class="indigator headercolor3" id="headercolor3"></div>
              </div>
              <div class="col">
                <div class="indigator headercolor4" id="headercolor4"></div>
              </div>
              <div class="col">
                <div class="indigator headercolor5" id="headercolor5"></div>
              </div>
              <div class="col">
                <div class="indigator headercolor6" id="headercolor6"></div>
              </div>
              <div class="col">
                <div class="indigator headercolor7" id="headercolor7"></div>
              </div>
              <div class="col">
                <div class="indigator headercolor8" id="headercolor8"></div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div> --}}
        <!--end switcher-->


        <!--start overlay-->
        <div class="overlay nav-toggle-icon"></div>
        <!--end overlay-->
        @yield('modal')


    </div>
    <!--end wrapper-->


    <!-- JS Files-->
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/simplebar/js/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/metismenu/js/metisMenu.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/toastr/toastr.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/sweetalert2/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script type="module" src="{{ asset('assets/plugins/ionicons/ionicons.esm.js') }}"></script>
    <!--plugins-->
    <script src="{{ asset('assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('assets/plugins/momentjs/moment.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/momentjs/moment-with-locales.min.js') }}"></script>
    {{-- <script src="/assets/plugins/apexcharts-bundle/js/apexcharts.min.js"></script> --}}
    {{-- <script src="/assets/plugins/chartjs/chart.min.js"></script> --}}
    {{-- <script src="/assets/js/index2.js"></script> --}}
    <!-- Main JS-->
    <script src="{{ asset('assets/js/main.js') }}"></script>
    <script>
        moment().locale("id")
    </script>
    @yield('js')

</body>

</html>
