<div class="content-casino">


    {!!  $casinoDetail->description!!}


    <h3>Most similar casinos online :</h3>

   @php
    $lines = 3;
    $columns = ['Logo', 'Bonus', 'Review', 'Casino'];

    @endphp
    @include('top10')

</div>
