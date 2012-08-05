<?php
include_once 'exceptions.SimpleBlogPostList.class.php';
include_once 'SimpleBlogPost.class.php';

/**
 * SimpleBlogPostList is a class to manage blogposts in an array.
 * Requires an open database connection
 * @author gehaxelt
 * @version 1.0
 */
class SimpleBlogPostList {
	
	/**
	 * Holds the loaded blogposts.
	 * @var SimpleBlogPost
	 */
	private $blogPosts = array();
	
	private $MYSQL_TABLE_PREFIX='';
	
	private $lastpost=-1;
	
	
	
	/**
	 * Constructor of SimpleBlogPostList. 
	 * @param string $MySQLTablePrefix
	 * @throws NotAStringException
	 */
	public function __construct($MySQLTablePrefix) {
		if(!is_string($MySQLTablePrefix))
			throw new NotAStringException('$MySQLTablePrefix is not a string.');
		
		$this->MYSQL_TABLE_PREFIX=$MySQLTablePrefix;
	}
	
	/**
	 * Loads $count blogposts from the database.
	 * @param int $count - Count of the blogposts to load from the database. Default value: 0 -> load all posts.
	 */
	public function loadLastSimpleBlogPosts($count=0) {
		if(!is_int($count))
			throw new NotAnIntegerException('$count is not an integer.');
		
		unset($blogPosts); //clear the current list
		
		if($count!=0){
			//load $count posts.
			$SQLquery = mysql_query("SELECT id from ".$this->MYSQL_TABLE_PREFIX."posts order by id desc limit ".$count);
		} else {
			//load all posts
			$SQLquery = mysql_query("SELECT id from ".$this->MYSQL_TABLE_PREFIX."posts order by id desc");
		}
		
		if(!$SQLquery)
			throw new MySQLQueryFailedException('Could not select last blogpost ids');
		
		$iterator=0;
		while($SQLResource = mysql_fetch_object($SQLquery)) {
			$this->blogPosts[$iterator] = new SimpleBlogPost($this->MYSQL_TABLE_PREFIX,(int) $SQLResource->id);
			$iterator++;
		}
		
		$this->lastpost=-1;
	}
	
	/**
	 * Returns the next SimpleBlogPost object in the list.
	 * @throws NoMorePostsException
	 * @return SimpleBlogPost object
	 */
	public function getNextSimpleBlogPost() {
		
		$this->lastpost++;
		if(!isset($this->blogPosts[$this->lastpost])) 
			throw new NoMorePostsException('No more posts to return.');
		
		return $this->blogPosts[$this->lastpost];

	}
	
	/**
	 * Checks whether there is a next SimpleBlogPost object in the list.
	 * @return boolean true if next SimpleBlogPost object exists
	 * @return boolean false if no next SimpleBlogPost object exists.
	 */
	public function isNextSimpleBlogPost() {
	
		if(!isset($this->blogPosts[($this->lastpost+1)])) //check if next post exists
			return false;
	
		return true;
	
	}
	
}
?>