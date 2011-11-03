[~
 **
 * Twitter Perch - Auto Follow by Keyword on Twitter
 *
 * Copyright (c) 2009 Iarfhlaith Kelly (webstrong.ie)
 * Licensed under GPL (http://www.gnu.org/licenses/gpl.html) license.
 *
 * Date: 2009-02-17
 *
 *
~]

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<title>Auto Follow by Keyword</title>
	
	<meta http-equiv="pragma" content="no-cache">
	<meta http-equiv="cache-control" value="no-cache, no store, must-revalidate">
	<meta http-equiv="content-language" content="en-us" />
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
	
	<link rel="shortcut icon" href="/favicon.ico" />
	<link rel="stylesheet" type="text/css" href="/css/screen.css" />
	
</head>

<body>
		
	<h1>Auto Follow People Who Tweet About....</h1>
	
	[~if $success~]
	
		<h2>[~$text.success~]</h2>
		<ul><li><a href='/'>Track another keyword</a></li></ul>
		
	[~else~]
			
		[~ validate id='keyword'  	message=$text.keyword   append='error' ~]
		[~ validate id='username' 	message=$text.username  append='error' ~]
		[~ validate id='password' 	message=$text.password  append='error' ~]
		[~ validate id='accValid' 	message=$text.accValid  append='error' ~]
		
		[~if $error~]
			<h2>Ooops! Have Another Go</h2>
			<ul>[~foreach from=$error item="val"~]<li>[~$val~]</li>[~/foreach~]</ul>
		[~/if~]
		
		<form method='post' action='index.php' name='index'>
			Keyword:
			<input name='keyword' id='name' value='[~$keyword~]' />
			<br />
			Twitter Username: 
			<input name='username' class='details' value='[~$username~]' />
			<br />
			Twitter Password:
			<input name='password' class='details' value='[~$password~]' type='password' />
		</form>
			
	[~/if~]
	
</body>
</html>