<!--

𝓣𝓱𝓮 𝓜𝓪𝓻𝓼 𝓑𝓸𝓸𝓴
𝓒𝓻𝓮𝓪𝓽𝓮𝓭 𝓫𝔂 𝓖𝓵𝓮𝓫 𝓟𝓪𝓹𝓬𝓱𝓲𝓴𝓱𝓲𝓷

-->


<?php
	$id = $_GET['id'];
	if ( is_null($id) )
	{
		header('Location: /category.php');
		exit;
	}
	include 'database/connect.php';
	include 'database/c_tool.php';
	$category = new Category($id, $storage);
	$category->delete();
	header('Location: /category.php');
	exit;
?>