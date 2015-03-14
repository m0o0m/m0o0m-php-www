<?php

	if( !defined('PROPER_START') )
	{
		header("HTTP/1.0 403 Forbidden");
		exit;
	}

	$site = api::send('self/site/list', array('id'=> security::encode($_POST['id']) ));
	$site = $site[0];

	$database = api::send('self/database/list');
	$me = api::send('self/whoami', array('quota'=>true))[0];
	
	if( !isset($_POST['sql']) || empty($_POST['sql']) )	
		$_GLOBALS['APP']['PASSWORD'] = random( rand(15, 20) );
	else
		$_GLOBALS['APP']['PASSWORD'] = security::encode( $_POST['sql'] );
	
	
	$_GLOBALS['APP']['VERSION'] = "4.1";
	$_GLOBALS['APP']['NAME'] = "wordpress";

	if( $_POST['path'] == 1 )
		$_GLOBALS['APP']['PATH'] = '/folder';
	else
		$_GLOBALS['APP']['PATH'] = '';
		
	/* ================ CLEAN UNUSED DATABASES ================ */
	foreach( $database as $d )
	{
		if ( ( empty( $d['size'] ) || $d['size']  == 0 ) && $d['desc'] == 'wordpress' )
		{
			api::send('self/database/del', array( 'database'=>  $d['name'] ));
			$count++;
		}
	}
	
	if ( $me['quotas'][2]['used'] >= $me['quotas'][2]['max'] )
		if ( $count <= 0 )
			throw new SiteException('Please remove one of your databases ', 400, 'quota reached');

			
	$new = api::send('self/database/add', array('type'=>'mysql', 'desc'=>'wordpress', 'pass'=> $_GLOBALS['APP']['PASSWORD'] ));
	$database = api::send( 'self/database/list', array( 'database' => $new['name'] ) )[0];
	$content = file_get_contents( __DIR__.'/import/wordpress-en_EN.zip' );
	
		// write config file on remote directory
		$conf = "
		; This is a configuration file linked to the quick installation
		; It has been automatically generated
		; #### PLEASE DO NOT REMOVE ####
		
		[CONFIG]
		cms = '".$_GLOBALS['APP']['NAME']."'
		version = '".$_GLOBALS['APP']['VERSION']."'
		directory = '".$_GLOBALS['APP']['PATH']."'
		database = '{$database['name']}'
		";
	
	$unzip = file_get_contents( __DIR__.'/unzip.php' );
	$unzip = str_replace("##PATH##", $_GLOBALS['APP']['PATH'], $unzip);
	$unzip = str_replace("##FILE##", $conf, $unzip);
	
	
	/* ================ SET UP SECURE SFTP CONNECTION ================ */
	$con = ssh2_connect( 'ftp.olympe.in', 22 );
	ssh2_auth_password( $con, $site['name'], $_POST['pass']);

	$sftp = ssh2_sftp( $con );
	
	var_dump ( $sftp );
	var_dump ( $con );
	
	exit();
	
	if ( !$login )
	{
		$_SESSION['MESSAGE']['TYPE'] = 'error';
		$_SESSION['MESSAGE']['TEXT']= "An error has occured. Cannot set up any connection to the remote directory.";
		$template->redirect('/panel/sites/config?id='.$site['id']);
	}
	
	/* ================ GENERATE TEMPORARY FILES ================ */
	file_put_contents ( __DIR__.'/temp/archive.zip', $content );
	file_put_contents ( __DIR__.'/temp/unzip.php', $unzip );
	
	ssh2_scp_send( $con, __DIR__.'/temp/archive.zip', '/file.zip' , 0644  );
	ssh2_scp_send( $con, __DIR__.'/temp/unzip.php', '/unzip.php' , 0644  );

	$check = file_get_contents( "https://".$site['name'].".olympe.in/unzip.php" );
	ssh2_sftp_unlink( $sftp, '/unzip.php' );
	
	/* ================ CLEAN UP ================ */
	unlink (  __DIR__.'/temp/archive.zip' );
	unlink (  __DIR__.'/temp/unzip.php' );
	
	if ($check == 'done')
	{
		$config = file_get_contents( __DIR__."/import/wp-config.php" );
		$config = str_replace("{{[database]}}", "{$database['name']}", $config);
		$config = str_replace("{{[server]}}", "{$database['server']}", $config);
		$config = str_replace("{{[password]}}", $_GLOBALS['APP']['PASSWORD'], $config);
		$config = str_replace("{{[salt]}}", file_get_contents('https://api.wordpress.org/secret-key/1.1/salt/') , $config);
		$config = str_replace("{{[random_char]}}", 'on_', $config);
		
		file_put_contents ( __DIR__.'/temp/config.php', $config );
		ssh2_scp_send( $con, __DIR__.'/temp/config.php', $_GLOBALS['APP']['PATH'].'/wp-config.php' , 0644 );	
		unlink (  __DIR__.'/temp/config.php' );
		
		header("Location: https://".$site['name'].".olympe.in".$_GLOBALS['APP']['PATH']."/wp-admin/install.php?step=1");
		return;
	}
	else if ($check == 'error')
	{
		$_SESSION['MESSAGE']['TYPE'] = 'error';
		$_SESSION['MESSAGE']['TEXT']= "An error has occured. Files couldn't be extracted. ";
		$template->redirect('/panel/sites/config?id='.$site['id']);
	}
	
	function random($length = 15) 
	{
			$characters = "abcdefghijklmnpqrstuvwxyABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"; 
			$charactersLength = strlen($characters);
			for ($i = 0; $i < $length; $i++) {
				$randomString .= $characters[rand(0, $charactersLength - 1)];
			}
			return $randomString;
	} 

?>
