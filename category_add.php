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
		$category = new Add( $_POST, $_FILES['book'], $storage );
		$result = $category->category_create();
		
		if ( $result )
		{
			header('Location: /category.php');
			exit;
		}

		$status = 'Не удалось добаить категорию';
	}
?>


<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Add category. The Mars Book.</title>
	<link rel="stylesheet" type="text/css" href="/css/category_add.css">
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
	<form id="add" action="/category_add.php" method="POST">
		<div id="add_container">
			<div id="add_title">Добаить категорию</div>
			<input required class="add_field" type="text" name="category" placeholder="Категория" maxlength="100">
			<input id="button" type="submit" name="submit" value="Добаить">
		</div>
	</form>
</body>
</html>