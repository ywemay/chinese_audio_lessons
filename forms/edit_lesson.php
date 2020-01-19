<?php
function lesson_form_validate($vals) {
  $errors = array();
  $reqs = array('en', 'zh-cn', 'zh-cn-pinying');
  if (ACTION == 'create') {
    $reqs[] = 'lesson';
    if(!preg_match("/^\w+$/", $vals['lesson'])) {
      $errors['invalid']['lesson'] = 'Lesson key shall be one word only.';
    }
  }
  else {
    if (!is_dir(DIR_LESSONS . '/' . LESSON)) {
      $errors['invalid']['lesson'] = 'Invalid lesson key';
    }
  }
  foreach($reqs as $k){
    if (!isset($vals[$k]) || !$vals[$k]) $errors['required'][$k] = TRUE;
  }

  $en_len = count(parse_sentences($vals['en']));
  $zh_len = count(parse_sentences($vals['zh-cn']));
  $py_len = count(parse_sentences($vals['zh-cn-pinying']));

  if ($en_len != $zh_len) $errors['#warnings']['enzh_len'] = 'English/Chinese sentence number is different ' . $en_len . ':' . $zh_len;
  if ($py_len != $zh_len) $errors['enzh_len'] = 'Pinying/Chinese sentence number is different ' . $py_len . ':' . $zh_len;

  return $errors;
}

function lesson_form_save($vals){
  $lesson = isset($vals['lesson']) ? $vals['lesson'] : LESSON;
  $ldir = DIR_LESSONS . '/' . $lesson;
  if (!is_dir($ldir)) {
    if (mkdir($ldir)) {
      define('LESSON', $lesson);
    }
  }

  foreach(array('en', 'zh-cn', 'zh-cn-pinying', 'vocab') as $k) {
    file_put_contents($ldir . '/' . $k . '.txt', $vals[$k]);
  }
  if (!empty($vals['generate-mp3'])) {
    foreach(array_keys($vals['generate-mp3']) as $l){
      $out = generate_lesson_mp3($l);
      echo "<div class=note>Generate audio for $l...$out</div>";
    }
  }
}
?>

<?php
$links = array();
$links[] = a('/', 'Back');
if(!empty($_POST)) {
  $values = $_POST;
  $errs = lesson_form_validate($_POST);
  echo theme_errors($errs);
  if (!$errs) {
    lesson_form_save($_POST);
    echo "<div class=msg>Saved successfully</div>";
    if (ACTION == 'create') {
      $links[] = a('/?a=edit&lesson=' . LESSON, 'Continue Editing');
      exit;
    }

  }
  if (LESSON)
  $links[] = a('/?lesson=' . LESSON , 'View Lesson');
}
echo implode(" &bull; ", $links);
$cols=60; $rows=20; ?>
<form action="<?php echo $_SERVER['REQUEST_URI'];?>" method=post>
<?php
  if(ACTION == 'create') {
?>
<label>Lesson Key:</label>
<input type="text" value="<?php echoval($values, 'lesson'); ?>" name=lesson />
<?php
  }
?>
<div>
<label>English text:</label><br />
<textarea name=en cols=<?php echo $cols;?> rows=<?php echo $rows;?>><?php echoval($values, 'en'); ?></textarea>
</div>
<?php
if (isset($values['en'])) {
  echo a('https://translate.google.com/#en/zh-CN/' . urlencode($values['en']), 'Google Translate En to Zh', array('attributes' => array('target'=>'_blank')));
}
?>
<div>
<label>Chinese text:</label><br />
<textarea name="zh-cn" cols=<?php echo $cols;?> rows=<?php echo $rows;?>><?php echoval($values, 'zh-cn'); ?></textarea>
</div>
<div>
<label>Chinese pinying:</label><br />
<textarea name="zh-cn-pinying" cols=<?php echo $cols;?> rows=<?php echo $rows;?>><?php echoval($values, 'zh-cn-pinying'); ?></textarea>
</div>

<label>Vocabulary:</label><br />
<textarea name="vocab" cols=<?php echo $cols;?> rows=<?php echo $rows;?>><?php echoval($values, 'vocab'); ?></textarea>
</div>

<div>
<div class="actions"><input type="checkbox" name="generate-mp3[en]" /> <label for="generate-mp3[en]">Generate mp3 file for English language</label></div>
<div class="actions"><input type="checkbox" name="generate-mp3[zh-cn]" /> <label for="generate-mp3[zh-cn]">Generate mp3 file for Chinese language</label></div>
</div>
<input type=submit value=Save />
</form>
