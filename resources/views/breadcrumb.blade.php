<div class="content-container">
	<div class="block-location left-block">
		<div class="block-content"><a  href="/">The Casinos</a>  </div>
	</div>
	<div class="block-location middle-block">
		<div class="block-content"><a href="{{ route('category', ['country' => $casino->country_title]) }} " >{{$casino->country_name}}</a></div>
	</div>
	<div class="block-location right-block">
		<div class="block-content"><a href="{{ route('category', ['country' => $casino->country_title,'city' => $casino->city_title]) }}" > {{$casino->city_name}}</a></div>
	</div>
</div>
