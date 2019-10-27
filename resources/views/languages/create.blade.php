@extends('layouts.app')
@section('content')
<section class="content">
   <div class="row">
   <div class="col-xs-12">
      <div class="box">
         <div class="box-header">
            <h3 class="box-title">Create New Language</h3>
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
                     {{ Form::open(['url' => 'admin/languages/create', 'method' => 'POST']) }}
                     <div class="box-body">
                        <div class="form-group col-sm-6">
                           {{ Form::label('Language Name', 'language-name') }}
                           {{ Form::text('name',null,['class' => 'form-control','id' => 'language-name']) }}
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