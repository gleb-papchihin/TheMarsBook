<!--

𝓣𝓱𝓮 𝓜𝓪𝓻𝓼 𝓑𝓸𝓸𝓴
𝓒𝓻𝓮𝓪𝓽𝓮𝓭 𝓫𝔂 𝓖𝓵𝓮𝓫 𝓟𝓪𝓹𝓬𝓱𝓲𝓴𝓱𝓲𝓷

-->


<?php
	$id = $_GET['id'];
	if ( is_null($id) )
	{
		header('Location: /');
		exit;
	}
?>

<?php
	
	include 'database/connect.php';
	include 'database/b_tool.php';
	
	$book = new Book($id, $storage);
	list($b_name, $b_rank, $b_path, $a_name, $c_name) = $book->content();
	$method = $_SERVER['REQUEST_METHOD'];
	if ( $method === 'POST' )
	{
		$category = $_POST['category'];
		$author = $_POST['author'];
		$file = $_FILES['book'];
		$name = $_POST['name'];

		$result = $book->change($name, $author, $category, $file);
		if ( $result )
		{
			header('Location: /');
			exit;
		}

		$status = 'Не удалось изменить запись';
	}
?>


<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Change book. The Mars Book.</title>
	<link rel="stylesheet" type="text/css" href="/css/change.css">
	<link rel="shortcut icon" href="/design/mars.ico" type="image/png">
</head>
<body>
	<div id="menu">
		<div id="menu_content">		
			<a href="/" id="performance">
				<span class="performance_text">The</span>
				<img id="icon" src="/design/mars.png">
				<span class="performance_text">Book</span>
			</a>
		</div>
	</div>
	<form id="change" action="/change.php/?id=<?php echo $id?>" method="POST" enctype="multipart/form-data">
		<div id="change_container">
			<div id="change_title">Редактировать книгу</div>
			<input class="change_field" value="<?php echo $b_name ?>" type="text" name="name" placeholder="Название книги" maxlength="300">
			<input class="change_field" value="<?php echo $a_name ?>" type="text" name="author" placeholder="Автор" maxlength="200">
			<input class="change_field" value="<?php echo $c_name ?>" type="text" name="category" placeholder="Категория" maxlength="100">
			<input type="hidden" name="MAX_FILE_SIZE" value="104857600">
			<label id="change_load" for="change_file">
				<img id="change_icon" src="/design/download.png">
				<span id="change_display">Выберите, чтобы изменить</span>
			</label>
			<input id="change_file" type="file" name="book" onchange="ChangeFilename(this)">
			<input id="button" type="submit" name="submit" value="Изменить">
		</div>
	</form>
	<script type="text/javascript" src="/js/change.js"></script>
</body>
</html>