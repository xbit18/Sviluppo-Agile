@extends('user.layouts.layout')

@section('content')


  <!-- ***** Breadcrumb Area Start ***** -->
  <div class="breadcumb-area bg-img bg-overlay" style="background-image: url({{ asset('img/bg-img/2.jpg') }});">
    <div class="container h-100">
      <div class="row h-100 align-items-center">
        <div class="col-12">
          <h2 class="title mt-70">Party created succesfully</h2>
        </div>
      </div>
    </div>
  </div>
  <div class="breadcumb--con">
    <div class="container">
      <div class="row">
        <div class="col-12">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="fa fa-home"></i> Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">New Party Created</li>
            </ol>
          </nav>
        </div>
      </div>
    </div>
  </div>
  <!-- ***** Breadcrumb Area End ***** -->


    <div class="container main-container">
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
    </div>
@endsection
