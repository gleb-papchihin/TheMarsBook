<!--

๐ฃ๐ฑ๐ฎ ๐๐ช๐ป๐ผ ๐๐ธ๐ธ๐ด
๐๐ป๐ฎ๐ช๐ฝ๐ฎ๐ญ ๐ซ๐ ๐๐ต๐ฎ๐ซ ๐๐ช๐น๐ฌ๐ฑ๐ฒ๐ด๐ฑ๐ฒ๐ท

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

		$status = 'ะะต ัะดะฐะปะพัั ะดะพะฑะฐะธัั ะฐะฒัะพัะฐ';
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
			<div id="add_title">ะะพะฑะฐะธัั ะฐะฒัะพัะฐ</div>
			<input required class="add_field" type="text" name="author" placeholder="ะะฒัะพั" maxlength="200">
			<input id="button" type="submit" name="submit" value="ะะพะฑะฐะธัั">
		</div>
	</form>
</body>
</html>