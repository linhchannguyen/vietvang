
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
                        <div class="table-responsive" id="tag_container">
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>First Name</th>
                                        <th>Last Name</th>
                                        <th>Email</th>
                                        <th>Gender</th>
                                        <th>Birthday</th>
                                        <th>Postcode</th>
                                        <th>Phone</th>
                                        <th>Address</th>
                                        <th>Action</th>
                                        <th style="width: 60px;">
                                        <button style="float: left;" type="button" name="bulk_delete" id="bulk_delete" class="btn btn-danger btn-xs"><i class="glyphicon glyphicon-remove"></i></button>
                                        <input style="float: right;" type="checkbox" id="checkall">
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach ($users as $user)
                                    <tr>
                                        <td>{{ $user->first_name }}</td>
                                        <td>{{ $user->last_name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->gender_name }}</td>
                                        <td>{{date('Y-m-d', strtotime($user->birthday))}}</td>
                                        <td>{{ $user->postcode }}</td>
                                        <td>{{ $user->phone }}</td>
                                        <td>{{ $user->address }}</td>
                                        <td class="text-center">
                                            <span class="edit-modal" 
                                                data-id="{{$user->id}}"
                                                data-firstname="{{$user->first_name}}"
                                                data-lastname="{{$user->last_name}}"
                                                data-gender="{{$user->gender_id}}"
                                                data-birthday="{{date('Y-m-d', strtotime($user->birthday))}}"
                                                data-postcode="{{$user->postcode}}"
                                                data-phone="{{$user->phone}}"
                                                data-address="{{$user->address}}"
                                                
                                                ><a href="#"><i class="fa fa-pencil fa-fw"></i></a></span>
                                            <span><a href="#" class="deleteItem" data-id="{{ $user->id }}"><i class="fa fa-trash-o fa-fw"></i></a></span>
                                        </td>
                                        <td style="text-align: right;"><input type="checkbox" class="checkItem" value="{{$user->id}}"></td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            {{ $users->links('vendor.pagination.bootstrap-4') }}
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
    
    <!-- Modal form to edit a form -->
    <div id="editModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">×</button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <form role="form">
                        <div class="form-group">
                            <label class="control-label">ID:</label>
                            <input type="text" class="form-control" id="id_edit" disabled>
                        </div>
                        <div class="form-group">
                            <label class="control-label">First name:</label>
                            <input type="text" class="form-control" id="edit_first_name" autofocus>
                            <p class="errorFirstname text-center alert-danger hidden"></p>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Last name:</label>
                            <input type="text" class="form-control" id="edit_last_name">
                            <p class="errorLastname text-center alert-danger hidden"></p>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Gender:</label>
                            <select class="form-control" name="edit_gender" id="edit_gender">
                                <option value="1">Male</option>
                                <option value="2">Female</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Birthday:</label>
                            <input type="date" class="form-control" id="edit_birthday">
                            <p class="errorBirthday text-center alert-danger hidden"></p>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Post code:</label>
                            <input type="text" class="form-control" id="edit_post_code">
                            <p class="errorPostcode text-center alert-danger hidden"></p>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Phone:</label>
                            <input type="number" class="form-control" id="edit_phone">
                            <p class="errorPhone text-center alert-danger hidden"></p>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Address:</label>
                            <textarea class="form-control" name="edit_address" id="edit_address"></textarea>
                            <p class="errorAddress text-center alert-danger hidden"></p>                           
                        </div>
                    </form>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary edit" data-dismiss="modal">
                            <span class='glyphicon glyphicon-check'></span> Edit
                        </button>
                        <button type="button" class="btn btn-warning" data-dismiss="modal">
                            <span class='glyphicon glyphicon-remove'></span> Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection