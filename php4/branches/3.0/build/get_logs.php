<?php

$WORKING_COPY_DIR = dirname(__FILE__) . '/../';
$OUTPUT_DIR = dirname(__FILE__) . '/release/';

$TO_REV = 'auto';
$FROM_REV = 'auto';

$SVN = 'svn';

//==================!!! don't edit below if not sure !!!==================

function make_sure_working_copy_is_fresh()
{
  global $WORKING_COPY_DIR;
  global $SVN;

  echo "Making sure the working copy is up-to-date...\n";

  exec("$SVN up $WORKING_COPY_DIR");
  exec("$SVN st $WORKING_COPY_DIR", $out);

  foreach($out as $line)
  {
    if(trim($line) != '')
    {
      echo "There are modifications in the working copy!!!";
      exit(1);
    }
  }
}

function query_svn_logs($working_copy, $to_rev, $from_rev)
{
  global $SVN;

  echo "Querying svn({$working_copy} r{$to_rev}:{$from_rev})...\n";

  $result = array();
  $out = array();
  exec("$SVN log {$working_copy} -r{$to_rev}:{$from_rev}", $out);

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
      echo "Merge detected($matches[3] $matches[2]:$matches[1])\n";

      $merge_out = query_svn_logs($working_copy . $matches[3], $matches[2], $matches[1]);

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

function get_last_svn_revision()
{
  global $WORKING_COPY_DIR;

  exec("svn info $WORKING_COPY_DIR", $out);

  foreach($out as $line)
  {
    if(preg_match('~Revision:\s*(\d+)~', $line, $m))
      return $m[1];
  }

  return -1;
}

function get_repos_uri()
{
  global $WORKING_COPY_DIR;

  exec("svn info $WORKING_COPY_DIR", $out);

  foreach($out as $line)
  {
    if(preg_match('~URL:(.*)$~', $line, $m))
      return trim($m[1]);
  }

  return -1;
}

function get_last_changelog_revision()
{
  global $WORKING_COPY_DIR;

  exec("head $WORKING_COPY_DIR/CHANGELOG", $out);

  foreach($out as $line)
  {
    if(preg_match('~r(\d+)\)$~', $line, $m))
      return $m[1];
  }
  return -1;
}

make_sure_working_copy_is_fresh();

$FROM_REV = ($FROM_REV == 'auto' ? get_last_changelog_revision() : $FROM_REV);
$TO_REV = ($TO_REV == 'auto' ? get_last_svn_revision() : $TO_REV);
$REPOS_URI = get_repos_uri();

$OUTPUT_FILE = "{$OUTPUT_DIR}/LOG-{$FROM_REV}-{$TO_REV}";

$out = query_svn_logs($WORKING_COPY_DIR, $TO_REV, $FROM_REV);
$out = process_logs($out);

$fh = fopen($OUTPUT_FILE, 'w');
fwrite($fh, "$REPOS_URI r{$FROM_REV}:{$TO_REV}\n\n");
fwrite($fh, implode("\n", $out));
fclose($fh);

echo "Done.";
?>
