<?php

class tests_search extends GroupTest 
{
	function tests_search() 
	{
	  $this->GroupTest('search tests');
	  $this->addTestFile(TEST_CASES_DIR . '/search/test_search_query.php');
	  $this->addTestFile(TEST_CASES_DIR . '/search/test_search_text_normalizer.php');
	  $this->addTestFile(TEST_CASES_DIR . '/search/test_search_phone_number_normalizer.php');
	  $this->addTestFile(TEST_CASES_DIR . '/search/test_search_indexer.php');
	  $this->addTestFile(TEST_CASES_DIR . '/search/test_full_text_search_indexer.php');
	  $this->addTestFile(TEST_CASES_DIR . '/search/test_search_full_text_find.php');
	}
}
?>