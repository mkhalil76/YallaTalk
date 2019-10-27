@extends('layouts.app')
@section('script')
<script type="text/javascript">
      jQuery(document).ready(function() {
        $( ".datepicker" ).datepicker({
                changeMonth: true,
                changeYear: true,
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
            <h3 class="box-title">Update Packege</h3>
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
                     {{ Form::open(['url' => 'admin/packeges/post-update-packege', 'method' => 'POST','class' => 'form-horizontal']) }}
                     <div class="box-body">
                        <div class="form-group">
                        <div class="form-group col-sm-8">
                           {{ Form::label('name', 'Package Name',['class' => 'col-sm-2 control-label']) }}
                           <div class="col-sm-10">
                           {{ Form::text('name',$packege->name,['class' => 'form-control', 'placeholder' => 'enter packege name']) }}
                           </div>
                        </div>
                        <div class="form-group col-sm-8">
                           {{ Form::label('discount', 'Discount',['class' => 'col-sm-2 control-label']) }}
                           <div class="col-sm-8">
                           {{ Form::text('discount',$packege->discount,['class' => 'form-control', 'placeholder' => 'enter the discount']) }}
                           </div>
                           <div class="col-sm-2"><span>%</span></div>
                        </div>
                        <div class="form-group col-sm-8">
                           {{ Form::label('price', 'Price', ['class' => 'col-sm-2 control-label']) }}
                           <div class="col-sm-10">
                           {{ Form::text('price',$packege->price,['class' => 'form-control', 'placeholder' => 'enter the price']) }}
                           </div>
                        </div>
                        <div class="form-group col-sm-8">
                           {{ Form::label('hours', 'Hours',['class' => 'col-sm-2 control-label']) }}
                           <div class="col-sm-10">
                           {{ Form::text('hours',$packege->hours,['class' => 'form-control', 'placeholder' => 'enter the number of hours']) }}
                           </div>
                        </div>
                        <input type="hidden" name="packege_id" value="{{$packege->id}}">
                        <div class="form-group col-sm-8">
                           {{ Form::label('expiry_date', 'Expiry Date',['class' => 'col-sm-2 control-label']) }}
                           <div class="col-sm-10">
                           {{ Form::text('expiry_date',$packege->expiry_date,['class' => 'form-control datepicker' , 'placeholder' => 'enter expiry date']) }}
                           </div>
                        </div>
                        <div class="form-group col-sm-8">
                           {{ Form::label('description', 'Description', ['class' => 'col-sm-2 control-label']) }}
                           <div class="col-sm-6">
                           {{ Form::textarea('description', $packege->description, ['class' => 'form-control', 'placeholder'=>'packege description']) }}
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
</section>
@endsection