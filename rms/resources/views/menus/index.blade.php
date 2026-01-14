@extends('layouts.app')
@section('title','Menus')
@section('content')
    <div class="glass" style="padding:16px;">
        <div style="display:flex;justify-content:space-between;align-items:center;">
            <h2>Menus</h2>
            <a class="btn" href="{{ route('menus.create') }}">Add Menu</a>
        </div>
        <table>
            <thead>
                <tr><th>Image</th><th>Name</th><th>Price</th><th>Available</th><th></th></tr>
            </thead>
            <tbody>
                @forelse($menus as $menu)
                    <tr>
                        <td>@if($menu->image)<img src="{{ asset('storage/'.$menu->image) }}" alt="" style="height:40px;">@endif</td>
                        <td>{{ $menu->name }}</td>
                        <td>${{ number_format($menu->price,2) }}</td>
                        <td>{{ $menu->is_available ? 'Yes' : 'No' }}</td>
                        <td>
                            <a class="btn" href="{{ route('menus.edit',$menu) }}">Edit</a>
                            <form class="inline" method="POST" action="{{ route('menus.destroy',$menu) }}">
                                @csrf @method('DELETE')
                                <button class="btn" type="submit" onclick="return confirm('Delete?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4">No menus</td></tr>
                @endforelse
            </tbody>
        </table>
        {{ $menus->links() }}
    </div>
@endsection
