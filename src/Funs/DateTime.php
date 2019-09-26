<?php

namespace Jalmatari\Funs;

use Carbon\Carbon;

trait DateTime
{

    public static function HijriNow()
    {
        return static::convertHijri(date("Y-m-d H:i:s"));
    }

    public static function HijriMonth($month = null, $justNum = false)
    {
        if (is_null($month)) {
            $Arabic = new \I18N_Arabic('Date');
            $month = $Arabic->date("m", time());
            $month = (int) $month;
            if ($justNum !== false)
                return $month;
        }
        $months = [ '--', 'محرم', 'صفر', 'ربيع الأول', 'ربيع الثاني', 'جمادى الأولى', 'جمادى الآخرة', 'رجب', 'شعبان', 'رمضان', 'شوال', 'ذو القعدة', 'ذو الحجة' ];

        return static::IsIn($months, (int) $month, '--');
    }

    public static function Hijri($date, $withTime = false)
    {
        return static::GetHjriDate($date, true, true, true, $withTime);
    }

    public static function Hjri($date, $withTime = false)
    {
        return static::GetHjriDate($date, true, true, true, $withTime);
    }

    public static function GetHjriDate($date, $day = true, $month = true, $year = true, $withTime = false)
    {
        $final_date = $date;
        if (static::BoolSetting('hjri_data_convert')) {
            $datetime = new \DateTime($date);
            $hjri_data_adjust = (int) static::Setting('hjri_data_adjust');
            $datetime->modify($hjri_data_adjust . ' day');
            $date = $datetime->format('Y-m-d H:i:s');
            $y = substr($date, 0, 4);
            $m = substr($date, 5, 2);
            $d = substr($date, 8, 2);
            $time = substr($date, 11, 8);
            if (($y > 1582) || (($y == 1582) && ($m > 10)) || (($y == 1582) && ($m == 10) && ($d > 14))) {
                $jd = (int) ((1461 * ($y + 4800 + (int) (($m - 14) / 12))) / 4) + (int) ((367 * ($m - 2 - 12 * ((int) (($m - 14) / 12)))) / 12) - (int) ((3 * ((int) (($y + 4900 + (int) (($m - 14) / 12)) / 100))) / 4) + $d - 32075;
            }
            else {
                $jd = 367 * $y - (int) ((7 * ($y + 5001 + (int) (($m - 9) / 7))) / 4) + (int) ((275 * $m) / 9) + $d + 1729777;
            }
            $l = $jd - 1948440 + 10632;
            $n = (int) (($l - 1) / 10631);
            $l = $l - 10631 * $n + 354;
            $j = ((int) ((10985 - $l) / 5316)) * ((int) ((50 * $l) / 17719)) + ((int) ($l / 5670)) * ((int) ((43 * $l) / 15238));
            $l = $l - ((int) ((30 - $j) / 15)) * ((int) ((17719 * $j) / 50)) - ((int) ($j / 16)) * ((int) ((15238 * $j) / 43)) + 29;
            $m = (int) ((24 * $l) / 709);
            $d = $l - (int) ((709 * $m) / 24);
            $y = 30 * $n + $j - 30;
            $final_date = "";
            if ($day) {
                $final_date = $d;
            }
            if ($month) {
                $final_date .= (($final_date != "") ? ' ' : '') . static::HijriMonth($m);
            }
            if ($year) {
                $final_date .= (($final_date != "") ? ' ' : '') . $y . 'هـ';
            }
            $final_date = $final_date . ($withTime ? ' &nbsp; ' . $time : '');
            $final_date = static::En2Ar($final_date);
        }

        return $final_date;

    }

    public static function convertHijri($date, $justyear = false, $justmonthnum = false, $withTime = false)
    {
        $day = $month = $year = true;
        if ($justyear) {
            $year = true;
            $day = $month = false;
        }
        elseif ($justmonthnum) {
            $month = true;
            $day = $year = false;
        }

        return static::GetHjriDate($date, $year, $day, $month, $withTime);
    }

    public function intPart($float)
    {
        if ($float < -0.0000001) {
            return ceil($float - 0.0000001);
        }
        else {
            return floor($float + 0.0000001);
        }

    }

