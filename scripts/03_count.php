<?php
$basePath = dirname(__DIR__);
$csvPath = $basePath . '/csv/map13';
$labels = [
    'ID' => '人行道最小調查單元流水號',
    'NAME' => '道路名稱',
    'PSTART' => '道路_起點',
    'PEND' => '道路_迄點',
    'SW_DIRECT' => '人行道方向', //1 = 東/南側
    'LENGTH' => '道路_長度(中心線長度)',
    'WIDTH_U' => '道路寬度(包含雙向人行道)',
    'WIDTH_C' => '車道寬度(不含人行道)',
    'SW_LENG' => '人行道長度',
    'SW_WTH' => '人行道總寬度',
    'SWD_WTH' => '人行道公設帶寬度',
    'SWT_WTH' => '行人通行總寬度',
    'SWW_WTH' => '人行道淨寬',
    'SW_PAVE' => '鋪面類型',
    'SHAPE_AR' => '人行道面積',
    'SW_AREA' => '人行道上設施物所佔面積',
    'SW_BK_L' => '人行道固定桿類設施數量(數量)',
    'SW_BK_B' => '人行道固定箱類設施數量(數量)',
    'SW_BK_E' => '人行道固定出入口設施數量(數量)',
    'SW_BK_P' => '人行道固定停車設施數量(數量)',
    'SW_BK_O' => '人行道其他固定設施數量(數量)',
    'SW_HOLE' => '人行道人手孔數量(數量)',
    'SW_TREE' => '人行道樹穴數量(數量)',
    'SW_RAMP' => '路緣斜坡數量',
    'SW_CARRAMP' => '橫越人行道之穿越道數量(數量)',
    'BLOK_NAME' => '人行道固定阻礙物名稱',
    'ARCADE' => '騎樓設置_設置',
    'AC_EVEN' => '騎樓設置_與人行道齊平',
    'U_OFFICE' => '退縮地',
    'U_WIDTH' => '退縮地提供人行淨寬度',
    'BC_PLACE' => '於人行道上設置自行車道_設置方式',
    'BC_WIDTH' => '自行車專用道寬度',
    'MEMO' => '附註說明',
    'SW_BRKRAT' => '破損程度',
    'SW_PAVE_C' => '鋪面材質一致性(不計退縮地舖面)',
    'SW_PASS' => '固定設施物或阻礙物影響通行程度', //1 = 無阻斷及阻礙
    'SW_USES' => '土地使用分區', //3 = 住宅區
    'COUNTY_NA' => '縣市',
    'VILL_NAME' => '鄉鎮市區',
    'KEY_DATE' => '人行道普查日期',
];

$skel = [
    '400' => 0.0,
    '250' => 0.0,
    '150' => 0.0,
    '100' => 0.0,
    'sum' => 0.0,
];
$pool = [];
foreach (glob($csvPath . '/*.csv') as $csvFile) {
    $fh = fopen($csvFile, 'r');
    $head = fgetcsv($fh, 2048);
    foreach ($head as $k => $v) {
        if (isset($labels[$v])) {
            $head[$k] = $labels[$v];
        }
    }
    while ($line = fgetcsv($fh, 2048)) {
        $data = array_combine($head, $line);
        if(!isset($pool[$data['縣市']])) {
            $pool[$data['縣市']] = $skel;
        }
        if ($data['人行道淨寬'] >= 4) {
            $pool[$data['縣市']]['400'] += round($data['人行道長度']);
        } elseif ($data['人行道淨寬'] >= 2.5) {
            $pool[$data['縣市']]['250'] += round($data['人行道長度']);
        } elseif ($data['人行道淨寬'] >= 1.5) {
            $pool[$data['縣市']]['150'] += round($data['人行道長度']);
        } else {
            $pool[$data['縣市']]['100'] += round($data['人行道長度']);
        }
        $pool[$data['縣市']]['sum'] += round($data['人行道長度']);
    }
}

print_r($pool);