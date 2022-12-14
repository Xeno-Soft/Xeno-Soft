<?php
/**
 * @class dt
 * @brief Date/time utilities
 *
 * @package Clearbricks
 * @subpackage Common
 *
 * @copyright Olivier Meunier & Association Dotclear
 * @copyright GPL-2.0-only
 */
class dt
{
    private static $timezones = null;

    /**
     * Timestamp formating
     *
     * Returns a date formated like PHP <a href="http://www.php.net/manual/en/function.strftime.php">strftime</a>
     * function.
     * Special cases %a, %A, %b and %B are handled by {@link l10n} library.
     *
     * @param string            $p        Format pattern
     * @param integer|boolean   $ts       Timestamp
     * @param string            $tz       Timezone
     * @return    string
     */
    public static function str(string $p, $ts = null, string $tz = null): string
    {
        if ($ts === null || $ts === false) {
            $ts = time();
        }

        $hash = '799b4e471dc78154865706469d23d512';
        $p    = preg_replace('/(?<!%)%(a|A)/', '{{' . $hash . '__$1%w__}}', $p);
        $p    = preg_replace('/(?<!%)%(b|B)/', '{{' . $hash . '__$1%m__}}', $p);

        if ($tz) {
            $T = self::getTZ();
            self::setTZ($tz);
        }

        // Avoid deprecated notice until PHP 9 should be supported or a correct strftime() replacement
        $res = @strftime($p, $ts);

        if ($tz) {
            self::setTZ($T);
        }

        $res = preg_replace_callback(
            '/{{' . $hash . '__(a|A|b|B)([0-9]{1,2})__}}/',
            function ($args) {
                $b = [
                    1  => '_Jan',
                    2  => '_Feb',
                    3  => '_Mar',
                    4  => '_Apr',
                    5  => '_May',
                    6  => '_Jun',
                    7  => '_Jul',
                    8  => '_Aug',
                    9  => '_Sep',
                    10 => '_Oct',
                    11 => '_Nov',
                    12 => '_Dec', ];

                $B = [
                    1  => 'January',
                    2  => 'February',
                    3  => 'March',
                    4  => 'April',
                    5  => 'May',
                    6  => 'June',
                    7  => 'July',
                    8  => 'August',
                    9  => 'September',
                    10 => 'October',
                    11 => 'November',
                    12 => 'December', ];

                $a = [
                    1 => '_Mon',
                    2 => '_Tue',
                    3 => '_Wed',
                    4 => '_Thu',
                    5 => '_Fri',
                    6 => '_Sat',
                    0 => '_Sun', ];

                $A = [
                    1 => 'Monday',
                    2 => 'Tuesday',
                    3 => 'Wednesday',
                    4 => 'Thursday',
                    5 => 'Friday',
                    6 => 'Saturday',
                    0 => 'Sunday', ];

                return __(${$args[1]}[(int) $args[2]]);
            },
            $res
        );

        return $res;
    }

    /**
     * Date to date
     *
     * Format a literal date to another literal date.
     *
     * @param string    $p        Format pattern
     * @param string    $dt        Date
     * @param string    $tz        Timezone
     * @return    string
     */
    public static function dt2str(string $p, string $dt, ?string $tz = null): string
    {
        return dt::str($p, strtotime($dt), $tz);
    }

    /**
     * ISO-8601 formatting
     *
     * Returns a timestamp converted to ISO-8601 format.
     *
     * @param integer    $ts        Timestamp
     * @param string    $tz        Timezone
     * @return    string
     */
    public static function iso8601(int $ts, string $tz = 'UTC'): string
    {
        $o  = self::getTimeOffset($tz, $ts);
        $of = sprintf('%02u:%02u', abs($o) / 3600, (abs($o) % 3600) / 60);

        return date('Y-m-d\\TH:i:s', $ts) . ($o < 0 ? '-' : '+') . $of;
    }

    /**
     * RFC-822 formatting
     *
     * Returns a timestamp converted to RFC-822 format.
     *
     * @param integer    $ts        Timestamp
     * @param string    $tz        Timezone
     * @return    string
     */
    public static function rfc822(int $ts, string $tz = 'UTC'): string
    {
        # Get offset
        $o  = self::getTimeOffset($tz, $ts);
        $of = sprintf('%02u%02u', abs($o) / 3600, (abs($o) % 3600) / 60);

        // Avoid deprecated notice until PHP 9 should be supported or a correct strftime() replacement
        return @strftime('%a, %d %b %Y %H:%M:%S ' . ($o < 0 ? '-' : '+') . $of, $ts);
    }

    /**
     * Timezone set
     *
     * Set timezone during script execution.
     *
     * @param    string    $tz        Timezone
     */
    public static function setTZ(string $tz)
    {
        if (function_exists('date_default_timezone_set')) {
            date_default_timezone_set($tz);

            return;
        }

        if (!ini_get('safe_mode')) {
            putenv('TZ=' . $tz);
        }
    }

    /**
     * Current timezone
     *
     * Returns current timezone.
     *
     * @return string
     */
    public static function getTZ(): string
    {
        if (function_exists('date_default_timezone_get')) {
            return date_default_timezone_get();
        }

        return date('T');
    }

    /**
     * Time offset
     *
     * Get time offset for a timezone and an optionnal $ts timestamp.
     *
     * @param string    $tz        Timezone
     * @param integer|boolean    $ts        Timestamp
     * @return integer
     */
    public static function getTimeOffset(string $tz, $ts = false): int
    {
        if (!$ts) {
            $ts = time();
        }

        $server_tz     = self::getTZ();
        $server_offset = date('Z', $ts);

        self::setTZ($tz);
        $cur_offset = date('Z', $ts);

        self::setTZ($server_tz);

        return $cur_offset - $server_offset;
    }

    /**
     * UTC conversion
     *
     * Returns any timestamp from current timezone to UTC timestamp.
     *
     * @param integer    $ts        Timestamp
     * @return integer
     */
    public static function toUTC(int $ts): int
    {
        return $ts + self::getTimeOffset('UTC', $ts);
    }

    /**
     * Add timezone
     *
     * Returns a timestamp with its timezone offset.
     *
     * @param string    $tz        Timezone
     * @param integer|boolean    $ts        Timestamp
     * @return integer
     */
    public static function addTimeZone(string $tz, $ts = false): int
    {
        if ($ts === false) {
            $ts = time();
        }

        return $ts + self::getTimeOffset($tz, $ts);
    }

    /**
     * Timzones
     *
     * Returns an array of supported timezones, codes are keys and names are values.
     *
     * @param boolean    $flip      Names are keys and codes are values
     * @param boolean    $groups    Return timezones in arrays of continents
     * @return array
     */
    public static function getZones(bool $flip = false, bool $groups = false): array
    {
        if (is_null(self::$timezones)) {
            // Read timezones from file
            if (!is_readable($f = dirname(__FILE__) . '/tz.dat')) {
                return [];
            }
            $tz  = file(dirname(__FILE__) . '/tz.dat');
            $res = [];
            foreach ($tz as $v) {
                $v = trim($v);
                if ($v) {
                    $res[$v] = str_replace('_', ' ', $v);
                }
            }
            // Store timezones for further accesses
            self::$timezones = $res;
        } else {
            // Timezones already read from file
            $res = self::$timezones;
        }

        if ($flip) {
            $res = array_flip($res);
            if ($groups) {
                $tmp = [];
                foreach ($res as $k => $v) {
                    $g              = explode('/', $k);
                    $tmp[$g[0]][$k] = $v;
                }
                $res = $tmp;
            }
        }

        return $res;
    }
}
