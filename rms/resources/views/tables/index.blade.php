@extends('layouts.app')
@section('title','Tables')
@section('content')
    <div class="glass" style="padding:16px;">
        <div style="display:flex;justify-content:space-between;align-items:center;">
            <h2>Tables</h2>
            <a class="btn" href="{{ route('tables.create') }}">Add Table</a>
        </div>
        <table>
            <thead><tr><th>Name</th><th>Floor</th><th>Capacity</th><th>Status</th><th></th></tr></thead>
            <tbody>
                @forelse($tables as $table)
                    <tr>
                        <td>{{ $table->name }}</td>
                        <td>{{ $table->floor?->name }}</td>
                        <td>{{ $table->capacity }}</td>
                        <td>{{ ucfirst($table->status) }}</td>
                        <td>
                            <a class="btn" href="{{ route('tables.edit',$table) }}">Edit</a>
                            <form class="inline" method="POST" action="{{ route('tables.destroy',$table) }}">
                                @csrf @method('DELETE')
                                <button class="btn" type="submit" onclick="return confirm('Delete?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5">No tables</td></tr>
                @endforelse
            </tbody>
        </table>
        {{ $tables->links() }}
    </div>
@endsection
