@extends('layouts.app')
@section('title','Add Floor')
@section('content')
    <div class="glass" style="padding:16px;">
        <h2>Add Floor</h2>
        <form method="POST" action="{{ route('floors.store') }}">
            @csrf
            <label>Name
                <input name="name" value="{{ old('name') }}" required>
            </label>
            <button class="btn" type="submit" style="margin-top:12px;">Save</button>
        </form>
    </div>
@endsection
