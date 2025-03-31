<?php

if (! function_exists('getFiscalYearDates')) {
    /**
     * Get the start and end dates for the current fiscal year.
     *
     * @param \Carbon\Carbon $now
     * @return array
     */
    function getFiscalYearDates(\Carbon\Carbon $now)
    {
        if ($now->month >= 4) {
            return [
                'start' => \Carbon\Carbon::create($now->year, 4, 1, 0, 0, 0),
                'end'   => \Carbon\Carbon::create($now->year + 1, 3, 31, 23, 59, 59),
            ];
        }

        return [
            'start' => \Carbon\Carbon::create($now->year - 1, 4, 1, 0, 0, 0),
            'end'   => \Carbon\Carbon::create($now->year, 3, 31, 23, 59, 59),
        ];
    }
}