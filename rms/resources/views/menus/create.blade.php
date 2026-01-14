@extends('layouts.app')
@section('title','Add Menu')
@section('content')
    <div class="glass" style="padding:16px;">
        <h2>Add Menu</h2>
        <form method="POST" action="{{ route('menus.store') }}" enctype="multipart/form-data">
            @csrf
            <label>Name
                <input name="name" value="{{ old('name') }}" required>
            </label>
            <label>Price
                <input name="price" type="number" step="0.01" value="{{ old('price') }}" required>
            </label>
            <label style="display:flex;gap:8px;align-items:center;margin-top:12px;">
                <input type="checkbox" name="is_available" value="1" checked> Available
            </label>
            <label>Image
                <input type="file" name="image" accept="image/*">
            </label>
            <button class="btn" type="submit" style="margin-top:12px;">Save</button>
        </form>
    </div>
@endsection
