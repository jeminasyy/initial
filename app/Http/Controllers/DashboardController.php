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

        // Get the categories for each type
        $requests = DB::table('categs')->where('type', 'Request')->select('id')->get()->toArray();
        $inquiries = DB::table('categs')->where('type', 'Inquiries')->select('id')->get()->toArray();
        $concerns = DB::table('categs')->where('type', 'Concerns')->select('id')->get()->toArray();
        $others = DB::table('categs')->where('type', 'Others')->select('id')->get()->toArray();

        // Get the total number of satisfied solution (new and reopened)
        $ticketSatisfied = Rating::where('satisfied', 1)->count();
        $reopenSatisfied = Reopenrating::where('satisfied', 1)->count();
        $satisfied = $ticketSatisfied + $reopenSatisfied;

        // Get the total number of rating/feedback (new and reopened)
        $totalTicketRating = Rating::count();
        $totalReopenRating = Reopenrating::count();
        $totalRating = $totalTicketRating + $totalReopenRating;

        // Calculate the satisfaction rating
        $calculate = ($satisfied / $totalRating) * 100;
        // Round up to 2 decimal points
        $studentSatisfaction = round($calculate, 2);

        $requestThisMonth = 0;
        $inquiryThisMonth = 0;
        $concernThisMonth = 0;
        $otherThisMonth = 0;

        // Get the total number of requests this month
        for ($x=0; $x < count($requests); $x++) {
            $add = Ticket::where('categ_id', $requests[$x]->id)->whereMonth('created_at', $thisMonth)->whereYear( 'created_at',$thisYear)->count();
            $requestThisMonth = $requestThisMonth + $add;
        }

        // Get the total number of inquiries this month
        for ($x=0; $x < count($inquiries); $x++) {
            $add = Ticket::where('categ_id', $inquiries[$x]->id)->whereMonth('created_at', $thisMonth)->whereYear( 'created_at',$thisYear)->count();
            $inquiryThisMonth = $inquiryThisMonth + $add;
        }

        // Get the total number of concern this month
        for ($x=0; $x < count($concerns); $x++) {
            $add = Ticket::where('categ_id', $concerns[$x]->id)->whereMonth('created_at', $thisMonth)->whereYear( 'created_at',$thisYear)->count();
            $concernThisMonth = $concernThisMonth + $add;
        }

        // Get the total number of others this month
        for ($x=0; $x < count($others); $x++) {
            $add = Ticket::where('categ_id', $others[$x]->id)->whereMonth('created_at', $thisMonth)->whereYear( 'created_at',$thisYear)->count();
            $otherThisMonth = $otherThisMonth + $add;
        }

        // Get the array of all tickets_id
        // Get the count of reopen with the ticket_id (Reopens table)
        // Push to array
        // Calculate the average

        $ticket_ids = DB::table('tickets')->select('id')->get()->toArray();
        $reopens = array();

        for ($x=0; $x < count($ticket_ids); $x++) {
            $count = Reopen::where('ticket_id', $ticket_ids[$x]->id)->count();
            array_push($reopens, $count);
        }

        $averageReopen = round(array_sum($reopens) / count($reopens));
 
        // Get the start and end date of each tickets
        // Calculate the diff of each start and end dates
        // Get the average

        $ticket_dates = DB::table('tickets')->select('created_at', 'dateResponded')->get()->toArray();
        $intervalsNew = array();

        $reopen_dates = DB::table('reopens')->select('created_at', 'dateResponded')->get()->toArray();
        $intervalsReopen = array();

        for ($x=0; $x < count($ticket_dates); $x++) {
            $interval = date_diff($ticket_dates[$x]->created_at, $ticket_dates[$x]->date_Responded);
            array_push($intervalsNew, $interval);
        }

        for ($x=0; $x < count($reopen_dates); $x++) {
            $interval = date_diff($reopen_dates[$x]->created_at, $reopen_dates[$x]->date_Responded);
            array_push($intervalsReopen, $interval);
        }

        $n = count($ticket_dates) + count($reopen_dates);
        dd($n);

        $sum = array_sum($ticket_dates) + array_sum($reopen_dates);

        return view('dashboard.index', compact('totalTickets', 'newTickets', 'resolvedTickets', 'reopenedTickets',
                                                'requestThisMonth', 'inquiryThisMonth', 'concernThisMonth', 'otherThisMonth',
                                                'studentSatisfaction', 'averageReopen'));
    }
}
