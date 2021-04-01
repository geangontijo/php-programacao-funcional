<?php

use PHPFuncional\Maybe;
use function igorw\pipeline;

require_once __DIR__ . '/vendor/autoload.php';

/** @var Maybe $data */
$data = require 'dados.php';

$contador = count($data->getOrElse());

$countrysNamesToUpperCase = fn(Maybe $editedData): Maybe => Maybe::of(
    array_map(
        function (array $country): array {
            $country['pais'] = mb_strtoupper($country['pais']);
            return $country;
        },
        $editedData->getOrElse()
    )
);

$filterOnlyCountrysHavingWhiteSpaceInName = fn(Maybe $editedData): Maybe => Maybe::of(
    array_filter(
        $editedData->getOrElse(),
        fn(array $country) => stripos($country['pais'], ' ') !== false
    )
);

$result = pipeline($countrysNamesToUpperCase, $filterOnlyCountrysHavingWhiteSpaceInName);
var_dump($result($data));
exit();


$brasil = $data[0];
$reduceCountryToCountMedals = fn(int $totalMedals, int $medalsCountry): int => $totalMedals + $medalsCountry;
$brazilianSumMedals = array_reduce($brasil['medalhas'], $reduceCountryToCountMedals, 0);

$countMedals = array_reduce($data,
    fn(int $totalMedals, array $country): int => $totalMedals +
        array_reduce($country['medalhas'], $reduceCountryToCountMedals, 0),
    0);

$compareMedals = fn(array $medalsCountry1, array $medalsCountry2): callable => fn(string $modality): int => $medalsCountry2[$modality] <=> $medalsCountry1[$modality];

usort($data, function (array $country1, array $country2) use ($compareMedals) {
    $medalsCountry2 = $country2['medalhas'];
    $medalsCountry1 = $country1['medalhas'];

    $medalsComparator = $compareMedals($medalsCountry1, $medalsCountry2);

    $goldComparation = $medalsComparator('ouro');
    $silverComparation = $medalsComparator('prata');
    $cooperComparation = $medalsComparator('bronze');

    return $goldComparation === 0 ? ($silverComparation === 0 ? $cooperComparation : $silverComparation) : $goldComparation;
});

var_dump($data);