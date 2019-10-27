@extends('layouts.app')
@section('script')
<script type="text/javascript">
$(document).ready(function(){
$('#service-providers-table').DataTable({
   "responsive": true
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
            <h3 class="box-title">Service Providers</h3>
         </div>
         <!-- /.box-header -->
         <div class="box-body">
            <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
               <div class="row">
                  <div class="col-xs-12">
                  </div>
               </div>
               <div class="row">
                  <div class="col-xs-12">
                     <table class="table table-bordered table-hover" id="service-providers-table">
                        <thead>
                           <tr role="row">
                              <th>Id</th>
                              <th>User Name</th>
                              <th>Avatar</th>
                              <th>Avaliabilty</th>
                              <th>Account Status</th>
                              <th>Call Type</th>
                              <th>Rating</th>
                              <th>Bank name</th>
                              <th>Bank address</th>
                              <th>Swift code / BIC</th>
                              <th>Name on the account</th>
                              <th>IBAN number</th>
                              <th>Branch number</th>
                           </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1;?>
                           @foreach($providers as $provider)
                            <tr>
                              <td>{{ $i++ }}</td>
                              <td>{{ $provider->user['first_name'] }} {{ $provider->user['last_name'] }}</td>
                              <td><img src="{{ $provider->image }}" height="50" width="50" alt=""></td>
                              <td><?php echo UserHelper::getUserAvailability($provider->availability) ?></td>
                              <td><?php echo UserHelper::getAcountStatus($provider->account_status) ?></td>
                              <td><?php echo UserHelper::getCallType($provider->call_type) ?></td>
                              <td>{{ $provider->rating }}</td>
                              <td>{{ $provider->bankAccount['bank_name'] }}</td>
                              <td>{{ $provider->bankAccount['bank_address'] }}</td>
                              <td>{{ $provider->bankAccount['swift_code'] }}</td>
                              <td>{{ $provider->bankAccount['name_on_the_account'] }}</td>
                              <td>{{ $provider->bankAccount['IBAN_number'] }}</td>
                              <td>{{ $provider->bankAccount['branch_number'] }}</td>
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