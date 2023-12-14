<?php

namespace App\Http\Traits;

use DateTime;

trait ProcessDataDateType
{
    public function generateWeekdays($dayOfWeek, $startDate, $endDate)
    {
        $result = [];
        $days = explode(',', $dayOfWeek);

        $currentDate = new DateTime($startDate);
        $endDate = new DateTime($endDate);

        while ($currentDate <= $endDate) {
            $currentDayOfWeek = $currentDate->format('l');

            if (in_array($currentDayOfWeek, $days) && $currentDate >= $startDate) {
                $result[] = $currentDate->format('Y-m-d');
            }

            $currentDate->modify('+1 day');
        }
    
        return $result;
    }
}
