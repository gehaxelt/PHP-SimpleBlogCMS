<?php session_start(); ?>

<?php
include_once '../inc/conf.php';
include_once 'exceptions.admin.php';
include_once '../inc/token.class.php';
include_once '../inc/SimpleBlogPost.class.php';
include_once '../inc/SimpleBlogPostList.class.php';

$PAGE_ACTION='';
$tokenizer = new Token();

if(!isset($_SESSION['MAX_AUTH_TRIES']))
	$_SESSION['MAX_AUTH_TRIES']= $BLOG_MAX_AUTH_TRIES;

if(!isset($_SESSION['MAX_AUTH_TIMEOUT']))
	$_SESSION['MAX_AUTH_TIMEOUT']=time();

$MYSQL_CONNECTION = mysql_connect($MYSQL_HOST,$MYSQL_USER,$MYSQL_PASSWORD);
mysql_select_db($MYSQL_DATABASE);



?>

<html>
	<head>
		<title><?php echo 'Admin - '.$BLOG_TITLE;?></title>
	</head>
	<body>
		<?php 
			if(isset($_GET['action']))
				$PAGE_ACTION=$_GET['action'];

			
			if(isset($_SESSION['admin_user']) && $_SESSION['admin_user'] == $BLOG_USER && $_SESSION['admin_ip']==$_SERVER['REMOTE_ADDR']) //is admin
			{
				
				switch($PAGE_ACTION) {
					
					case 'mainMenu': //Main menu
						$tokenizer->newToken("admintoken");
						
						echo "<a href='index.php?action=showPosts&token=".$tokenizer->getToken()."'>Show postings</a>"."<br>";
						echo "<a href='index.php?action=newPost&token=".$tokenizer->getToken()."'>New post</a>"."<br>";
						echo "<a href='index.php?action=logout&token=".$tokenizer->getToken()."'>Logout</a>"."<br>";
						break;
						
					case 'newPost': //create new post
						
						try {
							$tokenizer->compareToken("admintoken","token");
							$tokenizer->newToken("admintoken");
							
						} catch (Exception $err) {
								echo $err->getMessage();
								die();
						}
						
						if(!(isset($_POST['post_title'],$_POST['post_content']))) { //Not all fields filled... show form again
							
							//Create post form
							echo " 
									<form action='index.php?action=newPost&token=".$tokenizer->getToken()."' method='post'>
									<label for='post_title'>Title</label><input type='text' name='post_title' value='";
									
									if(isset($_POST['post_title']))  //Title value
										echo htmlentities($_POST['post_title']);
									
									echo "' /><br>
									<label for='post_content'>Content</label><textarea name='post_content'>"; 
									if(isset($_POST['post_content'])) //textarea content
										echo htmlentities($_POST['post_content']);
									
									echo "</textarea><br>
									<input type='submit' name='savepost' value='Save' />
									</form>
								";
									
						} else { //all fields filled ... save posting.
							
							try {
								//Create SimpleBlogPost object and save it.
								$post = new SimpleBlogPost($MYSQL_TABLE_PREFIX); //new Post object
								
								$post->setPostTitle($_POST['post_title']);
								$post->setPostContent($_POST['post_content']);
								$post->savePost();
								
								unset($post);
							
								echo "Post saved successfully."."<br>";
								echo "<a href='index.php?action=mainMenu'>Main menu</a>";
								
							} catch(Exception $err) {
								echo $err->getMessage();
								die();
							}
						}
						
						break;
					
					case 'editPost':

						try {
							$tokenizer->compareToken("admintoken","token");
							$tokenizer->newToken("admintoken");
							
							if(!isset($_GET['postId'])) //postId set?
								throw new GetIndexNotFoundException('$_GET["postId"] not set');
							
							$loadSimpleBlogPost = new SimpleBlogPost($MYSQL_TABLE_PREFIX,(int)$_GET['postId']);
							
							
						} catch (Exception $err) {
							echo $err->getMessage();
							die();
						}
						
						if(!(isset($_POST['post_title'],$_POST['post_content']))) { //Not all fields filled... show form again
								
							//Create post form
							echo "
									<form action='index.php?action=editPost&token=".$tokenizer->getToken()."&postId=".$loadSimpleBlogPost->getPostId()."' method='post'>
									<label for='post_title'>Title</label><input type='text' name='post_title' value='";
								
							echo htmlentities($loadSimpleBlogPost->getPostTitle());
								
							echo "' /><br>
									<label for='post_content'>Content</label><textarea name='post_content'>";
							
							echo htmlentities($loadSimpleBlogPost->getPostContent());
								
							echo "</textarea><br>
									<input type='submit' name='savepost' value='Save' />
									</form>
								";
								
						} else { //all fields filled ... save posting.
								
							try {
								
								//Create SimpleBlogPost object and save it.
								$post = new SimpleBlogPost($MYSQL_TABLE_PREFIX,(int)$_GET['postId']); //new Post object
						
								$post->setPostTitle($_POST['post_title']);
								$post->setPostContent($_POST['post_content']);
								$post->savePost();
						
								unset($post);
									
								echo "Post saved successfully."."<br>";
								echo "<a href='index.php?action=mainMenu'>Main menu</a>";
						
							} catch(Exception $err) {
								echo $err->getMessage();
								die();
							}
						}
						
						break;
						
					case 'showPosts': //List all posts and edit/delete buttons
						
						try {
							
							$tokenizer->compareToken("admintoken","token");
							$tokenizer->newToken("admintoken");
							
						} catch(Exception $err) {
							echo $err->getMessage();
							die();
						}
						
						//Creates a SimpleBlogPostList and loads all posts.
						$BlogList = new SimpleBlogPostList($MYSQL_TABLE_PREFIX);
						$BlogList->loadLastSimpleBlogPosts(5);
						
						//Iterates all SimpleBlogPosts and lists the title.
						//Additionally gives some options.
						while($BlogList->isNextSimpleBlogPost())
						{
							$tempSimpleBlogPost = $BlogList->getNextSimpleBlogPost();
							echo htmlentities($tempSimpleBlogPost->getPostTitle());
							echo " -> <a href='index.php?action=deletePost&token=".$tokenizer->getToken()."&postId=".$tempSimpleBlogPost->getPostId()."'>Delete post</a>, ";
							echo "  <a href='index.php?action=editPost&token=".$tokenizer->getToken()."&postId=".$tempSimpleBlogPost->getPostId()."'>Edit post</a><br>";
							
						} 
						
						unset($BlogList);
						unset($tempSimpleBlogPost);
						
						echo "<a href='index.php?action=showPosts&token=".$tokenizer->getToken()."'>Reload</a>"."<br>";
						echo "<a href='index.php?action=mainMenu'>Main menu</a>";
						
						break;
						
					case 'deletePost':
						try {
								
							$tokenizer->compareToken("admintoken","token");
							$tokenizer->newToken("admintoken");
							
							if(!isset($_GET['postId'])) //no Id set -> throw exception.
								throw new GetIndexNotFoundException('No postId set.');
							
							//Delete post.
							$DeleteSimpleBlogPost = new SimpleBlogPost($MYSQL_TABLE_PREFIX,(int)$_GET['postId']);
							$DeleteSimpleBlogPost->deletePost();
							
							unset($DeleteSimpleBlogPost);
							
						} catch(Exception $err) {
							echo $err->getMessage();
							die();
						}
						
						echo "Deleted post successfully."."<br>";
						echo "<a href='index.php?action=showPosts&token=".$tokenizer->getToken()."'>Show postings</a>"."<br>";
						echo "<a href='index.php?action=mainMenu'>Main menu</a>";
						
						break;
						
					case 'logout':
						try {
							
							$tokenizer->compareToken("admintoken", "token");
							$tokenizer->newToken("admintoken");
							
							unset($_SESSION['admin_user']);
							unset($_SESSION['admin_ip']);
							unset($_SESSION['MAX_AUTH_TRIES']);
							unset($_SESSION['MAX_AUTH_TIMEOUT']);
							
							session_destroy();
							
							echo "Logout successfull.";
							
						} catch(Exception $err) {
							echo $err->getMessage();
							die();
						}
						
						break;
				}
				
			} else { //No admin session
				
				if($PAGE_ACTION!='login') //if action != login, show login form
				{
					if($_SESSION['MAX_AUTH_TIMEOUT']<=time() && isset($_SESSION['MAX_AUTH_TIMEOUT'])) {
						//wait for auth timeout and reset values.
						unset($_SESSION['MAX_AUTH_TIMEOUT']);
						$_SESSION['MAX_AUTH_TRIES']=$BLOG_MAX_AUTH_TRIES;
						
					}
					
					if($_SESSION['MAX_AUTH_TRIES'] != 0) //Show login form only, if login attempts are avaiable.
					 {
					 	//print login form
						echo "
						<span>".$_SESSION['MAX_AUTH_TRIES']." login attempts left.</span><br> 
						<form action='index.php?action=login&token=".$tokenizer->newToken("admintoken")."' method='post' />
							<label for='username'>Username</label><input type='text' name='username' /><br>
							<label for='password'>Password</label><input type='password' name='password' /><br>
							<input type='submit' name='Login' value='Login'/>
						</form>
						";
					} else { 
						
						if(!isset($_SESSION['MAX_AUTH_TIMEOUT'])) {
							//Set login timeout
							$_SESSION['MAX_AUTH_TIMEOUT']=time()+$BLOG_MAX_AUTH_TIMEOUT;
							
						} else {
							//Display error message.
							echo 'No more login attemps left.'."<br />";
							echo 'Timeout will end at: '.date("H:i:s",$_SESSION['MAX_AUTH_TIMEOUT']);
						}
					}
				} else if ($PAGE_ACTION=='login') { //Process login
					try {
						//No need to check for auth timeout, because login token will be invalid after 1 try.
						
						$tokenizer->compareToken("admintoken", "token");
						$tokenizer->newToken("admintoken");
						
						if(!isset($_POST['username']))
							throw new PostIndexNotFoundException('Username was not set.');
						
						if(!isset($_POST['password']))
							throw new PostIndexNotFoundException('Password was not set.');
						
						if(!($BLOG_USER==$_POST['username'] && $BLOG_USER_PASSWORD==$_POST['password']))
							throw new InvalidLoginDataException('Wrong username or password given. Login failed.');
						
						//save user data
						$_SESSION['admin_ip']=$_SERVER['REMOTE_ADDR'];
						$_SESSION['admin_user']=$_POST['username'];
						
						//Resett auth sessions.
						unset($_SESSION['MAX_AUTH_TRIES']); 
						unset($_SESSION['MAX_AUTH_TIMEOUT']);
						
						echo "Login successfull."."<br>";
						echo "<a href='index.php?action=mainMenu'>Main menu</a>";
						
					} catch(InvalidLoginDataException $err) { //Logindata was wrong
						
						echo $err->getMessage();
						$_SESSION['MAX_AUTH_TRIES']--;
						echo "<br>Please try again. <a href='index.php'>Back to LoginPage.</a>";
						
					} catch (Exception $err) { //Other exceptions.
	
						echo $err->getMessage();
						echo "<br>Please try again. <a href='index.php'>Back to LoginPage.</a>";
						
					} 
				}
			}
			
		?>
	</body>
</html>