    public function Hijri2Greg($day, $month, $year, $string = false)
    {
        $day = (int) $day;
        $month = (int) $month;
        $year = (int) $year;

        $jd = $this->$this->intPart((11 * $year + 3) / 30) + 354 * $year + 30 * $month - $this->$this->intPart(($month - 1) / 2) + $day + 1948440 - 385;

        if ($jd > 2299160) {
            $l = $jd + 68569;
            $n = $this->$this->intPart((4 * $l) / 146097);
            $l = $l - $this->$this->intPart((146097 * $n + 3) / 4);
            $i = $this->intPart((4000 * ($l + 1)) / 1461001);
            $l = $l - $this->intPart((1461 * $i) / 4) + 31;
            $j = $this->intPart((80 * $l) / 2447);
            $day = $l - $this->intPart((2447 * $j) / 80);
            $l = $this->intPart($j / 11);
            $month = $j + 2 - 12 * $l;
            $year = 100 * ($n - 49) + $i + $l;
        }
        else {
            $j = $jd + 1402;
            $k = $this->intPart(($j - 1) / 1461);
            $l = $j - 1461 * $k;
            $n = $this->intPart(($l - 1) / 365) - $this->intPart($l / 1461);
            $i = $l - 365 * $n + 30;
            $j = $this->intPart((80 * $i) / 2447);
            $day = $i - $this->intPart((2447 * $j) / 80);
            $i = $this->intPart($j / 11);
            $month = $j + 2 - 12 * $i;
            $year = 4 * $k + $n + $i - 4716;
        }

        $data = [];
        $date['year'] = $year;
        $date['month'] = $month;
        $date['day'] = $day;

        if (!$string) {
            return $date;
        }
        else {
            return "{$year}-{$month}-{$day}";
        }

    }

    public function Greg2Hijri($day, $month, $year, $string = false)
    {
        $day = (int) $day;
        $month = (int) $month;
        $year = (int) $year;

        if (($year > 1582) or (($year == 1582) and ($month > 10)) or (($year == 1582) and ($month == 10) and ($day > 14))) {
            $jd = $this->intPart((1461 * ($year + 4800 + $this->intPart(($month - 14) / 12))) / 4) + $this->intPart((367 * ($month - 2 - 12 * ($this->intPart(($month - 14) / 12)))) / 12) - $this->intPart((3 * ($this->intPart(($year + 4900 + $this->intPart(($month - 14) / 12)) / 100))) / 4) + $day - 32075;
        }
        else {
            $jd = 367 * $year - $this->intPart((7 * ($year + 5001 + $this->intPart(($month - 9) / 7))) / 4) + $this->intPart((275 * $month) / 9) + $day + 1729777;
        }

        $l = $jd - 1948440 + 10632;
        $n = $this->intPart(($l - 1) / 10631);
        $l = $l - 10631 * $n + 354;
        $j = ($this->intPart((10985 - $l) / 5316)) * ($this->intPart((50 * $l) / 17719)) + ($this->intPart($l / 5670)) * ($this->intPart((43 * $l) / 15238));
        $l = $l - ($this->intPart((30 - $j) / 15)) * ($this->intPart((17719 * $j) / 50)) - ($this->intPart($j / 16)) * ($this->intPart((15238 * $j) / 43)) + 29;

        $month = $this->intPart((24 * $l) / 709);
        $day = $l - $this->intPart((709 * $month) / 24);
        $year = 30 * $n + $j - 30;

        $date = [];
        $date['year'] = $year;
        $date['month'] = $month;
        $date['day'] = $day;

        if (!$string) {
            return $date;
        }
        else {
            return "{$year}-{$month}-{$day}";
        }

    }

    public static function HijriMiladiMonthsBetweenTwoDates($start_date = null, $end_date = null)
    {

        $Arabic = new I18N_Arabic('Date');
        $dates = [];
        if (is_null($start_date)) {
            $start_date = static::Setting('project_start_hijri_date');
        }
        if (is_null($end_date)) {
            $time = time();
            $correction = $Arabic->dateCorrection($time);
            $end_date = $Arabic->date('d/m/Y', $time, $correction);
        }
        $start_date = static::YearMonthDayAsArr($start_date);
        $end_date = static::YearMonthDayAsArr($end_date);
        $start_month = $start_date[1];
        $start_day = $start_date[2];
        //$end_month = ($start_date[0] == $end_date[0]) ? $end_date[1] : 12;
        for ($i = $start_date[0]; $i <= $end_date[0]; $i++) {
            $end_month = ($i == $end_date[0]) ? $end_date[1] : 12;
            for ($ii = $start_month; $ii <= $end_month; $ii++) {
                $end_day = $Arabic->hijriMonthDays($ii, $i);
                $dates[] = [
                    "start"     => [
                        date('Y-m-d H:i:s', static::MiladiFromHijriArr([ $i, $ii, $start_day ], $Arabic)),
                        $start_day . '/' . $ii . '/' . $i
                    ],
                    "end"       => [
                        date('Y-m-d H:i:s', static::MiladiFromHijriArr([ $i, $ii, $end_day ], $Arabic)),
                        $end_day . '/' . $ii . '/' . $i
                    ],
                    "days"      => $end_day,
                    "month"     => (int) $ii,
                    "monthName" => static::HijriMonth($ii) . ($ii == 1 ? ' ' . static::En2Ar($i) . 'هـ' : ''),
                    "year"      => (int) $i
                ];
            }
            $start_month = 1;
            $start_day = 1;
        }

        return $dates;
    }

