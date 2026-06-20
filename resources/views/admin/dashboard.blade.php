@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2>Admin Dashboard</h2>
    <p>Welcome, {{ auth()->user()->name }}!</p>
</div>
@endsection