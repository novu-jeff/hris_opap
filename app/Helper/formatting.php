<?php

use Carbon\Carbon;

if(!function_exists('money_format')) {
    function money_format(float $amount, string $text = null) {
        $amount = number_format($amount, 2);
        $text = !is_null($text) ?? ' ' . $text;
        return  '₱' . $amount . $text;
    }
}

if(!function_exists('relative_time')) {
    function relative_time(string $date) {
        $date = Carbon::parse($date);

        $now = Carbon::now();
        $diffInYears = $now->diffInYears($date);
        $diffInMonths = $now->diffInMonths($date);
        $diffInWeeks = $now->diffInWeeks($date);
        $diffInDays = $now->diffInDays($date);
        $diffInHours = $now->diffInHours($date);
        $diffInMinutes = $now->diffInMinutes($date);

        if ($diffInYears > 0) {
            $relativeTime = $diffInYears . ' year' . ($diffInYears > 1 ? 's' : '') . ' ago';
        } elseif ($diffInMonths > 0) {
            $relativeTime = $diffInMonths . ' month' . ($diffInMonths > 1 ? 's' : '') . ' ago';
        } elseif ($diffInWeeks > 0) {
            $relativeTime = $diffInWeeks . ' week' . ($diffInWeeks > 1 ? 's' : '') . ' ago';
        } elseif ($diffInDays > 0) {
            $relativeTime = $diffInDays . ' day' . ($diffInDays > 1 ? 's' : '') . ' ago';
        } elseif ($diffInHours > 0) {
            $relativeTime = $diffInHours . ' hour' . ($diffInHours > 1 ? 's' : '') . ' ago';
        } else {
            $relativeTime = $diffInMinutes . ' minute' . ($diffInMinutes > 1 ? 's' : '') . ' ago';
        }

        return $relativeTime;
    }
}

if(!function_exists('see_more')) {
    function see_more($text, $lengthAllowed = null) {
        if($lengthAllowed != null && strlen($text) > $lengthAllowed) {
            $substr = substr($text , 0, $lengthAllowed);
            return $substr . '...' . ' <br><br><small class="text-muted fw-bold text-uppercase fst-italic">click to see full info</small>';
        } else {
            return $text;
        }
    }
}

if(!function_exists('generate_code')) {
    function generate_code($prefix) {
        return $prefix . '-' . time();
    }
}

if(!function_exists('trimEmail')) {
    function trimEmail($email) {
        $trim = explode('@', $email);
        return $trim[0];
    }
}

if(!function_exists('application_status')) {
    function application_status($status) {
        switch($status) {
            case 'pending':
                return '
                    <div class="alert alert-warning text-center fw-bold py-3">
                        pending application
                    </div>
                ';
            case 'interview';
                return '
                    <div class="alert alert-info text-center fw-bold py-3">
                        scheduled for interview
                    </div>
                ';
            case 'placement':
                return '
                    <div class="alert alert-info text-center fw-bold py-3">
                        job offer sent
                    </div>
                ';
            case 'onboard':
                return '
                    <div class="alert alert-info text-center fw-bold py-3">
                        requirements submission
                    </div>
                ';
            case 'hired':
                return '
                    <div class="alert alert-success text-center fw-bold py-3">
                        rejected
                    </div>
                ';
            case 'rejected':
                return '
                    <div class="alert alert-danger text-center fw-medium">
                        rejected
                    </div>
                ';
        }
    }
}

if(!function_exists('format_name')) {
    function format_name($firstname, $middlename, $lastname) {
        if(is_null($middlename) || empty($middlename)) {
            return $firstname . ' ' . $lastname;
        } else {
            return $firstname . ' ' . $middlename . ' ' . $lastname;
         }
    }
}

if(!function_exists('format_date')) {
    function format_date($date, $option) {
        switch($option) {
            case 'date_string':
                return Carbon::parse($date)->toFormattedDateString();
            case 'day_date_string':
                return Carbon::parse($date)->toFormattedDayDateString();
            case 'age':
                return Carbon::parse($date)->age;

        }
    }
}