    public static function HijriMiladiDayesBetweenTwoDates($start_date = null, $end_date = null)
    {

        $Arabic = new I18N_Arabic('Date');
        $dates = [];
        if (is_null($start_date)) {
            $start_date = static::Setting('project_start_hijri_date');
        }
        if (is_null($end_date)) {
            $time = time();
            $correction = $Arabic->dateCorrection($time);
            $end_date = $Arabic->date('d/m/Y', $time, $correction);
        }
        $start_date = static::YearMonthDayAsArr($start_date);
        $end_date = static::YearMonthDayAsArr($end_date);
        $start_month = $start_date[1];
        $start_day = $start_date[2];
        $end_month = ($start_date[0] == $end_date[0]) ? $end_date[1] : 12;
        for ($i = $start_date[0]; $i <= $end_date[0]; $i++) {
            for ($ii = $start_month; $ii <= $end_month; $ii++) {
                $end_day = $Arabic->hijriMonthDays($ii, $i);
                for ($day = $start_day; $day <= $end_day; $day++) {
                    $miladi_date = Carbon::createFromTimestamp(static::MiladiFromHijriArr([ $i, $ii, $day ], $Arabic))->startOfDay();
                    $dates[] = [
                        "miladi"     => $miladi_date->toDateTimeString(),
                        "miladi_end" => $miladi_date->copy()->endOfDay()->toDateTimeString(),
                        "hijri"      => $day . '/' . $ii . '/' . $i,
                        "day"        => (int) $day,
                        "month"      => (int) $ii,
                        "year"       => (int) $i
                    ];
                }
            }
            $start_month = 1;
            $start_day = 1;
            $end_month = ($i == $end_date[0]) ? $end_date[1] : 12;
        }

        return $dates;
    }

    public static function YearMonthDayAsArr($date)
    {
        if (!is_array($date)) {
            $date = explode('/', $date);
        }
        if (!(is_array($date) && count($date) >= 3)) {
            $date = [ 1437, 1, 1 ];
        }
        if ($date[0] < 1000) {
            $swap = $date[2];
            $date[2] = $date[0];
            $date[0] = $swap;
        }

        return $date;
    }

    public static function MiladiFromHijri($date)
    {
        $Arabic = new I18N_Arabic('Date');
        $date = static::YearMonthDayAsArr($date);
        $year = $date[0];
        $month = $date[1];
        $day = $date[2];
        $correction = $Arabic->mktimeCorrection($month, $year);
        $time = $Arabic->mktime(0, 0, 0, $month, $day, $year, $correction);

        return date('Y-m-d H:i:s', $time);
    }

    public static function TodayHijri()
    {
        $Arabic = new I18N_Arabic('Date');
        $correction = $Arabic->dateCorrection(time());

        return $Arabic->date("d/m/Y", time(), $correction);
    }

    public static function FirstDayInMonthHijri($month = 'm', $year = 'Y', $returnMiladi = false)
    {
        $Arabic = new I18N_Arabic('Date');
        $correction = $Arabic->dateCorrection(time());
        $data = $Arabic->date("1/$month/$year", time(), $correction);
        if ($returnMiladi !== false)
            $data = static::MiladiFromHijri($data);

        return $data;
    }

    public static function MiladiFromHijriArr($date, $Arabic)//$Arabic=new I18N_Arabic('Date');
    {
        $year = $date[0];
        $month = $date[1];
        $day = $date[2];
        $correction = $Arabic->mktimeCorrection($month, $year);
        $time = $Arabic->mktime(0, 0, 0, $month, $day, $year, $correction);

        return $time;
    }


    public static function DaysBetweenTwoDates($start_date = '', $end_date = '')
    {
        $start_date = strtotime($start_date);
        $end_date = strtotime($end_date);
        $datediff = $end_date - $start_date;

        return floor($datediff / 86400);//(60 * 60 * 24)
    }

    public static function Date($date)
    {
        $date=Carbon::parse($date);
        $format='ll';

        if(!is_null(setting('dateFormat')))
            $format=Funs::FirstSetting('dateFormat');

        return $date->isoFormat($format);
    }
}
