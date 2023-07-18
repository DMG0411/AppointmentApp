<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\AppointmentController;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/appointments/form', 'App\Http\Controllers\AppointmentController@createForm')->name('appointments.form');
Route::post('/appointments/search', 'App\Http\Controllers\AppointmentController@search')->name('appointments.search');
Route::post('/appointments/store', 'App\Http\Controllers\AppointmentController@store')->name('appointments.store');

Route::post('/search-available-hours', function () {
    $selectedDate = request('selected_date');
    $selectedUserId = request('user_id');
    $selectedHour = request('selected_hour');

    if (!isUserAvailable($selectedUserId, $selectedDate)) {
        return view('user_not_available', compact('selectedDate', 'selectedUserId'));
    }

    $bookedHours = DB::table('appointments')
        ->whereDate('appointment_date', '=', $selectedDate)
        ->where('user_id', '=', $selectedUserId)
        ->pluck('appointment_time');

    if ($bookedHours->contains($selectedHour)) {
        return view('hour_already_booked', compact('selectedDate', 'selectedHour', 'selectedUserId'));
    } else {
        DB::table('appointments')->insert([
            'user_id' => $selectedUserId,
            'appointment_date' => $selectedDate,
            'appointment_time' => $selectedHour,
        ]);

        return view('booking_success', compact('selectedDate', 'selectedHour', 'selectedUserId'));
    }
})->name('searchAvailableHours');

Route::get('/get-available-hours/{userId}/{selectedDate}', function ($userId, $selectedDate) {
    $selectedUser = User::findOrFail($userId);
    $availableHours = generateAllHoursInInterval($selectedUser, $selectedDate);

    return response()->json($availableHours);
});


Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [AppointmentController::class, 'showDashboard'])->name('dashboard');
});

Route::get('/welcome', function () {
    return view('welcome');
})->name('welcome');


require __DIR__.'/auth.php';

function generateAllHoursInInterval($selectedUser, $selectedDate)
{
    $intervals = [
        ['start' => '09:00', 'end' => '13:00'],
        ['start' => '15:30', 'end' => '21:00'],
    ];

    $bookedHours = DB::table('bookings')
        ->whereDate('booking_date', '=', $selectedDate)
        ->where('user_id', '=', $selectedUser->id)
        ->pluck('booking_time');

    $availableHours = collect([]);

    foreach ($intervals as $interval) {
        $startHour = Carbon::createFromFormat('H:i', $interval['start']);
        $endHour = Carbon::createFromFormat('H:i', $interval['end']);

        while ($startHour < $endHour) {
            $formattedHour = $startHour->format('H:i');

            if (!isBooked($selectedDate, $formattedHour, $selectedUser->id) && isHalfHourApart($formattedHour, $bookedHours)) {
                $availableHours->push($formattedHour);
            }

            $startHour->addMinutes(30);
        }
    }

    return $availableHours;
}

function isHalfHourApart($selectedTime, $bookedHours)
{
    $selectedTimeCarbon = Carbon::createFromFormat('H:i', $selectedTime);

    foreach ($bookedHours as $bookedHour) {
        $bookedHourCarbon = Carbon::createFromFormat('H:i', $bookedHour);

        if (abs($selectedTimeCarbon->diffInMinutes($bookedHourCarbon)) < 30) {
            return false;
        }
    }

    return true;
}

function isUserAvailable($userId, $date)
{
    $bookedDates = DB::table('bookings')
        ->where('user_id', '=', $userId)
        ->pluck('booking_date');

    return !$bookedDates->contains($date);
}

function isBooked($selectedDate, $selectedTime)
{
    return DB::table('bookings')
        ->whereDate('booking_date', '=', $selectedDate)
        ->where('booking_time', '=', $selectedTime)
        ->exists();
}