<?php

namespace App\Repositories;

trait CommonDate
{
    function formatDate($date)
    {
        return date('M d, Y', strtotime($date));
    }
}