<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>Register</title>

        <!-- Bootstrap Core CSS -->
        <link href="{{url('assets/admin/css/bootstrap.min.css')}}" rel="stylesheet">

        <!-- MetisMenu CSS -->
        <link href="{{url('assets/admin/css/metisMenu.min.css')}}" rel="stylesheet">

        <!-- Custom CSS -->
        <link href="{{url('assets/admin/css/startmin.css')}}" rel="stylesheet">

        <!-- Custom Fonts -->
        <link href="{{url('assets/admin/css/font-awesome.min.css')}}" rel="stylesheet" type="text/css">

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="col-md-4 col-md-offset-4">
                    <div class="login-panel panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">Đăng Ký Tài Khoản</h3>
                        </div>
                        <div class="panel-body">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                    @foreach ($errors->all() as $error)
                                        <li style="list-style: none;">{{ $error }}</li>
                                    @endforeach
                                    </ul>
                                </div>
                            @endif
                            @if(\Session::has('alert-success'))
                                <div class="alert alert-success">
                                    <div>{{Session::get('alert-success')}}</div>
                                </div>
                            @endif
                            <form role="form" action="{{route('signup')}}" method="post">
                                {{ csrf_field() }}
                                <div class="form-group">
                                    <label class="control-label">Họ tên:</label>
                                    <input type="text" id="name" name="name" class="form-control" placeholder="Nhập họ tên" autofocus>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Email:</label>
                                    <input type="email" id="email" name="email" class="form-control" placeholder="Nhập email">
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Mật khẩu:</label>
                                    <input type="password" id="password" name="password" class="form-control" placeholder="Nhập mật khẩu">
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Nhập lại mật khẩu:</label>
                                    <input type="password" id="repassword" name="repassword" class="form-control" placeholder="Nhập lại mật khẩu">
                                </div>
                                <div class="form-group">
                                    <a href="{{route('login')}}"">Đã có tài khoản</a>
                                </div>
                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary">Đăng ký</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<!-- jQuery -->
<script src="{{url('assets/admin/js/jquery.min.js')}}"></script>

<!-- Bootstrap Core JavaScript -->
<script src="{{url('assets/admin/js/bootstrap.min.js')}}"></script>

<!-- Metis Menu Plugin JavaScript -->
<script src="{{url('assets/admin/js/metisMenu.min.js')}}"></script>

<!-- Custom Theme JavaScript -->
<script src="{{url('assets/admin/js/startmin.js')}}"></script>

<script src="{{url('assets/admin/js/scripts-login.js')}}"></script>
</body>
</html>