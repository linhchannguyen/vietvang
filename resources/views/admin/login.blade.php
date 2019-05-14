<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>Login</title>

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
                            <h3 class="panel-title">Vui lòng đăng nhập</h3>
                        </div>
                        <div class="panel-body">
                            <ul style="padding-inline-start: 0px;">
                                @if ($errors->any())
                                <div class="alert alert-danger">
                                    @foreach ($errors->all() as $error)
                                        <li style="list-style: none;"> 
                                            {{ $error }}
                                        </li>
                                    @endforeach
                                </div>
                                @endif
                                @if(\Session::has('alert'))
                                    <div class="alert alert-danger">
                                        <div>{{Session::get('alert')}}</div>
                                    </div>
                                @endif
                                @if(\Session::has('alert-success'))
                                    <div class="alert alert-success">
                                        <div>{{Session::get('alert-success')}}</div>
                                    </div>
                                @endif
                            </ul>
                            <form action="{{route('login')}}" role="form" method="post">
                                {{ csrf_field() }}
                                <fieldset>
                                    <div class="form-group">
                                        <input class="form-control" placeholder="E-mail" name="email" id="email" type="email" autofocus>
                                    </div>
                                    <div class="form-group">
                                        <input class="form-control" placeholder="Mật khẩu" name="password" id="password" type="password" value="">
                                    </div>
                                    <div class="form-group">
                                        <a href="{{route('forget-password')}}">Quên mật khẩu</a> | <a href="{{route('signup')}}">Đăng ký</a>
                                    </div>
                                    <button id="btn-login" class="btn btn-lg btn-success btn-block">Đăng nhập</button>
                                </fieldset>
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
