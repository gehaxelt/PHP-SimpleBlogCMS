<?php
include_once 'exceptions.token.class.php';

/**
 * Generates and compares tokens to prevent CSRF vulnerabilities.
 * Generated token will be saved in a given session index.
 * @author gehaxelt
 * @version 1.0
 */
class Token {
	
	/**
	 * Holds the generated session token
	 * @var string $token
	 */
	private $token ='';
	
	/**
	 * Generates a new random token using microtime, rand, str_shuffle, md5 and substr to generate a random token.
	 * Sets the value to $this->token and to a session with the given $sessionIndex.
	 * @param string $sessionIndex - Index of the $_SESSION-array used to hold the new token.
	 * @throws NotAStringException
	 * @return string - $this->token 
	 */
	public function newToken($sessionIndex) {
		if(!is_string($sessionIndex))
			throw new NotAStringException('$sessionIndex is not a string');
		
		$this->token = substr(str_shuffle(md5(microtime(true).rand(0,100))),0,5);
		
		$_SESSION[$sessionIndex]=$this->token;
		
		return $this->token;
	}
	
	/**
	 * Getter method of the session token.
	 * @return string session token
	 */
	public function getToken() {
		return $this->token;
	}
	
	/**
	 * Compares the session token with another token given by $_GET or $_POST index
	 * @param string $sessionIndex - Index of $_SESSION where the session token is
	 * @param string $compareIndex - Index of $_GET or $_POST where the token is
	 * @param boolean $getMethod - Method to use to get the second token. True -> $_GET. False -> $_POST. Default value: true
	 * @throws NotAStringException
	 * @throws NotABooleanException
	 * @throws SessionIndexNotFoundException
	 * @throws GetIndexNotFoundException
	 * @throws PostIndexNotFoundException
	 * @throws TokenNotEqualException - if tokens are not equal
	 * @return boolean - if tokens are equal
	 */
	public function compareToken($sessionIndex,$compareIndex,$getMethod=true){
		
		if(!is_string($sessionIndex))
			throw new NotAStringException('$sessionIndex is not a string');
		
		if(!is_string($compareIndex))
			throw new NotAStringException('$compareIndex is not a string');
		
		if(!is_bool($getMethod))
			throw new NotABooleanException('$getMethod is not a boolean');
		
		if(!isset($_SESSION[$sessionIndex]))
			throw new SessionIndexNotFoundException('$_SESSION[$sessionIndex] is not set');
		
		if($getMethod) {
			
			if(!isset($_GET[$compareIndex]))
				throw new GetIndexNotFoundException('$_GET[$compareIndex] is not set');
		} else {
			
			if(!isset($_POST[$compareIndex]))
				throw new PostIndexNotFoundException('$_Posts[$compareIndex] is not set');
		}
		
		if($getMethod) { //$_GET[$compareIndex]
			
			if($_SESSION[$sessionIndex]!=$_GET[$compareIndex])
				throw new TokenNotEqualException('Tokens are not equal.');
			
		} else { //$_POST[$compareIndex]
			
			if($_SESSION[$sessionIndex]!=$_POST[$compareIndex])
				throw new TokenNotEqualException('Tokens are not equal.');
		}
		
		return true;
	}
	
}
?>