<div class="btn-group">
    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">الأوامر الجماعية
        <span class="caret"></span>
        <span class="sr-only">Toggle Dropdown</span>
    </button>
    <ul class="dropdown-menu" role="menu">
        <li>
            <a class="action-to-multi" href="#" id="publish_all" data-type="publish">
                <i class="fa fa-check"></i>
                نشر
            </a>
        </li>
        <li>
            <a class="action-to-multi" href="#" id="un_publish_all" data-type="un_publish">
                <i class="fa fa-times"></i>
                إلغاء نشر
            </a>
        </li>
        <li><a class="action-to-multi" href="#" id="delete_all" data-type="delete">
                <i class="fa fa-trash"></i>
                حذف
            </a>
        </li>
        @yield('multi-btns')
    </ul>
</div>