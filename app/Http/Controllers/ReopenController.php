<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Categ;
use App\Models\Reopen;
use App\Models\Ticket;
use App\Mail\VerifyNew;
use App\Models\Setting;
use App\Models\Student;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Mail\VoidedReopenedTicket;
use Illuminate\Support\Facades\DB;
use App\Mail\OngoingReopenedTicket;
use App\Mail\ResolvedReopenedTicket;
use Illuminate\Support\Facades\Mail;

class ReopenController extends Controller
{
    // ---- STUDENT VIEWS ----
    // Reopen Ticket
    // Verify email first
    public function inputReopen() {
        return view('email.reopen.index');
    }

    // Send code
    public function emailReopen(Request $request) {
        $code = Str::random(6);
        $find = DB::table('students')->where('email', $request->email)->first();
        
        if ($find) {
            $student = Student::find($find->id);

            $setting = DB::table('settings')->get()->toArray();
            $getSetting = Setting::find($setting[0]->id);
            if ($student->ongoingTickets >= $getSetting->ticketLimit) {
                return view('admin.tickets.limit-reached', [
                    'student' => $student
                ]);
            }
            if($student->tickets > $student->ongoingTickets) {
                $formFields['code'] = $code;
                $student->update($formFields);
                Mail::to($student->email)->send(new VerifyNew($student, $code));
                return redirect('/reopen/code');
            }
            return view('email.reopen.no-tickets');
        }
        return redirect('/reopen/code');
    }

    // Show verify code form
    public function codeReopen(){
        return view('email.reopen.code');
    }

    // Verify email with code
    public function verifyReopen(Request $request){
        $find = DB::table('students')->where('code', $request->code)->first();
        if (! $find){
            abort(404, 'Not Found');
        }
        $student = Student::find($find->id);
        // return view();
        return redirect()->route('viewReopen', [$student]);
    }

    // Display Student's Resolved and Inactive Tickets
    public function viewReopen(Student $student) {
        return view('email.reopen.view', [
            'tickets' => $student->tickets()->get()
        ]);
    }

    // View Reopen ticket form
    public function createReopen(Ticket $ticket){
        return view('email.reopen.create', [
            'ticket' => $ticket,
        ]);
    }

    // Reopen ticket
    public function storeReopen(Request $request, Ticket $ticket, Student $student) {
        $formFields = $request->validate([
            'reason' => 'required'
        ]);
        
        if($request->reassign == 1) {
            // dd(count($ticket->reopens));
            if(count($ticket->reopens) != 0) {
                $reopen = DB::table('reopens')->where('ticket_id', $ticket->id)->latest()->first();
                // dd($reopen->user_id);
                $currentUser = $reopen->user_id;
                // $users = DB::table('users')->whereNot('id', $currentUser)->where('verified', true)->where('role', 'FDO')->where('categ_id', 'like', '%' . $ticket->categ->id . '%')->get()->toArray();
                $users = DB::table('usercategs')->whereNot('user_id', $currentUser)->where('categ_id', $ticket->categ->id)->get()->toArray();
                $verified = DB::table('users')->where('verified', true)->where('role', 'FDO')->select('id')->get()->toArray();
                $verifiedUsers = array();
                for ($x=0; $x < count($verified); $x++) {
                    array_push($verifiedUsers, $verified[$x]->id);
                }

            } else {
                // $users = DB::table('users')->whereNot('id', $ticket->user->id)->where('verified', true)->where('role', 'FDO')->where('categ_id', 'like', '%' . $ticket->categ->id . '%')->get()->toArray();
                $users = DB::table('usercategs')->whereNot('user_id', $ticket->user->id)->where('categ_id', $ticket->categ->id)->get()->toArray();
                $verified = DB::table('users')->where('verified', true)->where('role', 'FDO')->select('id')->get()->toArray();
                $verifiedUsers = array();

                for ($x=0; $x < count($verified); $x++) {
                    array_push($verifiedUsers, $verified[$x]->id);
                }

                for ($x=0; $x < count($users); $x++) {
                    if (!(in_array($users[$x]->user_id, $verifiedUsers))){
                        unset($users[$x]);
                    }
                }
            }

            if (count($users) == 0) {
                $admins = DB::table('users')->where('verified', true)->where('role', 'Admin')->get()->toArray();
    
                $min = DB::table('tickets')->where('user_id', $admins[0]->id)->whereNot('status', 'Resolved')->whereNot('status', 'Voided')->count();
                $min_id = $admins[0]->id;
    
                for($x=1; $x<count($users); $x++){
                    $a = DB::table('tickets')->where('user_id', $admins[$x]->id)->whereNot('status', 'Resolved')->whereNot('status', 'Voided')->count();
                    if($min > $a) {
                        $min = $a;
                        $min_id = $admins[$x]->id;
                    }
                }
    
                $formFields['user_id'] = $min_id;
            } else {
                $firstKey = array_key_first($users);
                $min = DB::table('tickets')->where('user_id', $users[$firstKey]->user_id)->whereNot('status', 'Resolved')->count();
                $min_id = $users[$firstKey]->user_id;
                
                for($x=$firstKey+1; $x<count($users); $x++){
                    $a = DB::table('tickets')->where('user_id', $users[$x]->id)->whereNot('status', 'Resolved')->count();
                    if($min > $a) {
                        $min = $a;
                        $min_id = $users[$x]->id;
                    }
                }
                $formFields['user_id'] = $min_id;
            }
        } else {
            if (count($ticket->reopens) != 0){
            // if ($ticket->reopens){
                $reopen = DB::table('reopens')->where('ticket_id', $ticket->id)->latest()->first();
                $reopenUser = $reopen->user_id;
                $formFields['user_id'] = $reopenUser;
            }else {
                $formFields['user_id'] = $ticket->user->id;
            }
        }

        $formFields['ticket_id'] = $ticket->id;

        $studentField['ongoingTickets'] = $student->ongoingTickets + 1;

        Reopen::create($formFields);
        $ticketField['status'] = "Reopened";
        $student->update($studentField);
        $ticket->update($ticketField);

        return view('email.reopen.submitted');
    }

