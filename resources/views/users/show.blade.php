@extends('layouts.app')

@section('content')
 <section class="content">
      <div class="row">
        <!-- left column -->
        <div class="col-md-12">
          <!-- general form elements -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">user profile</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            {{ Form::open(['url' => 'admin/users/update', 'method' => 'POST']) }}
              <div class="box-body">
                <div class="form-group col-sm-8">
                  <label for="exampleInputEmail1">Email Address</label>
                  <input type="email" name="email" class="form-control" id="user_email" value="{{ $user->email }}">
                </div>
                <div class="form-group col-sm-8">
                  <label for="mobile-no">Mobile</label>
                  <input type="text" id="mobile-no" value="{{$user->mobile}}" class="form-control" name="mobile" >
                </div>
                <div class="form-group col-sm-8">
                  <label for="firs-name">First Name</label>
                  <input type="text" id="first-name" value="{{$user->first_name}}" class="form-control" name="first_name" >
                </div>
                <div class="form-group col-sm-8">
                  <label for="last-name">Last Name</label>
                  <input type="text" class="form-control" value="{{$user->last_name}}" id="last-name" name="last_name">
                </div>
                <div class="form-group col-sm-8">
                  <label for="user-country">Country</label>
                  <input type="text" class="form-control" value="{{$user->country}}" id="user-country" name="country">
                </div>
                <div class="form-group col-sm-8">
                  <label for="user-address1">Address 1</label>
                  <input type="text" class="form-control" value="{{$user->address1}}" id="user-address1" name="address1">
                </div>
                <div class="form-group col-sm-8">
                  <label for="user-address2">Address 2</label>
                  <input type="text" class="form-control" value="{{$user->address2}}" id="user-address2" name="address2">
                </div>
                <div class="form-group col-sm-8">
                  <label for="user-password">Password</label>
                  <input type="password" name="password" class="form-control" id="user-password" placeholder="password">
                </div>
                <input type="hidden" name="user_id" value="{{ $user->id }}">
              </div>
              <!-- /.box-body -->

              <div class="box-footer ">
              	{{ Form::submit('Update',['class' => 'btn btn-primary']) }}
              </div>
            {{ Form::close() }}
          </div>
          <!-- /.box -->
        </div>
      </div>
      <!-- /.row -->
 </section>
@endsection