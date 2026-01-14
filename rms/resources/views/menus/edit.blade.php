@extends('layouts.app')
@section('title','Edit Menu')
@section('content')
    <div class="glass" style="padding:16px;">
        <h2>Edit Menu</h2>
        <form method="POST" action="{{ route('menus.update',$menu) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <label>Name
                <input name="name" value="{{ old('name', $menu->name) }}" required>
            </label>
            <label>Price
                <input name="price" type="number" step="0.01" value="{{ old('price', $menu->price) }}" required>
            </label>
            <label style="display:flex;gap:8px;align-items:center;margin-top:12px;">
                <input type="checkbox" name="is_available" value="1" {{ $menu->is_available ? 'checked' : '' }}> Available
            </label>
            <label>Image
                <input type="file" name="image" accept="image/*">
            </label>
            @if($menu->image)
                <div style="margin-top:8px;">
                    <img src="{{ asset('storage/'.$menu->image) }}" alt="" style="height:60px;">
                </div>
            @endif
            <button class="btn" type="submit" style="margin-top:12px;">Update</button>
        </form>
    </div>
@endsection
