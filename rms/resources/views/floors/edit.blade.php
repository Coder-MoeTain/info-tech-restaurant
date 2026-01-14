@extends('layouts.app')
@section('title','Edit Floor')
@section('content')
    <div class="glass" style="padding:16px;">
        <h2>Edit Floor</h2>
        <form method="POST" action="{{ route('floors.update',$floor) }}">
            @csrf @method('PUT')
            <label>Name
                <input name="name" value="{{ old('name', $floor->name) }}" required>
            </label>
            <button class="btn" type="submit" style="margin-top:12px;">Update</button>
        </form>
    </div>
@endsection
