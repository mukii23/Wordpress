<?php
/***
// Random code generator.
***/

function cvf_td_generate_random_code($length=10) {
   $string = '';
   $characters = "!@#$%^&*()_<>?23456789ABCDEFHJKLMNPRTVWXYZabcdefghijklmnopqrstuvwxyz";
   for ($p = 0; $p < $length; $p++) {
      $string .= $characters[mt_rand(0, strlen($characters)-1)];
   }
   return $string;
}
