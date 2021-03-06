<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * The Hijri calendar type.
 *
 * @package    calendartype_hijri
 * @copyright  2008 onwards Foodle Group {@link http://foodle.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace calendartype_hijri;
use core_calendar\type_base;
use core_calendar\type_factory;

defined('MOODLE_INTERNAL') || die();
define('CALENDARTYPE_HIJRI_ALGORITHM_A', 0);
define('CALENDARTYPE_HIJRI_ALGORITHM_B', 1);

/**
 * Handles calendar functions for the hijri calendar.
 *
 * @package calendartype_hijri
 * @copyright 2008 onwards Foodle Group {@link http://foodle.org}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class structure extends type_base {

    /** @var float the islamic epoch */
    private $islamicepoch = 1948439.5;

    /** @var float the gregorian epoch */
    private $gregorianepoch = 1721425.5;

    /**
     * Returns the name of the calendar.
     *
     * This is the non-translated name, usually just
     * the name of the folder.
     *
     * @return string the calendar name
     */
    public function get_name() {
        return 'hijri';
    }

    /**
     * Returns a list of all the possible days for all months.
     *
     * This is used to generate the select box for the days
     * in the date selector elements. Some months contain more days
     * than others so this function should return all possible days as
     * we can not predict what month will be chosen (the user
     * may have JS turned off and we need to support this situation in
     * Moodle).
     *
     * @return array the days
     */
    public function get_days() {
        $days = array();

        for ($i = 1; $i <= 30; $i++) {
            $days[$i] = $i;
        }

        return $days;
    }

    /**
     * Returns a list of all the names of the months.
     *
     * @return array the month names
     */
    public function get_months() {
        $months = array();

        for ($i = 1; $i <= 12; $i++) {
            $months[$i] = get_string('month' . $i, 'calendartype_hijri');
        }

        return $months;
    }

    /**
     * Returns the minimum year of the calendar.
     *
     * @return int The minumum year
     */
    public function get_min_year() {
        return 1317;
    }

    /**
     * Returns the maximum year of the calendar.
     *
     * @return int The maximum year
     */
    public function get_max_year() {
        return 1473;
    }

    /**
     * Returns an array of years.
     *
     * @param int $minyear
     * @param int $maxyear
     * @return array the years
     */
    public function get_years($minyear = null, $maxyear = null) {
        if (is_null($minyear)) {
            $minyear = $this->get_min_year();
        }

        if (is_null($maxyear)) {
            $maxyear = $this->get_max_year();
        }

        $years = array();
        for ($i = $minyear; $i <= $maxyear; $i++) {
            $years[$i] = $i;
        }

        return $years;
    }

    /**
     * Returns a multidimensional array with information for day, month, year
     * and the order they are displayed when selecting a date.
     * The order in the array will be the order displayed when selecting a date.
     * Override this function to change the date selector order.
     *
     * @param int $minyear The year to start with
     * @param int $maxyear The year to finish with
     * @return array Full date information
     */
    public function get_date_order($minyear = null, $maxyear = null) {
        $dateinfo = array();
        $dateinfo['day'] = $this->get_days();
        $dateinfo['month'] = $this->get_months();
        $dateinfo['year'] = $this->get_years($minyear, $maxyear);

        return $dateinfo;
    }

    /**
     * Returns the number of days in a week.
     *
     * @return int the number of days
     */
    public function get_num_weekdays() {
        return 7;
    }

    /**
     * Returns an indexed list of all the names of the weekdays.
     *
     * The list starts with the index 0. Each index, representing a
     * day, must be an array that contains the indexes 'shortname'
     * and 'fullname'.
     *
     * @return array array of days
     */
    public function get_weekdays() {
        return array(
            0 => array(
                'shortname' => get_string('wday0', 'calendartype_hijri'),
                'fullname' => get_string('weekday0', 'calendartype_hijri')
            ),
            1 => array(
                'shortname' => get_string('wday1', 'calendartype_hijri'),
                'fullname' => get_string('weekday1', 'calendartype_hijri')
            ),
            2 => array(
                'shortname' => get_string('wday2', 'calendartype_hijri'),
                'fullname' => get_string('weekday2', 'calendartype_hijri')
            ),
            3 => array(
                'shortname' => get_string('wday3', 'calendartype_hijri'),
                'fullname' => get_string('weekday3', 'calendartype_hijri')
            ),
            4 => array(
                'shortname' => get_string('wday4', 'calendartype_hijri'),
                'fullname' => get_string('weekday4', 'calendartype_hijri')
            ),
            5 => array(
                'shortname' => get_string('wday5', 'calendartype_hijri'),
                'fullname' => get_string('weekday5', 'calendartype_hijri')
            ),
            6 => array(
                'shortname' => get_string('wday6', 'calendartype_hijri'),
                'fullname' => get_string('weekday6', 'calendartype_hijri')
            ),
        );
    }

    /**
     * Returns the index of the starting week day.
     *
     * This may vary, for example some may consider Monday as the start of the week,
     * where as others may consider Sunday the start.
     *
     * @return int
     */
    public function get_starting_weekday() {
        global $CFG;

        if (isset($CFG->calendar_startwday)) {
            $firstday = $CFG->calendar_startwday;
        } else {
            $firstday = get_string('firstdayofweek', 'langconfig');
        }

        if (!is_numeric($firstday)) {
            $startingweekday = 6; // Saturday.
        } else {
            $startingweekday = intval($firstday) % 7;
        }

        return get_user_preferences('calendar_startwday', $startingweekday);
    }

    /**
     * Returns the index of the weekday for a specific calendar date.
     *
     * @param int $year
     * @param int $month
     * @param int $day
     * @return int
     */
    public function get_weekday($year, $month, $day) {
        $gdate = $this->convert_to_gregorian($year, $month, $day);
        return intval(date('w', mktime(12, 0, 0, $gdate['month'], $gdate['day'], $gdate['year'])));
    }

    /**
     * Returns the number of days in a given month.
     *
     * @param int $year
     * @param int $month
     * @return int the number of days
     */
    public function get_num_days_in_month($year, $month) {
        $nextmonth = $this->get_next_month($year, $month);
        $temp = $this->convert_to_gregorian($nextmonth[1], $nextmonth[0], 1);
        $temp = $this->convert_from_gregorian($temp['year'], $temp['month'], $temp['day'] - 1);

        return $temp['day'];
    }

    /**
     * Get the previous month.
     *
     * If the current month is Muharram, it will get the last month of the previous year.
     *
     * @param int $year
     * @param int $month
     * @return array previous month and year
     */
    public function get_prev_month($year, $month) {
        if ($month == 1) {
            return array(12, $year - 1);
        } else {
            return array($month - 1, $year);
        }
    }

    /**
     * Get the next month.
     *
     * If the current month is Dhu al-Hijja, it will get the first month of the following year.
     *
     * @param int $year
     * @param int $month
     * @return array the following month and year
     */
    public function get_next_month($year, $month) {
        if ($month == 12) {
            return array(1, $year + 1);
        } else {
            return array($month + 1, $year);
        }
    }

    /**
     * Returns a formatted string that represents a date in user time.
     *
     * Returns a formatted string that represents a date in user time
     * <b>WARNING: note that the format is for strftime(), not date().</b>
     * Because of a bug in most Windows time libraries, we can't use
     * the nicer %e, so we have to use %d which has leading zeroes.
     * A lot of the fuss in the function is just getting rid of these leading
     * zeroes as efficiently as possible.
     *
     * If parameter fixday = true (default), then take off leading
     * zero from %d, else maintain it.
     *
     * @param int $time the timestamp in UTC, as obtained from the database
     * @param string $format strftime format
     * @param int|float|string $timezone the timezone to use
     *        {@link http://docs.moodle.org/dev/Time_API#Timezone}
     * @param bool $fixday if true then the leading zero from %d is removed,
     *        if false then the leading zero is maintained
     * @param bool $fixhour if true then the leading zero from %I is removed,
     *        if false then the leading zero is maintained
     * @return string the formatted date/time
     */
    public function timestamp_to_date_string($time, $format, $timezone, $fixday, $fixhour) {
        global $CFG;

        $amstring = get_string('am', 'calendartype_hijri');
        $pmstring = get_string('pm', 'calendartype_hijri');
        $amcapsstring = get_string('am_caps', 'calendartype_hijri');
        $pmcapsstring = get_string('pm_caps', 'calendartype_hijri');

        if (empty($format)) {
            $format = get_string('strftimedaydatetime', 'langconfig');
        }

        if (!empty($CFG->nofixday)) { // Config.php can force %d not to be fixed.
            $fixday = false;
        }

        $hdate = $this->timestamp_to_date_array($time, $timezone);
        // This is not sufficient code, change it. But it works correctly.
        $format = str_replace(array(
            '%a',
            '%A',
            '%d',
            '%b',
            '%B',
            '%h',
            '%m',
            '%C',
            '%y',
            '%Y',
            '%p',
            '%P'
        ), array(
            $hdate['weekday'],                                                  // For %a
            $hdate['weekday'],                                                  // %A
            (($hdate['mday'] < 10 && !$fixday) ? '0' : '') . $hdate['mday'],    // %d
            $hdate['month'],                                                    // %b
            $hdate['month'],                                                    // %B
            $hdate['month'],                                                    // %h
            ($hdate['mon'] < 10 ? '0' : '') . $hdate['mon'],                    // %m
            floor($hdate['year'] / 100),                                        // %C
            $hdate['year'] % 100,                                               // %y
            $hdate['year'],                                                     // %Y
            ($hdate['hours'] < 12 ? $amcapsstring : $pmcapsstring),             // %p
            ($hdate['hours'] < 12 ? $amstring : $pmstring)                      // and %P.
        ), $format);

        $gregoriancalendar = type_factory::get_calendar_instance('gregorian');
        return $gregoriancalendar->timestamp_to_date_string($time, $format, $timezone, $fixday, $fixhour);
    }

    /**
     * Given a $time timestamp in GMT (seconds since epoch), returns an array that
     * represents the date in user time.
     *
     * @param int $time Timestamp in GMT
     * @param float|int|string $timezone offset's time with timezone, if float and not 99, then no
     *        dst offset is applied {@link http://docs.moodle.org/dev/Time_API#Timezone}
     * @return array an array that represents the date in user time
     */
    public function timestamp_to_date_array($time, $timezone = 99) {
        $gregoriancalendar = type_factory::get_calendar_instance('gregorian');

        $date = $gregoriancalendar->timestamp_to_date_array($time, $timezone);
        $hdate = $this->convert_from_gregorian($date['year'], $date['mon'], $date['mday']);

        $date['month'] = get_string("month{$hdate['month']}", 'calendartype_hijri');
        $date['weekday'] = get_string("weekday{$date['wday']}", 'calendartype_hijri');
        $date['yday'] = ($hdate['month'] - 1) * 29 + intval($hdate['month'] / 2) + $hdate['day'];
        $date['year'] = $hdate['year'];
        $date['mon'] = $hdate['month'];
        $date['mday'] = $hdate['day'];

        return $date;
    }

    /**
     * Provided with a day, month, year, hour and minute in Gregorian
     * convert it into the equivalent Hijri date using the preferred algorithm..
     *
     * @param int $year
     * @param int $month
     * @param int $day
     * @param int $hour
     * @param int $minute
     * @return array the converted date
     */
    public function convert_from_gregorian($year, $month, $day, $hour = 0, $minute = 0) {
        static $algorithm = null;
        if ($algorithm === null) {
            $algorithm = get_config('calendartype_hijri', 'algorithm');
        }

        if ($algorithm == CALENDARTYPE_HIJRI_ALGORITHM_A) {
            $date = $this->convert_from_gregorian_algorithm_a($year, $month, $day, $hour, $minute);
        } else {
            $date = $this->convert_from_gregorian_algorithm_b($year, $month, $day, $hour, $minute);
        }

        return $date;
    }

    /**
     * Provided with a day, month, year, hour and minute in Hijri
     * convert it into the equivalent Gregorian date using the preferred algorithm.
     *
     * @param int $year
     * @param int $month
     * @param int $day
     * @param int $hour
     * @param int $minute
     * @return array the converted date
     */
    public function convert_to_gregorian($year, $month, $day, $hour = 0, $minute = 0) {
        static $algorithm = null;
        if ($algorithm === null) {
            $algorithm = get_config('calendartype_hijri', 'algorithm');
        }

        if ($algorithm == CALENDARTYPE_HIJRI_ALGORITHM_A) {
            $date = $this->convert_to_gregorian_algorithm_a($year, $month, $day, $hour, $minute);
        } else {
            $date = $this->convert_to_gregorian_algorithm_b($year, $month, $day, $hour, $minute);
        }

        return $date;
    }

    /**
     * This return locale for windows os.
     *
     * @return string locale
     */
    public function locale_win_charset() {
        return 'utf-8';
    }


    /* --------------------- Algorithm A set of functions --------------------- */

    /**
     * Provided with a day, month, year, hour and minute in Gregorian
     * convert it into the equivalent Hijri date using algorithm A.
     *
     * @param int $year
     * @param int $month
     * @param int $day
     * @param int $hour
     * @param int $minute
     * @return array the converted date
     */
    private function convert_from_gregorian_algorithm_a($year, $month, $day, $hour = 0, $minute = 0) {
        $jd = $this->gregorian_to_jd($year, $month, $day);
        $date = $this->jd_to_hijri($jd);
        $date['hour'] = $hour;
        $date['minute'] = $minute;

        return $date;
    }

    /**
     * Provided with a day, month, year, hour and minute in Hijri
     * convert it into the equivalent Gregorian date using algorithm A.
     *
     * @param int $year
     * @param int $month
     * @param int $day
     * @param int $hour
     * @param int $minute
     * @return array the converted date
     */
    private function convert_to_gregorian_algorithm_a($year, $month, $day, $hour = 0, $minute = 0) {
        $jd = $this->hijri_to_jd($year, $month, $day);
        $date = $this->jd_to_gregorian($jd);
        $date['hour'] = $hour;
        $date['minute'] = $minute;

        return $date;
    }

    /**
     * Convert given Julian day into Hijri date.
     *
     * @param int $jd the Julian day
     * @return array the Hijri date
     */
    private function jd_to_hijri($jd) {
        $jd = floor($jd) + 0.5;
        $year = floor(((30 * ($jd - $this->islamicepoch)) + 10646) / 10631);
        $month = min(12, ceil(($jd - (29 + $this->hijri_to_jd($year, 1, 1))) / 29.5) + 1);
        $day = ($jd - $this->hijri_to_jd($year, $month, 1)) + 1;

        $date = array();
        $date['year'] = $year;
        $date['month'] = $month;
        $date['day'] = $day;

        return $date;
    }

    /**
     * Convert given Hijri date into Julian day.
     *
     * @param int $year the year
     * @param int $month the month
     * @param int $day the day
     * @return int
     */
    private function hijri_to_jd($year, $month, $day) {
        return ($day + ceil(29.5 * ($month - 1)) + ($year - 1) * 354 + floor((3 + (11 * $year)) / 30) + $this->islamicepoch) - 1;
    }

    /**
     * Converts a Gregorian date to Julian Day Count.
     *
     * @param int $year the year
     * @param int $month the month
     * @param int $day the day
     * @return int the Julian Day for the given Gregorian date
     */
    private function gregorian_to_jd($year, $month, $day) {
        $part1 = $this->gregorianepoch - 1;
        $part2 = 365 * ($year - 1);
        $part3 = floor(($year - 1) / 4);
        $part4 = -floor(($year - 1) / 100);
        $part5 = floor(($year - 1) / 400);
        $part6 = floor((((367 * $month) - 362) / 12) + (($month <= 2) ? 0 : ($this->leap_gregorian($year) ? -1 : -2)) + $day);
        return $part1 + $part2 + $part3 + ($part4) + $part5 + $part6;
    }

    /**
     * Returns true if the Gregorian year supplied is a leap year.
     *
     * @param int $year
     * @return bool
     */
    private function leap_gregorian($year) {
        return (($year % 4) == 0) && (!((($year % 100) == 0) && (($year % 400) != 0)));
    }

    /**
     * Converts a JD to Gregorian date.
     *
     * @param int $jd the Julian Day
     * @return array the Gregorian date
     */
    private function jd_to_gregorian($jd) {
        $wjd = floor($jd - 0.5) + 0.5;
        $depoch = $wjd - $this->gregorianepoch;
        $quadricent = floor($depoch / 146097);
        $dqc = $depoch % 146097;
        $cent = floor($dqc / 36524);
        $dcent = $dqc % 36524;
        $quad = floor($dcent / 1461);
        $dquad = $dcent % 1461;
        $yindex = floor($dquad / 365);
        $year = ($quadricent * 400) + ($cent * 100) + ($quad * 4) + $yindex;
        if (!(($cent == 4) || ($yindex == 4))) {
            $year++;
        }
        $yearday = $wjd - $this->gregorian_to_jd($year, 1, 1);
        $leapadj = (($wjd < $this->gregorian_to_jd($year, 3, 1)) ? 0 : ($this->leap_gregorian($year) ? 1 : 2));
        $month = floor(((($yearday + $leapadj) * 12) + 373) / 367);
        $day = ($wjd - $this->gregorian_to_jd($year, $month, 1)) + 1;

        $date = array();
        $date['year'] = $year;
        $date['month'] = $month;
        $date['day'] = $day;

        return $date;
    }


    /* --------------------- Algorithm B set of functions --------------------- */

    /**
     * Provided with a day, month, year, hour and minute in Gregorian
     * convert it into the equivalent Hijri date using algorithm B.
     *
     * @param int $year
     * @param int $month
     * @param int $day
     * @param int $hour
     * @param int $minute
     * @return array the converted date
     */
    private function convert_from_gregorian_algorithm_b($year, $month, $day, $hour = 0, $minute = 0) {
        $delta = 1;

        if (($year > 1582) || (($year == 1582) && ($month > 10)) || (($year == 1582) && ($month == 10) && ($day > 14))) {
            $part1 = $this->int_part((1461 * ($year + 4800 + $this->int_part(($month - 14) / 12))) / 4);
            $part2 = $this->int_part((367 * ($month - 2 - 12 * ($this->int_part(($month - 14) / 12)))) / 12);
            $part3 = $this->int_part((3 * ($this->int_part(($year + 4900 + $this->int_part(($month - 14) / 12)) / 100))) / 4);
            $jd = $part1 + $part2 - $part3 + $day - 32075 + $delta; // Added delta=1 on jd to comply isna ruling 2007.
        } else {
            $part1 = $this->int_part((7 * ($year + 5001 + $this->int_part(($month - 9) / 7))) / 4);
            $part2 = $this->int_part((275 * $month) / 9);
            $jd = 367 * $year - $part1 + $part2 + $day + 1729777 + $delta;  // Added +1 on jd to comply isna ruling.
        }

        $l = $jd - 1948440 + 10632;
        $n = $this->int_part(($l - 1) / 10631);
        $l = $l - 10631 * $n + 354;

        $part1 = $this->int_part((10985 - $l) / 5316);
        $part2 = $this->int_part((50 * $l) / 17719);
        $part3 = $this->int_part($l / 5670);
        $part4 = $this->int_part((43 * $l) / 15238);
        $j = $part1 * $part2 + $part3 * $part4;

        $part1 = $this->int_part((30 - $j) / 15);
        $part2 = $this->int_part((17719 * $j) / 50);
        $part3 = $this->int_part($j / 16);
        $part4 = $this->int_part((15238 * $j) / 43);
        $l = $l - $part1 * $part2 - $part3 * $part4 + 29;

        $m = $this->int_part((24 * $l) / 709);
        $d = $l - $this->int_part((709 * $m) / 24);
        $y = 30 * $n + $j - 30;

        $date = array(
            'year'   => $y,
            'month'  => $m,
            'day'    => $d,
            'hour'   => $hour,
            'minute' => $minute
        );

        return $date;
    }

    /**
     * Provided with a day, month, year, hour and minute in Hijri
     * convert it into the equivalent Gregorian date using algorithm B.
     *
     * @param int $year
     * @param int $month
     * @param int $day
     * @param int $hour
     * @param int $minute
     * @return array the converted date
     */
    private function convert_to_gregorian_algorithm_b($year, $month, $day, $hour = 0, $minute = 0) {
        $delta = 1;

        // Added - delta=1 on jd to comply isna rulling.
        $part1 = $this->int_part((11 * $year + 3) / 30);
        $part2 = $this->int_part(($month - 1) / 2);
        $jd = $part1 + 354 * $year + 30 * $month - $part2 + $day + 1948440 - 385 - $delta;

        if ($jd > 2299160 ) {
            $l = $jd + 68569;
            $n = $this->int_part((4 * $l) / 146097);
            $l = $l - $this->int_part((146097 * $n + 3) / 4);
            $i = $this->int_part((4000 * ($l + 1)) / 1461001);
            $l = $l - $this->int_part((1461 * $i) / 4) + 31;
            $j = $this->int_part((80 * $l) / 2447);
            $d = $l - $this->int_part((2447 * $j) / 80);
            $l = $this->int_part($j / 11);
            $m = $j + 2 - 12 * $l;
            $y = 100 * ($n - 49) + $i + $l;
        } else {
            $j = $jd + 1402;
            $k = $this->int_part(($j - 1) / 1461);
            $l = $j - 1461 * $k;
            $n = $this->int_part(($l - 1) / 365) - $this->int_part($l / 1461);
            $i = $l - 365 * $n + 30;
            $j = $this->int_part((80 * $i) / 2447);
            $d = $i - $this->int_part((2447 * $j) / 80);
            $i = $this->int_part($j / 11);
            $m = $j + 2 - 12 * $i;
            $y = 4 * $k + $n + $i - 4716;
        }

        $date = array(
            'year'   => $y,
            'month'  => $m,
            'day'    => $d,
            'hour'   => $hour,
            'minute' => $minute
        );

        return $date;
    }

    /**
     * Convert a float number into an integer safely.
     *
     * @param float $floatnum
     *
     * @return float
     */
    private function int_part($floatnum) {
        if ($floatnum < -0.0000001) {
            return ceil($floatnum - 0.0000001);
        }
        return floor($floatnum + 0.0000001);
    }
}
