<html>
<?php
include_once 'inc/conf.php';
include_once 'inc/SimpleBlogPostList.class.php';

$MYSQL_CONNECTION = mysql_connect($MYSQL_HOST,$MYSQL_USER,$MYSQL_PASSWORD);
mysql_select_db($MYSQL_DATABASE);

$BlogList = new SimpleBlogPostList($MYSQL_TABLE_PREFIX);
$BlogList->loadLastSimpleBlogPosts($BLOG_INDEX_SHOW_COUNT);

?>
	<head>
		<title><?php echo htmlentities($BLOG_TITLE); ?></title>
	</head>
	<body>
		<h1>Welcome to <?php echo htmlentities($BLOG_TITLE);?></h1><br />
		
		<?php 	
		//List all postings	
		while($BlogList->isNextSimpleBlogPost())
		{
			$tempSimpleBlogPost = $BlogList->getNextSimpleBlogPost();
			echo htmlentities($tempSimpleBlogPost->getPostDate())." - ";
			echo "<b>".htmlentities($tempSimpleBlogPost->getPostTitle())."</b><br />";
			echo "<i>".htmlentities($tempSimpleBlogPost->getExcerpt())."<a href='".htmlentities($tempSimpleBlogPost->getPostId())."-Post-".htmlentities($tempSimpleBlogPost->getPostTitle())."'>... read more ...</a></i><br /><br />";			
		}
		
		unset($BlogList);
		unset($tempSimpleBlogPost);
		
		?>
	</body>
</html>