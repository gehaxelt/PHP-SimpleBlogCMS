<?php
include_once '../inc/conf.php';
include_once 'exceptions.setup.php';

try {
	
	if(!mysql_connect($MYSQL_HOST,$MYSQL_USER,$MYSQL_PASSWORD))
		throw new MySQLConnectionFailedException('Could not connect to MySQL server. Please check the inc/conf.php file.');
	
	echo 'Connection to MySQL server established successfully.'."<br>";
			
	if(!mysql_select_db($MYSQL_DATABASE))
		throw new MySQLSelectDatabaseFailedException('Could not select database '.$MYSQL_DATABASE.'.');
	
	echo 'Selected database successfully.'."<br>";
	
	if(!mysql_query("CREATE TABLE `".$MYSQL_DATABASE."`.`".$MYSQL_TABLE_PREFIX."posts` (`id` int(11) unsigned not null AUTO_INCREMENT PRIMARY KEY, `title` VARCHAR(255) NOT NULL , `content` TEXT NOT NULL , `time` INT(20) NOT NULL ) ENGINE=MyISAM, COLLATE utf8_general_ci;"))
		throw new MySQLTableCreationFailedException('Could not create tables.');
		
	echo 'Created tables successfully.'."<br>";
	
	echo 'Setup finished successfully'."<br>";
	
	
} catch (MySQLConnectionFailedException $err) {
	echo $err->getMessage();
	die();
} catch (MySQLSelectDatabaseFailedException $err) {
	echo $err->getMessage();
	die();
} catch (MySQLTableCreationFailedException $err) {
	echo $err->getMessage();
}
?>