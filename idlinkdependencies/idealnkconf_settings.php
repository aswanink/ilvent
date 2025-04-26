<?php
    date_default_timezone_set('Asia/Calcutta');
  
	
	 /* SITE PATH START */
  
	 define("DIR_ROOT", $_SERVER['DOCUMENT_ROOT'].'/php/ilvent/');    
	 define("SITE_ROOT", "http://".$_SERVER['HTTP_HOST'].'/php/ilvent/');;
	require_once(DIR_ROOT."idlinkdependencies/db_usrcredentials.php");
	define("COUNT",4);
	define("USERSIDE_NEWSCOUNT",4);
    /* SITE PATH END */
	
	
	define("SITE_NAME", "ilvent");
  	define("ASTRIK","<span class='errortext'>*</span>");
  	define("IDEA_TIMESTAMP", date('Y-m-d H:i:s')); 
	define("IDEA_DATE", date('Y-m-d')); 
	define("SYSMSG_TIME",1000);


require_once(DIR_ROOT."idlcls/class.idealink.php");



?> 