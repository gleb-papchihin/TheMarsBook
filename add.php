<!--

𝓣𝓱𝓮 𝓜𝓪𝓻𝓼 𝓑𝓸𝓸𝓴
𝓒𝓻𝓮𝓪𝓽𝓮𝓭 𝓫𝔂 𝓖𝓵𝓮𝓫 𝓟𝓪𝓹𝓬𝓱𝓲𝓴𝓱𝓲𝓷

-->


<?php
	echo header('Content-type: text/html');
	$method = $_SERVER['REQUEST_METHOD'];
	if ( $method === 'POST' )
	{	
		include 'database/connect.php';
		include 'database/add_tool.php';
		$book = new Add( $_POST, $_FILES['book'], $storage );
		$result = $book->create();
		
		if ( $result )
		{
			header('Location: /');
			exit;
		}

		$status = 'Не удалось добаить книгу';
	}
?>


<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Add book. The Mars Book.</title>
	<link rel="stylesheet" type="text/css" href="/css/add.css">
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
	<form id="add" action="/add.php" method="POST" enctype="multipart/form-data">
		<div id="add_container">
			<div id="add_title">Добаить книгу</div>
			<input required class="add_field" type="text" name="name" placeholder="Название книги" maxlength="300">
			<input required class="add_field" type="text" name="author" placeholder="Имя автора" maxlength="100">
			<input required class="add_field" type="text" name="category" placeholder="Категория" maxlength="100">
			<input type="hidden" name="MAX_FILE_SIZE" value="104857600">
			<label id="add_load" for="add_file">
				<img id="add_icon" src="/design/download.png">
				<span id="add_display">Выберите книгу</span>
			</label>
			<input required id="add_file" type="file" name="book" onchange="ChangeFilename(this)">
			<input id="button" type="submit" name="submit" value="Добаить">
		</div>
	</form>
	<script type="text/javascript" src="/js/add.js"></script>
</body>
</html>