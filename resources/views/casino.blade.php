@extends('layout')

@section('page_title', $casinoDetail->seo_title)

@section('page_description',  $casinoDetail->seo_description)

@section('page_keywords',  $casinoDetail->seo_keywords)

@section('context-js')

@endsection
    <title>@yield('page_title')</title>
@section('casino')

    <div class="background-section">
        <div class="header-container">
            <div class="poker-chip">
                <div class="inner-border"></div>
                <div class="inner-circle"></div>
                <span class="chip-letter">$</span>
            </div>
            <h1 class="h1">{{$casino->name}}</h1>
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

    <div class="container feuille-container ">
        <div class="row feuille" >
            <div class="col-lg-12 col-sm-12 ">
                <h2 class="h2">{{$casinoDetail->title}}</h2>
                <img src="/img/casino/{{$casino->img_url}}" alt="{{$casino->name}}" class="image-casino">
                @include("menu-casino")
            </div>
            <div class="col-lg-8 col-sm-12 ">
                @include("content-casino")
            </div>
            <div class="col-lg-4 col-sm-12">
                @include("sidebar")
            </div>
        </div>
    </div>
    @section('map')
        @include('map')
    @endsection




@endsection
