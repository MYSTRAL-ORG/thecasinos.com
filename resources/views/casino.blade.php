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
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var imgSrc = "{{ env('APP_URL') }}/img/casino/desktop/{{ $casino->img_url }}"; // Chemin par défaut
            var imgElement = document.querySelector('.image-casino');

            // Vérifie si l'écran est inférieur à 768 pixels
            if (window.innerWidth < 768) {
                imgSrc = "{{ env('APP_URL') }}/img/casino/mobile/{{ $casino->img_url }}"; // Chemin pour mobile
                imgElement.width = 300; // Définir la largeur pour mobile
                imgElement.height = 200; // Définir la hauteur pour mobile
            }

            imgElement.src = imgSrc; // Appliquer le chemin d'accès de l'image
            imgElement.alt = "{{ $casino->name }}"; // Appliquer le texte alternatif de l'image
        });

        document.addEventListener("DOMContentLoaded", function () {
            const paragraphs = document.querySelectorAll('.content-casino p');
            const SHOW_INITIAL = 2; // Nombre de paragraphes à montrer initialement

            if (paragraphs.length > SHOW_INITIAL) {
                for (let i = SHOW_INITIAL; i < paragraphs.length; i++) {
                    paragraphs[i].classList.add('hidden');
                }

                const loadMoreButton = document.createElement('button');
                loadMoreButton.id = 'loadMore';
                loadMoreButton.classList.add('casino-detail-load-more-btn');
                loadMoreButton.textContent = 'Load More';
                document.querySelector('.content-casino').appendChild(loadMoreButton);

                loadMoreButton.addEventListener('click', function () {
                    document.querySelectorAll('.content-casino p.hidden').forEach(function (p) {
                        p.classList.remove('hidden');
                    });
                    loadMoreButton.remove(); // Supprime le bouton après affichage de tout le contenu
                });
            }
        });

    </script>

@endsection


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

    <div class="container   pb-5">
        <div class="row feuille ">
            <div class="col-lg-12 col-sm-12 pt-5pb-2">
                <h2 class="h2">{{$casinoDetail->title}}</h2>
                <!--  <picture>
                    <source media="(min-width: 768px)"
                            srcset="{{ env('APP_URL') . '/img/casino/desktop/' . $casino->img_url }}">
                    <source media="(max-width: 767px)"
                            srcset="{{ env('APP_URL') . '/img/casino/mobile/' . $casino->img_url }}" width="300"
                            height="200">
                    <img loading="lazy" class="rounded-3 img-fluid image-casino center-image"
                         src="{{ env('APP_URL') . '/img/casino/desktop/' . $casino->img_url }}"
                         alt="{{ $casino->name }}">
                </picture> -->


                <!--  <img  loading="lazy" src="/img/casino/{{$casino->img_url}}" alt="{{$casino->name}}" class=" image-casino "> -->
                @include("menu-casino")
            </div>
            <div class="col-lg-8 col-sm-12 pt-5">
                @include("content-casino")
                <h3>Most similar casinos online :</h3>

                @php
                    $lines = 3;
                    $columns = ['Logo', 'Bonus', 'Review', 'Casino'];

                @endphp
                @include('top10')
            </div>
            <div class="col-lg-4 col-sm-12 pt-5">
                @include("sidebar")
            </div>
        </div>
    </div>

@endsection

