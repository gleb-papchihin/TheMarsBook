<!--

𝓣𝓱𝓮 𝓜𝓪𝓻𝓼 𝓑𝓸𝓸𝓴
𝓒𝓻𝓮𝓪𝓽𝓮𝓭 𝓫𝔂 𝓖𝓵𝓮𝓫 𝓟𝓪𝓹𝓬𝓱𝓲𝓴𝓱𝓲𝓷

-->


<?php
	$history = [
		"search" => $_GET["search"],
		"author" => $_GET["author"],
		"book" => $_GET["book"]
	];
?>

<?php
	$step = 30;
	$start = $_GET['start'];
	$stop = $start + $step;
	if ( is_null($start) ) $start = 0;
?>

<?php
	include 'database/connect.php';
	include 'database/c_content.php';
	$lib = new Category($start, $step, $history, $storage);
	$content = $lib->content();
	list($c_id, $c_name, $c_count_a, $c_count_b) = $content;

	$author = $_GET["author"];
	if ($author == 1)
	{
		$author = 2;
		$author_class = 'down';
	}
	elseif ($author == 2)
	{
		$author = 0;
		$author_class = 'up';
	}
	else $author = 1;
	$author = $lib->jumper('author', $author);

	$book = $_GET["book"];
	if ($book == 1)
	{
		$book = 2;
		$book_class = 'down';
	}
	elseif ($book == 2)
	{
		$book = 0;
		$book_class = 'up';
	}
	else $book = 1;
	$book = $lib->jumper('book', $book);
?>


<!DOCTYPE html>
<html>
<head>
	<title>Category. The Mars Book.</title>
	<link rel="stylesheet" type="text/css" href="/css/category.css">
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
	<form id="search" action="/category.php" method="GET">
		<div id="search_container">
			<div id="search_icon_container">
				<img id="search_icon" src="/design/magnifier.png">
				<input id="search_field" type="text" name="search" placeholder="Какую категорию найти?">
				<?php
					if ( !is_null($history['book']) )
					{
						$value = $history['book'];
						echo "<input type='hidden' name='book' value=$value>";
					}
					if ( !is_null($history['author']) )
					{
						$value = $history['author'];
						echo "<input type='hidden' name='author' value=$value>";	
					}

				?>
			</div>
		</div>
	</form>
	<div id="command">
		<div id="command_field">
			<a href="/" class="command_card" id="command_book">
				<img class="command_icon" src="/design/book.png">
				<div class="command_title">Книги</div>
			</a>
			<a href="/author.php" class="command_card" id="command_author">
				<img class="command_icon" src="/design/author.png">
				<div class="command_title">Авторы</div>
			</a>
			<a href="/category.php" class="command_card" id="command_category">
				<img class="command_icon" src="/design/active_c.png">
				<div class="command_title active_title">Категории</div>
			</a>
		</div>
	</div>
	<div id="list">
		<div id="list_command">
			<a href="/category_add.php" id="list_add">Добавить категорию</a>
		</div>
		<div id="list_tips">
			<div class="list_tip" id="list_category">Категория</div>
			<a href="<?php echo $book ?>" class="list_tip <?php echo $book_class ?>" id="list_book">Число книг</a>
			<a href="<?php echo $author ?>" class="list_tip <?php echo $author_class ?>" id="list_author">Число авторов</a>
		</div>
		<div id="list_display">
			<?php

				$epochs = count($c_name);
				for ($i = 0; $i < $epochs; $i++)
				{
					$ci = $c_id[$i];
					$cn = $c_name[$i];
					$cb = $c_count_b[$i];
					$ca = $c_count_a[$i];

					echo "<a href='/?category=".$cn."' class='list_item'>";
					echo	"<div class='item_category'>".$cn."</div>";
					echo 	"<div class='item_book'>".$cb."</div>";
					echo	"<div class='item_author'>".$ca."</div>";
					echo	"<div class='item_tool'>";
					echo		"<object><a href='/category_change.php/?id=".$ci."'><img class='tool' src='/design/pen.png'></a></object>";
					echo		"<object><a href='/category_delete.php/?id=".$ci."'><img class='tool' src='/design/delete.png'></a></object>";
					echo	"</div>";
					echo "</a>";

				}

			?>
		</div>
		<div id="list_control">
			<?php
				$current = intdiv($start, $step);
				if ($current > 0) $prev = true;
				else $prev = false;

				$residual = $lib->residual();
				$residual = $residual / $step;
				if ($residual > 0) $next = true;
				else $next = false;

				if ( $next ) 
				{
					$next_url = $lib->create_get($history);
					$next_url = $next_url . "start=" . ($start + $step);
					$next_url = "href='$next_url'";
				}
				else $next_url = null;

				if ( $prev ) 
				{
					$prev_url = $lib->create_get($history);
					$prev_url = $prev_url . "start=" . ($start - $step);
					$prev_url = "href='$prev_url'";
				}
				else $prev_url = null;
			?>
			<a <?php echo $prev_url ?>  class="list_button" id="prev"><img class="list_icon" src="/design/prev.png"></a>
			<a <?php echo $next_url ?> class="list_button" id="next"><img class="list_icon" src="/design/next.png"></a>
		</div>
	</div>
</body>
</html>