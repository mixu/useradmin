<?php

/**
 * Core set manipulation
 * @author Mikito Takada
 * @package default
 * @version 1.0
 */

class Set {
   /**
    * Check if supplied array is numeric.
    *
    * This code is based upon the similarly named
    * function in the CakePHP framework (licenced under the MIT Licence).
    *
    * @param <type> $array
    * @return <type>
    */
   static function is_numeric($array = null) {
      return self::numeric($array);
   }
   static function numeric($array = null) {
      if (empty($array)) {
         return false;
      }

      if ($array === range(0, count($array) - 1)) {
         return true;
      }

      $numeric = true;
      $keys = array_keys($array);
      $count = count($keys);

      for ($i = 0; $i < $count; $i++) {
         if (!is_numeric($array[$keys[$i]])) {
            $numeric = false;
            break;
         }
      }
      return $numeric;
   }

   /**
    * Set the given array keys to the default values if they are empty.
    *
    * Useful for filling in configuration in classes without needing to constantly check for isset().
    *
    * @param array $array
    * @param array $defaults
    */
   static function defaults($array, $defaults) {
      if(!is_array($array)) {
         $array = array();
      }
      foreach($defaults as $key => $default) {
         if(!isset($array[$key])) {
            $array[$key] = $default;
         }
      }
      return $array;
   }
}