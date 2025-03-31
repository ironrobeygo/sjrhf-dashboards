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

if(! function_exists('getLast12Months')) {
    /**
     * Get the status of an opportunity based on its proposal status.
     *
     * @param string $status
     * @return string|null
     */
    function getLast12Months()
    {
        return [
            'cutoff' => \Carbon\Carbon::now()->subMonths(12),
            'now' => \Carbon\Carbon::now()
        ];
    }
}