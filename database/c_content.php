<?php

class Category
{
	public $stop;
	public $step;
	public $start;
	public $history;
	public $connection;
	public $query;

	function __construct($start, $step, $history, $connection)
	{
		$this->step = $step;
		$this->start = $start;
		$this->history = $history;
		$this->connection = $connection;
		$this->stop = $start + $this->step;
		$this->query = $this->create_query();
	}

	function residual()
	{
		$query = "	SELECT COUNT(*) as total 
					FROM ( $this->query 
					LIMIT $this->stop, 999999999) as matrix";
		$count = $this->connection->query("$query");
		if ( !$count ) return 0;
		$count = $count->fetch_assoc();
		$count = $count['total'];
		return $count;
	}

	function create_query()
	{
		if (!is_null($this->history['search']))
		{
			$name = $this->history['search'];
			$condition = "WHERE c_name like '$name'";
		}
		$query = "SELECT * FROM category " . $condition;
		return $query;
	}

	function create_order()
	{
		$value = [null, 'DESC', 'ASC'];

		$author = $this->history['author'];
		$author = $value[$author];

		$book = $this->history['book'];
		$book = $value[$book];

		$result = "ORDER BY ";
		if ( !is_null($author) ) $result = $result . "c_count_a $author";
		if ( !is_null($author) and !is_null($book) ) $result = $result . ', ';
		if ( !is_null($book) ) $result = $result . "c_count_b $book";
		if ( is_null($author) and is_null($book) ) $result = "ORDER BY c_id DESC";
		return $result;
	}

	function content()
	{

		$order 	= $this->create_order();		
		$query  = " $this->query
					$order
					LIMIT $this->start, $this->stop";
		$points = $this->connection->query( $query );
		$epochs = $points->num_rows;
		$c_id  		= [];
		$c_name  	= [];
		$c_count_b 	= [];
		$c_count_a 	= [];

		for ($i = 0; $i < $epochs; $i++)
		{
			$point = $points->fetch_assoc();
			array_push($c_id, $point['c_id']);
			array_push($c_name, $point['c_name']);
			array_push($c_count_a, $point['c_count_a']);
			array_push($c_count_b, $point['c_count_b']);
		}

		return [$c_id, $c_name, $c_count_a, $c_count_b];
	}

	function create_get($history)
	{
		$result = "/category.php/?";
		foreach ($history as $key => $value)
		{
			if ( !is_null($value) ) $result = "$result"."$key"."="."$value"."&";	
		}
		return $result;
	}

	function jumper( $key, $value )
	{
		$history = $this->history;
		$history[$key] = $value;
		$magic = $this->create_get($history);
		return $magic;
	}

	function __destruct()
	{
		$this->connection->close();
	}
}

?>
