@extends('layouts.app')
@section('title','Edit Table')
@section('content')
    <div class="glass" style="padding:16px;">
        <h2>Edit Table</h2>
        <form method="POST" action="{{ route('tables.update',$table) }}">
            @csrf @method('PUT')
            <label>Name
                <input name="name" value="{{ old('name', $table->name) }}" required>
            </label>
            <label>Capacity
                <input name="capacity" type="number" min="1" value="{{ old('capacity', $table->capacity) }}" required>
            </label>
            <label>Floor
                <select name="floor_id">
                    @foreach($floors as $id => $name)
                        <option value="{{ $id }}" {{ $table->floor_id == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </label>
            <label>Status
                <select name="status">
                    @foreach(['available','occupied','reserved','cleaning'] as $status)
                        <option value="{{ $status }}" {{ $table->status === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </label>
            <button class="btn" type="submit" style="margin-top:12px;">Update</button>
        </form>
    </div>
@endsection
