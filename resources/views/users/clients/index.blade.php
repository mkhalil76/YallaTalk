@extends('layouts.app')
@section('script')
<script type="text/javascript">
$(document).ready(function(){
$('#clients-table').DataTable();
});
</script>
@endsection
@section('content')
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <div class="box">
        <div class="box-header">
          <h3 class="box-title">Clients</h3>
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
                <table class="table table-bordered table-hover" id="clients-table">
                  <thead>
                    <tr role="row">
                      <th>Id</th>
                      <th>User Name</th>
                      <th>Avatar</th>
                      <th>Birth Of Date</th>
                      <th>Avaliabilty</th>
                      <th>Account Status</th>
                      <th>gender</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $i = 1;?>
                    @foreach($clients as $client)
                    <?php $user_info = UserHelper::getUserInfo($client->user_id);?>
                    <tr>
                      <td>{{ $i++ }}</td>
                      <td>{{$user_info->first_name}} {{ $user_info->last_name}}</td>
                      <td><img src="{{ $client->image }}" height="50" width="50" alt=""></td>
                      <td>{{ $client->birth_of_date }}</td>
                      <td><?php echo UserHelper::getUserAvailability($client->availability) ?></td>
                      <td><?php echo UserHelper::getAcountStatus($client->account_status) ?></td>
                      <td><?php echo UserHelper::getUserGender($client->gender) ?></td>
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