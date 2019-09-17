<?php

namespace Jalmatari\Http\Controllers\Core;

use Jalmatari\Models\sync;
use Jalmatari\Models\users;
use Jalmatari\User;
use DB;
use Jalmatari\Funs\Funs;
use Password;
use Schema;


class AppController extends MyBaseController
{
    public $allAbleTables = [
        "users",
        "sync",
    ];

    public function getSection()
    {
        $section = request('section');
        if (!$section)
            $header = 'No section provided!';
        else if (!in_array($section, $this->allAbleTables))
            $header = 'There Is No section With This Name!';
        else
            return $section;
        $this->dieWithMessage($header);

    }

    public function dieWithMessage($message)
    {
        header("Content-Type: text/plain");
        die($message);
    }

    public function items()
    {
        $section = $this->getSection();

        $afterId = Funs::IsInInput('id', 0);
        $perPage = Funs::IsInInput('perPage', 20);

        $page = Funs::IsInInput('page', 1);
        request()->merge([ 'page' => $page ]);

        $table = DB::table($section);
        if ($section == "users")
            $table = $table->select('id', 'name', 'show_name');
        if ($section == "sync")
            $table = $table->whereIn('table', $this->allAbleTables);
        $updated = request('updated');
        if ($updated) {
            $ids = sync::where('table', $section)
                ->where('action', 1)//updated
                ->where('id', '>', $updated)
                ->distinct()
                ->pluck('row_id');
            $table = $table->whereIn('id', $ids);
            $afterId = 0;
        }
        //if ($section == "tdbr_ayat")
        //$table = $table->select('id', 'ayah_num', 'sora_num', 'ayah_id', 'tdbr_type', 'tdbr_id', 'created_at', 'updated_at');
        $table = $table->forPageAfterId($perPage, $afterId);
        $table = $table->paginate($perPage);
        $table->id = $afterId;

        $this->setHeadersFromPaginate($table);

        $table = $table->items();

        /*if ($section == "sync") {
            $table = sync::ItemsForTable('tdbr_media', array_pluck($table, 'id'))->toArray();
        }*/

        return $table;
    }

    public function setHeadersFromPaginate($paginate)
    {
        $total = $paginate->total();
        $pages = $paginate->lastPage();
        $page = $paginate->currentPage();
        $nextPage = $page < $pages ? $page + 1 : 0;
        $perPage = $paginate->perPage();
        $currItem = $nextPage ? $page * $perPage : $total;


        header("total: " . $paginate->total());
        header("pages: " . $pages);
        header("currItem: " . $currItem);
        header("page: " . $page);
        header("nextPage: " . $nextPage);
        header("perPage: " . $paginate->perPage());
        header("id: " . $paginate->id);
    }

    public function deletedItems()
    {
        $section = $this->getSection();
        $ids = DB::table($section)
            ->select('id')
            ->orderBy('id')
            ->get()
            ->pluck('id')
            ->toArray();
        $lastId = last($ids);

        $range = range(1, $lastId);

        $range = array_diff($range, $ids);
        $ids = array_values($range);

        header("lastExistsId: " . $lastId);

        return $ids;

    }

    public function publishedItems()
    {
        $section = $this->getSection();
        $statusCol = Funs::IsInInput('col', 'status');
        if (!Schema::hasColumn($section, $statusCol))
            $this->dieWithMessage("This Section does't have Published Property");


        $ids = DB::table($section)
            ->select('id')
            ->where($statusCol, 0)
            ->get()
            ->pluck('id')
            ->toArray();

        return $ids;

    }

    public function sectionsWithNewData()
    {
        $sections = [];
        $onlyTables = $this->allAbleTables;
        if (request()->has('only')) {
            $onlyTables = explode(',', request('only'));
            $onlyTables[] = "sync";
        }
        foreach ($this->allAbleTables as $table) {
            if (in_array($table, $onlyTables)) {
                $count = 0;
                if (request()->has('updated')) {
                    $afterId = request('updated');
                    $count = sync::where('table', $table)
                        ->where('action', 1)//updated
                        ->where('id', '>', $afterId)
                        ->distinct()
                        ->get([ 'row_id' ])
                        ->count();
                }
                else if (request()->has($table)) {
                    $afterId = request($table);
                    $count = DB::table($table)
                        ->forPageAfterId(10, $afterId)
                        ->count();
                }
                if ($count)
                    $sections[ $table ] = $count;
            }
        }

        return $sections;
    }

