

<br />
@if (\Session::has('success'))
<div class="alert alert-success">
    <p>{{ \Session::get('success') }}</p>
</div><br />
@endif
<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

        <!-- Sidebar user panel (optional) -->
        @if (! Auth::guest())
        <div class="user-panel">
            <div class="pull-left image">
                <img src="{{ asset('la-assets/img/Profile_Image/')}}<?php echo '/'.((\Session::get('employee_details')['image'] != '' ) ? \Session::get('employee_details')['image'] : 'images.png'); ?>" class="img-circle" alt="User Image" style="border-radius: 50%"> 
            </div>

            <div class="pull-left info">
                <p>{{ Auth::user()->name }}</p>
                <!-- Status -->
                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>
        @endif

        <!-- search form (Optional) -->
        @if(LAConfigs::getByKey('sidebar_search'))
        <form action="#" method="get" class="sidebar-form">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Search..."/>
                <span class="input-group-btn">
                    <button type='submit' name='search' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i></button>
                </span>
            </div>
        </form>
        @endif
        <!-- /.search form -->

        <!-- Sidebar Menu -->
        <ul class="sidebar-menu">
            <li class="header">MODULES</li>
            <!-- Optionally, you can add icons to the links -->
            <li><a href="{{ url(config('laraadmin.adminRoute')) }}"><i class='fa fa-home'></i> <span>Dashboard</span></a></li>
                <?php
                $menuItems = Dwij\Laraadmin\Models\Menu::where("parent", 0)->orderBy('hierarchy', 'asc')->get();
                $role_id = DB::table('role_user')->select(['role_id'])->whereRaw('user_id = "' . Auth::user()->id . '"')->first();
                $roleMenu = DB::table('sidebar_menu_accesses')->whereRaw('role_id = ' . $role_id->role_id . ' and deleted_at IS NULL')->pluck('menu_id');
                ?>
            @foreach ($menuItems as $menu)
            @if($menu->type == "module")
            <?php
            $temp_module_obj = Module::get($menu->name);
            ?>
            @la_access($temp_module_obj->id)
            @if(isset($module->id) && $module->name == $menu->name && in_array($menu->id, $roleMenu))
            <?php echo LAHelper::print_menu($menu, true); ?>
            @else
            <?php
            if (in_array($menu->id, $roleMenu)) {
                echo LAHelper::print_menu($menu);
            }
            ?>
            @endif
            @endla_access
            @else
            <?php
            if (in_array($menu->id, $roleMenu)) {
                echo LAHelper::print_menu($menu);
            }
            ?>
            @endif
            @endforeach
            <!-- LAMenus -->

        </ul><!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
</aside>
