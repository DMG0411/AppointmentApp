<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Carbon;
use App\Models\Appointment;
use Illuminate\Support\Facades\Auth;
use Carbon\CarbonInterval;


class AppointmentController extends Controller
{

    public function createForm()
    {
        $users = User::all();
        return view('appointments.form', compact('users'));
    }
    
    public function search(Request $request)
    {
        $user = $request->input('user');
        $date = $request->input('date');

        session()->flash('selected_user', $user);
        session()->flash('selected_date', $date);

        $selectedDate = Carbon::parse($date);
        if ($selectedDate->isWeekend()) {
            return redirect()->route('appointments.form')->with('error', 'Nu se pot face programări în weekend.');
        }

        $appointments = Appointment::where('user_id', $user)
            ->whereDate('appointment_date', $date)
            ->orderBy('appointment_time')
            ->get();

        $morningStart = Carbon::parse('09:00');
        $afternoonEnd = Carbon::parse('21:00');

        // Create a set with available hours in the interval 9:00 - 21:00
        $allAvailableHours = [];
        $currentTime = $morningStart;
        while ($currentTime <= $afternoonEnd) {
            $allAvailableHours[] = $currentTime->format('H:i');
            $currentTime->addMinutes(30);
        }


        // Excluding the unavailable housr and overriding hours (12:30, 13:00, 13:30, 14:00, 14:30, 20:30, 21:00)
        $excludedHours = ['12:30', '13:00', '13:30', '14:00', '14:30', '15:00','20:30', '21:00'];
        foreach ($appointments as $appointment) {
            $startTime = Carbon::parse($appointment->appointment_time);
            $oneHourBeforeStartTime = $startTime->copy()->subHour()->format('H:i');
            $thirtyMinutesBeforeStartTime = $startTime->copy()->subMinutes(30)->format('H:i');
            $excludedHours[] = $startTime->format('H:i'); // Add appointment hour
            $excludedHours[] = $oneHourBeforeStartTime; // Add hour with 60 mins less then appointment
            $excludedHours[] = $thirtyMinutesBeforeStartTime; // Add hour with 30 mins less then appointment

            // Exclude hour that overrides
            $endTime = $startTime->copy()->addHours(1)->addMinutes(30);
            while ($startTime < $endTime) {
                $excludedHours[] = $startTime->format('H:i');
                $startTime->addMinutes(30);
            }
        }

        $availableHours = array_diff($allAvailableHours, $excludedHours);

        return view('appointments.results', compact('availableHours'));
    }

    public function index(){
        $user = Auth::user();
        $appointments = Appointment::where('user_id', $user->id)->get();
        return view('dashboard',compact('appointments'));
    }

    public function store(Request $request)
    {

        $userId = session()->get('selected_user');
        $date = session()->get('selected_date');
        $hour = $request->input('hour');

        $appointment = new Appointment();
        $appointment->user_id = $userId;
        $appointment->appointment_date = $date;
        $appointment->appointment_time = $hour;
        $appointment->save();

        return redirect()->route('appointments.form')->with('success', 'The appointment was made succefully!');
    }
}

