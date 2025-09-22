<?php

namespace App\Services\Statistics;

use App\Models\SupportTicket;
use DB;

class SupportService 
{
    public function getOpenTickets()
    {
        $total = SupportTicket::select(DB::raw("count(id) as data"))
                ->where('status', 'Open')
                ->get();  
        
        return $total[0]['data'];
    }


    public function getRepliedTickets()
    {
        $total = SupportTicket::select(DB::raw("count(id) as data"))
                ->where('status', 'Replied')
                ->get();  
        
        return $total[0]['data'];
    }


    public function getPendingTickets()
    {
        $total = SupportTicket::select(DB::raw("count(id) as data"))
                ->where('status', 'Pending')
                ->get();  
        
        return $total[0]['data'];
    }


    public function getResolvedTickets()
    {
        $total = SupportTicket::select(DB::raw("count(id) as data"))
                ->where('status', 'Resolved')
                ->get();  
        
        return $total[0]['data'];
    }


    public function getClosedTickets()
    {
        $total = SupportTicket::select(DB::raw("count(id) as data"))
                ->where('status', 'Closed')
                ->get();  
        
        return $total[0]['data'];
    }


    public function ticketsToday()
    {
        $today = \Carbon\Carbon::today();

         $total = SupportTicket::select(DB::raw("count(id) as data"))
                ->whereDate('created_at', $today) 
                ->get();  
        
        return $total[0]['data'];
    }


    public function ticketsCurrentMonth()
    {
        $total = SupportTicket::select(DB::raw("count(id) as data"))
                ->whereMonth('created_at', date('m'))
                ->whereYear('created_at', date('Y'))
                ->get();  
        
        return $total[0]['data'];
    }


    public function ticketsPastMonth()
    {
        $date = \Carbon\Carbon::now();
        $pastMonth =  $date->subMonth()->format('m');

        $total = SupportTicket::select(DB::raw("count(id) as data"))
                ->whereMonth('created_at', $pastMonth)
                ->whereYear('created_at', date('Y'))
                ->get();  
        
        return $total[0]['data'];
    }


     public function ticketsCurrentYear()
    {
        $total = SupportTicket::select(DB::raw("count(id) as data"))
                ->whereYear('created_at', date('Y'))
                ->get();  
        
        return $total[0]['data'];
    }

}