<?php

/**
 * Twitter Perch - Auto Follow by Keyword on Twitter
 *
 * Copyright (c) 2009 Iarfhlaith Kelly (webstrong.ie)
 * Licensed under GPL (http://www.gnu.org/licenses/gpl.html) license.
 *
 * Date: 2009-02-17
 */

class twitterPerch
{
   /**
    * The class constructor.
	*
    */
	public function __construct()
	{
		// Initialise Packages
		require_once('XML/RSS.php');
		require_once('HTTP/Request.php');
	}
	
   /**
    * Adds the user details to the list
    *
	* @param	array	$vars
	* @return 	boolean
    */
	public  function add($vars)
	{		
		$datetime = time();
		$dbh      = dbConnection::get()->handle();
		
		// Add Record
		$sql = "INSERT INTO searches
				 ( keyword
				 , username
				 , password
				 , datetime
				 , ip)
				 
				VALUES
				 ('{$vars['keyword']}'
				 ,'{$vars['username']}'
				 ,'{$vars['password']}'
				 ,'{$datetime}'
				 ,'{$_SERVER['REMOTE_ADDR']}')";
		
		$affected =& $dbh->exec($sql);
		if (PEAR::isError($affected)) return(false);
		
		return(true);
	}
	
  /**
    * Remove the user details from the list
    *
	* @param	array	$vars
	* @return 	boolean
    */
	public  function remove($vars)
	{		
		$dbh      = dbConnection::get()->handle();
		
		// Add Record
		$sql = "DELETE FROM searches WHERE username = '{$vars['username']}' AND password = '{$vars['password']}'";
		
		$affected =& $dbh->exec($sql);
		if (PEAR::isError($affected)) return(false);
		
		return(true);
	}
	
   /**
    * Load the list of keywords to be auto followed.
    * 
    * The rules are that a user cannot auto follow more then 20 Twitter users in any 24 hour period.
    * 
    * If the script runs every 10 minutes then we need to know when each keyword was last run
    * so that we don't run it again for another 24 hours.
    * 
    * We do this by getting the current timestamp and then selecting only the records that have a lastRun
    * timestamp of more then 60x60x24 = 86,400 seconds behind the current timestamp.
    * 
	* @param	integer	$now The sync'd timestamp to be used to build the rules to decide what records to run
	* @param	array	$lag The accepted timelag between now and the recorded lastRun value
	* @return 	boolean
    */
	public  function loadList($now, $lag)
	{
		$list = array();
		$rule = $now - $lag;
		
		$dbh = dbConnection::get()->handle();
		
		$sql = "SELECT * FROM searches WHERE lastRun < '{$rule}'";
		
		// Execute the query
		$result =& $dbh->query($sql);
		
		while($row = $result->fetchRow())
		{
			array_push($list, $row);
		}
		
		return($list);
	}
	
  /**
    * Attempt to Auto Follow.
    * 
	* @param	string	$q The url-encoded version of the keyword(s) to search for on Twitter.
	* @param	string	$username The username of the person wanting to do the auto follow.
	* @param	string	$password The password of the person wanting to do the auto follow.
	* @param	integer	$qty The max number of people to follow in one run. Default is 20.
	* @param	integer	$latestStatus The last status reached during the last time this record was processed. Stops redundant checks.
	* @return 	boolean
    */
	public function process($q, $username, $password, $qty=20, $latestStatus='')
	{	
		$num = 0;
		$rss =& new XML_RSS("http://search.twitter.com/search.rss?q=".$q."&rpp=20&since_id=".$latestStatus);
		$rss->parse();
	
		foreach ($rss->getItems() as $item)
		{
			// Get username from Link (Twitter username of person to follow)
			$tempname = substr($item['link'], 19 );
			$position = strpos($tempname    , '/');
			$twitUser = substr($tempname    , 0, $position);
			
			// Follow User
			$req = new HTTP_Request('http://twitter.com/friendships/create/'.$twitUser.'.xml');
			
			$req->setMethod(HTTP_REQUEST_METHOD_POST);
			$req->setBasicAuth($username, $password);
		
			$response     = $req->sendRequest();
			$responseCode = $req->getResponseCode();
			
			// Count New Followers
			if($responseCode == '200') $num++;
		}
		
		return($num);
	}

   /**
    * Update the Last Run information for the records that were use in the last run :)
    * 
    * This prevents them from being run more often then is allowed.
    * 
	* @param	integer	$now The sync'd timestamp to be used to build the rules to decide what records to run
	* @param	array	$lag The accepted timelag between now and the recorded lastRun value
	* @return 	boolean
    */
	public  function updateLastRun($now, $lag)
	{
		$rule = $now - $lag;
		
		$dbh = dbConnection::get()->handle();
		
		$sql = "UPDATE searches SET lastRun = '{$now}', runNumber = runNumber+1 WHERE lastRun < '{$rule}'";
		
		$affected =& $dbh->exec($sql);

		// Check for Error
		if (PEAR::isError($affected))
		{
    		return(false);
		}
		
		return(true);
	}

   /**
    * Store the activity of the AutoFollow script
    *
	* @param	integer	$runtime	 - The timestamp of when the script was run (unique identifier).
	* @param	integer $recordCount - The number of records processed in this run
	* @param	integer $followCount - The number of new follows in this script
	* @return 	boolean
    */
	public  function log($runtime, $recordCount, $followCount)
	{		
		$dbh      = dbConnection::get()->handle();
		
		// Add Record
		$sql = "INSERT INTO autoFollowLog
				 ( runtime
				 , recordCount
				 , followCount)
				 
				VALUES
				 ('{$runtime}'
				 ,'{$recordCount}'
				 ,'{$followCount}')";
		
		$affected =& $dbh->exec($sql);
		if (PEAR::isError($affected)) return(false);
		
		return(true);
	}

   /**
    * Checks the validity of the supplied Twitter credentials.
    * 
    * Will return false if the username/password combo fails to login.
    *
	* @param 	string  $username - The supplied Twitter username
	* @param 	boolean $empty    - Optional flag, decides whether the email is optional or not
	* @param 	array 	$params   - The extra parameters provided by SmartyValidate
	* @param 	array 	$formvars - The other form variables provided by the form
	* @return 	boolean			  - True if email exists, false if not.
    */
	public function isValidTwitterCredentials($username, $empty, &$params, &$formvars)
	{
		// Test Parameters
		if (!isset($username) || !isset($formvars[$params['field2']])) return($empty);
		
		$username = cleanValue($username);
		$password = $formvars[$params['field2']];
		
		$req = new HTTP_Request('http://twitter.com/account/verify_credentials.xml');
		
		$req->setMethod(HTTP_REQUEST_METHOD_POST);
		$req->setBasicAuth($username, $password);
		
		$response     = $req->sendRequest();
		$responseCode = $req->getResponseCode();
		$responseBody = $req->getResponseBody();
		
		if($responseCode == '200') return(true);
		
		return(false);
	}
}

?>