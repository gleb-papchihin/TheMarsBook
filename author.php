<!--

𝓣𝓱𝓮 𝓜𝓪𝓻𝓼 𝓑𝓸𝓸𝓴
𝓒𝓻𝓮𝓪𝓽𝓮𝓭 𝓫𝔂 𝓖𝓵𝓮𝓫 𝓟𝓪𝓹𝓬𝓱𝓲𝓴𝓱𝓲𝓷

-->


<?php
	$history = [
		"category" => $_GET["category"],
		"search" => $_GET["search"],
		"count" => $_GET["count"],
		"order" => $_GET["order"]
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
	include 'database/a_content.php';
	$lib = new Author($start, $step, $history, $storage);
	$content = $lib->content();

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

	$count = $_GET["count"];
	if ($count == 1)
	{
		$count = 2;
		$count_class = 'down';
	}
	elseif ($count == 2)
	{
		$count = 0;
		$count_class = 'up';
	}
	else $count = 1;
	$count = $lib->jumper('count', $count);
?>


<!DOCTYPE html>
<html>
<head>
	<title>Author. The Mars Book.</title>
	<link rel="stylesheet" type="text/css" href="/css/author.css">
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
	<form id="search" action="/author.php" method="GET">
		<div id="search_container">
			<div id="search_icon_container">
				<img id="search_icon" src="/design/magnifier.png">
				<input id="search_field" type="text" name="search" placeholder="Какого автора найти?">
				<?php
					if ( !is_null($history['category']) )
					{
						$value = $history['category'];
						echo "<input type='hidden' name='category' value='$value'>";
					}
					if ( !is_null($history['count']) )
					{
						$value = $history['count'];
						echo "<input type='hidden' name='count' value=$value>";
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
				<img class="command_icon" src="/design/book.png">
				<div class="command_title">Книги</div>
			</a>
			<a href="/author.php" class="command_card" id="command_author">
				<img class="command_icon" src="/design/active_a.png">
				<div class="command_title active_title">Авторы</div>
			</a>
			<a href="/category.php" class="command_card" id="command_category">
				<img class="command_icon" src="/design/category.png">
				<div class="command_title">Категории</div>
			</a>
		</div>
	</div>
	<div id="list">
		<div id="list_command">
			<a href="/author_add.php" id="list_add">Добавить автора</a>
		</div>
		<div id="list_tips">
			<div class="list_tip" id="list_author">Автор</div>
			<div class="list_tip" id="list_category">Категории</div>
			<a href="<?php echo $count ?>" class="list_tip <?php echo $count_class ?>" id="list_count">Число книг</a>
			<a href="<?php echo $order ?>" class="list_tip <?php echo $order_class ?>" id="list_estimate">Оценка</a>
		</div>
		<div id="list_display">
			<?php
				foreach ($content as $name => $value) 
				{
					$a_id = $value['id'];
					$count = $value['a_count_b'];
					if ($count) $rank = $value['a_rank_sum'];
					$rank = $ranks[$rank];
					$category = $value['category'];
					$c_link= "";
					foreach ($category as $value)
					{
						$link = $lib->jumper('category', $value);
						$href = "<a class='ref' href='".$link."'>".$value."</a>";
						$c_link = $c_link . $href . " ";
					}

					echo "<a href='/?author=".$name."' class='list_item'>";
					echo	"<div class='item_author'>".$name."</div>";
					echo 	"<object class='item_category'>".$c_link."</object>";
					echo	"<div class='item_count'>".$count."</div>";
					echo	"<div class='item_rank'>".$rank."</div>";
					echo	"<div class='item_tool'>";
					echo		"<object><a href='/author_change.php/?id=".$a_id."'><img class='tool' src='/design/pen.png'></a></object>";
					echo		"<object><a href='/author_delete.php/?id=".$a_id."'><img class='tool' src='/design/delete.png'></a></object>";
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