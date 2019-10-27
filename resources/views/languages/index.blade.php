@extends('layouts.app')

@section('script')
<script type="text/javascript">
$(document).ready(function() {
    $('#language-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{!! url('admin/languages/languages-for-datatable/') !!}',
            type:'POST',
              'headers': {
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
               },
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'name', name: 'name' },                      
            { data: 'created_at', name: 'created_at'},
            { data: 'actions', name: 'actions', searchable: false, orderable:false },          
         ]
    });
});
</script>
@endsection

@section('content')

<section class="content">
   <div class="row">
   <div class="col-xs-12">
      <div class="box">
         <div class="box-header">
            <h3 class="box-title">Languages</h3>
         </div>
         <!-- /.box-header -->
         <div class="box-body">
            <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
               <div class="row">
                  <div class="col-md-12">
                     <a href="{{ url('admin/languages/create') }}">
                     <button type="button" class="btn btn-default" id="add-new" style="margin-bottom: 28px;">
                     <i class="fa fa-plus" aria-hidden="true"></i> Create Language
                     </button>
                     </a>
                  </div>
               </div>
               <div class="row">
                  <div class="col-sm-10">
                     <table class="table table-bordered table-hover" id="language-table">
                        <thead>
                           <tr role="row">
                              <th>Id</th>
                              <th>Language</th>
                              <th>Create Date</th>
                              <th>Actions</th>
                           </tr>
                        </thead>
                        <tbody>
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