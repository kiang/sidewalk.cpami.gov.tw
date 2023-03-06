<?php
$basePath = dirname(__DIR__);

$jsonPath = $basePath . '/docs/json/sidewalks';

$pool = [];
$fc = [
    'type' => 'FeatureCollection',
    'features' => [],
];
foreach (glob($basePath . '/raw/DynamicLayer_MapServer_13/*.json') as $jsonFile) {
    $json = json_decode(file_get_contents($jsonFile), true);

    foreach ($json['features'] as $f) {
        unset($f['id']);
        $city = $f['properties']['COUNTY_NA'];
        $area = $f['properties']['VILL_NAME'];
        if (!isset($pool[$city])) {
            $pool[$city] = [];
        }
        if (!isset($pool[$city][$area])) {
            $pool[$city][$area] = $fc;
        }
        $f['properties'] = [
            'city' => $city,
            'area' => $area,
            'name' => $f['properties']['NAME'],
            'begin' => $f['properties']['PSTART'],
            'end' => $f['properties']['PEND'],
            'road' => "{$city}{$area}{$f['properties']['NAME']}在{$f['properties']['PSTART']}與{$f['properties']['PEND']}之間",
            'width' => $f['properties']['SW_WTH'],
        ];
        if (!empty($f['geometry'])) {
            $pool[$city][$area]['features'][] = $f;
        }
    }
}

foreach ($pool as $city => $data1) {
    $cityPath = $jsonPath . '/' . $city;
    if (!file_exists($cityPath)) {
        mkdir($cityPath, 0777, true);
    }
    foreach ($data1 as $area => $data) {
        file_put_contents($cityPath . '/' . $area . '.json', json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}
