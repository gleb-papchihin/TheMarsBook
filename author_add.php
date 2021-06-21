<!--

𝓣𝓱𝓮 𝓜𝓪𝓻𝓼 𝓑𝓸𝓸𝓴
𝓒𝓻𝓮𝓪𝓽𝓮𝓭 𝓫𝔂 𝓖𝓵𝓮𝓫 𝓟𝓪𝓹𝓬𝓱𝓲𝓴𝓱𝓲𝓷

-->


<?php

	$method = $_SERVER['REQUEST_METHOD'];
	if ( $method === 'POST' )
	{	
		include 'database/connect.php';
		include 'database/add_tool.php';
		$author = new Add( $_POST, $_FILES['book'], $storage );
		$result = $author->author_create();
		
		if ( $result )
		{
			header('Location: /author.php');
			exit;
		}

		$status = 'Не удалось добаить автора';
	}
?>


<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Add author. The Mars Book.</title>
	<link rel="stylesheet" type="text/css" href="/css/author_add.css">
	<link rel="shortcut icon" href="/design/mars.ico" type="image/png">
</head>
<body>
	<div class="seed" id="menu">
		<div id="menu_content">		
			<a href="/" id="performance">
				<span class="performance_text">The</span>
				<img id="icon" src="/design/mars.png">
				<span class="performance_text">Book</span>
			</a>
		</div>
	</div>
	<form id="add" action="/author_add.php" method="POST">
		<div id="add_container">
			<div id="add_title">Добаить автора</div>
			<input required class="add_field" type="text" name="author" placeholder="Автор" maxlength="200">
			<input id="button" type="submit" name="submit" value="Добаить">
		</div>
	</form>
</body>
</html>