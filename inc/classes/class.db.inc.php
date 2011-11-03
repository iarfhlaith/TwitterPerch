<?php

/**
 * Twitter Perch - Auto Follow by Keyword on Twitter
 *
 * Copyright (c) 2009 Iarfhlaith Kelly (webstrong.ie)
 * Licensed under GPL (http://www.gnu.org/licenses/gpl.html) license.
 *
 * Date: 2009-02-17
 */

class dbConnection
{
	private $_handle = null;

	public static function get()
	{
    	static $db = null;
	
		if ($db == null)
		{
			$db = new dbConnection();
		}
	
    	return $db;
	}

	private function __construct()
	{
		$dsn = array(
		   'phptype' => 'mysql'
		, 'username' => DB_USER
		, 'password' => DB_PASS
		, 'hostspec' => DB_IP
		, 'database' => DB_NAME);

		// Connect to Database
		$this->_handle =& MDB2::connect($dsn);

		if (PEAR::isError($this->_handle))
		{
			die($this->_handle->getMessage());
		}

		// Set Default Fetch Mode
		$this->_handle->setFetchMode(MDB2_FETCHMODE_ASSOC);
		
		// Keep Studly Caps Naming Convention
		$this->_handle->setOption('portability',MDB2_PORTABILITY_ALL ^ MDB2_PORTABILITY_FIX_CASE);
	}
  
	public function handle()
	{
		return $this->_handle;
	}
}

?>