@extends('layout')
@section('casino')

    <div class="background-section">
        <div class="header-container">
            <div class="poker-chip">
                <div class="inner-border"></div>
                <div class="inner-circle"></div>
                <span class="chip-letter">$</span>
            </div>
            <h1>{{$casino->name}}</h1>
        </div>
        <div class="overlay">
            <div class="light light1"></div>
            <div class="light light2"></div>
            <div class="light light3"></div>
            <div class="light light4"></div>
            <div class="light light5"></div>
            <div class="light light6"></div>
            <div class="light light7"></div>
            <div class="light light8"></div>
            <div class="light light9"></div>
        </div>
        @include("breadcrumb")
    </div>

    <div class="feuille-container">
        <section class="feuille">
            <h2>{{$casinoDetail->title}}</h2>
            <img src="/img/casino/{{$casino->img_url}}" alt="Description de l'image" class="image-casino">
            @include("menu-casino")
            @include("content-casino")
            @include("sidebar")
        </section>
    </div>





@endsection
