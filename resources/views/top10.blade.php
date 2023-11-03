

<?php


$data = [
    [
        "logo" => "/img/casinos/logos/happy-luke.png",
        "brand" => "Happy Luke",
        "bonus" => "200% up to 500 usd",
        "note" => "4.5 / 5",
        "review" => "#",
        "casino" => "#"
    ],
    [
        "logo" => "/img/casinos/logos/dafabet.png",
        "brand" => "Dafabet",
        "bonus" => "200% up to 500 usd",
        "note" => "4.5 / 5",
        "review" => "#",
        "casino" => "#"
    ],
    [
        "logo" => "/img/casinos/logos/empire777-casino-logo.png",
        "brand" => "Empire 777",
        "bonus" => "200% up to 500 usd",
        "note" => "4.5 / 5",
        "review" => "#",
        "casino" => "#"
    ],
    [
        "logo" => "/img/casinos/logos/bet_and_you_logo.png",
        "brand" => "Bet & You",
        "bonus" => "200% up to 500 usd",
        "note" => "4.5 / 5",
        "review" => "#",
        "casino" => "#"
    ],
    [
        "logo" => "/img/casinos/logos/k9win_casino_logo.png",
        "brand" => "K9 Win",
        "bonus" => "200% up to 500 usd",
        "note" => "4.5 / 5",
        "review" => "#",
        "casino" => "#"
    ],
    [
        "logo" => "/img/casinos/logos/k9win_casino_logo.png",
        "brand" => "K9 Win",
        "bonus" => "200% up to 500 usd",
        "note" => "4.5 / 5",
        "review" => "#",
        "casino" => "#"
    ],
    [
        "logo" => "/img/casinos/logos/k9win_casino_logo.png",
        "brand" => "K9 Win",
        "bonus" => "200% up to 500 usd",
        "note" => "4.5 / 5",
        "review" => "#",
        "casino" => "#"
    ],
    [
        "logo" => "/img/casinos/logos/k9win_casino_logo.png",
        "brand" => "K9 Win",
        "bonus" => "200% up to 500 usd",
        "note" => "4.5 / 5",
        "review" => "#",
        "casino" => "#"
    ],
    [
        "logo" => "/img/casinos/logos/k9win_casino_logo.png",
        "brand" => "K9 Win",
        "bonus" => "200% up to 500 usd",
        "note" => "4.5 / 5",
        "review" => "#",
        "casino" => "#"
    ],
    [
        "logo" => "/img/casinos/logos/k9win_casino_logo.png",
        "brand" => "K9 Win",
        "bonus" => "200% up to 500 usd",
        "note" => "4.5 / 5",
        "review" => "#",
        "casino" => "#"
    ]
];


$tableId = uniqid("casino-table-");
echo '<table class="casino-table" id="' . $tableId . '">';
echo '<thead>';
echo '<tr>';
foreach ($columns as $col) {
    switch ($col) {
        case 'Logo':
            echo "<th class='casino-logo'>Logo</th>";
            break;
        case 'Brand':
            echo "<th class='casino-brand'>Brand</th>";
            break;
        case 'Bonus':
            echo "<th class='casino-bonus'>Bonus</th>";
            break;
        case 'Note':
            echo "<th class='casino-note'>Note</th>";
            break;
        case 'Review':
            echo "<th class='casino-review'>Review</th>";
            break;
        case 'Casino':
            echo "<th class='casino-link'>Casino</th>";
            break;
    }
}
echo '</tr>';
echo '</thead>';
echo '<tbody>';

foreach ($data as $key => $casino) {
    $hiddenClass = ($key >= $lines) ? 'hidden-row' : ''; // Appliquez la classe "hidden-row" après les lignes définies par $lines
    echo '<tr class="' . $hiddenClass . '">';
    foreach ($columns as $col) {
        switch ($col) {
            case 'Logo':
                echo "<td class='casino-logo'><img src='{$casino["logo"]}' alt='Casino Logo'></td>";
                break;
            case 'Brand':
                echo "<td class='casino-brand'>{$casino["brand"]}</td>";
                break;
            case 'Bonus':
                echo "<td class='casino-bonus'>{$casino["bonus"]}</td>";
                break;
            case 'Note':
                echo "<td class='casino-note'>{$casino["note"]}</td>";
                break;
            case 'Review':
                echo "<td class='casino-review'><a href='{$casino["review"]}'>Review</a></td>";
                break;
            case 'Casino':
                echo "<td class='casino-link'><a href='#'><button>Play</button></a></td>";
                break;
        }
    }
    echo '</tr>';
}

// Ligne Load More / Load Less
echo '<tr>';
echo '<td colspan="' . count($columns) . '" class="load-more-cell">';
echo '<button class="casino-detail-load-more-btn">Load More</button>';
echo '<button class="casino-detail-load-less-btn" style="display: none;">Load Less</button>';
echo '</td>';
echo '</tr>';

echo '</tbody>';
echo '</table>';
?>
