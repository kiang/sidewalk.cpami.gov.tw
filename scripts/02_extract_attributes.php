<?php
$basePath = dirname(__DIR__);
$csvPath = $basePath . '/csv/map13';
if (!file_exists($csvPath)) {
    mkdir($csvPath, 0777, true);
}
$pool = [];
foreach (glob($basePath . '/raw/sidewalk_DynamicLayer_MapServer_13/*.json') as $jsonFile) {
    $json = json_decode(file_get_contents($jsonFile), true);
    foreach ($json['features'] as $f) {
        $city = $f['attributes']['COUNTY_NA'];
        if (!isset($pool[$city])) {
            $pool[$city] = fopen($csvPath . '/' . $city . '.csv', 'w');
            fputcsv($pool[$city], array_keys($f['attributes']));
        }
        fputcsv($pool[$city], $f['attributes']);
    }
}
