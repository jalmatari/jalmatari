<aside class="main-sidebar">
    <section class="sidebar">
        @include('admin.layouts.userPanel')
        <form action="{{route_('admin.search')}}" method="get" class="sidebar-form">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="بحث..." value="{{request('q')}}"/>
                <span class="input-group-btn">
                                <button type='submit' id='search-btn' class="btn btn-flat">
                                    <i class="fa fa-search"></i></button>
                            </span>
            </div>
        </form>
        <ul class="sidebar-menu">
            <li class="header">الواجهة الرئيسية</li>
            <?php
            $Mainmenu = Funs::AdminMenus();
            ?>
            @foreach($Mainmenu as $menu)
                {!! Funs::PrintMenu($menu) !!}
            @endforeach
            <li class="header">/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\</li>
            <li>
                <a href="{{route_('clearCache')}}">
                    <i class="fa fa-refresh"></i><span>تنظيف الكاش</span>
                </a>
            </li>
        </ul>
    </section>
</aside>
