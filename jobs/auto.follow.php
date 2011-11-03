<?php

/**
 * Twitter Perch - Auto Follow by Keyword on Twitter
 *
 * Copyright (c) 2009 Iarfhlaith Kelly (webstrong.ie)
 * Licensed under GPL (http://www.gnu.org/licenses/gpl.html) license.
 *
 * Date: 2009-02-17
 */

// Initialise Packages
require_once('../inc/initialise.php');

// Initialise Defaults
$now          = time();
$lag		  = 86400;
$response	  = false;
$responseCode = false;
$recordCount  = 0;
$followCount  = 0;

// Start Twitter Perch
$tp = new twitterPerch;

$list = $tp->loadList($now, $lag);

foreach($list as $search)
{
	$recordCount++;
	$q = urlencode($search['keyword']);
	
	$followCount = $followCount + $tp->process($q, $search['username'], $search['password'], 20);
}

// Update the lastRun values
$tp->updateLastRun($now, $lag);

// Add Activity To Log
$tp->log($now, $recordCount, $followCount);

?>