    public function forgetPassword()
    {

        $response = Password::sendResetLink([ 'email' => request('email') ], function (Message $message) {
            $message->subject($this->getEmailSubject());
        });

        $this->dieWithMessage(trans($response));
    }

    public function newUser()
    {
        $data = request()->only([ 'email', 'password', 'name' ]);
        $validator = $validator = \Validator::make(
            $data
            , [
            'name'     => 'required|max:100',
            'email'    => 'email|unique:users|required',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return $validator->messages();
        }
        else {
            $credentials = request()->only('email', 'password', 'name');
            $credentials['password'] = \Hash::make($credentials['password']);

            $user = User::create($credentials);
            $users = users::where('username', $user->name)->count();
            $user->username = $user->name . ($users ? "-{$user->id}" : "");
            $user->api_token = str_random(60);
            $user->created_by_app = 1;
            $user->save();

            return [
                'id'    => $user->id,
                'token' => $user->api_token
            ];
        }


    }

    public function login()
    {
        if (auth()->attempt(request()->only('email', 'password'))) {
            $user = auth()->user();
            $user->api_token = str_random(60);
            $user->save();

            return [
                'name'  => $user->name,
                'id'    => $user->id,
                'token' => $user->api_token
            ];
        }

        return [ "يرجى التأكد من تطابق البريد الإلكتروني مع كلمة المرور" ];
    }

    public function newApp()
    {
        $header = "wrong os!";
        $ver = request('ver');
        $os = request('os');
        if (!$ver)
            $header = "Old version Not provided!";

        if (!$os)
            $header = "OS Not provided!";
        if ($ver && in_array($os, [ 'iphone', 'android' ])) {
            $version = explode('.', Funs::Setting("app_{$os}_version"));
            $ver = explode('.', $ver);

            $isThereNewApp = false;
            for ($i = 0; $i < count($ver); $i++) {
                if (($i == 0 && $version[0] > $ver[0]) ||
                    ($i == 1 && $version[0] == $ver[0] && $version[1] > $ver[1])) {
                    $isThereNewApp = true;
                    break;
                }
            }
            if ($isThereNewApp) {
                return [
                    'newVersion' => implode('.', $version),
                    'oldVersion' => implode('.', $ver),
                    'updateMsg'  => Funs::Setting("app_update_msg")
                ];
            }

            return false;
        }

        $this->dieWithMessage($header);

    }

    public function denouncementItem()
    {

        $itemId = request('id');
        $type = request('type');
        $type = str_replace('tdbr_', '', $type);
        $denouncement = request('denouncement');
        $userId = 0;
        if (request()->has('uid') && request('uid') >= 1)
            $userId = request('uid');
        $denouncements = denouncement::InsertDenouncement($itemId, $type, $denouncement, $userId);

        $data = [
            "denouncements" => Funs::Ar($denouncements),
            "itemId"        => $itemId,
            "type"          => $type,
        ];

        return $data;
    }

    public function shareItem()
    {
        $itemId = request('itemId');
        $type = request('type');
        $type = str_replace('tdbr_', '', $type);
        $userId = 0;
        if (request()->has('uid') && request('uid') >= 1)
            $userId = request('uid');
        $data = [
            "itemId" => $itemId,
            "type"   => $type,
            "shares" => Funs::Ar(shares::ShareItem($itemId, $type, $userId))
        ];

        return $data;
    }

    public function checkAndLoginUser($uid, $token)
    {
        $user = null;
        if (strlen($token) == 60) {
            $user = users::where('api_token', $token)
                ->where('id', $uid)
                ->first();
            if ($user)
                auth()->loginUsingId($user->id);
        }

        return $user;
    }

    public function likeItem()
    {
        $request = request();
        $userId = 0;
        if (request()->has('uid') && request('uid') >= 1)
            $userId = request('uid');

        if ($userId > 0) {
            $itemId = $request->get('itemId');
            $type = $request->get('type');
            $type = str_replace('tdbr_', '', $type);
            $like = $request->get('like');
            $tem = likes::InsertLike($itemId, $type, $like, $userId);
            $tem2 = likes::LiksUnLikesForItem($itemId, $type, !$like);
            if ($like == 1) {
                $likes = $tem;
                $unLikes = $tem2;
            }
            else {

                $likes = $tem2;
                $unLikes = $tem;
            }
            $data = [
                "likes"   => Funs::Ar($likes),
                "unLikes" => Funs::Ar($unLikes),
                "itemId"  => $itemId,
                "type"    => $type,

            ];

            return $data;
        }

        return [ 'يرجى تسجيل الدخول من أجل تنفيذ العملية!' ];
    }

    public function addTadars()
    {
        $data = request()->data;
        $data = json_decode($data, true);

        $type = $data['type'];
        $msg = "للأسف تعذر إضافة الوقفة!";
        $done = false;
        if (substr($type, 0, 5) == 'tdbr_') {
            $userId = 0;
            $user = Funs::IsIn($data, 'user', false);
            if ($user && isset($user['uid']) && isset($user['token']))
                $userId = $user['uid'];
            $userId = $userId > 0 ? $userId : 0;
            $cols = Funs::TableCols($type);
            $isExists = false;
            if ($type != "tdbr_media") {
                $tdbrTbl = 'Jalmatari\Models\\' . $type;
                $tdbrTbl = $tdbrTbl::where($cols[1], $data[ $cols[1] ])->first();
                if ($tdbrTbl) {
                    $isExists = true;
                    $msg = "الوقفة موجودة مسبقاً!";
                }
            }
            if (!$isExists) {
                $data['status'] = Funs::IsSpecialAuth('auto_publish_tdbr', users::find($userId)) ? 1 : 0;
                $data['user_id'] = $userId;
                if (isset($data['save_in_sora_name']))
                    $ayat = [ [ "ayah_id" => 0, "sora_num" => $data['save_in_sora_name'], "ayah_num" => 0 ] ];
                else
                    $ayat = $data['ayat'];

                $links = null;
                if (isset($data['links']))
                    $links = $data['links'];
                unset($data['ayat'], $data['type'], $data['ac'], $data['user'], $data['links']);

                $tdbrItem = Funs::SaveDataToTable($data, $type, null, 1);
                tdbr_ayat::saveAyatFromSite($type, $tdbrItem->id, $ayat);
                if ($links)
                    tdbr_links::saveLinksFromSite($type, $tdbrItem->id, $links);
                $done = true;
                $msg = "تم إرسال الوقفة بنجاح.\nشكراً لمساهمتك.";
            }
        }

        return [ 'msg' => $msg, 'done' => $done ];
    }

    public function tafser()
    {
        $result = "<div class='tafseer-item'><div class='qus'> عذراً، حصل خطأ عند محاولة جلب التفسير </div></div>";
        $tafserName = Funs::IsInInput("name", '');
        $page = Funs::IsInInput("page", 1);
        $isJson = request()->has('json');

        $tafser = tafser::where([ 'status' => 1, 'name' => $tafserName ])->first();
        if ($tafser) {
            $result = $isJson ? [] : "";
            $ayatForPage = quran::where('page', $page)->get();
            $counter = 1;
            foreach ($ayatForPage as $ayah) {

                $tafserTxt = $ayah->tafserByName($tafserName)->first();
                if ($isJson) {
                    $result[] = $tafserTxt;
                }
                else {
                    if ($tafserTxt)
                        $tafserTxt = $tafserTxt->text;
                    $result .= "<div class='tafseer-item' id='a{$ayah->sora_id}_{$ayah->ayah_num}'><div class='ayah'><span class='num'>"
                        . Funs::Ar($counter++)
                        . "</span> <span class='tadobr_ayah' > ﴿ "
                        . Funs::StripStr($ayah->ayah, 200)
                        . " "
                        . Funs::Ar($ayah->ayah_num)
                        . " ﴾</span> </div><div class='text-block'>{$tafserTxt}</div></div>";
                }
            }
        }
        if (!$isJson)
            die($result);

        return $result;
    }

    public function export()
    {
        $from = request()->has('from') ? request('from') : 1;
        $to = request()->has('to') ? request('to') : $from;
        $type = request()->has('type') ? request('type') : "html";
        $isPdf = $type == 'pdf';
        $tdbrTypes = tdbr_ayat::tadarsTypesWithAr();

        $types = request()->has('tdbr_types') ? explode(',', request('tdbr_types')) : array_keys($tdbrTypes);
        if ($isPdf && $to > $from + 9)
            $to = $from + 9;
        set_time_limit(50);
        $ayat = quran::whereBetween('id', [ $from, $to ])->get();

        $tdbr_types = [];
        foreach ($types as $tdbrType)
            if (isset($tdbrTypes[ $tdbrType ]))
                $tdbr_types[ $tdbrType ] = $tdbrTypes[ $tdbrType ];
        $tdbrTypes = $tdbr_types;

        $title = quran::TitleForTwoAyat($from, $to);


        $html = view('export.pdf', compact('ayat', 'tdbrTypes', 'title', 'type', 'isPdf'))->render();
        if ($type == 'word') {
            $headers = [
                "Content-type"        => "application/vnd.ms-word",
                "Content-Disposition" => "attachment;Filename=tadars.doc"
            ];
            $html = response()->make($html, 200, $headers);

            return $html;
        }
        elseif ($type == 'pdf') {
            require_once(base_path('vendor/tecnickcom/tcpdf/examples/tcpdf_include.php'));

            // create new PDF document
            $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            // set document information
            $pdf->SetCreator('Tadars.com');
            $pdf->SetAuthor('Jamal Al-matari');
            $pdf->SetTitle('Tadars');
            $pdf->SetSubject('Tadars');
            $pdf->SetKeywords('Tadars');

            // set default header data
            //dd(public_path('admini/dist/img/logo.png'));

            //$this->Image(public_path('admini/dist/img/logo.png'), 10, 10, 15, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
            //
            $pdf->SetHeaderData('logo.png', 10, "تـدارس القــرآن الكريــم", "T a d a r s . c o m");
            //dd('s');
            // set header and footer fonts
            $pdf->setHeaderFont([ 'aealarabiya', '', PDF_FONT_SIZE_MAIN ]);
            $pdf->setFooterFont([ 'aealarabiya', '', PDF_FONT_SIZE_DATA ]);

            // set default monospaced font
            $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

            // set margins
            $pdf->SetMargins(PDF_MARGIN_LEFT, 20, PDF_MARGIN_RIGHT);
            $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
            $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

            // set auto page breaks
            $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);

            // set image scale factor
            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

            // set some language dependent data:
            $lg = [];
            $lg['a_meta_charset'] = 'UTF-8';
            $lg['a_meta_dir'] = 'rtl';
            $lg['a_meta_language'] = 'ar';
            $lg['w_page'] = 'صفحة';

            // set some language-dependent strings (optional)
            $pdf->setLanguageArray($lg);

            // ---------------------------------------------------------

            // set font
            $pdf->SetFont('dejavusans', '', 12);

            // add a page
            $pdf->AddPage();
            $pdf->setRTL(true);


            $pdf->SetFont('dejavusans', '', 12);
            $html = str_replace('لله', 'للّه', $html);

            $pdf->WriteHTML($html, true, 0, true, 0);

            // ---------------------------------------------------------

            //$lastPage = $pdf->getPage();
            //$pdf->deletePage($lastPage);
            //Close and output PDF document
            $pdf->Output('tadars.pdf', 'I');
        }
        else
            die($html);
    }


    public function app($userId = null)
    {
        $userId = is_null($userId) ? 0 : $userId;
        $visits = visits::VisitUser($userId);
        $isUserSharePage = $userId >= 1;
        $otherUserShareMsg = $userShareMsg = Funs::UserSetting('share-msg');
        $otherUserShareName = $userShareName = Funs::UserSetting('share-name');
        $userLink = url('/app' . (auth()->check() ? '/' . auth()->user()->id : ''));


        if ($isUserSharePage) {
            $otherUserShareMsg = Funs::UserSetting('share-msg', null, $userId);
            $otherUserShareName = Funs::UserSetting('share-name', null, $userId);
        }
        $data = compact('visits', 'userShareMsg', 'userShareName',
            'userLink', 'isUserSharePage', 'otherUserShareMsg',
            'otherUserShareName', 'appShare','userId');

        return view('app', $data);
    }
    public function visitStore(){
        visits::VisitUser(request('user'),request('type'));
        return request('url');
    }

    public function saveUserShareMsg()
    {
        $isLoggedin = auth()->check();
        if ($isLoggedin) {
            Funs::UserSetting('share-msg', request('userMsg'));
            Funs::UserSetting('share-name', request('userName'));
        }

        return $isLoggedin;
    }
}
