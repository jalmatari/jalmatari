<?php  namespace Jalmatari\Http\Middleware;

use Closure;

class AfterMiddleware {

    public function handle($request, Closure $next)
    {
        //dd('AfterMiddleware');
        $response = $next($request);
        /*$buffer = $response->getContent();
        if(strpos($buffer,'<pre>') !== false)
        {
            $replace = array(
                '/<!--[^\[](.*?)[^\]]-->/s' => '',
                "/<\?php/"                  => '<?php ',
                "/\r/"                      => '',
                "/>\n</"                    => '><',
                "/>\s+\n</"                 => '><',
                "/>\n\s+</"                 => '><',
            );
        }
        else
        {
            $replace = array(
                '/<!--[^\[](.*?)[^\]]-->/s' => '',
                "/<\?php/"                  => '<?php ',
                "/\n([\S])/"                => '$1',
                "/\r/"                      => '',
                "/\n/"                      => '',
                "/\t/"                      => '',
                "/ +/"                      => ' ',
            );
        }
        $buffer = preg_replace(array_keys($replace), array_values($replace), $buffer);
        $response->setContent($buffer);*/
        //ini_set('zlib.output_compression', 'On'); // If you like to enable GZip, too!
        return $response;
    }
}