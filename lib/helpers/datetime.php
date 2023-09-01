<?php

if (!function_exists('fa_date_user'))
{
    /**
     * Return user date in Human Mode
     *
     * @access public
     * @param string 	$fmt format type
     * @param integer 	$time Unix timestamp
     * 
     * @return string
     */
    function fa_date_user($fmt = 'DATE_RFC822', $time = '')
    {
        $formats = array(
            'IT_DATETIME_SEC' => 'd/m/Y H:i:s',
            'IT_DATETIME' => 'd/m/Y H:i',
            'IT_DATE' => 'd/m/Y',
            'IT_TIME' => 'H:i'
        );

        if (!isset($formats[$fmt]))
        {
            return false;
        }

        if ($time == '')
        {
            return false;
        }

        return date($formats[$fmt], strtotime($time));
    }
}


if (!function_exists('fa_date_to_sql'))
{
    /**
     * Convert date dd/mm/YYYY in Sql format YYYY-mm-dd
     * 
     * @param string $date
     * @param string $delimiter
     * 
     * @return string
     */
    function fa_date_to_sql($date = NULL, $delimiter = '/')
    {
        if ($date === NULL OR $date === '')
        {
            return false;
        }

        if (fa_date_is_valid($date))
        {
            return $date;
        }

        if (preg_match('@^[0-9]{1,2}' . $delimiter . '[0-9]{1,2}' . $delimiter . '[0-9]{4}$@', $date))
        {
            $date = explode($delimiter, $date);
            return $date[2] . '-' . $date[1] . '-' . $date[0];
        }
        
        return false;
    }
}

if (!function_exists('fa_datetime_to_sql'))
{
    /**
     * Convert datetime dd/mm/YYYY HH:ii in Sql format YYYY-mm-dd HH:ii:ss
     * 
     * @param string $datetime
     * @param string $delimiter
     * 
     * @return string
     */
    function fa_datetime_to_sql($datetime = NULL, $delimiter = '/')
    {
        if ($datetime === NULL OR $datetime === '')
        {
            return false;
        }

        if (fa_datetime_is_valid($datetime))
        {
            return $datetime;
        }

        if (preg_match('/^[0-9]{1,2}\\' . $delimiter . '[0-9]{1,2}\\' . $delimiter . '[0-9]{4} [0-9]{2}\:[0-9]{2}\:[0-9]{2}$/', $datetime))
        {
            $datetime = explode($delimiter, $datetime);
            $time = explode(' ', $datetime[2]);
            $datetime[2] = $time[0];

            list($hour, $minutes) = explode(':', $time[1]);
            return $datetime[2] . '-' . $datetime[1] . '-' . $datetime[0] . ' ' . $hour . ':' . $minutes . ':' . (!empty($secods) ? $secods : ($minutes == '59' ? '59' : '00'));
        }
        else if (preg_match('/^[0-9]{1,2}\\' . $delimiter . '[0-9]{1,2}\\' . $delimiter . '[0-9]{4} [0-9]{2}\:[0-9]{2}$/', $datetime))
        {
            $datetime = explode($delimiter, $datetime);
            $time = explode(' ', $datetime[2]);
            $datetime[2] = $time[0];

            list($hour, $minutes) = explode(':', $time[1]);
            return $datetime[2] . '-' . $datetime[1] . '-' . $datetime[0] . ' ' . $hour . ':' . $minutes . ':' . (!empty($secods) ? $secods : ($minutes == '59' ? '59' : '00'));
        }

        return false;
    }
}

