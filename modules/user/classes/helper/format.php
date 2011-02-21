<?php

/**
 *
 * @author Mikito Takada
 * @package default
 * @version 1.0
 */

define("SECOND", 1);
define("MINUTE", 60 * SECOND);
define("HOUR", 60 * MINUTE);
define("DAY", 24 * HOUR);
define("MONTH", 30 * DAY);

/**
 * Helper for string formatting.
 */
class Helper_Format {
   /**
    * Relative time
    * @param timestamp $time
    * @return string
    */
   public static function relative_time($time) {
      if (!is_numeric($time)) {
         return $time;
      }
      if ($time < 0) {
         return __('never');
      }
      $delta = time() - $time;

      if ($delta < 1 * MINUTE) {
         return $delta == 1 ? "one second ago" : $delta . " seconds ago";
      }
      if ($delta < 2 * MINUTE) {
         return "a minute ago";
      }
      if ($delta < 45 * MINUTE) {
         return floor($delta / MINUTE) . " minutes ago";
      }
      if ($delta < 90 * MINUTE) {
         return "an hour ago";
      }
      if ($delta < 24 * HOUR) {
         return floor($delta / HOUR) . " hours ago";
      }
      if ($delta < 48 * HOUR) {
         return "yesterday";
      }
      if ($delta < 30 * DAY) {
         return floor($delta / DAY) . " days ago";
      }
      if ($delta < 12 * MONTH) {
         $months = floor($delta / DAY / 30);
         return $months <= 1 ? "one month ago" : $months . " months ago";
      } else {
         $years = floor($delta / DAY / 365);
         return $years <= 1 ? "one year ago" : $years . " years ago";
      }
   }

   /**
    * Takes a MySQL format datetime yyyy-mm-dd hh:mm:ss and returns an European date dd/mm/yyyy at hh:mm:ss
    * @param string $value
    * @return string
    */
   public static function friendly_datetime($value) {
      if (($value == '0000-00-00 00:00:00') || ($value == '0000-00-00')) {
         return __('never');
      }
      $date = date_parse($value); // req. PHP >= 5.2.0
      $now = getdate();
      if ( ($date['year'] == $now['year']) && ($date['month'] == $now['mon']) && ($date['day'] == $now['mday']) ) {
         return 'today at '.date('H:i:s', strtotime($value));
      } else if( ($date['year'] == $now['year']) && ($date['month'] == $now['month']) && ($date['day'] == ($now['day']-1) ) ) {
         return 'yesterday at '.date('H:i:s', strtotime($value));
      } else {
         return date('m/d/Y \a\t H:i:s', strtotime($value));
      }
   }
   /**
    * Takes a MySQL format datetime yyyy-mm-dd and returns an European date dd/mm/yyyy
    * @param string $value
    * @return string
    */
   public static function friendly_date($value) {
      if (($value == '0000-00-00 00:00:00') || ($value == '0000-00-00')) {
         return __('never');
      }
      return date('m/d/Y', strtotime($value));
   }


}