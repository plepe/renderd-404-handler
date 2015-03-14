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

$refresh = 20;
Header("Refresh: {$refresh}");
Header("Retry-After: {$refresh}");
Header("Expires: " . Date("r", time() + $refresh));

if($file_404) {
  $content_type = mime_content_type($file_404);
  Header("content-type: {$content_type}");
  readfile($file_404);
  exit;
}

print "The requested URL {$_SERVER['REQUEST_URI']} was not found on this server.";
