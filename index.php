<?php
define('DIR_LESSONS', __DIR__ . '/lessons');
require_once(dirname(__FILE__) . '/inc/functions.php');
?>
<html>
<head>
  <title>Audio Lessons</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <style type="text/css" media="all">
  @import url("css/style.css");
  </style>
</head>
<body>
<?php

$files = glob(DIR_LESSONS . "/*/en.txt");

global $lesson;

$lesson = isset($_GET['lesson']) ? $_GET['lesson'] : FALSE;
$action = isset($_GET['a']) ? $_GET['a'] : FALSE;
$action_file = $action ? $action . '.php' : FALSE;
define('LESSON', $lesson);
define('ACTION', $action);

if($action && file_exists($action_file)) {
  include($action_file);
}
elseif (!$lesson) {
  print '<h1>Choose lesson:</h1>';
  print a('index.php?a=create', 'New Lesson');
  print lessons_links($files);
  echo getplayer('m3u/en/playlist-en.m3u');
}
else {
  $text_en = lesson_file('en.txt');
  $text_zh = lesson_file('zh-cn.txt');
  $text_py = lesson_file('zh-cn-pinying.txt');
  $text_vocab = lesson_file('vocab.txt');

  $mp3_en = lang2mp3('en');
  $mp3_zh = lang2mp3('zh');

  print a('index.php', 'Back');
  $video = getVideoFile();
  if ($video) {
    print getplayer($video);
  }
  h1('English');
  print getplayer($mp3_en);
  print "<br />";

  foreach($text_en as $n=>$l){
    tooltipsentences($l, @$text_zh[$n], @$text_py[$n]);
    print "<br />";
  }

  h1('Chinese');
  print getplayer($mp3_zh);

  print "<br />";
  foreach($text_zh as $n=>$l){
    tooltipsentences($l, $text_py[$n], $text_en[$n]);
    print "<br />";
  }

  if ($text_vocab) {
    h1('Vocabulary');
    print implode('<br />', $text_vocab);
  }
}
?>
</body>
</html>
