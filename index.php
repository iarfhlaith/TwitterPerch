<?php

/**
 * Twitter Perch - Auto Follow by Keyword on Twitter
 *
 * Copyright (c) 2009 Iarfhlaith Kelly (webstrong.ie)
 * Licensed under GPL (http://www.gnu.org/licenses/gpl.html) license.
 *
 * Date: 2009-02-17
 */

require_once ('inc/initialise.php');

// Defaults
$valid   = false;
$success = false; 
$page	 = 'index';

// Start Twitter Perch
$tp = new twitterPerch;

// Initialise Form Validators
if(empty($_POST))
{
	SmartyValidate::connect($smarty, true);
	
	SmartyValidate::register_validator('keyword'  , 'keyword'  			, 'notEmpty');
	SmartyValidate::register_validator('username' , 'username' 			, 'isWord'  , false, true);
	SmartyValidate::register_validator('password' , 'password:6'	    , 'isLength', false, true);
	SmartyValidate::register_validator('accValid' , 'username:password' , 'isValid');
}
else
{
	SmartyValidate::connect($smarty);
	
	SmartyValidate::register_object('tp', $tp);
	SmartyValidate::register_criteria('isValid' , 'tp->isValidTwitterCredentials');
	
	if($valid=SmartyValidate::is_valid($_POST))
	{
		SmartyValidate::disconnect();
		
		// Clean Values
		$formVars = array(
				     'keyword' => cleanValue($_POST['keyword'])
			,		'username' => cleanValue($_POST['username'])
			,		'password' => cleanValue($_POST['password']));
		
		// Add To List
		$success = $tp->add($formVars);
	}
}

// Assign Variables
$smarty->assign('text'     , $lang[$page]);
$smarty->assign('success'  , $success);
$smarty->assign($_POST);

// Trim the Whitespace
$smarty->load_filter('output','trimwhitespace');

// Display in Template
$smarty->display($page.'.tpl');
?>