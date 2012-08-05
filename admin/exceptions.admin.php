<?php
if(!class_exists("InValidAdminSessionException")) {
	class InValidAdminSessionException extends Exception {}
}
if(!class_exists("AdminSessionNotStartedException")) {
	class AdminSessionNotStartedException extends Exception {}
}
if(!class_exists("PostIndexNotFoundException")) {
	class PostIndexNotFoundException extends Exception {}
}
if(!class_exists("InvalidLoginDataException")) {
	class InvalidLoginDataException extends Exception {}
}
?>