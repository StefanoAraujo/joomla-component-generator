<?php

	$connect= mysql_connect($server,$user,$password);
	mysql_query("set names 'utf8'");
	mysql_select_db($db,$connect);
	$resource = mysql_query('SHOW TABLES FROM '.$db);
	$setting;
	$i = 0;
	while($row=mysql_fetch_array($resource))
	{
		if(stripos($row[0],$prefix)===0)
		{
			$columns = null;
			$r_columns = mysql_query('SHOW COLUMNS FROM '.$row[0]);
			while($row_coumn=mysql_fetch_array($r_columns))
			{
				$columns[] = $row_coumn[0];
				if($row_coumn[3]=='PRI')
					$setting[$i]['primary'] = $row_coumn[0];
			}
			$setting[$i]['name'] 		= strtoupper(substr(str_ireplace($prefix,'',$row[0]),0,1)).substr(str_ireplace($prefix,'',$row[0]),1);
			$setting[$i]['columns'] 	= $columns;
			$setting[$i]['table'] 		= str_ireplace($joomla_prefix,'#__',$row[0]);
			$i++;
		}
	}
	
	foreach($setting as $s)
	{
		$result = file_get_contents(dirname(__FILE__).'\\sys_default.tmpl.php');
		$result = str_ireplace('<component>',$component_name,$result);
		$result = str_ireplace('<view>',strtolower($s['name']),$result);
		$result = str_ireplace('<primary_id>',strtolower($s['primary']),$result);
		
		@mkdir(dirname(__FILE__).'\\'.$component_name.'\\backend\\views\\');
		@mkdir(dirname(__FILE__).'\\'.$component_name.'\\backend\\views\\'.strtolower($s['name']));
		@mkdir(dirname(__FILE__).'\\'.$component_name.'\\backend\\views\\'.strtolower($s['name']).'\\tmpl');

		@mkdir(dirname(__FILE__).'\\'.$component_name.'\\frontend\\views\\');
		@mkdir(dirname(__FILE__).'\\'.$component_name.'\\frontend\\views\\'.strtolower($s['name']));
		@mkdir(dirname(__FILE__).'\\'.$component_name.'\\frontend\\views\\'.strtolower($s['name']).'\\tmpl');
		
		file_put_contents(dirname(__FILE__).'\\'.$component_name.'\\backend\\views\\'.strtolower($s['name']).'\\tmpl\\default.php', $result);
		
		file_put_contents(dirname(__FILE__).'\\'.$component_name.'\\frontend\\views\\'.strtolower($s['name']).'\\tmpl\\default.php', $result);		
	}

?>