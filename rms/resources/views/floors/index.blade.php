@extends('layouts.app')
@section('title','Floors')
@section('content')
    <div class="glass" style="padding:16px;">
        <div style="display:flex;justify-content:space-between;align-items:center;">
            <h2>Floors</h2>
            <a class="btn" href="{{ route('floors.create') }}">Add Floor</a>
        </div>
        <table>
            <thead><tr><th>Name</th><th></th></tr></thead>
            <tbody>
                @forelse($floors as $floor)
                    <tr>
                        <td>{{ $floor->name }}</td>
                        <td>
                            <a class="btn" href="{{ route('floors.edit',$floor) }}">Edit</a>
                            <form class="inline" method="POST" action="{{ route('floors.destroy',$floor) }}">
                                @csrf @method('DELETE')
                                <button class="btn" type="submit" onclick="return confirm('Delete?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="2">No floors</td></tr>
                @endforelse
            </tbody>
        </table>
        {{ $floors->links() }}
    </div>
@endsection
