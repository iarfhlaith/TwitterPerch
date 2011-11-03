<?php

/**
 * Twitter Perch - Auto Follow by Keyword on Twitter
 *
 * Copyright (c) 2009 Iarfhlaith Kelly (webstrong.ie)
 * Licensed under GPL (http://www.gnu.org/licenses/gpl.html) license.
 *
 * Date: 2009-02-17
 */

// Set Paths to Key Libraries
$smartyPath = "/your/path/to/smarty";
$pearPath   = "/your/path/to/pear";

// Force Error Level
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 'on');

$addLength  = strlen($_SERVER["SCRIPT_FILENAME"]) - strlen($_SERVER["SCRIPT_NAME"]);

// Global Constants
define("WEB_ROOT" , substr($_SERVER["SCRIPT_FILENAME"], 0, $addLength));
define("DB_USER"  , 'username');
define("DB_IP"    , '0.0.0.0');
define("DB_PASS"  , 'password');
define("DB_NAME"  , 'database');

// Config php.ini Commands
ini_set('session.use_trans_sid'	, false);
ini_set('include_path'			, '.' . PATH_SEPARATOR . $pearPath . PATH_SEPARATOR . $smartyPath);

// Start The Session
session_start();

// Include PEAR MDB2
require_once('MDB2.php');

// Smarty Template Engine
require_once('Smarty.class.php');
require_once('SmartyValidate.class.php');
$smarty = new Smarty;

// Set Smarty Variables
$smarty->config_dir      = WEB_ROOT.'/inc';
$smarty->template_dir    = WEB_ROOT.'/tpl';
$smarty->compile_dir     = WEB_ROOT.'/tpl_c';
$smarty->left_delimiter  = '~]';
$smarty->right_delimiter = '[~';

// Include Class Files
require_once (WEB_ROOT.'/inc/classes/class.db.inc.php');
require_once (WEB_ROOT.'/inc/classes/class.perch.inc.php');

// Include Language Files
require_once (WEB_ROOT.'/inc/config/english.php');

?>