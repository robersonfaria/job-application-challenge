<?php

if (!function_exists('hasConsecutiveSameDigits')) {
    function hasConsecutiveSameDigits($number)
    {
        return (boolean)preg_match('/(\d)\1\1+/', $number);
    }
}
