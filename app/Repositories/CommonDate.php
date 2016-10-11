<?php

namespace App\Repositories;

trait CommonDate
{
    function formatDate($date, $format = null)
    {
        if(is_null($date)) {
            return null;
        }

        if(!empty($format)) {
            return date($format, strtotime($date));
        }

        return date('M d, Y', strtotime($date));
    }
}