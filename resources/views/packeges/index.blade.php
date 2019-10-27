@extends('layouts.app')

@section('script')
<script type="text/javascript">
$(document).ready(function(){
    $('#packeges-table').DataTable();
});
</script>
@endsection

@section('content')

<section class="content">
   <div class="row">
   <div class="col-xs-12">
      <div class="box">
         <div class="box-header">
            <h3 class="box-title">Packeges</h3>
         </div>
         <!-- /.box-header -->
         <div class="box-body">
            <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
               <div class="row">
                  <div class="col-md-12">
                     <a href="{{ url('admin/packeges/create') }}">
                     <button type="button" class="btn btn-default" id="add-new" style="margin-bottom: 28px;">
                     <i class="fa fa-plus" aria-hidden="true"></i> Create New Packege
                     </button>
                     </a>
                  </div>
               </div>
               <div class="row">
                  <div class="col-sm-10">
                     <table class="table table-bordered table-hover" id="packeges-table">
                        <thead>
                           <tr role="row">
                              <th>Id</th>
                              <th>Name</th>
                              <th>Hours</th>
                              <th>Start At</th>
                              <th>End At</th>
                              <th>Price</th>
                              <th>Discount</th>
                              <th>Description</th>
                              <th>Number Of Users</th>
                              <th>Action</th>
                           </tr>
                        </thead>
                        <tbody>
                          <?php $i = 1;?>
                          @foreach($packeges as $packege)
                            <tr>
                              <td><?=$i++?></td>
                              <td>{{ $packege->name }}</td>
                              <td>{{ $packege->hours }}</td>
                              <td>{{ $packege->created_at->toDateString() }}</td>
                              <td>{{ $packege->expiry_date }}</td>
                              <td>{{ $packege->price }}</td>
                              <td>{{ $packege->discount }}</td>
                              <td>{{ $packege->description }}</td>
                              <td></td>
                              <td>
                               <div class="btn-toolbar" role="toolbar" aria-label="Toolbar with button groups">
                               <div class="btn-group mr-2" role="group" aria-label="First group">
                                 <a href="{{ url('admin/packeges/update/'.$packege->id) }}"><button class="btn btn-primary">update</button></a>
                                 </div>
                                 <div class="btn-group mr-2" role="group" aria-label="First group">
                                 <a href="{{ url('admin/packeges/delete/'.$packege->id) }}"><button class="btn btn-danger">delete</button></a>
                                 </div>
                              </div>
                              </td>
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