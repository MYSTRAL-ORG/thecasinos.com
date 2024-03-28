@extends('layout')
@section('page_title')
    List of Casinos in {{ $location}}
@endsection

@php
    use Illuminate\Support\Str;
@endphp
@section('category')
	<div class="container feuille-container">
		<section class="  feuille">
			<h1 class="text-dark fs-1 mb-4">List of Casinos in {{ $location}}</h1>
			<p class="m-2">
            @if($categoryCity != null )
                {!!$categoryCity->header_text!!}
           @else
               {!! $category->header_text !!}
            @endif
   </p>
<div class="">

   <div  class="row" >
   @foreach($casinos as $casino)
       <div class="col-lg-4 col-md-6 col-sm-12 mt-2 mb-2">
           <div class=" casino-box  ">
               <div class="casino-image">


                   <img loading="lazy"
                        src="{{ env('APP_URL') . '/img/casino/mobile/' . $casino->img_url }}"
                        alt="{{ $casino->name }}"
                        srcset="{{ env('APP_URL') . '/img/casino/mobile/' . $casino->img_url }} 480w,
             {{ env('APP_URL') . '/img/casino/tablet/' . $casino->img_url }} 768w,
             {{ env('APP_URL') . '/img/casino/desktop/' . $casino->img_url }} 1024w"
                        sizes="(max-width: 480px) 480px, (max-width: 768px) 768px, 1024px">




                   <div class="category-location">
                       <img loading="lazy" src="{{ env('APP_URL') }}/img/icons/location.png" alt="Location">
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

       @if($casinos->currentPage() >1) <a  class="button" href="{{$casinos->previousPageUrl()}}">Previous</a>@endif
       @if($casinos->currentPage() < $casinos->lastPage())<a  class="button" href="{{$casinos->nextPageUrl()}}">Next</a>@endif
   </div>
</div>
<h2>About Casinos in {{ $location}}</h2>

<p>
    @if($categoryCity != null )
        {!! $categoryCity->footer_text !!}
   @else
       {!! $category->footer_text !!}
    @endif


<h3>Let's try online :</h3>
@php
   $lines = 3; // Nombre de lignes à afficher par défaut
   $columns = ['Logo', 'Brand', 'Bonus', 'Note', 'Review', 'Casino']; // Les colonnes à afficher

@endphp
@include("top10")
</section>
</div>
@endsection
@section('meta-tags')

    <meta name="_fromIndex" content="{{ $fromIndex }}"/>
@endsection


