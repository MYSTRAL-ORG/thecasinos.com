
@extends('layout')

@section('page_title', $casinoDetail->seo_title)

@section('page_description',  $casinoDetail->seo_description)
@php
    $features = collect([]);

    if($casino->self_parking) $features->push('Self parking');
    if($casino->valet) $features->push('Valet');
    if($casino->restaurants) $features->push('Restaurants');
    if($casino->hotels) $features->push('Hotels');
    if($casino->shops) $features->push('Shops');
    if($casino->spas) $features->push('Spas');

    if($casino->cat_tablegames) $features->push('Table Games');
    if($casino->cat_poker) $features->push('Poker Tables');
    if($casino->cat_slotmachines) {
        // Pour éviter les doublons de "Gaming Machine" et "Slot Machines"
        if(!$features->contains('Slot Machines')) {
            $features->push('Slot Machines');
        }
    }
    if($casino->cat_sportsbook) $features->push('Sports Book');
    if($casino->cat_horseracing) $features->push('Horse Racing');
    if($casino->cat_simulcasting) $features->push('Simulcast');
    if($casino->cat_offtrack) $features->push('Off Track');
    if($casino->cat_greyhounds) $features->push('Greyhounds');
    if($casino->cat_bingo) $features->push('Bingo');
    $featuresList = $features->implode(', '); // Transforme la collection en chaîne de caractères séparée par des virgules
@endphp


@section('page_keywords')
    Available facilities: {{ $featuresList }}
@endsection
@section('context-js')
    <link rel="preload" as="image" href="{{ env('APP_URL') . '/img/casino/mobile/' . $casino->img_url }}">

@endsection


@section('casino')

    <div class="background-section">
        <div class="header-container">
            <div class="poker-chip">
                <div class="inner-border"></div>
                <div class="inner-circle"></div>
                <span class="chip-letter">$</span>
            </div>
            <h1 class="h1" >{{$casino->name}}</h1>
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
            <div class="col-lg-12 col-sm-12 pb-2">
                <h2 class="h2">{{$casinoDetail->title}}</h2>

                <img loading="lazy"
                     class="image-casino "
                     src="{{ env('APP_URL') . '/img/casino/mobile/' . $casino->img_url }}"
                     alt="{{ $casino->name }}"
                     srcset="{{ env('APP_URL') . '/img/casino/mobile/' . $casino->img_url }} 600w,
             {{ env('APP_URL') . '/img/casino/tablet/' . $casino->img_url }} 768w,
             {{ env('APP_URL') . '/img/casino/desktop/' . $casino->img_url }} 1024w"
                     sizes="(max-width: 600px) 600px, (max-width: 768px) 768px, 1024px">


                <!--  <img  loading="lazy" src="/img/casino/{{$casino->img_url}}" alt="{{$casino->name}}" class=" image-casino "> -->
                @include("menu-casino")
            </div>
            <div class="col-lg-8 col-sm-12 pt-2">
                @include("content-casino")
            </div>
            <div class="col-lg-4 col-sm-12 pt-2">
                @include("sidebar")
            </div>
        </div>
    </div>





@endsection
@section('map')

@endsection
