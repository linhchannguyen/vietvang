<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>Admin Register</title>

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
                <div class="col-md-4"></div>
                <div class="col-md-4">
                    <div class="login-panel panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">Đăng Ký Tài Khoản</h3>
                        </div>
                        <div class="panel-body">
                            <!-- @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                    @foreach ($errors->all() as $error)
                                        <li style="list-style: none;">{{ $error }}</li>
                                    @endforeach
                                    </ul>
                                </div>
                            @endif -->
                            @if(\Session::has('alert-success'))
                                <div class="alert alert-success">
                                    <div>{{Session::get('alert-success')}}</div>
                                </div>
                            @endif
                            <form role="form" action="{{route('admin_signup')}}" method="post">
                                {{ csrf_field() }}
                                <div class="form-group ">
                                    <label class="control-label">First name *:</label>
                                    <input type="text" id="first_name" name="first_name" class="form-control" placeholder="Please Input first name" autofocus>
                                    {!! $errors->first('first_name', '<p class="help-block alert-danger">:message</p>') !!}
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Last name *:</label>
                                    <input type="text" id="last_name" name="last_name" class="form-control" placeholder="Please Input last name">
                                    {!! $errors->first('last_name', '<p class="help-block alert-danger">:message</p>') !!}
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Email *</label>
                                    <input type="email" id="email" name="email" class="form-control" placeholder="Please input your email">
                                    {!! $errors->first('email', '<p class="help-block alert-danger">:message</p>') !!}
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Password *</label>
                                    <input type="password" id="password" name="password" class="form-control" placeholder="Please input your password">
                                    {!! $errors->first('password', '<p class="help-block alert-danger">:message</p>') !!}
                                </div>        
                                
                                <div class="form-group">
                                    <label class="control-label">Repassword *</label>
                                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Password confirm">
                                    {!! $errors->first('password_confirmation', '<p class="help-block alert-danger">:message</p>') !!}
                                </div>                          
                                <div class="form-group">
                                    <a href="{{route('login')}}">Đã có tài khoản</a>
                                </div>
                                
                                <div class="text-center">
                                    <button type="submit" class="form-control btn btn-primary">Đăng ký</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-4"></div>
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