<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
    public function index()
    {
        // Fetch attendance logs ordered by timestamp
        $logs = DB::table('attendances')->orderBy('timestamp')->take(500)->get();
    
        // Group logs by date and employee
        $grouped = [];
    
        foreach ($logs as $log) {
            $date = date('Y-m-d', strtotime($log->timestamp));
            $employeeId = $log->employee_id;
            $grouped[$date][$employeeId][] = Carbon::parse($log->timestamp);
        }
    
        $final = [];
    
        // Process each group of logs for each employee
        foreach ($grouped as $date => $employees) {
            foreach ($employees as $employeeId => $timestamps) {
                // Sort timestamps, remove duplicates, and reset array keys
                $timestamps = collect($timestamps)->sort()->unique()->values();
                $logCount = $timestamps->count();
    
                $record = [
                    'clock_in' => null,
                    'lunch_in' => null,
                    'lunch_out' => null,
                    'clock_out' => null,
                    'aut' => [
                        'tardiness' => [
                            'minutes' => 0,
                            'reason' => null
                        ],
                        'undertime' => [
                            'minutes' => 0,
                            'reason' => null
                        ],
                        'early_lunch_in' => [
                            'minutes' => 0,
                            'reason' => null
                        ],
                        'overtime' => [
                            'minutes' => 0,
                            'reason' => null
                        ]
                    ]
                ];
    
                // Allocate logs for time-in, lunch-in, lunch-out, and time-out
                if ($logCount >= 4) {
                    $record['clock_in'] = $timestamps[0]->format('h:i A');
                    $record['lunch_in'] = $timestamps[1]->format('h:i A');
                    $record['lunch_out'] = $timestamps[$logCount - 2]->format('h:i A');
                    $record['clock_out'] = $timestamps[$logCount - 1]->format('h:i A');
                } else {
                    foreach ($timestamps as $ts) {
                        $hour = (int)$ts->format('H');
                        if (!$record['clock_in'] && $hour >= 5 && $hour <= 9) {
                            $record['clock_in'] = $ts->format('h:i A');
                        } elseif (!$record['lunch_in'] && $hour >= 11 && $hour <= 12) {
                            $record['lunch_in'] = $ts->format('h:i A');
                        } elseif (!$record['lunch_out'] && $hour >= 12 && $hour <= 13) {
                            $record['lunch_out'] = $ts->format('h:i A');
                        } elseif (!$record['clock_out'] && $hour >= 15 && $hour <= 18) {
                            $record['clock_out'] = $ts->format('h:i A');
                        }
                    }
                }
    
                // Define fixed working hours and limits
                $fixedStart = Carbon::createFromTime(7, 0);        // Earliest time-in
                $latestAllowedIn = Carbon::createFromTime(9, 0);   // Max time-in without tardiness
                $breakStart = Carbon::createFromTime(12, 0);
                $requiredMinutes = 480; // Standard workday in minutes
    
                // Parse times for easy comparisons
                $clock_in = $record['clock_in'] ? Carbon::createFromFormat('h:i A', $record['clock_in']) : null;
                $clock_out = $record['clock_out'] ? Carbon::createFromFormat('h:i A', $record['clock_out']) : null;
                $lunch_in = $record['lunch_in'] ? Carbon::createFromFormat('h:i A', $record['lunch_in']) : null;
                $lunch_out = $record['lunch_out'] ? Carbon::createFromFormat('h:i A', $record['lunch_out']) : null;
    
                // Adjust for early time-in
                $actualStart = ($clock_in && $clock_in->lt($fixedStart)) ? $fixedStart->copy() : $clock_in;

                // Calculate tardiness if time-in is after 9 AM
                if ($actualStart && $actualStart->gt($latestAllowedIn)) {
                    $late = $actualStart->diffInMinutes($latestAllowedIn);
                    $record['aut']['tardiness'] = [
                        'minutes' => $late,
                        'reason' => "Late by {$late} minute(s). Time-in at {$record['clock_in']}, beyond 09:00 AM."
                    ];
                }

                // Calculate early lunch-in if lunch-in is before 12 PM
                if ($lunch_in && $lunch_in->lt($breakStart)) {
                    $early = $breakStart->diffInMinutes($lunch_in);
                    $record['aut']['early_lunch_in'] = [
                        'minutes' => $early,
                        'reason' => "Lunch-in was {$early} minute(s) earlier than 12:00 PM."
                    ];
                }

                // Calculate total work time
                if ($actualStart && $clock_out) {
                    $total = $clock_out->diffInMinutes($actualStart);

                    // Deduct break time if lunch-in and lunch-out are provided
                    if ($lunch_in && $lunch_out && $lunch_out->gt($lunch_in)) {
                        $breakMinutes = $lunch_out->diffInMinutes($lunch_in);
                        $total -= $breakMinutes;
                    } elseif ($actualStart->lt($breakStart) && $clock_out->gt(Carbon::createFromTime(13))) {
                        $total -= 60; // Deduct lunch hour if necessary
                    }

                    // Check if there is undertime (less than 480 minutes)
                    if ($total < $requiredMinutes) {
                        $ut = $requiredMinutes - $total;
                        $record['aut']['undertime'] = [
                            'minutes' => $ut,
                            'reason' => "Worked only {$total} minute(s), {$ut} minute(s) short of 480 minutes."
                        ];
                    } else {
                        // Calculate overtime if work exceeds 480 minutes
                        $overtime = $total - $requiredMinutes;
                        $record['aut']['overtime'] = [
                            'minutes' => $overtime,
                            'reason' => "Worked {$total} minutes, which is {$overtime} minute(s) of overtime."
                        ];
                    }
                }

    
                // Store the final record for each employee on the respective date
                $final[$date][$employeeId] = $record;
            }
        }
    
        dd($final);
    }
}
