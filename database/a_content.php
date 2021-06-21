<?php

class Author
{
	public $stop;
	public $step;
	public $start;
	public $filter;
	public $history;
	public $connection;
	public $join = "SELECT * FROM author
					LEFT JOIN book ON author.a_id=book.a_ref
					LEFT JOIN category ON book.c_ref=category.c_id";

	function __construct($start, $step, $history, $connection)
	{
		$this->step = $step;
		$this->start = $start;
		$this->history = $history;
		$this->connection = $connection;
		$this->stop = $start + $this->step;
		$this->filter = $this->create_filter();
	}

	function residual()
	{
		$query = "	SELECT COUNT(*) as total 
					FROM ( SELECT * FROM author 
					LIMIT $this->stop, 999999999) as matrix";
		$count = $this->connection->query("$query");
		if ( !$count ) return 0;
		$count = $count->fetch_assoc();
		$count = $count['total'];
		return $count;
	}

	function create_filter()
	{
		$category = $this->history['category'];
		$search = $this->history['search'];

		if ( $category or $search )
		{
			$condition = 'WHERE ';
		}
		else $condition = null;

		if ( $category ) $condition = $condition . "c_name LIKE '$category'";
		if ( $category and ($search) ) $condition = $condition . " AND ";
		if ( $search ) $condition = $condition . "LOCATE('$search', a_name)";

		$column = "a_id, a_name, a_count_b, a_rank_sum, c_name";
		$query 	= "SELECT $column FROM ($this->join) as filter ". $condition;
		return $query;
	}

	function index_string($ids)
	{
		$index = "(";
		$lenght = count($ids);
		for ($i = 0; $i < $lenght; $i++)
		{
			$id = $ids[$i];
			$index = $index . "$id";
			if ( $i != $lenght-1 ) $index = $index . ", ";
		}
		$index = $index . ")";
		return $index;
	}

	function create_order()
	{
		$value = [null, 'DESC', 'ASC'];

		$order = $this->history['order'];
		$order = $value[$order];

		$count = $this->history['count'];
		$count = $value[$count];

		$result = "ORDER BY ";
		if ( !is_null($order) ) $result = $result . "a_rank_sum $order";
		if ( !is_null($order) and !is_null($count) ) $result = $result . ', ';
		if ( !is_null($count) ) $result = $result . "a_count_b $count";
		if ( is_null($order) and is_null($count) ) $result = "ORDER BY a_id DESC";
		return $result;
	}

	function content()
	{
		$query 	= " SELECT DISTINCT a_id FROM
					($this->filter) as content
					LIMIT $this->start, $this->stop";

		$ids 	= $this->connection->query( $query );
		$epochs = $ids->num_rows;
		$a_ids 	= [];

		for ($i = 0; $i < $epochs; $i++)
		{
			$id = $ids->fetch_assoc();
			$id = $id['a_id'];
			array_push($a_ids, $id);
		}

		$order = $this->create_order();
		$index 	= $this->index_string($a_ids);
		$column = "a_id, a_name, a_count_b, (a_count_b + a_new_b ) as a_count, a_rank_sum, c_name";
		$query 	= " SELECT $column FROM ($this->join) as content
					WHERE a_id IN $index " . $order;
		$points = 	$this->connection->query( $query );
		$epochs = 	$points->num_rows;

		$author = [];
		
		for ($i = 0; $i < $epochs; $i++)
		{
			$point = $points->fetch_assoc();
			$category = $point['c_name'];
			$name = $point['a_name'];

			if ( array_key_exists($name, $author) )
			{
				if ( !in_array($category, $author[$name]['category']))
				{
					array_push($author[$name]['category'], $category);
				}
			}
			else
			{
				$author[$name] = [];
				$author[$name]['id'] = $point['a_id'];
				$author[$name]['a_count'] = $point['a_count'];
				$author[$name]['a_count_b'] = $point['a_count'];

				if ( $point['a_count_b'] > 0 ) $a_rank_sum = intdiv($point['a_rank_sum'], $point['a_count_b']);
				else $a_rank_sum = 0;
				$author[$name]['a_rank_sum'] = $a_rank_sum;
				$author[$name]['category'] = array($category);
			}
		}
		return $author;
	}

	function create_get($history)
	{
		$result = "/author.php/?";
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