if (!function_exists('fa_sql_to_date'))
{
    /**
     * Convert sql date in other format
     * 
     * @param string $datetime
     * @param string $fomrat, default "%A %d %B %Y"
     * 
     * @return string
     */
    function fa_sql_to_date($date = NULL, $format = '%A %d %B %Y')
    {
        if ($date === NULL OR $date === '')
        {
            return false;
        }

        $time = strtotime($date);

        if (!$time)
        {
            return false;
        }


        $day_number = date("N", $time);
        $year_low = date("y", $time);
        $year_upp = date("Y", $time);

        $day = intval(date("d", $time));
        $month = intval(date("m", $time));

        if (!checkdate($month, $day, $year_upp))
        {
            return false;
        }

        if (strstr($format, '%a') !== false OR strstr($format, '%A') !== false)
        {
            $short = strstr($format, '%a') !== false ? true : false;
            $search = $short ? '%a' : '%A';
            $replace = fa_date_day_name($day_number, $short);
            $format = str_replace($search, $replace, $format);
        }

        if (strstr($format, '%b') !== false OR strstr($format, '%B') !== false)
        {
            $short = strstr($format, '%b') !== false ? true : false;
            $search = $short ? '%b' : '%B';
            $replace = fa_date_month_name($month, $short);
            $format = str_replace($search, $replace, $format);
        }

        $month_sql = $month;

        if ($month <= 9)
        {
            $month_sql = '0' . $month;
        }

        $day_sql = $day;

        if ($day <= 9)
        {
            $day_sql = '0' . $day;
        }

        $hours_12 = $hours_24 = $minutes = $seconds = 0;

        if (strstr($date, ' ') !== false)
        {
            $hours_12 = date('h', strtotime($date));
            $hours_24 = date('H', strtotime($date));
            $minutes = date('i', strtotime($date));

            $seconds = date('s', strtotime($date));
        }

        $format = str_replace(array('%y', '%Y', '%dd', '%d', '%mm', '%m', '%h', '%H', '%i', '%s'), array($year_low, $year_upp, $day_sql, $day, $month_sql, $month, $hours_12, $hours_24, $minutes, $seconds), $format);

        return $format;
    }
}


if (!function_exists("fa_date_now"))
{
    /**
     * Return current date and time in sql format (Y-m-d H:i:s)
     * 
     * @return string
     */
    function fa_date_now()
    {
        return date("Y-m-d H:i:s", time());
    }
}

if (!function_exists("fa_date_today"))
{
    /**
     * Return current date with custom format
     * 
     * @param string $format format, default "Y-m-d"
     * 
     * @return string
     */
    function fa_date_today($format = 'Y-m-d')
    {
        return date($format, time());
    }
}


if (!function_exists("fa_date_week_start"))
{
    /**
     * Return start date of week for given date
     * 
     * @return string $date date, default NULL (today)
     * 
     * @return string
     */
    function fa_date_week_start($currdate = NULL)
    {
        $currdate = $currdate ? $currdate : date_today();
        $currtime = strtotime($currdate);

        $dayN = date("N", $currtime);
        $start_date = date("Y-m-d", strtotime("-" . ($dayN - 1) . ' days', $currtime));

        return $start_date;
    }

}

if (!function_exists("fa_date_week_end"))
{
    /**
     * Return end date of week for given date
     * 
     * @return string $date date, default NULL (today)
     * 
     * @return string
     */
    function fa_date_week_end($currdate = NULL)
    {
        $currdate = $currdate ? $currdate : date_today();
        $currtime = strtotime($currdate);
        $dayN = date("N", $currtime);
        $end_date = date("Y-m-d", strtotime("+" . (7 - $dayN) . ' days', $currtime));

        return $end_date;
    }
}

if (!function_exists('fa_date_diff_days'))
{
    /**
     * Return date difference days
     * 
     * @param date $date1   start date
     * @param date $date2   end date
     * 
     * @return int
     */
    function fa_date_diff_days($date1, $date2)
    {
        $today = new DateTime(date($date1));
        $appt = new DateTime(date($date2));
        return $appt->diff($today)->format("%r%a");
    }
}

