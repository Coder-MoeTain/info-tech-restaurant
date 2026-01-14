@extends('layouts.app')
@section('title','Dashboard')
@section('content')
    <div class="glass" style="padding:16px;">
        <h2>Dashboard</h2>
        <div class="grid" style="grid-template-columns: repeat(auto-fit,minmax(180px,1fr)); margin-top:12px;">
            <div class="glass" style="padding:12px;">Menus: {{ $menuCount }}</div>
            <div class="glass" style="padding:12px;">Floors: {{ $floorCount }}</div>
            <div class="glass" style="padding:12px;">Tables: {{ $tableCount }}</div>
        </div>
    </div>
@endsection
