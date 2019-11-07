<?php

$score = $_GET['score'];
echo str_replace('image_share_score', $score, file_get_contents('share.html'));