if (!function_exists("fa_date_is_valid"))
{

    /**
     * Check if given date is a sql valid date
     * 
     * @return string $date $date
     * 
     * @return boolean
     */
    function fa_date_is_valid($date)
    {
        if (preg_match('/[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}/', $date))
        {
            return strtotime($date) !== false;
        }

        return false;
    }

}

if (!function_exists("fa_datetime_is_valid"))
{

    /**
     * Controlla che la data e ora indicata sia una data valida nel formato mysql YYYY-MM-DD HH:II
     * 
     * @return string $datetime Data ora, default NULL (odierna)
     * 
     * @return string
     */
    function fa_datetime_is_valid($datetime)
    {
        if (preg_match('/[0-9]{4}-[0-9]{1,2}-[0-9]{1,2} [0-9]{2}\:[0-9]{2}$/', $datetime))
        {
            $datetime .= ':00';
        }

        if (preg_match('/[0-9]{4}-[0-9]{1,2}-[0-9]{1,2} [0-9]{2}\:[0-9]{2}\:[0-9]{2}$/', $datetime))
        {
            return strtotime($datetime) !== false;
        }

        return false;
    }

}

if (!function_exists('fa_date_day_name'))
{

    /**
     * Return day name by number of day of a date
     * 
     * @param mixed  $date     date / number of day
     * @param bool   $short    short if true, long if false
     * 
     * @return string day 
     */
    function fa_date_day_name($date, $short = false)
    {
        $day_name = false;

        if (empty($date))
        {
            return false;
        }

        if (is_numeric($date) && $date > 0)
        {
            switch ($date)
            {
                case 1: $day_name = $short ? _f('Mon') : _f('Monday');
                    break;
                case 2: $day_name = $short ? _f('Tue') : _f('Tuesday');
                    break;
                case 3: $day_name = $short ? _f('Wed') : _f('Wednesday');
                    break;
                case 4: $day_name = $short ? _f('Thu') : _f('Thursday');
                    break;
                case 5: $day_name = $short ? _f('Fri') : _f('Friday');
                    break;
                case 6: $day_name = $short ? _f('Sat') : _f('Saturday');
                    break;
                case 7: $day_name = $short ? _f('Sun') : _f('Sunday');
                    break;
            }
        }

        if (!$day_name && fa_date_is_valid($date))
        {
            $day_number = date("N", strtotime($date));
            return fa_date_day_name($day_number, $short);
        }

        return $day_name;
    }

}

if (!function_exists('fa_date_month_name'))
{

    /**
     * Get month name by number or date
     * 
     * @param mixed  $date     date / number or month (1-12)
     * @param bool   $short    short mode, default false, full month name
     * 
     * @return string|boolean
     */
    function fa_date_month_name($date, $short = false)
    {
        $moth_name = false;

        if (empty($date))
        {
            return false;
        }

        if (is_numeric($date) && $date > 0)
        {
            switch ($date)
            {
                case 1: $moth_name = $short ? _f('Jan') : _f('January');
                    break;
                case 2: $moth_name = $short ? _f('Feb') : _f('February');
                    break;
                case 3: $moth_name = $short ? _f('Mar') : _f('March');
                    break;
                case 4: $moth_name = $short ? _f('Apr') : _f('April');
                    break;
                case 5: $moth_name = $short ? _f('May') : _f('MAY');
                    break;
                case 6: $moth_name = $short ? _f('Jun') : _f('June');
                    break;
                case 7: $moth_name = $short ? _f('Jul') : _f('July');
                    break;
                case 8: $moth_name = $short ? _f('Aug') : _f('August');
                    break;
                case 9: $moth_name = $short ? _f('Sep') : _f('September');
                    break;
                case 10: $moth_name = $short ? _f('Oct') : _f('October');
                    break;
                case 11: $moth_name = $short ? _f('Nov') : _f('November');
                    break;
                case 12: $moth_name = $short ? _f('Dec') : _f('December');
                    break;
            }
        }

        if (!$moth_name && fa_date_is_valid($date))
        {
            $month = date("m", strtotime($date));
            return fa_date_month_name($month, $short);
        }

        return $moth_name;
    }

}


