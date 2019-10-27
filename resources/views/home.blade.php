@extends('layouts.app')

@section('content')
<section class="container">
    <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <a href="{{ url('admin/service-providers/show-inactive-accounts') }}"><span class="info-box-icon bg-aqua"><i class="fas fa-snowflake"></i></span></a>

            <div class="info-box-content">
              <span class="info-box-text">Freez Accounts</span>
              <span class="info-box-number">{{ UserHelper::CountFreezdAccounts() }}<small></small></span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <a href="{{url('admin/admins/appointments')}}">
            <span class="info-box-icon bg-orange"><i class="far fa-calendar"></i></span>
            </a>
            <div class="info-box-content">
              <span class="info-box-text">Calender</span>
              <span class="info-box-number"></span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->

        <!-- fix for small devices only -->
        <div class="clearfix visible-sm-block"></div>

        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-green"><i class="fa fa-headphones "></i></span>

            <div class="info-box-content">
              <span class="info-box-text">Active Calls</span>
              <span class="info-box-number">{{ UserHelper::countActiveCalls() }}</span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-yellow"><i class="fa fa-user-circle"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">New Members</span>
              <span class="info-box-number">2,000</span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
      </div>
          <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <a href="{{ url('admin/payments/track-money') }}"><span class="info-box-icon bg-red"><i class="fas fa-dollar-sign"></i></span></a>

            <div class="info-box-content">
              <span class="info-box-text">Tracking Money</span>
              <span class="info-box-number"><small></small></span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <a href="{{url('admin/packeges/index')}}">
            <span class="info-box-icon bg-orange"><i class="fa fa-calendar"></i></span>
            </a>
            <div class="info-box-content">
              <span class="info-box-text">Packeges</span>
              <span class="info-box-number">{{ UserHelper::packegesCount() }}</span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->

        <!-- fix for small devices only -->
        <div class="clearfix visible-sm-block"></div>

        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <a href="{{url('admin/payments/refund')}}">
            <span class="info-box-icon bg-green"><i class="fas fa-credit-card"></i></span>
            </a>
            <div class="info-box-content">
              <span class="info-box-text">Refund Requests</span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <a href={{ url('admin/payments/transfer-money') }} >
            <span class="info-box-icon bg-yellow"><i class="far fa-money-bill-alt"></i></span>
            </a>
            <div class="info-box-content">
              <span class="info-box-text">Transfer Money</span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Report</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <div class="btn-group">
                  <button type="button" class="btn btn-box-tool dropdown-toggle" data-toggle="dropdown">
                    <i class="fa fa-wrench"></i></button>
                  <ul class="dropdown-menu" role="menu">
                    <li><a href="#">Action</a></li>
                    <li><a href="#">Another action</a></li>
                    <li><a href="#">Something else here</a></li>
                    <li class="divider"></li>
                    <li><a href="#">Separated link</a></li>
                  </ul>
                </div>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <div class="row">
                <!-- /.col -->
                <div class="col-md-12">
                  <p class="text-center">
                    <strong>Appotmint Status</strong>
                    <?php $statistic = UserHelper::appotmentStatistic()?>
                  </p>
                  <!-- /.progress-group -->
                  <div class="progress-group">
                    <span class="progress-text">Rejected</span>
                    <span class="progress-number"><b>{{ $statistic['REJECTED'] }}
                    </b></span>

                    <div class="progress sm">
                      <?php 
                      if($statistic['ALL'] == 0) {
                        $rate = 0;
                      } else {
                        $rate = ($statistic['REJECTED']/$statistic['ALL'])*100; 
                      }?>
                      <div class="progress-bar progress-bar-red" style="width: {{$rate}}%"></div>
                    </div>
                  </div>
                  <!-- /.progress-group -->
                  <div class="progress-group">
                    <span class="progress-text">Aproved</span>
                    <span class="progress-number"><b>{{ $statistic['APPROVED'] }}</b></span>
                    <?php 
                    if($statistic['ALL'] == 0){
                      $rate = 0;
                    } else {
                      $rate = ($statistic['APPROVED']/$statistic['ALL'])*100;
                    }
                     ?>
                    
                    <div class="progress sm">
                      <div class="progress-bar progress-bar-green" style="width: {{$rate}}%"></div>
                    </div>
                  </div>
                  <!-- /.progress-group -->
                  <div class="progress-group">
                    <span class="progress-text">Pending</span>
                    <span class="progress-number"><b>{{ $statistic['PENDING'] }}</b></span>
                    <?php 
                    if($statistic['ALL'] == 0) {

                    } else {
                      $rate = ($statistic['PENDING']/$statistic['ALL'])*100;
                      } ?>
                    <div class="progress sm">
                      <div class="progress-bar progress-bar-yellow" style="width: {{ $rate }}%"></div>
                    </div>
                  </div>
                  <!-- /.progress-group -->
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->
            </div>
            <!-- ./box-body -->
            <!-- /.box-footer -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
      </div>
</section>

@endsection
