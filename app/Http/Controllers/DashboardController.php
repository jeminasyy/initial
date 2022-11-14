<?php

namespace App\Http\Controllers;

use App\Models\Categ;
use App\Models\Rating;
use App\Models\Reopen;
use App\Models\Reopenrating;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function dashboard() {
        $totalTickets = Ticket::count();
        $newTickets = Ticket::where('status', 'New')->count();
        $resolvedTickets = Ticket::where('status', 'Resolved')->count();
        $reopenedTickets = Reopen::count();

        $thisMonth = Carbon::now()->format('m');
        $thisYear = Carbon::now()->format('Y');

        $requestThisMonth = 0;
        $inquiryThisMonth = 0;
        $concernThisMonth = 0;
        $otherThisMonth = 0;

        $requests = DB::table('categs')->where('type', 'Request')->get()->toArray();
        $inquiries = DB::table('categs')->where('type', 'Inquiries')->get()->toArray();
        $concerns = DB::table('categs')->where('type', 'Concerns')->get()->toArray();
        $others = DB::table('categs')->where('type', 'Others')->get()->toArray();

        $ticketSatisfied = Rating::where('satisfied', 1)->count();
        $reopenSatisfied = Reopenrating::where('satisfied', 1)->count();
        $satisfied = $ticketSatisfied + $reopenSatisfied;

        $totalTicketRating = Rating::count();
        $totalReopenRating = Reopenrating::count();
        $totalRating = $totalTicketRating + $totalReopenRating;

        $studentSatisfaction = ($satisfied / $totalRating) * 100;

        for ($x=0; $x < count($requests); $x++) {
            $add = Ticket::where('categ_id', $requests[$x]->id)->whereMonth('created_at', $thisMonth)->whereYear( 'created_at',$thisYear)->count();
            $requestThisMonth = $requestThisMonth + $add;
        }

        for ($x=0; $x < count($inquiries); $x++) {
            $add = Ticket::where('categ_id', $inquiries[$x]->id)->whereMonth('created_at', $thisMonth)->whereYear( 'created_at',$thisYear)->count();
            $inquiryThisMonth = $inquiryThisMonth + $add;
        }

        for ($x=0; $x < count($concerns); $x++) {
            $add = Ticket::where('categ_id', $concerns[$x]->id)->whereMonth('created_at', $thisMonth)->whereYear( 'created_at',$thisYear)->count();
            $concernThisMonth = $concernThisMonth + $add;
        }

        for ($x=0; $x < count($others); $x++) {
            $add = Ticket::where('categ_id', $others[$x]->id)->whereMonth('created_at', $thisMonth)->whereYear( 'created_at',$thisYear)->count();
            $otherThisMonth = $otherThisMonth + $add;
        }

        return view('dashboard.index', compact('totalTickets', 'newTickets', 'resolvedTickets', 'reopenedTickets',
                                                'requestThisMonth', 'inquiryThisMonth', 'concernThisMonth', 'otherThisMonth',
                                                'studentSatisfaction'));
    }
}
