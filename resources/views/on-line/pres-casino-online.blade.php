<div class="review-casino-box">
  <div class="review-top-section">
    <img class="review-casino-logo" src="{{env('APP_URL').$casinoOnLine->logo}}">
    <div class="review-white-line"></div>
    <!-- Ajout du système de note -->
    <div class="review-rating-box">
      <div class="review-rating-inner">
		<i class="fas fa-star review-star-icon"></i>
        <span class="review-rating-text">4</span>
        <span class="review-rating-fraction">,5</span>
      </div>
    </div>
    <!-- Fin de l'ajout du système de note -->
  </div>
  <div class="review-middle-section">
    <div class="review-left-col">
      <h3>Key Features</h3>
      <ul class="review-ul">
          @foreach (explode('|', $casinoOnLine->key_feature) as $feature)
            <li>{{$feature}}</li>
          @endforeach

      </ul>
    </div>
    <div class="review-right-col">
      <img class="review-casino-image" src="{{env('APP_URL').$casinoOnLine->screenshot}}">
    </div>
  </div>
  <div class="review-bottom-section">
    <button class="review-left-button" disabled>{{$casinoOnLine->bonus}}</button>
    <button class="review-right-button">Register & Play</button>
  </div>
</div>