if (!function_exists('fa_date_next'))
{

    /**
     * Return next date by next date "n" number
     * 
     * @param date|datetime $datetime       date
     * @param int           $next_day_n     next day number
     */
    function fa_date_next($datetime, $next_day_n, $format = 'Y-m-d')
    {
        if (!fa_date_is_valid($datetime) && !fa_datetime_is_valid($datetime))
        {
            return false;
        }

        $date = date('Y-m-d', strtotime($datetime));
        $date_next = false;

        while (!$date_next)
        {
            $date_timestamp = strtotime('+1 day', strtotime($date));
            $date = date('Y-m-d', $date_timestamp);

            $date_n = date('N', $date_timestamp);


            if ($date_n == $next_day_n)
            {
                $date_next = date($format, $date_timestamp);
            }
        }

        return $date_next;
    }

}

if (!function_exists('fa_date_prev'))
{

    /**
     * Return previous date by next date "n" number
     * 
     * @param date|datetime $datetime       date
     * @param int           $next_day_n     previous day number
     */
    function fa_date_prev($datetime, $prev_day_n, $format = 'Y-m-d')
    {
        if (!fa_date_is_valid($datetime) && !fa_datetime_is_valid($datetime))
        {
            return false;
        }

        $date = date('Y-m-d', strtotime($datetime));
        $date_prev = false;

        while (!$date_prev)
        {
            $date_timestamp = strtotime('-1 day', strtotime($date));
            $date = date('Y-m-d', $date_timestamp);

            $date_n = date('N', $date_timestamp);

            if ($date_n == $prev_day_n)
            {
                $date_prev = date($format, $date_timestamp);
            }
        }

        return $date_prev;
    }

}

if (!function_exists('fa_date_sub_days'))
{

    /**
     * Sub n days from a date
     * 
     * @param date $date    start date
     * @param int $days     number of date to sub
     * 
     * @return date
     */
    function fa_date_sub_days($date, $days)
    {
        return date('Y-m-d', strtotime('-' . $days . ' days', strtotime($date)));
    }

}


if (!function_exists('fa_date_add_days'))
{

    /**
     * Add n days from a date
     * 
     * @param date $date    start date
     * @param int $days     number of date to add
     * 
     * @return date
     */
    function fa_date_add_days($date, $days)
    {
        return date('Y-m-d', strtotime('+' . $days . ' days', strtotime($date)));
    }

}


if (!function_exists('fa_date_days_between'))
{

    /**
     * Get dates list between two date
     * 
     * @param date   $fromdate              fromdate
     * @param date   $todate                todate
     * @param string $date_label_format     array value string,date format
     * @param string $date_key_format       array key string,date forma
     * 
     * @return array
     */
    function fa_date_days_between($fromdate, $todate, $date_label_format = '%d %B %Y', $date_key_format = '%Y-%mm-%dd')
    {
        if (!fa_date_is_valid($fromdate) || !fa_date_is_valid($todate))
        {
            return false;
        }

        $dates = array();

        for ($i = strtotime($fromdate . ' 00:00:00'); $i <= strtotime($todate . ' 23:59:59'); $i += 86400)
        {
            $date = date("Y-m-d", $i);
            $dates[fa_sql_to_date($date, $date_key_format)] = fa_sql_to_date($date, $date_label_format);
        }

        return $dates;
    }

}


if(!function_exists('fa_microtime'))
{
    /**
     * Get microtime
     * 
     * @param date   $fromdate              fromdate
     * @param date   $todate                todate
     * @param string $date_label_format     array value string,date format
     * @param string $date_key_format       array key string,date forma
     * 
     * @return array
     */
    function fa_microtime()
    {
        return microtime(true)*10000;
    }
}