<?php 
    $faker =  Faker\Factory::create();
?>


<section class="sidebar">
    <!-- Sidebar user panel -->
    <div class="user-panel">
    <div class="pull-left image">
        <img src="{{ $faker->imageUrl(160,160) }}" class="img-circle" alt="User Image">
    </div>
    <div class="pull-left info">
        <p>{{ Auth::user()->first_name }}</p>
        <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
    </div>
    </div>
    <!-- search form -->
    <!-- /.search form -->
    <!-- sidebar menu: : style can be found in sidebar.less -->
    <ul class="sidebar-menu" data-widget="tree">
    <li class="header">MAIN NAVIGATION</li>
    <li class="treeview">
        <a href="#">
        <i class="fas fa-users"></i>
        <span>Users</span>
        <span class="pull-right-container">
        </span>
        </a>
        <ul class="treeview-menu">
        <li><a href="{{ url('admin/clients') }}"><i class="fas fa-users"></i> Clients</a></li>
        <li><a href="{{ url('admin/service-providers')}}"><i class="fas fa-users"></i> Service Provider</a></li>
        <li ><a href="{{ url('admin/admins') }}"><i class="fas fa-users"></i> Admin </a>
        </li>
        </ul>
    </li>
    <li>
        <a href="{{ url('admin/topics') }}">
        <i class="fa fa-th"></i> <span>Topics</span>
        <span class="pull-right-container">
        </span>
        </a>
    </li>
    <li>
        <a href="{{ url('admin/languages') }}">
        <i class="fa fa-laptop"></i>
        <span>Languages</span>
        <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
        </span>
        </a>
    </li>
    <li>
        <a href="{{ url('admin/admins/appointments') }}">
        <i class="fa fa-calendar"></i> <span>Appointment</span>
        </a>
    </li>
    <li>
        <a href="{{ url('admin/settings') }}">
        <i class="fas fa-cogs"></i> <span>Commisions</span>
        </a>
    </li>
    </ul>
</section>
