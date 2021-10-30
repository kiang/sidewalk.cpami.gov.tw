<?php
$basePath = dirname(__DIR__);
$layers = array(
  'sidewalk/DynamicLayer/MapServer/0' => '地標',
  'sidewalk/DynamicLayer/MapServer/1' => '人行道人手孔',
  'sidewalk/DynamicLayer/MapServer/2' => '人行道人行坡道',
  'sidewalk/DynamicLayer/MapServer/3' => '人行道固定設施(桿類)',
  'sidewalk/DynamicLayer/MapServer/4' => '人行道固定設施(箱類)_點',
  'sidewalk/DynamicLayer/MapServer/5' => '人行道固定設施(其他)_點',
  'sidewalk/DynamicLayer/MapServer/6' => '自行車道',
  'sidewalk/DynamicLayer/MapServer/7' => '人行道(線)',
  'sidewalk/DynamicLayer/MapServer/8' => '人行道樹穴',
  'sidewalk/DynamicLayer/MapServer/9' => '人行道固定設施(箱類)_面',
  'sidewalk/DynamicLayer/MapServer/10' => '人行道固定設施(停車)',
  'sidewalk/DynamicLayer/MapServer/11' => '人行道固定設施(出入口)',
  'sidewalk/DynamicLayer/MapServer/12' => '人行道固定設施(其他)_面',
  'sidewalk/DynamicLayer/MapServer/13' => '人行道(面)',
);
foreach($layers AS $layerUrl => $layerName) {
  $layerId = str_replace('/', '_', $layerUrl);
  $idFile = $basePath . '/raw/' . $layerId . 'Id';
  $layerPath = $basePath . '/raw/' . $layerId;
  if(!file_exists($layerPath)) {
    mkdir($layerPath, 0777, true);
  }
  if(!file_exists($idFile)) {
    file_put_contents($idFile, '0');
  }
  $lastId = intval(file_get_contents($idFile));
  $objects = array();

  while(++$lastId) {
    $objects[] = $lastId;
    if($lastId % 200 === 0) {
      $targetFile = $layerPath . '/data_' . $lastId . '.json';
      if(!file_exists($targetFile)) {
        $q = implode(',', $objects);
        $json = gzdecode(shell_exec("curl -k 'http://sidewalk.cpami.gov.tw/ArcGIS/rest/services/{$layerUrl}/query?objectIds={$q}&outFields=*&outSR=4326&returnGeometry=true&f=json' -H 'Host: sidewalk.cpami.gov.tw' -H 'User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:54.0) Gecko/20100101 Firefox/54.0' -H 'Accept: */*' -H 'Accept-Language: en-US,en;q=0.5' -H 'Accept-Encoding: gzip, deflate, br' -H 'Content-Type: application/x-www-form-urlencoded' -H 'Referer: http://sidewalk.cpami.gov.tw/ArcGIS/rest/services/' -H 'Connection: keep-alive'"));
        $obj = json_decode($json, true);
        if(!isset($obj['features'][0])) {
          file_put_contents($idFile, $lastId);
          echo "{$layerId} done";
          break;
        }
        echo "processing {$layerId}/{{$lastId}}\n";
        file_put_contents($targetFile, $json);
      }
      $objects = array();
      file_put_contents($idFile, $lastId);
    }
  }
}
