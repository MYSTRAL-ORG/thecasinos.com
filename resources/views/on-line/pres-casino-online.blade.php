<div class="  mt-3 row review-casino-box pt-2">
  <div class="col-sm-12 col-lg-12 review-top-section">
    <img class="review-casino-logo" src="{{env('APP_URL').$casinoOnLine->logo}}" alt="">
    <div class="review-white-line"></div>
    <!-- Ajout du système de note -->
    <div class="review-rating-box">
      <div class="review-rating-inner">
		<i class="fas fa-star review-star-icon"></i>
        <span class="review-rating-text">@if($notePart1 != null) {{$notePart1}} @endif</span>
        <span class="review-rating-fraction"> @if($notePart2 != null), {{$notePart2}} @endif </span>
      </div>
    </div>
    <!-- Fin de l'ajout du système de note -->
  </div>


        <div class="col-sm-12 col-lg-6  review-left-col   pt-4 ">
          <h3>Key Features</h3>
          <ul class="review-ul  ">
              @foreach (explode('|', $casinoOnLine->key_feature) as $feature)
                <li>{{$feature}}</li>
              @endforeach
          </ul>
        </div>

        <div class=" col-sm-12 col-lg-6">
          <img class="review-casino-image" src="{{env('APP_URL').$casinoOnLine->screenshot}}" alt="">
        </div>

  <div class=" col-sm-12 col-lg-6 review-bottom-section">
    <button class="review-left-button" disabled>{{$casinoOnLine->bonus}}</button>
    <a href="{{$casinoOnLine->register_link}}" target="_blank" class="button review-right-button">Register & Play</a>
  </div>
</div>
