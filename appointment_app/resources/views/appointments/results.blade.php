@extends('layouts.app')

@section('content')
    <div class="container mx-auto mt-8">
        <div class="bg-white dark:bg-gray-800 p-8 rounded-lg shadow-md">
            <h1 class="text-3xl font-semibold mb-4">Available hours</h1>

            <form action="{{ route('appointments.store') }}" method="post">
                @csrf

                <div class="mb-4">
                    <label for="availableHoursSelect">Select available hour:</label>
                    <select name="hour" id="availableHoursSelect" required>
                        @foreach ($availableHours as $hour)
                            <option value="{{ $hour }}">{{ $hour }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    Submit
                </button>
            </form>
        </div>
    </div>
@endsection
