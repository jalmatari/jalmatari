<?php
/**
 * Created by PhpStorm.
 * User: jalmatari
 * Date: 13/7/2019
 */

namespace Jalmatari\Funs;


use Artisan;
use Auth;
use DB;
use Illuminate\Database\Eloquent\Collection;
use Jalmatari\Models\myModel;
use Jalmatari\Models\tables;
use Redirect;
use Route;

trait HelperFuns
{
    use DateTime, MyAuth, Database;


    public static function http_post($url, $data)
    {
        $data_url = http_build_query($data);
        $data_len = strlen($data_url);

        return file_get_contents($url, false, stream_context_create([ 'http' => [ 'method' => 'POST', 'header' => "Content-Type: application/x-www-form-urlencoded\r\n" . "Connection: close\r\nContent-Length: $data_len\r\n", 'content' => $data_url ] ]));
    }

    public static function strip_html($html, $sub_str = 0)
    {
        return static::StripStr($html, $sub_str);

    }

    public static function StripStr($str, $strLen = 0, $addTxt = '')
    {
        $str = strip_tags($str);
        $str = str_replace('&nbsp;', ' ', $str);
        $str = preg_replace('!\s+!', ' ', $str);
        $str = trim($str);
        if ($strLen != 0 && mb_strlen($str) > $strLen) {
            $str = mb_substr($str, 0, $strLen) . '...' . $addTxt;
        }

        return $str;

    }

    public static function StringBetweenTwoStrs($string, $start, $end)
    {
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0)
            return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;

