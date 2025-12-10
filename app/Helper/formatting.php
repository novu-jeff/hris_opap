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
        } elseif ($diffInMinutes > 0) {
            $relativeTime = $diffInMinutes . ' minute' . ($diffInMinutes > 1 ? 's' : '') . ' ago';
        } else {
            $relativeTime = 'now';
        }
        
        return $relativeTime;
    }
}

if (!function_exists('relative_time_duration')) {
    
    function relative_time_duration(string $date)
    {

        if (!$date) {
            return 'N/A';
        }

        $date = Carbon::parse($date);
        $now  = Carbon::now();

        // If hire date is in the future
        if ($date->greaterThan($now)) {
            return 'not started yet';
        }

        $diff = $date->diff($now);

        $years  = $diff->y;
        $months = $diff->m;
        $days   = $diff->d;

        $parts = [];

        if ($years > 0) {
            $parts[] = $years . ' year' . ($years > 1 ? 's' : '');
        }

        if ($months > 0) {
            $parts[] = $months . ' month' . ($months > 1 ? 's' : '');
        }

        if ($days > 0) {
            $parts[] = $days . ' day' . ($days > 1 ? 's' : '');
        }

        return count($parts) ? implode(', ', $parts) : 'less than a day'; 
    }
}

/*
function relative_time_duration(string $date)
    {

        $date = format_date($date, 'carbon_date');  
        // Parse the given date and get the current date
        $date = Carbon::parse($date);  
        $now = Carbon::now(); 
        
        // Calculate the difference in years, months, and days
        $diffInYears = $now->diffInYears($date);
        $diffInMonths = $now->diffInMonths($date) % 12;
        $dateAfterYearsAndMonths = $date->copy()->addYears($diffInYears)->addMonths($diffInMonths);
        $diffInDays = $now->diffInDays($dateAfterYearsAndMonths);

       // dd( $diffInYears, $diffInMonths, $diffInDays, $dateAfterYearsAndMonths);
        $output = '';

        // Append years to the output if any
        if ($diffInYears > 0) {
            $output .= $diffInYears . ' year' . ($diffInYears > 1 ? 's' : '');
        }

        // Append months to the output if any
        if ($diffInMonths > 0) {
            $output .= ($output ? ', ' : '') . $diffInMonths . ' month' . ($diffInMonths > 1 ? 's' : '');
        }

        // Append days to the output if the duration is less than a year but greater than zero days
        if ($diffInYears === 0 && $diffInDays > 0) {
            $output .= ($output ? ', ' : '') . $diffInDays . ' day' . ($diffInDays > 1 ? 's' : '');
        }

        // Return the output or fallback to 'less than a day' if the time difference is negligible
        return $output ?: 'less than a day'; 
    }

*/

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
            case 'onboarding':
                return '
                    <div class="alert alert-info text-center fw-bold py-3">
                        requirements submission
                    </div>
                ';
            case 'hired':
                return '
                    <div class="alert alert-success text-center fw-bold py-3">
                        hired
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

if (!function_exists('format_date')) {
    function format_date($date, $option) {
        // Try to detect the input format
        try {
            // Normalize the date format to YYYY-MM-DD if needed
            // If the input date is in `d/m/Y` format (e.g., 30/7/2021), convert it to `Y-m-d` format
            if (preg_match('/\d{1,2}\/\d{1,2}\/\d{4}/', $date)) {
                // Convert d/m/Y to Y-m-d
                $date = Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
            } 
            // If the date is in `m/d/Y` format (e.g., 07/30/2021), convert it to `Y-m-d` format
            elseif (preg_match('/\d{1,2}\/\d{1,2}\/\d{2,4}/', $date)) {
                $date = Carbon::createFromFormat('m/d/Y', $date)->format('Y-m-d');
            }

            // Parse the normalized date
            $carbonDate = Carbon::parse($date);

            // Return the formatted result based on the provided option
            switch ($option) {
                case 'date_string': // E.g., Jul 30, 2021
                    return $carbonDate->toFormattedDateString();
                case 'day_date_string': // E.g., Friday, Jul 30, 2021
                    return $carbonDate->toFormattedDayDateString();
                case 'day_date_time_string': // E.g., Fri, Jul, 2021 12:00 AM
                    return $carbonDate->format('D, M, Y g:i A');
                case 'age': // Calculates age based on the date
                    return $carbonDate->age;
                case 'carbon_date':
                    return $carbonDate;
                default:
                    throw new Exception("Invalid format option provided.");
            }
        } catch (\Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }
}


if(!function_exists('format_id')) {
    function format_id($id, $length) {
        return str_pad($id, $length, '0', STR_PAD_LEFT);
    }
}

if(!function_exists('computeAge')) {
    function computeAge($birthday) {
        return Carbon::parse($birthday)->age ?? '';
    }
}

if (!function_exists('file_type')) {
    function file_type($string) {
        $extension = strtolower(pathinfo($string, PATHINFO_EXTENSION));
        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
            return 'image';
        } else {
            return $extension;
        }
    }
}


if(!function_exists('format_time')) {
    function format_time($time) {
        try {
            return Carbon::parse($time)->format('h:i A');
        } catch (\Exception $e) {
            return $time;
        }
    }
}

if (!function_exists('format_getFileName')) {
    
    function format_getFileName($path) {

        $file = basename($path);
        
        return $file;
    }
}


if (!function_exists('format_extension')) {

    function format_extension($path) {

        $extension = pathinfo($path, PATHINFO_EXTENSION);
        
        switch (strtolower($extension)) {
            case 'doc':
            case 'docx':
                return 'fa-solid fa-file-word'; 
            case 'pdf':
                return 'fa-solid fa-file-pdf'; 
            case 'xls':
            case 'xlsx':
                return 'fa-solid fa-file-excel'; 
            default:
                return 'fa-solid fa-file'; 
        }
    }
}

if (!function_exists('formatTime')) {
    function formatTime(int $mins): string {
        $hours = intdiv($mins, 60);
        $minutes = $mins % 60;

        $parts = [];
        if ($hours > 0) {
            $parts[] = $hours . ' hr' . ($hours > 1 ? 's' : '');
        }
        if ($minutes > 0 || $hours === 0) {
            $parts[] = $minutes . ' min' . ($minutes > 1 ? 's' : '');
        }

        return implode('  and ', $parts);
    }

}

if (!function_exists('status_alert')) {
    function status_alert($status)
    {
        $status = strtolower($status);

        return match ($status) {
            'pending'     => '<div style="width: 100% !important;" class="alert alert-warning text-uppercase fw-bold text-center mb-2 py-2 px-3">Pending</div>',
            'approved', 'mentioned'    => '<div style="width: 100% !important;" class="alert alert-success text-uppercase fw-bold text-center mb-2 py-2 px-3">Approved</div>',
            'disapproved' => '<div style="width: 100% !important;" class="alert alert-danger text-uppercase fw-bold text-center mb-2 py-2 px-3">Disapproved</div>',
            'cancelled'   => '<div style="width: 100% !important;" class="alert alert-secondary text-uppercase fw-bold text-center mb-2 py-2 px-3">Cancelled</div>',
            default       => '<div style="width: 100% !important;" class="alert alert-light text-uppercase fw-bold text-center mb-2 py-2 px-3">Unknown Status</div>',
        };
    }
}