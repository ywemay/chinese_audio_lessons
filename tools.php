<h1>Lesson Tools</h1>

<?php

if (!LESSON) {
  $errors[] = 'Lesson key is required!';
  print theme_errors($errors);
  exit;
}
$en_text = lesson_file('en.txt');

echo A('https://translate.google.com/#en/zh-CN/' . urlencode( implode('', $en_text)), 'Google Translate En to Zh', array('attributes' => array('target'=>'_blank')));
?>
