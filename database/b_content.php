<?php

class Library
{
	public $stop;
	public $step;
	public $start;
	public $query;
	public $history;
	public $connection;
	public $join = "SELECT * FROM book
					LEFT JOIN author ON book.a_ref = author.a_id
					LEFT JOIN category ON book.c_ref=category.c_id";

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
					FROM ( SELECT * FROM ($this->query) as joined
					LIMIT $this->stop, 99999999) as matrix";
		$count = $this->connection->query("$query");
		if ( !$count ) return 0;
		$count = $count->fetch_assoc();
		$count = $count['total'];
		return $count;
	}

	function create_query()
	{
		$category = $this->history['category'];
		$search = $this->history['search'];
		$author = $this->history['author'];
		$rank = $this->history['rank'];
		$rank_c = !is_null($rank);

		if ( $category or $search or $author or $rank_c )
		{
			$condition = 'WHERE ';
		}
		else $condition = null;

		if ( $category ) $condition = $condition . "c_name LIKE '$category'";
		if ( $category and ($search or $author or $rank_c) ) $condition = $condition . " AND ";
		if ( $search ) $condition = $condition . "LOCATE('$search', b_name)";
		if ( $search and ($author or $rank_c) ) $condition = $condition . " AND ";
		if ( $author ) $condition = $condition . "a_name LIKE '$author'";
		if ( $author and ($rank_c) ) $condition = $condition . " AND ";
		if ( $rank_c ) $condition = $condition . "b_rank = $rank";

		$column = "b_id, b_name, b_rank, a_name, c_name";
		$query 	= "SELECT $column FROM ($this->join) as content ". $condition;
		return $query;
	}

	function content()
	{
		$order_dict = [null, 'DESC', 'ASC'];
		$order = $this->history['order'];
		$order = $order_dict[$order];
		if ( !is_null($order) ) $order = "ORDER BY b_rank $order";
		else $order = "ORDER BY b_id DESC";

		$query 	= " $this->query
					$order
					LIMIT $this->start, $this->stop";

		$points = 	$this->connection->query( $query );
		$epochs = 	$points->num_rows;
		$b_name = 	[];
		$b_rank = 	[];
		$a_name = 	[];
		$c_name = 	[];
		$b_id 	= 	[];
		
		for ($i = 0; $i < $epochs; $i++)
		{
			$point = $points->fetch_assoc();
			array_push($b_id, $point['b_id']);
			array_push($b_name, $point['b_name']);
			array_push($b_rank, $point['b_rank']);
			array_push($a_name, $point['a_name']);
			array_push($c_name, $point['c_name']);
		}

		return [$b_id, $b_name, $b_rank, $a_name, $c_name];
	}

	function create_get($history)
	{
		$result = "/?";
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