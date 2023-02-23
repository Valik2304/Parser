<?php
require 'phpQuery.php';

function print_arr($arr)
{
    echo '<pre>' . print_r($arr, true) . '</pre>';
}

function get_count($blockWords){
    $str   = $blockWords->text();
    $words = explode(' ', $str);
    $out   = [];
    foreach ($words as $word) {
        isset($out[$word]) ? $out[$word]++ : $out[$word] = 1;
    }

    arsort($out);
    print_arr($out);
}
function get_content($url)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $res = curl_exec($ch);
    curl_close($ch);
    return $res;
}

function parser($url, $start, $end)
{
    if ($start < $end) {
        $file           = get_content($url);
        $doc            = phpQuery::newDocument($file);
        $allSmartphones = $doc->find('.products-layout__container');

        foreach ($doc->find('.products-layout__container .product-card') as $card) {
            $card = pq($card);

//        $card->find('source')->remove();
            $img   = $card->find('.product-card__img img')->attr('data-src');
            $title = $card->find('.product-card__title');
            $price = $card->find('.product-card__buy-box')->html();

            echo "<img src='$img'>";
            echo $title;
            echo $price;
            echo '<hr>';
        }

        get_count($allSmartphones);

        $next = $doc->find('.pagination .current')->next();
        $href = $next->find('a')->attr('href');
//        print_arr($href);
        if (!empty($href)) {
            $start++;
            parser($href, $start, $end);
        }
    }
}

$url   = 'https://allo.ua/ua/mobilnye-telefony-i-sredstva-svyazi/proizvoditel-samsung/';
$start = 0;
$end   = 1;
parser($url, $start, $end);

