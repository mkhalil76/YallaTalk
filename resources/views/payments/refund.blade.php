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
               <h3 class="box-title">Refund Requests</h3>
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
                                 <th>Refund Amount</th>
                                 <th>User Name</th>
                                 <th>User Balance</th>
                                 <th>Request Date</th>
                                 <th>Status</th>
                                 <th>Action</th>
                              </thead>
                              <tbody>
                                 <?php $i = 1;?>
                                 @foreach($refunds as $refund)
                                 <?php $user_info = UserHelper::getUserInfo($refund->user_id) ?>
                                 <tr>
                                    <td><?=$i++?></td>
                                    <td>{{ $refund->amount." $" }}</td>
                                    <td>{{ $user_info->first_name." ".$user_info->last_name }}</td>
                                    <td>{{ $refund->current_balance }}</td>
                                    <td>{{ date("Y-m-d", strtotime($refund->created_at)) }}</td>
                                    <td>{{ UserHelper::getRefundStatus($refund->status) }}</td>
                                    @if($refund->status == 0)
                                    <td>
                                       <div class="btn-toolbar" role="toolbar" aria-label="Toolbar with button groups">
                                          <div class="btn-group mr-2" role="group" aria-label="First group">
                                             <a href="{{ url('admin/payments/accept-refund/'.$refund->id) }}"><button class="btn btn-success">Approve</button></a>
                                          </div>
                                          <div class="btn-group mr-2" role="group" aria-label="Second group">
                                             <a href="{{ url('admin/payments/reject-refund/'.$refund->id) }}"><button class="btn btn-danger">Reject</button></a>
                                          </div>
                                       </div>
                                    </td>
                                    @else
                                    <td></td>
                                    @endif
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