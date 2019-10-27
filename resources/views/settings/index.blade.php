@extends('layouts.app')
@section('content')
<section class="content">
   <div class="row">
   <div class="col-xs-12">
      <div class="box">
         <div class="box-header">
            <h3 class="box-title">Commisions</h3>
         </div>
         <!-- /.box-header -->
         <div class="box-body">
            <div class="row">
               <div class="col-sm-12">
                  <div class="box box-primary">
                     <div class="box-header with-border">
                     </div>
                     <!-- /.box-header -->
                     
                     {{ Form::open(['url' => 'admin/settings/update-commision', 'method' => 'POST','class' => 'form-horizontal']) }}
                     <div class="box-body">
                        <div class="form-group">
                        <div class="form-group col-sm-8">
                           {{ Form::label('amount', 'Commision Value', ['class' => 'col-sm-2 control-label']) }}
                           <div class="col-sm-6">
                           {{ Form::text('amount', $commision , ['class' => 'form-control', 'placeholder'=>'Commision Value']) }}
                           </div>
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
   </div>
</section>
@endsection