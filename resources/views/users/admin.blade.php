@extends('layouts.app')
@section('script')
<script type="text/javascript">
$(document).ready(function(){
$('#admins-table').DataTable();
});
</script>
@endsection
@section('content')
<section class="content">
   <div class="row">
   <div class="col-xs-12">
      <div class="box">
         <div class="box-header">
            <h3 class="box-title">Admins</h3>
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
                     <table class="table table-bordered table-hover" id="admins-table">
                        <thead>
                           <tr role="row">
                              <th>Id</th>
                              <th>User Name</th>
                              <th>Email</th>
                              <th>Mobile</th>
                              <th>Address</th>
                           </tr>
                        </thead>
                        <tbody>
                          <?php $i = 1;?>
                          @foreach($admins as $admin)
                            <tr>
                              <td>{{ $i++ }}</td>
                              <td>{{ $admin->first_name }} {{ $admin->last_name }}</td>
                              <td>{{ $admin->email }}</td>
                              <td>{{ $admin->mobile }}</td>
                              <td>{{ $admin->address1 }} {{ $admin->address1 }}</td>
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