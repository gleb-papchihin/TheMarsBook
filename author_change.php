<!--

𝓣𝓱𝓮 𝓜𝓪𝓻𝓼 𝓑𝓸𝓸𝓴
𝓒𝓻𝓮𝓪𝓽𝓮𝓭 𝓫𝔂 𝓖𝓵𝓮𝓫 𝓟𝓪𝓹𝓬𝓱𝓲𝓴𝓱𝓲𝓷

-->


<?php
	$id = $_GET['id'];
	if ( is_null($id) )
	{
		header('Location: /author.php');
		exit;
	}
?>

<?php
	
	include 'database/connect.php';
	include 'database/a_tool.php';
	
	$author = new Author($id, $storage);
	list($a_name, $a_rank, $a_count, $a_new) = $author->content();
	$method = $_SERVER['REQUEST_METHOD'];
	if ( $method === 'POST' )
	{
		$name = $_POST['author'];
		$result = $author->change($name);
		if ( $result )
		{
			header('Location: /author.php');
			exit;
		}

		$status = 'Не удалось изменить запись';
	}
?>


<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Change author. The Mars Book.</title>
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
	<form id="change" action="/author_change.php/?id=<?php echo $id?>" method="POST" enctype="multipart/form-data">
		<div id="change_container">
			<div id="change_title">Редактировать автора</div>
			<input class="change_field" value="<?php echo $a_name ?>" type="text" name="author" placeholder="Категория" maxlength="200">
			<input id="button" type="submit" name="submit" value="Изменить">
		</div>
	</form>
</body>
</html>