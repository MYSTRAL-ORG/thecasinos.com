<table class="casino-table" id="{{ uniqid("casino-table-") }}">
     <thead>
     <tr>
        @foreach ($columns as $col)
            @switch($col)
                @case('Logo')
                    <th class='casino-logo'>Logo</th>
                    @break
                @case( 'Brand')
                     <th class='casino-brand'>Brand</th>
                    @break
                @case( 'Bonus')
                     <th class='casino-bonus'>Bonus</th>
                    @break
                @case( 'Note')
                     <th class='casino-note'>Note</th>
                    @break
                @case( 'Review')
                     <th class='casino-review'>Review</th>
                    @break
                @case( 'Casino')
                     <th class='casino-link'>Casino</th>
                    @break
            @endswitch
         @endforeach
        </tr>
    </thead>
    <tbody>

@foreach ($casinosOnLineActif as $key => $casino)

     <tr class="@if($key >= $lines)  'hidden-row' @endif ">
     @foreach ($columns as $col)
        @switch ($col)
            @case( 'Logo')
                <td class='casino-logo'><img src='{{env('APP_URL').$casino->icone}}' alt='Casino Logo'></td>
                @break
            @case( 'Brand')
                <td class='casino-brand'>{{$casino->nom_casino}}</td>
                @break
            @case( 'Bonus')
                <td class='casino-bonus'>{{$casino->bonus}}</td>
                @break
            @case( 'Note')
                <td class='casino-note'>{{$casino->note}}</td>
                @break
             @case( 'Review')
                <td class='casino-review'><a href='{{ route('casino-online', ['id' => $casino->id]) }}'>Review</a></td>
                @break
            @case( 'Casino')
                <td class='casino-link'><a href='{{$casino->register_link}}'><button>Play</button></a></td>
                @break
             @endswitch
    @endforeach
     </tr>
@endforeach

<tr>
<td colspan="{{ count($columns) }}" class="load-more-cell">
<button class="casino-detail-load-more-btn">Load More</button>
<button class="casino-detail-load-less-btn" style="display: none;">Load Less</button>
</td>
</tr>

</tbody>
</table>

