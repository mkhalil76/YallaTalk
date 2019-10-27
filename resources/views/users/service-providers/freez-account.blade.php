@extends('layouts.app')
@section('script')
<script type="text/javascript">
$(document).ready(function(){
$('#service-provider-table').DataTable();
});
</script>
@endsection
@section('content')
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <div class="box">
        <div class="box-header">
          <h3 class="box-title">YallaTalk Inactive Providers</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
            <div class="row">
              <div class="col-md-12">
              </div>
            </div>
            <div class="row">
              <div class="col-sm-12">
                <table class="table table-bordered table-hover" id="service-provider-table">
                  <thead>
                    <tr role="row">
                      <th>Id</th>
                      <th>User Name</th>
                      <th>Avatar</th>
                      <th>Birth Of Date</th>
                      <th>Avaliabilty</th>
                      <th>Account status</th>
                      <th>Call Type</th>
                      <th>Gender</th>
                      <th>Rating</th>
                      <td>Activiate</td>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($providers as $provider)
                    <?php $user_info = UserHelper::getUserInfo($provider->user_id)?>
                    <tr>
                      <td>{{ $provider->id }}</td>
                      <td>{{ $user_info->first_name." ".$user_info->last_name }}</td>
                      <td><img src="{{ $provider->image}}" alt="" width="60" height="60"/></td>
                      <td>{{ $provider->birth_of_date }}</td>
                      <td><?php echo UserHelper::getUserAvailability($provider->availability) ?></td>
                      <td><?php echo UserHelper::getAcountStatus($provider->account_status) ?></td>
                      <td><?php echo UserHelper::getCallType($provider->call_type) ?></td>
                      <td><?php echo UserHelper::getUserGender($provider->gender) ?></td>
                      <td>{{ $provider->rating }}</td>
                      <td><a href="{{ url('admin/service-providers/defreez-account/'.$provider->id) }}"<button class="btn btn-primary">Activiate</button></a></td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <!-- /.box-body -->
        </div>
        <!-- /.box -->
      </div>
      <!-- /.col -->
    </div>
  </section>
  @endsection