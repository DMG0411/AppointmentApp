@extends('layouts.app')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                                <th>{{ __('Appointment Date') }}</th>
                                <th>{{ __('Appointment Time') }}</th>
                                <th>{{ __('Created at') }}</th>
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
<!-- Script for AJAX call -->
<script>
    // When the document is ready, make the AJAX call
    $(document).ready(function () {
        // AJAX call to fetch appointments data
        $.ajax({
            url: "{{ route('dashboard') }}",
            type: "GET",
            dataType: "json",
            success: function (data) {
                // Populate the table with data received
                var tableBody = $('#appointments-table tbody');
                $.each(data, function (index, appointment) {
                    var row = $('<tr></tr>');
                    row.append('<td>' + appointment.appointment_date + '</td>');
                    row.append('<td>' + appointment.appointment_time + '</td>');
                    row.append('<td>' + appointment.created_at + '</td>');
                    tableBody.append(row);
                });
            },
            error: function (error) {
                console.log("Error fetching appointments data: ", error);
            }
        });
    });
</script>

@endsection
