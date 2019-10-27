@extends('layouts.app')
@section('script')
<script type="text/javascript">
$(document).ready(function(){
    $('#money_table').DataTable();
});
</script>
@endsection
@section('content')
<section class="content">
   <div class="row">
   <div class="col-xs-12">
      <div class="box">
         <div class="box-header">
            <h3 class="box-title">Money Track</h3>
         </div>
         <!-- /.box-header -->
         <div class="box-body">
            <div class="row">
               <div class="col-sm-12">
                  <div class="box box-primary">
                     <div class="box-header with-border">
                     </div>
                     <!-- /.box-header -->
                     
                     <div class="box-body">
                        <table class="table table-hover" id='money_table'>
                           <thead>
                              <th>#</th>
                              <th>Call Duration</th>
                              <th>Money Spent</th>
                              <th>User Name</th>
                              <th>Time</th>
                              <th>Balance Accumulative</th>
                              <th>Status</th>
                           </thead>
                           <tbody>
                              <?php $i = 1;?>
                              @foreach($calls as $call)
                              <?php $call_price = round(UserHelper::computeMoneyForCall($call->start_at, $call->end_at, $call->service_provider_id), 2);
                              ?>
                              <tr>
                                 <td><?=$i++?></td>
                                 <td>{{ UserHelper::ParseCallTime($call->start_at, $call->end_at) }}</td>
                                 <td>{{ "$ ".$call_price }}
                                 </td>
                                 <td>{{ UserHelper::getClientName($call->client_id) }} </td>
                                 <td>{{ date("H:m a", strtotime($call->start_at)) }} </td>
                                 <td>{{ "$ ".UserHelper::getClientBalance($call->client_id) }} </td>
                                 <td>{{ UserHelper::getTransferStatus($call->id)}} </td>
                              </tr>
                              @endforeach
                           </tbody>
                        </table>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>
@endsection