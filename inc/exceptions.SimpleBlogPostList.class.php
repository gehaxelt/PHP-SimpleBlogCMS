<?php
if(!class_exists("NotAnIntegerException")) {
	class NotAnIntegerException extends Exception {}
}
if(!class_exists("NotAStringException")) {
	class NotAStringException extends Exception {}
}
if(!class_exists("MySQLQueryFailedException")) {
	class MySQLQueryFailedException extends Exception {}
}
if(!class_exists("MySQLFetchAssocFailedException")) {
	class MySQLFetchAssocFailedException extends Exception {}
}
?>