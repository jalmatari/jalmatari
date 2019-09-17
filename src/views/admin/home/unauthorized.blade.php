
@section('body')
    <br><br><br><br><br><br><br><br>
    <div class="error-page">
        <h2 class="headline text-yellow">
            عذراً!
        </h2>
        <div class="error-content">
            <h3><i class="fa fa-warning text-yellow"></i>
                ليس لك صلاحية عرض الصفحة!
                </h3>
            <p>
                يمكنك العودة إلى لوحة التحكم من خلال الرابط التالي:.
              <a href='{{route_('admin')}}'>
                  العودة إلى الموقع
              </a>
                أو يمكنك إستخدام النموذج التالي للبحث.
            </p>
            <form class='search-form'>
                <div class='input-group'>
                    <input type="text" name="search" class='form-control' placeholder="بحث"/>
                    <div class="input-group-btn">
                        <button type="submit" name="submit" class="btn btn-warning btn-flat"><i class="fa fa-search"></i></button>
                    </div>
                </div><!-- /.input-group -->
            </form>
        </div><!-- /.error-content -->
    </div><!-- /.error-page -->
    <br><br><br><br><br><br><br><br><br><br><br><br>
@stop