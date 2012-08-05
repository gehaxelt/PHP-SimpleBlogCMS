<?php
if(!class_exists("SessionNotEnabledException")) {
	class SessionNotEnabledException extends Exception {}
}
if(!class_exists("NotAStringException")) {
	class NotAStringException extends Exception {}
}
if(!class_exists("NotABooleanException")) {
	class NotABooleanException extends Exception {}
}
if(!class_exists("TokenNotEqualException")) {
	class TokenNotEqualException extends Exception {}
}
if(!class_exists("SessionIndexNotFoundException")) {
	class SessionIndexNotFoundException extends Exception {}
}
if(!class_exists("GetIndexNotFoundException")) {
	class GetIndexNotFoundException extends Exception {}
}
if(!class_exists("PostIndexNotFoundException")) {
	class PostIndexNotFoundException extends Exception {}
}
?>