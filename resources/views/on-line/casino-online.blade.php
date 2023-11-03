@extends('layout')

@section('page_title', "Casino Online Title - TheCasinos.com")

@section('page_description',   $casinoOnLine->nom_casino )

@section('page_keywords',$casinoOnLine->nom_casino)

@section('context-js')

@endsection

<title>@yield('page_title')</title>
@section('casino-online')

		<div class="background-section">
			<div class="header-container">
				<div class="poker-chip">
					<div class="inner-border"></div>
					<div class="inner-circle"></div>
					<span class="chip-letter">$</span>
				</div>
				<h1>{{$casinoOnLine->nom_casino}}</h1>
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
            <div class="content-container">
                <div class="block-location left-block">
                    <div class="block-content">Online</div>
                </div>
                <div class="block-location middle-block">
                    <div class="block-content"><a href="#">Casino</a></div>
                </div>
            </div>
		</div>





	<div class="container feuille-container">
		<section class="row feuille">
			<h2>{{$casinoOnLine->sous_titre}}</h2>
			@include("on-line/pres-casino-online")
			@include("on-line/menu-casino-online")
			@include("on-line/content-casino-online")
			@include("on-line/sidebar-online")
		</section>
	</div>
@endsection
