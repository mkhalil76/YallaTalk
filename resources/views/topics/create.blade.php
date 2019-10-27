@extends('layouts.app')
@section('content')
<section class="content">
   <div class="row">
   <div class="col-xs-12">
      <div class="box">
         <div class="box-header">
            <h3 class="box-title">Create New Topic</h3>
         </div>
         <!-- /.box-header -->
         <div class="box-body">
            <div class="row">
               <div class="col-sm-12">
                  <div class="box box-primary">
                     <div class="box-header with-border">
                     </div>
                     <!-- /.box-header -->
                     <!-- form start -->
                     {{ Form::open(['url' => 'admin/topics/create', 'method' => 'POST','enctype' => 'multipart/form-data']) }}
                     <div class="box-body">
                        <div class="form-group col-sm-8">
                           {{ Form::label('topic-name', 'Topic Name') }}
                           {{ Form::text('topic_name',null,['class' => 'form-control','id' => 'topic-name']) }}
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
         </div>
      </div>
   </div>
</section>
@endsection