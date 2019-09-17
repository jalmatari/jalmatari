<header class="main-header">
    <!-- Logo -->
    <a href="{{route_('admin')}}" class="logo">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <span class="logo-mini margin"><b><i class="fa fa-sliders margin"></i></b></span>
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg"><b>{{Funs::Setting("site_title")}}</b> </span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top" role="navigation">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </a>

        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">

                <!-- User Account: style can be found in dropdown.less -->
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <img src="{{auth()->user()->photo}}" class="user-image"
                             alt="User Image"/>
                        <span class="hidden-xs">{{auth()->user()->name}} </span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- User image -->
                        <li class="user-header">
                            <img src="{{auth()->user()->photo}}" class="img-circle"
                                 alt="User Image"/>
                            <p>
                                {{ auth()->user()->name }}
                                ( {{ Funs::GetJobTitle(auth()->user()->job_title)}} )
                                {{auth()->user()->username}}
                                <small>عضو <span class="moment" data-date="{{auth()->user()->created_at}}"></span>
                                </small>
                            </p>
                        </li>
                        <!-- Menu Body -->
                        <li class="user-body">
                            المجموعات:
                            <?php

                            $userGroups = Funs::UserGroups(auth()->id());

                            ?>
                            @foreach($userGroups as $row)
                                <span class="label user-group-lbl">{{$row["name"]}}</span>
                            @endforeach

                        </li>
                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <?php
                            /*
                    <div class="pull-right">
                        <a href="#" class="btn btn-primary"><i class="fa fa-fw fa-user"></i>الملف الشخصي</a>
                    </div>
                            */
                            ?>
                            <div class="pull-left">
                                <a href="{{route_('admin.logout')}}" class="btn btn-danger"><i
                                        class="fa fa-fw fa-sign-out"></i>تسجيل الخروج</a>
                            </div>
                        </li>
                    </ul>
                </li>
                <!-- Control Sidebar Toggle Button
                <li>
                    <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
                </li>-->
            </ul>
        </div>
    </nav>
</header>
