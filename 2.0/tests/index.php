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


ob_start();

require_once('setup.php');

$tests = parse_ini_file('tests.ini');

if (isset($_GET['test'])) 
{
	$test = $_GET['test'];
  include($test.'.php');
  
  if (class_exists($test)) 
  {
    $test =& new $test();    
    $test->run(new HtmlReporter());
  }
  echo "<p><a href='index.php'>back</a></p>";
  echo debug :: parse_html_console();
	exit();
}
?>
<h1>unit test suite</h1>
Pick a test:
<ol>
<?php
$tests = array_flip($tests);
foreach ( $tests as $test ) 
{
?>
	<li><a href="<?php echo ( $_SERVER['PHP_SELF'] ); ?>?test=<?php echo (urlencode($test)); ?>"><?php echo ($test); ?></a></li>
	<?php
}
?>
</ol>
<a href="<?php echo ( $_SERVER['PHP_SELF'] ); ?>?test=tests_all"><b>test all</b></a>
<?
ob_end_flush();
?>