<?php
include_once 'exceptions.SimpleBlogPost.class.php';

/**
 * Post class magaging post of the simpleblog.
 * Requires open database connection and simpleblog table structur.
 * @author gehaxelt
 * @version 1.0
 */
class SimpleBlogPost {
	
	private $post_id=0;
	private $post_title='';
	private $post_content='';
	private $post_time=0;
	private $MYSQL_TABLE_PREFIX='';
	
	/**
	 * Constructor setting the post time.
	 * @param string $MysqlTablePrefix - Table prefix defined in the conf - file.
	 * @param int postId - Loads the data from the database. Default value: 0 -> loads no data.
	 */
	public function __construct($MysqlTablePrefix,$postId = 0) {
		if(!is_string($MysqlTablePrefix))
			throw new NotAStringException('$MysqlTablePrefix is not a string.');
		
		if(!is_int($postId))
			throw new NotAnIntegerException('$postid is not an integer.');
		
		$this->MYSQL_TABLE_PREFIX = $MysqlTablePrefix;
		
		$this->post_time=time();
		if($postId!=0)
			$this->loadPost($postId);
	}
	
	/**
	 * Getter method. Returns the id number of the post.
	 * @return int post id.
	 */
	public function getPostId() {
		return $this->post_id;
	}
	
	/**
	 * Setter method of the post id.
	 * @param int $postId
	 * @throws NotAnIntegerException
	 */
	public function setPostId($postId) {
		if(!is_int($postId))
			throw new NotAnIntegerException('$postId is not an integer.');
		$this->post_id = $postId;
	}
	
	/**
	 * Getter mehtod. Returns the title of the post.
	 * @return string post title.
	 */
	public function getPostTitle() {
		return $this->post_title;
	}
	
	/**
	 * Setter method of the post title.
	 * @param string $postTitle
	 * @throws NotAStringException
	 */
	public function setPostTitle($postTitle) {
		if(!is_string($postTitle)) 
			throw new NotAStringException('$postTitle is not a string.');
		$this->post_title=$postTitle;
	}
	
	
	/**
	 * Getter method. Returns the content of the post.
	 * @return string post content.
	 */
	public function getPostContent() {
		return $this->post_content;
	}
	
	/**
	 * Setter method of the post content.
	 * @param string $postContent
	 * @throws NotAStringException
	 */
	public function setPostContent($postContent) {
		if(!is_string($postContent))
			throw new NotAStringException('$postContent is not a string.');
		$this->post_content=$postContent;
	}
	
	/**
	 * Returns the date of the post.
	 * @return string post date dd.mm.yyyy
	 */
	public function getPostDate() {
		return date("d.m.Y",$this->post_time);
	}
	
	/**
	 * Loads the data of a post from the database
	 * @param int $postId
	 * @throws NotAnIntegerException
	 * @throws MySQLQueryFailedException
	 * @throws MySQLFetchObjectFailedException
	 */
	public function loadPost($postId) {
		
		if(!is_int($postId))
			throw new NotAnIntegerException('$postId is not an integer');
		
		$SQLquery = mysql_query("SELECT id,title,content,time from ".$this->MYSQL_TABLE_PREFIX."posts where id =".$postId." limit 1");
		
		if(!$SQLquery)
			throw new MySQLQueryFailedException('Could not load post from database');
		
		$SQLResource = mysql_fetch_object($SQLquery);
		
		if(!$SQLResource)
			throw new MySQLFetchObjectFailedException('Could not fetch the object');
		
		$this->post_id= $SQLResource->id;
		$this->post_title= $SQLResource->title;
		$this->post_content= $SQLResource->content;
		$this->post_time= $SQLResource->time;
	}
	
	/**
	 * Saves / updates a post.
	 * If the post id equals zero, new entry will be created. Otherwise an update will be done.
	 * @throws MySQLQueryFailedException
	 */
	public function savePost() {
		
		if($this->post_id==0) {//process with insert

			$SQLquery=mysql_query("INSERT INTO ".$this->MYSQL_TABLE_PREFIX."posts (title,content,time) VALUES ('".mysql_real_escape_string($this->post_title)."','".mysql_real_escape_string($this->post_content)."',".$this->post_time.")");
		
			if(!$SQLquery)
				throw new MySQLQueryFailedException('Could not insert new post');
			
			$this->post_id=mysql_insert_id();
		
		} else { //process with update
			
			$SQLquery=mysql_query("UPDATE ".$this->MYSQL_TABLE_PREFIX."posts  SET title ='".mysql_real_escape_string($this->post_title)."', content = '".mysql_real_escape_string($this->post_content)."', time=".$this->post_time."");
			
			if(!$SQLquery)
				throw new MySQLQueryFailedException('Could not update new post');
			
		}
		
			$this->writeToFile();
	}
	
	/**
	 * Delets a SimpleBlogPost from the database.
	 * @throws UnsavedPostNotDeletableException
	 * @throws MySQLQueryFailedException
	 */
	public function deletePost() {
		if($this->post_id==0)
			throw new UnsavedPostNotDeletableException('You cannot delete an unsaved post.');
		
		$SQLquery=mysql_query("DELETE from ".$this->MYSQL_TABLE_PREFIX."posts where id =".$this->post_id);
		if(!$SQLquery)
			throw new MySQLQueryFailedException('Could not delete blogpost.');
		
		if(!file_exists('../posts/'.$this->post_id))
			throw new DirNotExistsException('Directory'.'../posts/'.$this->post_id.' does not exist and could not be removed.');
		
		unlink('../posts/'.$this->post_id.'/index.html');
		rmdir('../posts/'.$this->post_id);
	}
	
	/**
	 * Returns an excerpt of the post content as a preview.
	 * @throws NoPostContentSetException
	 * @return string - Excerpt of the content. 15 words long.
	 */
	public function getExcerpt() {
		if($this->post_content=='')
			throw new NoPostContentSetException('No content of which an excerpt could be created.');
		
		$content_split=explode(" ",$this->post_content); //Split content into seperate words
		$excerpt='';
		
		for ($i=0;$i<15;$i++)
			if(isset($content_split[$i]))
				$excerpt.=$content_split[$i]." ";
		
		return $excerpt;
	}
	
	/**
	 * Creates the directory and index.html file for the blogpost
	 * @throws DirCreationErrorException
	 * @throws CouldNotGetTemplateContentException
	 * @throws CouldNotWriteIndexFileException
	 */
	private function writeToFile() {
		if(!file_exists('../posts/'.$this->post_id)) //Create new directory if it does not already exist.
			if(!mkdir('../posts/'.$this->post_id))
				throw new DirCreationErrorException('Could not create new dir for post '.$this->post_id);
		
		if(!$template=@file_get_contents('../css/post.tpl'))
			throw new CouldNotGetTemplateContentException('Could not get the content of the template.');
		
		//Replace the keywords of the template
		$template=preg_replace('/%%PAGETITLE%%/', $this->getPostTitle(), $template);
		$template=preg_replace('/%%PAGEDATE%%/', $this->getPostDate(), $template);
		$template=preg_replace('/%%PAGECONTENT%%/', $this->getPostContent(), $template);
		
		if(!file_put_contents('../posts/'.$this->post_id.'/index.html', $template))
			throw new CouldNotWriteIndexFileException('Could not write post conten to its index.html');
	}
	
	
}
?>