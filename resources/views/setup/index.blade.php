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
  <link rel="stylesheet" href="{{ asset("assets/plugins/toastr/toastr.min.css") }}">

  <title>Samu DIS - Setup</title>
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
                  <h4>Samu DIS</h4>
                  <p>Setup System Configuration</p>
                </div>
                <form class="form-body row g-3" method="post" action="{{ route("setup.doSetup") }}">
                    @csrf
                  <div class="col-6">
                    <label class="form-label">DB Driver</label>
                    <div class="d-flex justify-content-between align-items-center gap-2">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="type" id="type1" value="mysql">
                            <label class="form-check-label" for="type1">MySQL</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="type" id="type2" value="pgsql">
                            <label class="form-check-label" for="type2">PostgreSQL</label>
                        </div>
                    </div>
                  </div>
                  <div class="col-6">
                    <label class="form-label">Database Host</label>
                    <input name="host" value="localhost" type="text" placeholder="localhost" class="form-control">
                  </div>
                  <div class="col-6">
                    <label class="form-label">Database User</label>
                    <input name="username" type="text" placeholder="jhondoe" class="form-control">
                  </div>
                  <div class="col-6">
                    <label class="form-label">Database Password</label>
                    <input name="password" type="password" placeholder="****" class="form-control">
                  </div>
                  <div class="col-6">
                    <label class="form-label">Database Name</label>
                    <input name="database" value="egateway" type="text" placeholder="egateway" class="form-control">
                  </div>
                  <div class="col-6">
                    <label class="form-label">Database Port</label>
                    <input name="port" type="text" placeholder="5432" class="form-control">
                  </div>
                  <div class="col-12">
                    <a href="#" id="btn-test-connection">✅ Test Connection</a>
                  </div>
                  <div class="col-12 col-lg-12">
                    <div class="d-grid">
                      <button type="submit" id="btn-submit" class="disabled btn btn-primary">Continue Setup</button>
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


</body>
<script src="{{ asset("assets/js/jquery.min.js") }}"></script>
<script src="{{ asset("assets/plugins/toastr/toastr.min.js") }}"></script>
<script>
  $(document).ready(function(){
    @foreach ($errors->all() as $error)
      toastr.error(`{{ $error }}`)
    @endforeach
    $('[name="type"]').change(function(){
        const value = $(this).val()
        if(value == "pgsql"){
          $('[name="username"]').val("postgres")
          $('[name="port"]').val("5432")
        }else{
          $('[name="username"]').val("root")
          $('[name="port"]').val("3306")
        }
    })

    $('#btn-test-connection').click(function(){
      const data = $('form').serialize()
      $.ajax({
        url : `{{ route("setup.test-connection") }}`,
        method : "POST",
        data : data,
        success : function(res){
          $('#btn-submit').removeClass('disabled')
          toastr.success(res.message)
        },
        error : function(xhr, status, err){
          toastr.error(xhr?.responseJSON?.message ?? err.toString())
        }
      })
    })
  })
</script>
</html>
