@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Dashboard</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                            Party created successfully!<br>
                            Name: {{ $party->name }}<br>
                            Genre: {{ $party->genre }}<br>
                            Mood: {{ $party->mood }}<br>
                            Type: {{ $party->type }}<br>
                            Source: {{ $party->source }}<br>
                        <br><br>
                    </div>
                </div>
            </div>
        </div>
@endsection
