<?php

$TO_REV = 'HEAD';
$FROM_REV = 1230;
$SVN_PATH = '../';

$OUTPUT_DIR = './release';

function query_svn_logs($svn_path, $to_rev, $from_rev)
{
  echo "Querying svn...\n";

  $result = array();
  $out = array();
  $cmd = "svn log {$svn_path} -r{$to_rev}:{$from_rev}";
  exec($cmd, $out);

  foreach($out as $string)
  {
    //skipping empty lines
    if(trim($string) == '')
      continue;

    //skipping ------ lines
    if(preg_match('~^-{20,}~', $string))
      continue;

    //skipping revision info lines
    if(preg_match('~^r\d+~', $string))
      continue;

    if(preg_match('~svn\s+merge\s+-r\s*(\d+):(\d+)\s+(\S+)\s+(\S*)~', $string, $matches))
    {
      echo "Merge detected\n";

      $merge_out = query_svn_logs($svn_path . $matches[3], $matches[2], $matches[1]);

      foreach($merge_out as $merge_string)
        $result[] =  $merge_string;

    }
    else
    {
      $result[] = $string;
    }
  }

  return $result;
}

function process_logs($out)
{
  echo "Processing logs...\n";

  $result = array();
  $fixes = array();
  foreach($out as $string)
  {
    //putting fixes to the top
    if(preg_match('~(fixed:?\s|fix\s)~', $string))
      $fixes[] = $string;
    else
      $result[] = $string;
  }

  if($fixes)
    array_unshift($result, "\n");

  foreach($fixes as $fix)
    array_unshift($result, $fix);

  return $result;
}

$out = query_svn_logs($SVN_PATH, $TO_REV, $FROM_REV);
$out = process_logs($out);

$file = "{$OUTPUT_DIR}/LOG-{$TO_REV}-{$FROM_REV}";
$fh = fopen($file, 'w');
fwrite($fh, implode("\n", $out));
fclose($fh);

?>
