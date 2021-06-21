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
	$rank = $_GET['rank'];
	if ( !is_null($rank) ) $book->change_rank( $rank );

	list($b_name, $b_rank, $b_path, $a_name, $c_name) = $book->content();

	if ($b_rank == 1)
	{
		$bad = 'background: #BBF18E';
	}
	elseif ($b_rank == 2) 
	{
		$bad = 'background: #BBF18E'; 
		$middle = 'background: #BBF18E'; 
	}
	elseif ($b_rank == 3) 
	{
		$bad = 'background: #BBF18E'; 
		$middle = 'background: #BBF18E'; 
		$perfect = 'background: #BBF18E'; 
	}

?>


<!DOCTYPE html>
<html>
<head>
	<title>Book. The Mars Book.</title>
	<link rel="stylesheet" type="text/css" href="/css/book.css">
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
	<div id="book">
		<div id="book_container">
			<div id="book_name"><?php echo $b_name;?></div>
			<a href="/?author=<?php echo $a_name ?>" id="book_author">By <?php echo $a_name;?></a>
			<div id="book_control">
				<a href="/books/<?php echo $b_path; ?>" id="book_download">Скачать</a>
				<form method="GET" name="estimate" id="book_rank" onchange="Change()">
					<label for="rank_bad">
						<div class="round" id="bad" onclick="Bad()" style="<?php echo $bad;?>"></div>
					</label>
					<input class="rank_item" id="rank_bad" type="radio" name="rank" value="1">
					<label for="rank_middle">
						<div class="round" id="middle" onclick="Middle()" style="<?php echo $middle; ?>"></div>
					</label>
					<input class="rank_item" id="rank_middle" type="radio" name="rank" value="2">
					<label for="rank_perfect">
						<div class="round" id="perfect" onclick="Perfect()" style="<?php echo $perfect; ?>"></div>
					</label>
					<input class="rank_item" id="rank_perfect" type="radio" name="rank" value="3">
					<div id="rank_display">как вам эта книга?</div>
				</form>
			</div>
		</div>
	</div>
	<div id="category">
		<div id="category_container">
			<a href="/?category=<?php echo $c_name ?>" class="category_item"><?php echo $c_name ?></a>
		</div>
	</div>
	<script type="text/javascript" src="/js/book.js"></script>
</body>
</html>