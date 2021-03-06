@extends('layouts.app')

@section('script')
<script type="text/javascript">
$(document).ready(function(){
    $('#topics-table').DataTable();
});
</script>
@endsection

@section('content')
<section class="content">
   <div class="row">
   <div class="col-xs-12">
      <div class="box">
         <div class="box-header">
            <h3 class="box-title">Topics</h3>
         </div>
         <!-- /.box-header -->
         <div class="box-body">
            <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
               <div class="row">
                  <div class="col-md-12">
                     <a href="{{ url('admin/topics/create') }}">
                     <button type="button" class="btn btn-default" id="add-new" style="margin-bottom: 28px;">
                     <i class="fa fa-plus" aria-hidden="true"></i> Create Topic
                     </button>
                     </a>
                  </div>
               </div>
               <div class="row">
                  <div class="col-sm-8">
                     <table class="table table-bordered table-hover" id="topics-table">
                        <thead>
                           <tr role="row">
                              <th>Id</th>
                              <th>Topic Name</th>
                              <th>Topic Icon</th>
                              <th>Create Date</th>
                              <th>Actions</th>
                           </tr>
                        </thead>
                        <tbody>
                          <?php $i = 1;?>
                         @foreach($topics as $topic)
                          <tr>
                            <td>{{ $i++ }}</td>
                            <td>{{ $topic->topic_name }}</td>
                            <td><img src="{{ $topic->icon }}"
                              width="120" height="120"></td>
                            <td>{{ date("Y-m-d", strtotime($topic->created_at)) }}</td>
                            <td>
                            <div class="btn-toolbar" role="toolbar" aria-label="Toolbar with button groups">
                              <div class="btn-group mr-2" role="group" aria-label="First group">
                              <a href="{{ url('admin/topics/update/'.$topic->id) }}"><button class="btn btn-primary btn-sm">Update</button></a>
                              </div>
                              <div class="btn-group mr-2" role="group" aria-label="Second group">
                              <a href="{{ url('admin/topics/delete/'.$topic->id) }}"><button class="btn btn-danger btn-sm">Delete</button></a>
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