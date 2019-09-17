<!-- Sidebar user panel -->
<div class="user-panel">
    <div class="pull-right image">
        <img src="{{auth()->user()->photo}}" class="img-circle"
             alt="User Image"/>
    </div>
    <div class="pull-left info">
        <p title="<h4>{{Funs::GetJobTitle(auth()->user()->job_title)}}</h4>" data-toggle="tooltip"
           data-placement="left" data-html="true"> {{ auth()->user()->name }}</p>

        <a href="#" title="<h5>{{Funs::GetJobTitle(auth()->user()->job_title)}}</h5>" data-toggle="tooltip"
           data-placement="left" data-html="true"><i class="fa fa-circle text-success"></i> متصل</a>
    </div>
</div>
