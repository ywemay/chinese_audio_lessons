<?php

/** 
 * Loads lesson file lines from a lesson file
 */
function lesson_file($filename){
  if (!LESSON) return FALSE;
  $fn = DIR_LESSONS . '/' . LESSON . '/' . $filename;

  return file_exists($fn) ? file($fn) : FALSE;
}

function theme_table($vars) {

  extract($vars);
  $class = '';
  if (isset($attributess['class']) && $attributess['class']) {
    $class = $attributess['class'];
    if (is_array($class)) $class = implode(' ', $class);
    $class = ' class="' . $class . '"';
  }
  $out = "<table$class>\n";
  if ($header) {
    $out .= " <thead>\n";
    foreach($header as $k=>$th) {
      $out .= "   <th>$th</th>\n";
    }
    $out .= " </thead>\n";
  }
  if ($rows) {
    $header = $header ? $header : $rows[0];
    $out .= " <tbody>\n";
    foreach ($rows as $row) { 
      foreach(array_keys($header) as $k) {
        $td = isset($row[$k]) ? $row[$k] : '';
        $out .= "   <td>$td</td>\n";
      }
      $out .= " </tbody>\n";
    }
  }
  $out .= "</table>\n";
  return $out;
}

/**
 * Loads lesson links from files array
 */
function lessons_links($files){
  $rows = array();
  foreach ($files as $f) {
    $lKey = basename(dirname($f));
    $rows[] = array(
      'view' => "<a href=\"index.php?lesson=$lKey\">$lKey</a>",
      'edit' => "<a href=\"index.php?lesson=$lKey&a=edit\">Edit</a> "
        . "<a href=\"index.php?lesson=$lKey&a=tools\">Tools</a>",
    );
  }

  $out = theme_table(array(
    'header' => array(
      'view' => 'Lesson',
      'edit' => 'Actions',
    ),
    'rows' => $rows,
  ));
  return $out;
}

/**
 * Language to mp3 file based on global lesson key
 */
function lang2mp3($l, $mode = 'fast') {
  if ($l == 'zh') $l = 'zh-cn';
  if ($l == 'pinying') $l = 'zh-cn-pinying';
  $name = 'lessons/' . LESSON . '/' . $l . ($mode == 'slow' ? '_slow' : '') . '.mp3';
  return file_exists($name) ? $name : FALSE;
}

/**
 * Check for video file.
 */
function getVideoFile() {
  $name = 'lessons/' . LESSON . '/video.webm';
  if (file_exists($name)) return $name;
  return FALSE;
}

/**
 * Prints H1
 */
function h1($text){
  print '<h1>' . $text . '</h1>' . "\n";
}

/**
 * Prints H1
 */
function div($text, $class = 'text'){
  print '<div class="'.$class.'">' . $text . '</div>' . "\n";
}

function lesson2div($arr) {
  div(nl2br(implode($arr)));
}

function a($link, $title, $params = array()){
  $attributes='';
  if (isset($params['attributes']))
  foreach ($params['attributes'] as $k=>$v) {
    $attributes .= " $k=\"$v\"";
  }
  return '<a href="' . $link . '"'
    . $attributes
    . '>' . $title . '</a>';
}

function tooltipdiv($text, $tooltip){
  print '<div class="tooltip">' . $text . '
  <span class="tooltiptext">' . $tooltip . '</span>
</div>';
}

function tooltipspan($text, $tooltip){
  print '<span class="tooltip">
  <span class="tooltiptext">' . $tooltip . '</span>' . $text . '
</span>';
}

/**
 * Bulilds oudio player code
 */
function getplayer($filename){
  if (!$filename) return '';
  $default_type = 'audio/mpeg';
  $type = preg_match("/\.m3u$/", $filename) ? 'audio/mpegurl' : $default_type;
  $type = preg_match("/\.webm$/", $filename) ? 'video/webm' : $default_type;
  $tag = 'audio';
  if ($type == 'video/webm') {
    $tag = 'video';
  }
  $rz = '<div class="audio-player"><'. $tag. ' controls loop>
         <source src="' . $filename . '" type="' . $type . '">
         Your browser does not support the audio element.
         </' . $tag . '></div>' . "\n";
  return $rz;
}

/**
 * Parse all sentences from a string.
 */
function parse_sentences($txt){
  preg_match_all("/(.*?)(\:|\.|\?|\!|。|？|！|：|\,|，)/", $txt, $mt);
  return $mt[0];
}

function tooltipsentences($src, $trans, $scrb = FALSE){
  $src = parse_sentences($src);
  $trans = parse_sentences($trans);
  $scrb = $scrb ? parse_sentences($scrb) : $scrb;

  foreach($src as $n => $l) {
    $tooltip = isset($trans[$n]) ?
      '<span class="transcript"> ' . $trans[$n] . "</span>\n" : '';
    $tooltip .= $scrb && isset($scrb[$n]) ?
      '<span class="translation"> ' . $scrb[$n] . "</span>\n" : '';
    tooltipspan($l, $tooltip);
  }
}

/**
 * Check the global $values variable for a key to be set and returns value
 */
function echoval($arr, $key) {
  if(isset($arr[$key])) echo $arr[$key];
}

function theme_errors(&$errors){
  if (isset($errors['#warnings'])) {
    $warns = $errors['#warnings'];
    unset($errors['#warnings']);
  }
  if (isset($errors['#notes'])) {
    $notes = $errors['#notes'];
    unset($errors['#notes']);
  }
  $out = "";
  if ($errors) {
    foreach ($errors as $err) {
      if (is_array($err)) $out .= theme_errors($err);
      else
        $out .= "<div class=\"error\">$err</div>";
    }
  }
  if ($warns) {
    foreach ($warns as $wrn) {
      $out .= "<div class=\"warning\">$wrn</div>";
    }
  }
  if ($notes) {
    foreach ($notes as $note) {
      $out .= "<div class=\"note\">$note</div>";
    }
  }
  return $out;
}

function generate_lesson_mp3($lng) {
  if (!LESSON) return FALSE;

  $text_file = DIR_LESSONS . '/' . LESSON . '/' . $lng . '.txt';
  $mp3_file = DIR_LESSONS . '/' . LESSON . '/' . $lng . '.mp3';
  return `gtts-cli.py -f "$text_file" -l "$lng" -o "$mp3_file"`;
}
?>
