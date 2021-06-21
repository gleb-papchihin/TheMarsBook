<!--

𝓣𝓱𝓮 𝓜𝓪𝓻𝓼 𝓑𝓸𝓸𝓴
𝓒𝓻𝓮𝓪𝓽𝓮𝓭 𝓫𝔂 𝓖𝓵𝓮𝓫 𝓟𝓪𝓹𝓬𝓱𝓲𝓴𝓱𝓲𝓷

-->


<?php
	$history = [
		"category" => $_GET["category"],
		"author" => $_GET["author"],
		"search" => $_GET["search"],
		"order" => $_GET["order"],
		"rank" => $_GET["rank"],
	];

	$ranks = [
		'Новое',
		'Плохо',
		'Хорошо',
		'Отлично'
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
	include 'database/b_content.php';
	$lib = new Library($start, $step, $history, $storage);

	$content = $lib->content();
	list($b_id, $b_name, $b_rank, $a_name, $c_name) = $content;
	$epochs = count($b_id);

	$order = $_GET["order"];
	if ($order == 1)
	{
		$order = 2;
		$order_class = 'down';
	}
	elseif ($order == 2)
	{
		$order = 0;
		$order_class = 'up';
	}
	else $order = 1;
	$order = $lib->jumper('order', $order);
?>


<!DOCTYPE html>
<html>
<head>
	<title>Welcome home. The Mars Book.</title>
	<link rel="stylesheet" type="text/css" href="/css/index.css">
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
	<form id="search" action="/" method="GET">
		<div id="search_container">
			<div id="search_icon_container">
				<img id="search_icon" src="/design/magnifier.png">
				<input id="search_field" type="text" name="search" placeholder="Какую книгу найти?">
				<?php
					if ( !is_null($history['category']) )
					{
						$value = $history['category'];
						echo "<input type='hidden' name='category' value='$value'>";
					}
					if ( !is_null($history['author']) )
					{
						$value = $history['author'];
						echo "<input type='hidden' name='author' value='$value'>";
					}
					if ( !is_null($history['rank']) )
					{
						$value = $history['rank'];
						echo "<input type='hidden' name='rank' value=$value>";
					}
					if ( !is_null($history['order']) )
					{
						$value = $history['order'];
						echo "<input type='hidden' name='order' value=$value>";	
					}
				?>
			</div>
		</div>
	</form>
	<div id="command">
		<div id="command_field">
			<a href="/" class="command_card" id="command_book">
				<img class="command_icon" src="/design/active_b.png">
				<div class="command_title active_title">Книги</div>
			</a>
			<a href="/author.php" class="command_card" id="command_author">
				<img class="command_icon" src="/design/author.png">
				<div class="command_title">Авторы</div>
			</a>
			<a href="/category.php" class="command_card" id="command_category">
				<img class="command_icon" src="/design/category.png">
				<div class="command_title">Категории</div>
			</a>
		</div>
	</div>
	<div id="list">
		<div id="list_command">
			<a href="/add.php" id="list_add">Добавить книгу</a>
		</div>
		<div id="list_tips">
			<div class="list_tip" id="list_book">Книга</div>
			<div class="list_tip" id="list_author">Автор</div>
			<div class="list_tip" id="list_category">Категория</div>
			<a href="<?php echo $order ?>" class="list_tip <?php echo $order_class ?>" id="list_estimate">Оценка</a>
		</div>
		<div id="list_display">
			<?php
				for ($i =0; $i < $epochs; $i++)
				{
					$bi = $b_id[$i];
					$bn = $b_name[$i];
					$cn = $c_name[$i];
					$an = $a_name[$i];
					$rank = $ranks[ $b_rank[$i] ];
					$bru = $lib->jumper('rank', $b_rank[$i]);
					$au = $lib->jumper('author', $a_name[$i]);
					$cu = $lib->jumper('category', $c_name[$i]);

					echo "<a href='/book.php/?id=".$bi."' class='list_item'>";
					echo	"<div class='item_book'>".$bn."</div>";
					echo 	"<object class='item_author'><a class='ref' href='".$au."'>".$an."</a></object>";
					echo	"<object class='item_category'><a class='ref' href='".$cu."'>".$cn."</a></object>";
					echo	"<object class='item_rank'><a class='ref' href='".$bru."'>".$rank."</a></object>";
					echo	"<div class='item_tool'>";
					echo		"<object><a href='/change.php/?id=".$bi."'><img class='tool' src='/design/pen.png'></a></object>";
					echo		"<object><a href='/delete.php/?id=".$bi."'><img class='tool' src='/design/delete.png'></a></object>";
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