        return substr($string, $ini, $len);
    }

    public static function Ar($number_en, $Ar2En = false)
    {
        return static::En2Ar($number_en, $Ar2En);
    }

    public static function En2Ar($number_en, $Ar2En = false)
    {
        $_en = [ '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' ];
        $_arabic = [ '٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩', '١٠' ];
        $number_ar = "";
        for ($i = 0, $maxI = strlen($number_en); $i < $maxI; $i++) {
            $v = mb_substr($number_en, $i, 1);
            $idx = array_search($v, $_en);
            if ($Ar2En) {
                $idx = array_search($v, $_arabic);
            }

            if ($idx !== false) {
                $number_ar .= ($Ar2En ? $_en[ $idx ] : $_arabic[ $idx ]);
            }
            else {
                $number_ar .= $v;
            }
        }

        return $number_ar;
    }


    public static function Form($type, $arr = [])
    {
        if (isset($arr[3]['disabled']) && is_array($arr[3]['disabled'])) {
            return static::CallStaticFun('SelectWithMulti', $arr);
        }
        if ($type == 'json') {
            return static::CallStaticFun('jsonFormFiled', $arr);
        }
        if ($type == 'number') {
            $arr = array_merge([ 'number' ], $arr);
            $type = 'input';
        }

        return call_user_func_array([ 'Form', $type ], $arr);

    }

    public static function jsonFormFiled($name, $text = null, $fun_to_call = [])
    {
        $html = '';
        if (isset($fun_to_call['class']) && substr($fun_to_call['class'], 0, 3) == "fun") {
            $fun_name = explode(' ', $fun_to_call['class']);
            $fun_name = $fun_name[0];
            $html .= static::CallStaticFun($fun_name, [ $name, $text ]);
        }
        else {
            $arr = json_decode($text);
            foreach ($arr as $row) {
                $html .= $row;
            }
        }

        return $html;
    }

    public static function CallTableFun($table, $fun_name, $pars = [])
    {
        $data = static::CallModelFun($table, $fun_name, $pars);
        if (is_null($data))
            $data = static::CallStaticFun($fun_name, $pars);

        return $data;

    }

    public static function CallStaticFun($fun_name, $pars = [])
    {
        if (is_null($pars))
            $pars = [];
        if (!is_array($pars))//may its one pbject
            $pars = [ $pars ];
        $data = null;
        if (method_exists(static::class, $fun_name))
            $data = call_user_func_array([ 'static', $fun_name ], $pars);

        return $data;
    }

    public static function CallModelFun($modelName, $fun_name, $pars = [])
    {
        if (is_null($pars))
            $pars = [];
        if (!is_array($pars))//may its one pbject
            $pars = [ $pars ];
        $table = tables::where('name', $modelName)->first();

        $data = null;

        if (method_exists($table->namespace . $modelName, $fun_name))
            $data = call_user_func_array([ $table->namespace . $modelName, $fun_name ], $pars);

        return $data;
    }

    /**
     * Create a select box field.
     *
     * @param string $name
     * @param array $list
     * @param string $selected
     * @param array $options
     * @return string
     */
    public static function SelectWithMulti($name, $list = [], $selected = null, $options = [])
    {
        $html = '';
        $disabled = [];
        if (isset($options['disabled'])) {
            $disabled = $options['disabled'];
            unset($options['disabled']);
        }

        foreach ($list as $value => $display) {
            $html .= '<option value="' . $value . '" ' . static::getSelectedValue($value, $selected) . ' ' . static::getDisabledValue($value, $disabled) . ' >' . $display . '</option>';
        }
        $optionsHtml = '';
        foreach ($options as $attr => $val) {
            $optionsHtml .= ' ' . $attr . '="' . $val . '"';
        }

        return '<select name="' . $name . '" ' . $optionsHtml . '>' . $html . '</select>';
    }

    /**
     * Determine if the value is selected.
     *
     * @param string $value
     * @param string $selected
     * @return string
     */
    public static function getSelectedValue($value, $selected)
    {
        if (is_array($selected)) {
            return in_array($value, $selected) ? 'selected' : null;
        }

        return ((string) $value == (string) $selected) ? 'selected' : null;
    }

    /**
     * Determine if the value is selected.
     *
     * @param string $value
     * @param string $selected
     * @return string
     */
    public static function getDisabledValue($value, $selected)
    {
        if (is_array($selected)) {
            return in_array($value, $selected) ? 'disabled' : null;
        }

        return ((string) $value == (string) $selected) ? 'disabled' : null;
    }


    public static function IsIn($arr, $key, $else = null)
    {
        $val = $else;
        $key = (string) $key;
        $isCollection = false;
        if (is_object($arr))
            $isCollection = get_class($arr) == 'Illuminate\Database\Eloquent\Collection';
        if (!is_array($arr) && isset($arr->{$key}))
            $val = $arr->{$key};
        else if ((is_array($arr) || $isCollection) && isset($arr[ $key ]))
            $val = $arr[ $key ];

        return $val;
    }

    public static function IsInInput($value, $else = false)
    {
        if (request()->has($value))
            $value = request($value);
        else
            $value = $else;

        return $value;
    }

    public static function InInput($value, $else = false)
    {

        return static::IsInInput($value, $else);
    }

    public static function AddKeyValueToArr($arr)
    {
        $finalArr = [];
        foreach ($arr as $key => $val) {
            $finalArr[] = [ 'key' => $key, 'value' => $val ];
        }

        return $finalArr;
    }

    public static function ArrayByValuesAsKeys($arr)
    {
        $arr = array_combine($arr, $arr);

        return $arr;
    }

    public static function GetRandomColor($start = 0, $end = 255)
    {
        return sprintf("#%02X%02X%02X", mt_rand($start, $end), mt_rand($start, $end), mt_rand($start, $end));
    }

    public static function GetFromUrl($url)
    {
        $ch = curl_init();
        $timeout = 30;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
        //return file_get_contents($url);
    }

    public static function UnDuplicateArray($modelArr = [])
    {
        if (is_a($modelArr, Collection::class))
            $modelArr = $modelArr->toArray();

        elseif (!is_array($modelArr))
            $modelArr = (array) $modelArr;


        $modelArr = array_unique($modelArr, SORT_REGULAR);
        $modelArr = array_values($modelArr);

        return $modelArr;
    }

    public static function GetUnDuplicateArr($modelArr = [])
    {
        return static::UnDuplicateArray($modelArr);
    }

    public static function TableBtn($color = 'primary', $href = 'href="#"', $title = '', $faIcon = 'magic', $str = '')
    {
        return '<a class="btn btn-' . $color . '" ' . $href . ' data-toggle="tooltip" title=" ' . $title . '">'
            . '<i class="fa fa-' . $faIcon . '"></i> ' . $str . ' </a>';
    }

    public static function GetArrWithFirstItem($arr, $firstIndex = 0)
    {
        if (is_array($firstIndex) && count($firstIndex) >= 1)
            $firstIndex = array_values($firstIndex)[0];
        if (!is_string($firstIndex) && !is_int($firstIndex))
            $firstIndex = (int) $firstIndex;
        if (array_key_exists($firstIndex, $arr)) {
            $swapVal = $arr[ $firstIndex ];
            unset($arr[ $firstIndex ]);
            $arr = [ $firstIndex => $swapVal ] + $arr;
        }

        return $arr;
    }

    public static function GetArrWithFirstItemFromObject($theObject, $first_index = 0, $key = 'id', $val = 'title')
    {
        if (!is_array($theObject))
            $theObject = $theObject->toArray();
        if (!is_array($theObject))
            $theObject = [];
        $theObject = array_column($theObject, $val, $key);
        $theObject = static::GetArrWithFirstItem($theObject, $first_index);

        return $theObject;
    }

    public static function HexByColorName($colorName)
    {
        $colors = [
            'blue'       => '357ca5',
            'light-blue' => '357ca5',
            'aqua'       => '00a7d0',
            'green'      => '008d4c',
            'yellow'     => 'db8b0b',
            'red'        => 'd33724',
            'gray'       => 'b5bbc8',
            'navy'       => '001a35',
            'teal'       => '30bbbb',
            'purple'     => '555299',
            'orange'     => 'ff7701',
            'maroon'     => 'ca195a',
            'black'      => '000',
        ];

        return '#' . static::IsIn($colors, $colorName, 'bbb');
    }


    public static function Percent($total, $num)
    {
        $percent = $total == 0 ? $num : round((100 / $total) * $num, 2);

        return $percent;
    }

    public static function tremStr($str, $strDel = '')
    {
        $str = trim($str, " \t\n\r\0\x0B{$strDel}");

        return $str;
    }

    public static function ListArr($list, $addArr = [], $toEnd = false)
    {
        if (!is_array($list)) {
            $list = array_values((array) $list);
            $list = static::IsIn($list, 0, []);
        }
        if (!$toEnd)
            $list = $addArr + $list;
        else
            $list = $list + $addArr;

        return $list;
    }

    public static function ListWithAll($list)
    {
        return static::ListArr($list, [ 0 => 'الجميع' ]);
    }

    public static function PrepareForREGX($word)
    {
        $chars = [];
        if (mb_strlen($word) > 1) {
            do {
                $c = mb_strlen($word);
                $chars[] = mb_substr($word, 0, 1);
                $word = mb_substr($word, 1, $c - 1);
            } while (!empty($word));
        }
        else {
            $chars = [ $word ];
        }
        $chars = array_map(function ($one_char) {
            return $one_char . '(َ|ً|ُ|ِ|ٍ|ْ|ّ|ٌ|)(َ|ً|ُ|ِ|ٍ|ْ|ّ|ٌ|)';
        }, $chars);

        return '((^|\s)' . implode('', $chars) . '($|\s))';

    }

    public static function HighlightWords($words = [], $str = '')
    {
        if (!is_array($words))
            $words = [ $words ];
        $search_words_regx = [];
        foreach ($words as $word) {
            $search_words_regx[] = static::PrepareForREGX($word);
        }
        foreach ($search_words_regx as $search_word_regx) {
            $str = preg_replace($search_word_regx, '<mark>$0</mark>', $str);
        }

        return $str;
    }

    public static function ReplaceSqlTashkel($col)
    {
        return "replace(replace(replace(replace(replace(replace(replace(replace(`$col`,'َ',''),'ً',''),'ُ',''),'ِ',''),'ٍ',''),'ْ',''),'ّ',''),'ٌ','')";
    }

    public static function ClearTashkel($word)
    {
        return str_replace([ 'َ', 'ً', 'ُ', 'ِ', 'ٍ', 'ْ', 'ّ', 'ٌ', '\'', '"' ], '', $word);
    }

    public static function Controllers($withNamespace = false)
    {

        $controllers = get_declared_classes();
        $path = app_path('Http/Controllers');
        $files = \File::allFiles($path);
        foreach ($files as $file) {
            $className = app()->getNamespace() . 'Http\Controllers\\';
            $className .= $file->getRelativePathname();
            $className = str_replace([ '/', '.php' ], [ '\\', '' ], $className);
            $controllers[] = $className;
        }
        $ignoredClasses = [ 'MyBaseController', 'ResetPasswordController', 'RegisterController', 'LoginController', 'ForgotPasswordController', 'Controller', 'VerificationController' ];
        $controllers = array_filter($controllers, function ($class) use ($ignoredClasses) {
            $class = explode('\\', $class);
            $class = last($class);
            $isController = substr($class, -10) == 'Controller';
            $isNotFromIgnored = !in_array($class, $ignoredClasses);


            return $isController && $isNotFromIgnored;
        });

        if (!$withNamespace)
            $controllers = array_map(function ($controller) {
                return last(explode('\\', $controller));
            }, $controllers);

        $controllers = array_values($controllers);

        return $controllers;
    }

    public static function Models($withNamespace = false)
    {
        $models = array_filter(get_declared_classes(), function ($class) {
            $myModel_ = myModel::class;
            $isModel = strpos($class, '\Models\\') !== false && $class != $myModel_;

            return $isModel;
        });

        if (!$withNamespace)
            $models = array_map(function ($model) {
                return last(explode('\\', $model));
            }, $models);

        $models = array_values($models);

        return $models;
    }

    public static function Artisan($command, $args = [])
    {
        Artisan::call($command, $args);
    }

    public static function ArrayWithoutEmptyValues($arr)
    {
        return array_filter($arr, function ($value) { return $value !== ''; });
    }

    public static function SaveFromUrl($url, $toPath = "download", $fileName = null)
    {
        $newFIleName = $toPath . '/' . static::FileNameInPath($url, $toPath, $fileName);

        $file = static::GetFromUrl($url);
        file_put_contents(public_path($newFIleName), $file);

        return $newFIleName;
    }

    public static function FileNameInPath($file, $path, $newFileName = null, $ext = null)
    {
        $fileName = $file;
        if (is_null($ext)) {
            $fileInfo = static::FileExtension($file);
            $fileName = $fileInfo['filename'];
            $ext = $fileInfo['extension'];

        }
        if (!is_null($newFileName))
            $fileName = $newFileName;

        $newFileName .= '.' . $ext;
        $path = public_path($path);

        $i = 1;
        while (file_exists($path . '/' . $newFileName))
            $newFileName = $fileName . '-' . $i++ . '.' . $ext;

        return $newFileName;
    }

    public static function FileExtension($file, $fileInfo = true)
    {
        $ext = pathinfo($file);
        $ext_ = explode('?', $ext['extension']);
        if (!$fileInfo)
            return $ext_[0];

        $ext['extension'] = $ext_[0];


        return $ext;
    }

    public static function RandomSrting($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[ rand(0, $charactersLength - 1) ];
        }

        return $randomString;
    }

    public static function LastVideoInYoutubeChannel($channelId = null)
    {

        if (is_null($channelId))
            $channelId = static::Setting('youtube-channel-id');
        $appKey = static::Setting('google-youtube-key');
        $url = "https://www.googleapis.com/youtube/v3/search?order=date&part=snippet&channelId={$channelId}&key={$appKey}&maxResults=1";
        $video = static::GetFromUrl($url);
        $video = json_decode($video)->items[0];
        $video = (object) [
            'id'          => $video->id->videoId,
            'title'       => $video->snippet->title,
            'date'        => $video->snippet->publishedAt,
            'description' => $video->snippet->description,
            'channel'     => $video->snippet->channelTitle,

        ];

        return $video;
    }

    public static function paginateFooter($paginator, $append = [])
    {
        $last = ($paginator->currentPage() * $paginator->perPage());
        $first = $last - $paginator->perPage() + 1;
        $i = $first;

        $total = $paginator->total();
        $html = '<div class="pagination-btns">' . $paginator->appends($append)->links() . '</div>
                 <div class="pagination-results-text">' . __('Show From :first to :last Of Total :total', [ 'first' => $first, 'last' => $last, 'total' => $total ]) . '</div>';
        $paginateFooter = (object) [
            'last'  => $last,
            'first' => $first,
            'i'     => $i,
            'total' => $total,
            'html'  => $html

        ];

        return $paginateFooter;
    }

    public static function YoutubeIdFromUrl($url)
    {
        return preg_replace(
            '/\s*[a-zA-Z\/\/:\.]*youtu(be.com\/watch\?v=|.be\/)([a-zA-Z0-9\-_]+)([a-zA-Z0-9\/\*\-\_\?\&\;\%\=\.]*)/i',
            '$2',
            $url
        );
    }

    public static function YoutubeIframeFromUrl($url, $start = 0, $end = 0)
    {
        $endStr = "";
        if (strrpos($url, '<iframe') !== false)//its allready iframe
            return $url;
        $ytId = static::YoutubeIdFromUrl($url);
        if ($start <= 1)
            $start = static::GetSecondsFromYouTubeUrl($url);

        if ($end > 1)
            $endStr = "&end=" . $end;

        $iframe = '<iframe src="//www.youtube.com/embed/' . $ytId . '?start=' . $start . $endStr . '"   width="560" height="315" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>';

        return $iframe;
    }

    public static function GetSecondsFromYouTubeUrl($url)
    {
        $start = 0;
        $str = '';
        if (strrpos($url, '?t=') !== false)
            $str = '?t=';
        elseif (strrpos($url, '&t=') !== false)
            $str = '&t=';
        if ($str != '') {
            $url = explode($str, $url);
            $url = $url[ count($url) - 1 ];
            $url = explode('&', $url);
            $url = $url[0];
            if (strrpos($url, 'h') !== false) {//hour
                $tem = explode('h', $url);
                $url = count($tem) > 1 ? $tem[1] : '';
                $start = $tem[0] * 60 * 60;

            }
            if (strrpos($url, 'm') !== false) {//ment
                $tem = explode('m', $url);
                $url = count($tem) > 1 ? $tem[1] : '';
                $start += $tem[0] * 60;
            }
            if (strrpos($url, 's') !== false) {//ment
                $tem = explode('s', $url);
                //$url = count($tem) > 1 ? $tem[1] : '';
                $start += $tem[0];
            }
        }

        return $start;

    }

    public static function dd()
    {
        dd(get_class_methods(new static()));
    }

    // Script end
    public static function rutime($ru, $rus, $index)
    {
        return ($ru["ru_$index.tv_sec"] * 1000 + intval($ru["ru_$index.tv_usec"] / 1000))
            - ($rus["ru_$index.tv_sec"] * 1000 + intval($rus["ru_$index.tv_usec"] / 1000));
    }
}
