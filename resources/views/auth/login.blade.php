<!doctype html>
<html lang="en" class="light-theme">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- loader-->
  <link href="{{ asset("assets/css/pace.min.css") }}" rel="stylesheet" />
  <script src="{{ asset("assets/js/pace.min.js") }}"></script>

  <!--plugins-->
  <link href="{{ asset("assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css") }}" rel="stylesheet" />

  <!-- CSS Files -->
  <link href="{{ asset("assets/css/bootstrap.min.css") }}" rel="stylesheet">
  <link href="{{ asset("assets/css/bootstrap-extended.css") }}" rel="stylesheet">
  <link href="{{ asset("assets/css/style.css") }}" rel="stylesheet">
  <link href="{{ asset("assets/css/icons.css") }}" rel="stylesheet">
  {{-- <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet"> --}}

  <title>Samu DIS - Login</title>
</head>

<body class="bg-white">

  <!--start wrapper-->
  <div class="wrapper">
    <div class="">
      <div class="row g-0 m-0">
        <div class="col-xl-6 col-lg-12">
          <div class="login-cover-wrapper">
            <div class="card shadow-none">
              <div class="card-body">
                <div class="text-center">
                  <img src="{{ asset('assets/images/samu-logo.png') }}" class="img img-fluid" alt="Samu Logo">
                  <h4 class="fw-bold">Login</h4>
                </div>
                <form class="form-body row g-3" method="post" action="{{ route("login.doLogin") }}">
                    @csrf
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            @foreach ($errors->all() as $error)
                                <p class="m-0"><i class='bx bxs-error-alt me-2' ></i> {{ $error }}</p>
                            @endforeach
                        </div>

                    @endif
                  <div class="col-12">
                    <label class="form-label">Email</label>
                    <input name="email" value="{{ old("email") }}" type="email" placeholder="email@company" class="form-control">
                  </div>
                  <div class="col-12">
                    <label class="form-label">Password</label>
                    <input name="password" required type="password" placeholder="******" class="form-control">
                  </div>
                  <div class="col-12 col-lg-12">
                    <div class="d-grid">
                      <button type="submit" class="btn btn-info text-white">Login</button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-6 col-lg-12">
          <div class="position-fixed top-0 h-100 d-xl-block d-none login-cover-img">
          </div>
        </div>
      </div>
      <!--end row-->
    </div>
  </div>
  <!--end wrapper-->

<script src="{{ asset("assets/js/jquery.min.js") }}"></script>
<script src="{{ asset("assets/js/bootstrap.bundle.min.js") }}"></script>
</body>

</html>
