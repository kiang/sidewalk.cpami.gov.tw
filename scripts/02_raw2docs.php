<?php
$basePath = dirname(__DIR__);
$jsonPath = $basePath . '/docs/json/sidewalks';
if (!file_exists($jsonPath)) {
    mkdir($jsonPath, 0777, true);
}

$pool = [];
$fc = [
    'type' => 'FeatureCollection',
    'features' => [],
];
// foreach (glob($basePath . '/raw/DynamicLayer_MapServer_13/*.json') as $jsonFile) {
//     $json = json_decode(file_get_contents($jsonFile), true);

//     foreach ($json['features'] as $f) {
//         unset($f['id']);
//         $city = $f['properties']['COUNTY_NA'];
//         if (!isset($pool[$city])) {
//             $pool[$city] = $fc;
//         }
//         $f['properties'] = [
//             'city' => $f['properties']['COUNTY_NA'] . $f['properties']['VILL_NAME'],
//             'road' => "{$f['properties']['NAME']}在{$f['properties']['PSTART']}與{$f['properties']['PEND']}之間",
//             'width' => $f['properties']['SW_WTH'],
//             'length' => $f['properties']['LENGTH'],
//         ];
//         $pool[$city]['features'][] = $f;
//     }
// }

foreach (glob($basePath . '/raw/DynamicLayer_MapServer_7/*.json') as $jsonFile) {
    $json = json_decode(file_get_contents($jsonFile), true);

    foreach ($json['features'] as $f) {
        unset($f['id']);
        $city = $f['properties']['COUNTY_NA'];
        if (!isset($pool[$city])) {
            $pool[$city] = $fc;
        }
        print_r($f['properties']); exit();
        $f['properties'] = [
            'city' => $f['properties']['COUNTY_NA'] . $f['properties']['VILL_NAME'],
            'road' => "{$f['properties']['NAME']}在{$f['properties']['PSTART']}與{$f['properties']['PEND']}之間",
            'width' => $f['properties']['SW_WTH'],
            'length' => $f['properties']['LENGTH'],
        ];
        $pool[$city]['features'][] = $f;
    }
}

foreach ($pool as $city => $data) {
    file_put_contents($jsonPath . '/' . $city . '.json', json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}
