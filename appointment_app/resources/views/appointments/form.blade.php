@extends('layouts.app')

@section('content')
<style>
    .white-text {
        color: white;
    }
</style>
    <div class="container mx-auto mt-8">
        <div class="bg-white dark:bg-gray-800 p-8 rounded-lg shadow-md">
            <h1 class="text-3xl font-semibold mb-4 white-text">Appointments form</h1>

            <form action="{{ route('appointments.search') }}" method="post">
                @csrf

                <div class="mb-4">
                    <label for="userSelect" class="white-text">Select desired consultant:</label>
                    <select name="user" id="userSelect" required>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label for="dateSelect" class="white-text">Select desired day:</label>
                    <input type="date" name="date" id="dateSelect" min="{{ date('Y-m-d') }}" required>
                </div>

                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Check available hours
                </button>
            </form>
        </div>
    </div>
@endsection
