@extends('admin.layouts.template')
@section('head')
    <style>
        .small-boxes .col-lg-2 col-md-3 {
            cursor: pointer;
        }

        .tasks-stat-cats {
            margin-bottom: 20px;
            border: 1px solid #00a65a;
            border-radius: 10px;
        }

        .tasks-stat-cats table {
            border: 0px;
        }

        .tasks-stat-cats table tr > :first-child {
            border-right-width: 0px;
        }

        .tasks-stat-cats table tr /*> :not(:first-child)*/
        {
            text-align: center;
        }

        .note_under-table {
            margin-bottom: 10px;
            position: relative;
            top: -15px;
        }
    </style>
@stop


@section('body')
    @if(view()->exists('admin.homeView'))
        @include('admin.homeView')
    @endif
    @if(Funs::IsAdmini())
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">الأوامر السريعة</h3>
            </div>
            <div class="box-body">
                <div class="box-header with-border">
                    <i class="fa fa-sliders"></i>
                    <h3 class="box-title">إعدادت الموقع</h3>
                </div>
                <a class="btn btn-app" href="{{route_('admin.settings')}}">
                    <i class="fa fa-sliders"></i>
                    الإعدادت
                </a>
                <a class="btn btn-app" href="{{route_('admin.permissions.edit',2)}}">
                    <i class="fa fa-edit"></i>
                    تعديل صلاحيات مدير النظام
                </a>
                <a class="btn btn-app" href="{{route_('admin.routes.add')}}">
                    <span class="badge bg-aqua"><i class="fa fa-plus"></i></span>
                    <i class="fa fa-link"></i>
                    إضافة رابط
                </a>
                <a class="btn btn-app" href="{{route_('admin.menu.add')}}">
                    <span class="badge bg-blue"><i class="fa fa-plus"></i></span>
                    <i class="fa fa-list fa-flip-horizontal"></i>
                    إضافة قائمة
                </a>
                <a class="btn btn-app" href="{{route_('admin.permissions.edit',2)}}">
                    <i class="fa fa-edit"></i> تعديل صلاحيات مدير النظام
                </a>
                <a class="btn btn-app" href="{{route_('admin.tables')}}">
                    <i class="fa fa-database"></i>
                    قاعدة البيانات
                </a>

            </div><!-- /.box-body -->
        </div><!-- /.box -->
    @endif


@stop