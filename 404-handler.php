<?php include "conf.php"; /* load a local configuration */ ?>
<?php
Header("HTTP/1.1 404 Not Found");

function renderd_conf_parse($file) {
  $block = null;
  $ret = array();

  $f = fopen($file, "r");

  while($r = fgets($f)) {
    chop($r);

    // comment
    if(preg_match("/^\s*#/", $r, $m))
      ;

    // new block opener
    elseif(preg_match("/\[(.*)\]/", $r, $m)) {
      $block = $m[1];
      $ret[$block] = array();
    }

    elseif($block && preg_match("/^([^=]*)=([^#]*)/", $r, $m))
      $ret[$block][trim($m[1])] = trim($m[2]);
  }

  fclose($f);

  return $ret;
}

$renderd_conf = renderd_conf_parse($renderd_conf_file);
$file_404 = null;

foreach($renderd_conf as $block=>$data) {
  if(array_key_exists('URI', $data) &&
     substr($_SERVER['REQUEST_URI'], 0, strlen($data['URI'])) == $data['URI']) {
    if(array_key_exists('404_image', $data))
      $file_404 = $data['404_image'];
  }
}

if(!$file_404) {
  $data = $renderd_conf['renderd'];
  if(array_key_exists('404_image', $data))
    $file_404 = $data['404_image'];
}

print $file_404;
exit;

$list=array(array(10, array(
  'body'=>"The requested URL {$_SERVER['REQUEST_URI']} was not found on this server.",
)));

$list=weight_sort($list);
if(isset($list[0]['header'])) {
  foreach($list[0]['header'] as $header)
    Header($header);
}

$refresh = 20;
Header("Refresh: {$refresh}");
Header("Retry-After: {$refresh}");
Header("Expires: " . Date("r", time() + $refresh));

print $list[0]['body'];
