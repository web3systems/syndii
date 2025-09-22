<?php

namespace App\Services\Statistics;

use App\Models\User;
use DB;

class RegistrationService 
{
    private $year;
    private $month;

    public function __construct(int $year, int $month)
    {
        $this->year = $year;
        $this->month = $month;
    }


    public function getAllUsers()
    {
        $users = User::select(DB::raw("count(id) as data"), DB::raw("MONTH(created_at) month"))
                ->whereYear('created_at', $this->year)               
                ->groupBy('month')
                ->orderBy('month')
                ->get()->toArray();  
        
        $data = [];

        for($i = 1; $i <= 12; $i++) {
            $data[$i] = 0;
        }

        foreach ($users as $row) {				            
            $month = $row['month'];
            $data[$month] = intval($row['data']);
        }
        
        return $data;
    }


    public function getNewUsersCurrentMonth()
    {
        $total_users = User::select(DB::raw("count(id) as data"))
                ->whereMonth('created_at', $this->month)
                ->get();  
        
        return $total_users[0]['data'];
    }


    public function getNewUsersPastMonth()
    {
        $date = \Carbon\Carbon::now();
        $pastMonth =  $date->subMonth()->format('m');

        $total_users = User::select(DB::raw("count(id) as data"))
                ->whereMonth('created_at', $pastMonth)
                ->get();  
        
        return $total_users[0]['data'];
    }


    public function getTotalUsers()
    {
        $total_users = User::select(DB::raw("count(id) as data"))
                ->get();  
        
        return $total_users[0]['data'];
    }


    public function getTotalSubscribers()
    {
        $total_users = User::select(DB::raw("count(id) as data"))
                ->where('group', 'subscriber')
                ->get();  
        
        return $total_users[0]['data'] ?? 0;
    }


    public function getTotalNonSubscribers()
    {
        $total_users = User::select(DB::raw("count(id) as data"))
                ->where('group', 'user')
                ->get();  
        
        return $total_users[0]['data'] ?? 0;
    }

     public function getTotalReferred()
    {
        $total_users = User::select(DB::raw("count(id) as data"))
                ->whereNotNull('referred_by')
                ->get();  
        
        return $total_users[0]['data'] ?? 0;
    }


    public function getNewUsersCurrentYear()
    {
        $total_users = User::select(DB::raw("count(id) as data"))
                ->whereYear('created_at', $this->year)
                ->get();  
        
        return $total_users[0]['data'];
    }


    public function getNewSubscribersCurrentMonth()
    {
        $total_users = User::select(DB::raw("count(id) as data"))
                ->whereMonth('created_at', $this->month)
                ->where('group', 'subscriber')
                ->get();  
        
        return $total_users[0]['data'];
    }


    public function getNewSubscribersPastMonth()
    {
        $date = \Carbon\Carbon::now();
        $pastMonth =  $date->subMonth()->format('m');

        $total_users = User::select(DB::raw("count(id) as data"))
                ->whereMonth('created_at', $pastMonth)
                ->where('group', 'subscriber')
                ->get();  
        
        return $total_users[0]['data'];
    }

    public function getNewSubscribersCurrentYear()
    {
        $total_users = User::select(DB::raw("count(id) as data"))
                ->whereYear('created_at', $this->year)
                ->where('group', 'subscriber')
                ->get();  
        
        return $total_users[0]['data'];
    }


    public function registrationsToday()
    {
        $today = \Carbon\Carbon::today();

        $total_users = User::select(DB::raw("count(id) as data"))
                ->whereDate('created_at', $today)  
                ->get();  
        
        return $total_users[0]['data'] ?? 0;
    }


    public function subscribersToday()
    {
        $today = \Carbon\Carbon::today();

        $total_users = User::select(DB::raw("count(id) as data"))
                ->whereDate('created_at', $today)  
                ->where('group', 'subscriber')
                ->get();  
        
        return $total_users[0]['data'] ?? 0;
    }


    public function onlineToday()
    {
         $twoHoursAgo = \Carbon\Carbon::now()->subHours(2);

        $count = DB::table('sessions')
            ->where('last_activity', '>=', $twoHoursAgo->timestamp)
            ->distinct('ip_address')
            ->count('ip_address');
        
        return $count;
    }

}