#!/usr/bin/php

<?php
$fo = fopen('debug.txt','a');
fwrite($fo, 'ok');
fclose($fo);
?>
