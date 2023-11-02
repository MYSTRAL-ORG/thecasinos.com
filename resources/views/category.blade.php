@extends('layout')
@section('page_title', 'TheCasinos.com : Online reference to Onsite experience')


@php
    use Illuminate\Support\Str;
@endphp
@section('category')
	<div class="container feuille-container">
		<section class=" feuille">
			<h2>List of Casinos in {{ $location}}</h2>
			<p>{!! $category->header_text !!}</p>
			<div class="">

                <div  class="row" >
                @foreach($casinos as $casino)
                    <div class="col-lg-4 col-md-6 col-sm-12 mt-2 mb-2">
                        <div class=" casino-box  ">
                            <div class="casino-image">
                                <img src="{{env('APP_URL').'/img/casino/'.$casino->img_url  }}" alt="{{$casino->name}} Casino">
                                <div class="category-location">
                                    <img src="{{ env('APP_URL') }}/img/icons/location.png" alt="Location Icon">
                                    <span>{{$casino->city_name}}</span>
                                </div>
                            </div>
                            <div class="casino-info">
                                <h3 class=" align-middle ">{{Str::limit($casino->name,26)}} </h3>
                                <p class="three-lines">{{ Str::limit($casino->resume_1_line, 80) }}</p>

                                <a class="button" href="{{ route('casino', ['country' => $casino->country_title  ,'city' => $casino->city_title , 'name' => $casino->slug] )}}">View casino</a>
                            </div>
                        </div>
                    </div>
              @endforeach
                </div>
			</div>

			<div class="pagination-container">

				<div class="pagination">

                    @if($casinos->currentPage() >1) <a  class="button" href="{{$casinos->previousPageUrl()}}">Précédente</a>@endif
                    @if($casinos->currentPage() < $casinos->lastPage())<a  class="button" href="{{$casinos->nextPageUrl()}}">Suivante</a>@endif
				</div>
			</div>
			<h2>About Casinos in {{ $location}}</h2>

			<p>{!!  $category->footer_text  !!}</p>
			<h3>Let's try online :</h3>
			@php
				$lines = 3; // Nombre de lignes à afficher par défaut
				$columns = ['Logo', 'Brand', 'Bonus', 'Note', 'Review', 'Casino']; // Les colonnes à afficher

			@endphp
            @include("top10")
		</section>
	</div>
@endsection
