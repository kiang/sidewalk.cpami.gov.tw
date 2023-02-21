<?php
$basePath = dirname(__DIR__);
$layers = array(
  // 'DynamicLayer/MapServer/0' => '地標',
  // 'DynamicLayer/MapServer/1' => '人行道人手孔',
  // 'DynamicLayer/MapServer/2' => '人行道人行坡道',
  // 'DynamicLayer/MapServer/3' => '人行道固定設施(桿類)',
  // 'DynamicLayer/MapServer/4' => '人行道固定設施(箱類)_點',
  // 'DynamicLayer/MapServer/5' => '人行道固定設施(其他)_點',
  // 'DynamicLayer/MapServer/6' => '自行車道',
  'DynamicLayer/MapServer/7' => '人行道(線)',
  // 'DynamicLayer/MapServer/8' => '人行道樹穴',
  // 'DynamicLayer/MapServer/9' => '人行道固定設施(箱類)_面',
  // 'DynamicLayer/MapServer/10' => '人行道固定設施(停車)',
  // 'DynamicLayer/MapServer/11' => '人行道固定設施(出入口)',
  // 'DynamicLayer/MapServer/12' => '人行道固定設施(其他)_面',
  'DynamicLayer/MapServer/13' => '人行道(面)',
  // 'DisFree_Road/MapServer/0' => '2020',
  // 'DisFree_Road/MapServer/1' => '2019',
  // 'DisFree_Road/MapServer/2' => '2018',
  // 'DisFree_Road/MapServer/3' => '2017',
  // 'DisFree_Road/MapServer/4' => '2016',
  // 'DisFree_Road/MapServer/5' => '2015',
  // 'DisFree_Road/MapServer/6' => '2014',
  // 'DisFree_Road/MapServer/7' => '2013',
  // 'DisFree_Road/MapServer/8' => '2012',
);
$context = stream_context_create(array(
  "ssl" => array(
    "verify_peer" => false,
    "verify_peer_name" => false,
  ),
));

foreach ($layers as $layerUrl => $layerName) {
  // ref https://community.esri.com/t5/arcgis-enterprise-questions/how-to-find-the-minimum-and-maximum-values-of-an/td-p/64752
  $meta = json_decode(file_get_contents("https://sidewalk.cpami.gov.tw/arcgis/rest/services/{$layerUrl}/query?where=&text=&objectIds=&time=&timeRelation=esriTimeRelationOverlaps&geometry=&geometryType=esriGeometryEnvelope&inSR=&spatialRel=esriSpatialRelIntersects&distance=&units=esriSRUnit_Foot&relationParam=&outFields=&returnGeometry=true&returnTrueCurves=false&maxAllowableOffset=&geometryPrecision=&outSR=&havingClause=&returnIdsOnly=false&returnCountOnly=false&orderByFields=&groupByFieldsForStatistics=&outStatistics=%5B%7B%22statisticType%22%3A%22min%22%2C%22onStatisticField%22%3A%22objectid%22%2C+%22outStatisticFieldName%22%3A%22MinID%22%7D%2C%0D%0A%7B%22statisticType%22%3A%22max%22%2C%22onStatisticField%22%3A%22objectid%22%2C+%22outStatisticFieldName%22%3A%22MaxID%22%7D%5D&returnZ=false&returnM=false&gdbVersion=&historicMoment=&returnDistinctValues=false&resultOffset=&resultRecordCount=&returnExtentOnly=false&sqlFormat=none&datumTransformation=&parameterValues=&rangeValues=&quantizationParameters=&featureEncoding=esriDefault&f=pjson"), true);
  $layerId = str_replace('/', '_', $layerUrl);
  $idFile = $basePath . '/raw/' . $layerId . 'Id';
  $layerPath = $basePath . '/raw/' . $layerId;
  if (!file_exists($layerPath)) {
    mkdir($layerPath, 0777, true);
  }
  if (!file_exists($idFile)) {
    file_put_contents($idFile, '0');
  }
  $lastId = intval(file_get_contents($idFile));
  $objects = array();

  while (++$lastId) {
    $objects[] = $lastId;
    if ($lastId % 200 === 0) {
      $targetFile = $layerPath . '/data_' . $lastId . '.json';
      if (!file_exists($targetFile)) {
        $q = implode(',', $objects);
        $json = file_get_contents("https://sidewalk.cpami.gov.tw/ArcGIS/rest/services/{$layerUrl}/query?objectIds={$q}&outFields=*&outSR=4326&returnGeometry=true&f=geojson", false, $context);
        $obj = json_decode($json, true);
        echo "processing {$layerId}/{{$lastId}} [{$meta['features'][0]['attributes']['MaxID']}]\n";
        file_put_contents($targetFile, $json);
      }
      $objects = array();
      file_put_contents($idFile, $lastId);
      if ($lastId >= $meta['features'][0]['attributes']['MaxID']) {
        echo "{$layerId} done\n\n";
        break;
      }
    }
  }
}
