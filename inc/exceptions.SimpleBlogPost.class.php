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
if(!class_exists("MySQLFetchObjectFailedException")) {
	class MySQLFetchObjectFailedException extends Exception {}
}
if(!class_exists("UnsavedPostNotDeletableException")) {
	class UnsavedPostNotDeletableException extends Exception {}
}
if(!class_exists("NoPostContextSetException")) {
	class NoPostContentSetException extends Exception {}
}
if(!class_exists("DirCreationErrorException")) {
	class DirCreationErrorException extends Exception {}
}
if(!class_exists("CouldNotGetTemplateContentException")) {
	class CouldNotGetTemplateContentException extends Exception {}
}
if(!class_exists("CouldNotWriteIndexFileException")) {
	class CouldNotWriteIndexFileException extends Exception {}
}
if(!class_exists("DirNotExistsException")) {
	class DirNotExistsException extends Exception {}
}
?>