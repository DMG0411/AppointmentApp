@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12 text-center">
            <div class="card">
                <div class="card-header">{{ __('Appointments') }}</div>
                <div class="card-body">
                    <table class="table" border="1">
                        <thead>
                            <tr>
                                <th>{{ __('Appointment Date | ') }}</th>
                                <th>{{ __(' Appointment Time | ') }}</th>
                                <th>{{ __(' Created at') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($appointments as $appointment)
                                <tr>
                                    <td>{{ $appointment->appointment_date }}</td>
                                    <td>{{ $appointment->appointment_time }}</td>
                                    <td>{{ $appointment->created_at }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