    // Update Ticket Priority
    public function updatePriority(Request $request, Reopen $reopen) {
        if ($reopen->user->id != auth()->id()){
            abort(403, 'Unauthorized Action');
        }

        $formFields = $request->validate([
            'priority' => 'required'
        ]);

        $ticket = Ticket::find($reopen->ticket->id);

        if ($request->priority == "High" || $request->priority == "Medium" || $request->priority == "Low"){
            if($ticket->status == "New"){
                $formFields['status'] = "Opened"; 
            }
            $ticket->update($formFields);
            return redirect()->route('ticket', [$ticket]);
        } else {
            auth()->logout();
        }
    }

    // Mark as ongoing
    public function setOngoing(Ticket $ticket, Reopen $reopen) {
        if ($reopen->user->id != auth()->id()){
            abort(403, 'Unauthorized Action');
        }

        $ticket = Ticket::find($reopen->ticket->id);

        $formFields['status'] = "Ongoing";

        Mail::to($ticket->student->email)->send(new OngoingReopenedTicket($reopen, $ticket));
        
        $ticket->update($formFields);
        
        return redirect()->route('ticket', [$ticket]);
    }

    // Display Void Ticket Form
    public function void(Reopen $reopen) {
        return view('admin.reopen.void', [
            'reopen' => $reopen
        ]);
    }

    // Set Ticket as Voided
    public function setVoided(Request $request, Reopen $reopen) {
        if ($reopen->user->id != auth()->id()){
            abort(403, 'Unauthorized Action');
        }

        $formFields = $request->validate([
            'response' => 'required'
        ]);

        $formFields['status'] = "Void";
        $formFields['dateResponded'] = now();

        $ticket = Ticket::find($reopen->ticket->id);
        $ticketField['status'] = "Voided";

        $student = Student::find($ticket->student_id);
        $studentField['ongoingTickets'] = $student->ongoingTickets - 1;

        $ticket->update($ticketField);
        $student->update($studentField);
        $reopen->update($formFields);

        Mail::to($ticket->student->email)->send(new VoidedReopenedTicket($reopen, $ticket));

        return redirect()->route('ticket', [$ticket]);
    }

