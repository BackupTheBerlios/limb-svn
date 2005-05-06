<?php

$TO_REV = 'HEAD';
$FROM_REV = 1230;
$SVN_PATH = '../';

$OUTPUT_DIR = './release';

function query_svn_logs($svn_path, $to_rev, $from_rev)
{
  echo "Querying svn...\n";

  $out = array();
  $cmd = "svn log {$svn_path} -r{$to_rev}:{$from_rev}";
  exec($cmd, $out);

  foreach($out as $index => $string)
  {
    if(preg_match('~svn\s+merge\s+-r\s*(\d+):(\d+)\s+(\S+)\s+(\S*)~', $string, $matches))
    {
      echo "Merge detected\n";

      $merge_out = query_svn_logs($svn_path . $matches[3], $matches[2], $matches[1]);
      $out[$index] = "\n========= EXPANDING MERGE START =========\n" .
                     implode("\n", $merge_out) .
                     "\n========= EXPANDING MERGE END =========\n";
    }
  }

  return $out;
}

$out = query_svn_logs($SVN_PATH, $TO_REV, $FROM_REV);

$file = "{$OUTPUT_DIR}/LOG-{$TO_REV}-{$FROM_REV}";
$fh = fopen($file, 'w');
fwrite($fh, implode("\n", $out));
fclose($fh);

?>
