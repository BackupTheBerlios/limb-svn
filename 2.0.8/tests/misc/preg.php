<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/


$s = 'a = "wow that\'s a reall \"string\"this is found" c ="hey\"\""';

echo $p = '"[^"\\\\]*(?:\\\\.[^"\\\\]*)*"';
echo "<br>";
echo "<br>";

preg_match_all('/' . $p . '/', $s, $matches);

var_dump($matches);

echo "<br>";
echo "<br>";

$line = '"wow \#" #comment';

$p = '([^"#]+|"(.*)")|(#[^#]*)';

$no_comment_line = ereg_replace($p, "\\1", $line);

echo $no_comment_line;

echo "<br>";
echo "<br>";

$no_comment_line = preg_replace('/'. $p . '/', "\\1", $line);

echo $no_comment_line;


?>