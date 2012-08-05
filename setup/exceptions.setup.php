<?php
if(!class_exists("InvalidLoginDataException")) {
	class MySQLConnectionFailedException extends Exception {}
}
if(!class_exists("InvalidLoginDataException")) {
	class MySQLSelectDatabaseFailedException extends Exception {}
}
if(!class_exists("InvalidLoginDataException")) {
	class MySQLTableCreationFailedException extends Exception {}
}
?>