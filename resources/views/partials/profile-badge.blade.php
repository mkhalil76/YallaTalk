<?php 
    $faker =  Faker\Factory::create();
?>
<li class="dropdown user user-menu">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
        <img src="{{ $faker->imageUrl(160,160) }}" class="user-image" alt="User Image">
        <span class="hidden-xs">{!! Auth::user()->first_name !!}</span>
    </a>
    <ul class="dropdown-menu">
        <!-- User image -->
        <li class="user-header">
            <img src="{{ $faker->imageUrl(160,160) }}" class="img-circle" alt="User Image">

            <p>
                {!! Auth::user()->first_name !!}
                <small>Member since {!! Auth::user()->created_at->diffForHumans() !!}</small>
            </p>
        </li>
        <!-- Menu Body -->

        <!-- Menu Footer-->
        <li class="user-footer">
            <div class="pull-left">
                <a href="{{url('admin/users/profile/'.Auth::user()->id)}}" class="btn btn-default btn-flat">Profile</a>
            </div>
            <div class="pull-right">
                <form name="logout" action="{{ route('logout') }}" method="post">
                    {{ csrf_field() }}
                    <a href="javascript:document.logout.submit();" class="btn btn-default btn-flat">Sign out</a>
                </form> 
            </div>
        </li>
    </ul>
</li>
