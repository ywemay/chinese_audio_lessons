<?php
if (!LESSON) {
  echo "<div class=error>Lesson key required...</div>";
  exit;
}
print '<h1>Edit lesson ' . LESSON . '</h1>';

$values = array(
  'lesson' => LESSON,
  'en' => implode("", lesson_file('en.txt')),
  'zh-cn' => implode("", lesson_file('zh-cn.txt')),
  'zh-cn-pinying' => implode("", lesson_file('zh-cn-pinying.txt')),
  'vocab' => implode("", lesson_file('vocab.txt')),
);

include(__DIR__ . '/forms/edit_lesson.php');
