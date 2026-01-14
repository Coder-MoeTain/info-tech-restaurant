@extends('layouts.app')
@section('title','Add Table')
@section('content')
    <div class="glass" style="padding:16px;">
        <h2>Add Table</h2>
        <form method="POST" action="{{ route('tables.store') }}">
            @csrf
            <label>Name
                <input name="name" value="{{ old('name') }}" required>
            </label>
            <label>Capacity
                <input name="capacity" type="number" min="1" value="{{ old('capacity',4) }}" required>
            </label>
            <label>Floor
                <select name="floor_id">
                    @foreach($floors as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </label>
            <label>Status
                <select name="status">
                    <option value="available">Available</option>
                    <option value="occupied">Occupied</option>
                    <option value="reserved">Reserved</option>
                    <option value="cleaning">Cleaning</option>
                </select>
            </label>
            <button class="btn" type="submit" style="margin-top:12px;">Save</button>
        </form>
    </div>
@endsection
