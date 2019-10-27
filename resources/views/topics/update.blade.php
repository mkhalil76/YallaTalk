@extends('layouts.app')
@section('content')
<section class="content">
   <div class="row">
      <div class="box">
         <div class="box-header">
            <h3 class="box-title">Update {{ $topic->topic_name }} Topic</h3>
         </div>
         <!-- /.box-header -->
         <div class="box-body">
            <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
               <div class="row">
                  <div class="col-sm-6"></div>
                  <div class="col-sm-6"></div>
               </div>
               <div class="row">
                  <div class="col-sm-12">
                     {{ Form::open(['url' => 'admin/topics/update/'.$topic->id, 'method' => 'POST','enctype' => 'multipart/form-data']) }}
                     <div class="box-body">
                        <div class="form-group col-sm-8">
                           {{ Form::label('topic-name', 'Topic Name') }}
                           {{ Form::text('topic_name',$topic->topic_name,['class' => 'form-control','id' => 'topic-name']) }}
                        </div>
                        <div class="form-group col-sm-8">
                           <label for="topic-icon">Topic Icon</label>
                           <input type="file" id="topic-icon" name="topic_icon">
                        </div>
                     </div>
                     <div class="box-footer">
                        {{ Form::submit('Save',['class' => 'btn btn-primary']) }}
                     </div>
                     {{ Form::close() }}
                  </div>
               </div>
            </div>
            <!-- /.box-body -->
         </div>
         <!-- /.box -->
      </div>
      <!-- /.col -->
   </div>
   </div>
   </div>
</section>
@endsection