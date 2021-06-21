<?php

class Author
{
	public $id;
	public $author;
	public $connection;
	public $categories = [];

	function __construct($id, $connection)
	{
		$this->id = $id;
		$this->connection = $connection;
		$this->get();
	}

	function get()
	{
		$query = "SELECT * FROM author WHERE a_id = $this->id";
		$author = $this->connection->query( "$query" );
		$author = $author->fetch_assoc();
		$this->author = $author;

		$query = "SELECT DISTINCT c_ref FROM author, book WHERE author.a_id = book.a_ref";
		$categories = $this->connection->query( "$query" );
		$epochs = $categories->num_rows;

		for ($i = 0; $i < $epochs; $i++)
		{
			$category = $categories->fetch_assoc();
			$category = $category['c_ref'];
			array_push($this->categories, $category);
		}
	}

	function content()
	{
		$a_rank = $this->author['a_rank_sum'] / $this->author['a_count_b'];
		$a_count = $this->author['a_count_b'];
		$a_new = $this->author['a_new_b'];
		$a_name = $this->author['a_name'];
		return [$a_name, $a_rank, $a_count, $a_new];
	}

	function change($name)
	{
		if ( !is_string($name) ) return false;
		if ( strlen($name) > 200 ) return false;
		if ( strlen($name) == 0 ) return false;
		if ( $this->author['a_name'] === $name ) return true;

		$query = "UPDATE author SET a_name = '$name' WHERE a_id = $this->id";
		$this->connection->query( "$query" );
		return true;
	}

	function category_count_author($c_id)
	{
		$query = "SELECT c_count_a FROM category WHERE c_id = $c_id";
		$count = $this->connection->query("$query");
		$count = $count->fetch_assoc();
		$count = $count['c_count_a'];
		return $count;
	}

	function category_count_author_dec($c_id)
	{
		if ( !$c_id ) return false;
		$c_count_a = $this->category_count_author($c_id);
		$c_count_a--;

		$query = "	UPDATE category SET c_count_a = $c_count_a
					WHERE c_id = $c_id";
		$result = $this->connection->query("$query");
	}

	function delete()
	{
		$query = "DELETE FROM author WHERE a_id = $this->id";
		$this->connection->query( "$query" );

		foreach ($this->categories as $id)
		{
			$this->category_count_author_dec($id);
		}
	}

	function __destruct()
	{
		$this->connection->close();
	}
}

?>