<?php

namespace App\Services;

use Illuminate\Support\Carbon;

class BusinessDaysService
{
    /**
     * Suma días hábiles (L–V) a una fecha
     */
    public static function add(Carbon $date, int $days): Carbon
    {
        $date = $date->copy();

        while ($days > 0) {
            $date->addDay();

            if ($date->isWeekday()) {
                $days--;
            }
        }

        return $date;
    }
}
