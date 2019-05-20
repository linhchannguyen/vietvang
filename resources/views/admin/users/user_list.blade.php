
@extends('admin.layout.master')
@section('content')

<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Users</h1>
            </div>
            <div class="col-lg-12">
            <form method="post" enctype="multipart/form-data" action="{{ route('import') }}">
            {{ csrf_field() }}
                <div class="form-group">
                    <table class="table">
                        <tr>
                            <td width="40%" align="right"></td>
                            <td width="30%">
                                <input type="file" name="user_file">
                                {!! $errors->first('user_file', '<p class="help-block alert-danger">:message</p>') !!}
                            </td>
                            <td width="30%" align="left">
                                <input type="submit" name="upload" class="btn btn-primary" value="Import">
                                <a href="{{ route('export') }}" class="btn btn-primary">Export</a>
                            </td>
                        </tr>
                    </table>
                </div>
            </form>
            </div>
            <!-- /.col-lg-12 -->
        </div>
        <!-- /.row -->
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                    @if($message = Session::get('success'))
                    <div class="alert alert-success alert-block">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                            <strong>{{ $message }}</strong>
                    </div>
                    @endif
                    @if($message = Session::get('error'))
                    <div class="alert alert-danger alert-block">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                            <strong>{{ $message }}</strong>
                    </div>
                    @endif
                        <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Email</th>
                                    <th>Gender</th>
                                    <th>Birthday</th>
                                    <th>Postcode</th>
                                    <th>Phone</th>
                                    <th>Address</th>
                                    <th>Activated</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->first_name }}</td>
                                    <td>{{ $user->last_name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->gender_name }}</td>
                                    <td>{{date('Y-m-d', strtotime($user->birthday))}}</td>
                                    <td>{{ $user->postcode }}</td>
                                    <td>{{ $user->phone }}</td>
                                    <td>{{ $user->address }}</td>
                                    <td>{{ $user->activated }}</td>
                                    <td>Delete</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                </div>
                <!-- /.table-responsive -->

            </div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
        </div>
    </div>
</div>

@endsection