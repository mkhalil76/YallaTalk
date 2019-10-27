@extends('layouts.app')
@section('content')
<section class="content">
   <div class="row">
   <div class="col-xs-12">
      <div class="box">
         <div class="box-header">
            <h3 class="box-title">Fill Money</h3>
         </div>
         <!-- /.box-header -->
         <div class="box-body">
            <div class="row">
               <div class="col-sm-12">
                  <div class="box box-primary">
                     <div class="box-header with-border">
                     </div>
                     <!-- /.box-header -->
                     
                     {{ Form::open(['url' => 'admin/payments/post-fill-money', 'method' => 'POST','class' => 'form-horizontal']) }}
                     <div class="box-body">
                        <div class="form-group">
                        <div class="form-group col-sm-8">
                           {{ Form::label('amount', 'Amount', ['class' => 'col-sm-2 control-label']) }}
                           <div class="col-sm-6">
                           {{ Form::text('amount', null, ['class' => 'form-control', 'placeholder'=>'amount of money']) }}
                           </div>
                        </div>
                     </div>
                     <input type="hidden" name="user_id" value="{{ $user->id }}">
                     <input type="hidden" name="call_id" value="{{ $call_id }}">
                     <input type="hidden" name="call_price" value="{{ $call_price }}">
                     <div class="box-footer">
                        {{ Form::submit('Send',['class' => 'btn btn-primary']) }}
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