    // Display resolve ticket form
    public function resolve(Reopen $reopen) {
        if ($reopen->user->id != auth()->id()){
            abort(403, 'Unauthorized Action');
        }
        return view('admin.reopen.resolve', [
            'reopen' => $reopen
        ]);
    }

    // Set status as pending while waiting for student's feedback
    public function setPending(Request $request, Reopen $reopen) {
        $formFields = $request->validate([
            'response' => 'required'
        ]);

        $formFields['status'] = "Resolve";
        $formFields['dateResponded'] = now();

        $ticket = Ticket::find($reopen->ticket->id);
        $ticketField['status'] = "Pending";

        // $student = Student::find($ticket->student_id);
        // $studentField['ongoingTickets'] = $student->ongoingTickets - 1;

        $ticket->update($ticketField);
        // $student->update($studentField);
        $reopen->update($formFields);

        Mail::to($ticket->student->email)->send(new ResolvedReopenedTicket($reopen, $ticket));

        return redirect()->route('ticket', [$ticket]);
    }

    // Show transfer ticket form
    public function transfer(Reopen $reopen) {
        if ($reopen->user->id != auth()->id()){
            abort(403, 'Unauthorized Action');
        }
        return view('admin.reopen.transfer', [
            'reopen' => $reopen,
            'ticket' => $reopen->ticket,
            'categs' => Categ::all(),
            'users' => User::all()
        ]);
    }

    // Transfer Ticket
    public function setTransfer(Request $request, Reopen $reopen) {
        if ($reopen->user->id != auth()->id()){
            abort(403, 'Unauthorized Action');
        }

        $ticket = Ticket::find($reopen->ticket->id);

        if (!$request->categ_id && !$request->user_id){
            return back()->withErrors(['categ_id' => 'Form is empty'])->onlyInput('categ_id');
        }

        if ($request->categ_id) {
            $formFields['categ_id'] = $request->categ_id;
        }

        if ($request->user_id) {
            $formFields['user_id'] = $request->user_id;
            $reopenFields['user_id'] = $request->user_id;
        } else {
            if ($ticket->status != "Resolved" && $ticket->status != "Voided") {
                // $users = DB::table('users')->where('verified', true)->where('categ_id', 'like', '%' . $request->categ_id . '%')->get()->toArray();
                $users = DB::table('usercategs')->where('categ_id', $request->categ_id)->get()->toArray();
                $verified = DB::table('users')->where('verified', true)->where('role', 'FDO')->select('id')->get()->toArray();
                $verifiedUsers = array();
                for ($x=0; $x < count($verified); $x++) {
                    array_push($verifiedUsers, $verified[$x]->id);
                }

                for ($x=0; $x < count($users); $x++) {
                    if (!(in_array($users[$x]->user_id, $verifiedUsers))){
                        unset($users[$x]);
                    }
                }

                if (count($users) == 0) {
                    $admins = DB::table('users')->where('verified', true)->where('role', 'Admin')->get()->toArray();
    
                    $min = DB::table('tickets')->where('user_id', $admins[0]->id)->whereNot('status', 'Resolved')->count();
                    $min_id = $admins[0]->id;
    
                    for($x=1; $x<count($users); $x++){
                        $a = DB::table('tickets')->where('user_id', $admins[$x]->id)->whereNot('status', 'Resolved')->count();
                        if($min > $a) {
                            $min = $a;
                            $min_id = $admins[$x]->id;
                        }
                    }
                    $formFields['user_id'] = $min_id;
                    $reopenFields['user_id'] = $min_id;
                } else {
                    $min = DB::table('tickets')->where('user_id', $users[0]->id)->whereNot('status', 'Resolved')->count();
                    $min_id = $users[0]->id;
                    
                    for($x=1; $x<count($users); $x++){
                        $a = DB::table('tickets')->where('user_id', $users[$x]->id)->whereNot('status', 'Resolved')->count();
                        if($min > $a) {
                            $min = $a;
                            $min_id = $users[$x]->id;
                        }
                    }
                    $formFields['user_id'] = $min_id;
                    $reopenFields['user_id'] = $min_id;
                }
            }
        }

        $ticket->update($formFields);
        $reopen->update($reopenFields);
        return redirect()->route('ticket', [$ticket]);
    }
}
