<?php

class Book
{
	public $id;
	public $book;
	public $author;
	public $category;
	public $connection;

	function __construct($id, $connection)
	{
		$this->id = $id;
		$this->connection = $connection;
		$this->get();
	}

	function get()
	{
		$query = "SELECT * FROM book WHERE b_id = $this->id";
		$book = $this->connection->query( "$query" );
		$book = $book->fetch_assoc();
		$this->book = $book;

		$a_id = $this->book['a_ref'];
		$query = "SELECT * FROM author WHERE a_id = $a_id";
		$author = $this->connection->query( "$query" );
		if ( $author ) $author = $author->fetch_assoc();
		else  $author = null;
		$this->author = $author;

		$c_id = $this->book['c_ref'];
		$query = "SELECT * FROM category WHERE c_id = $c_id";
		$category = $this->connection->query( "$query" );
		$category = $category->fetch_assoc();
		$this->category = $category;
	}

	function content()
	{
		$b_name = $this->book['b_name'];
		$b_rank = $this->book['b_rank'];
		$b_path = $this->book['b_path'];
		$a_name = $this->author['a_name'];
		$c_name = $this->category['c_name'];
		return [$b_name, $b_rank, $b_path, $a_name, $c_name];
	}

	function author_new_book($a_id)
	{
		$query = "SELECT a_new_b FROM author WHERE a_id = $a_id";
		$count = $this->connection->query("$query");
		$count = $count->fetch_assoc();
		$count = $count['a_new_b'];
		return $count;
	}

	function author_new_dec($a_id)
	{
		if ( !$a_id ) return false;
		$a_new_b = $this->author_new_book($a_id);
		$a_new_b--;

		$query = "	UPDATE author SET a_new_b = $a_new_b
					WHERE a_id = $a_id";
		$result = $this->connection->query("$query");
		return true;
	}

	function author_rank($a_id)
	{
		$query = "SELECT a_rank_sum FROM author WHERE a_id = $a_id";
		$rank = $this->connection->query("$query");
		$rank = $rank->fetch_assoc();
		$rank = $rank['a_rank_sum'];
		return $rank;
	}

	function author_rank_update($a_id, $value, $rank)
	{
		if ( !$a_id ) return false;
		$rank_sum = $this->author_rank($a_id);
		if ( $rank == 0 ) 
		{
			$this->author_new_dec($a_id);
			$this->author_count_inc($a_id);
		}
		$rank = $rank_sum + $value;
		$query = "UPDATE author SET a_rank_sum = $rank WHERE a_id = $a_id";
		$this->connection->query( "$query" );	
		return true;
	}

	function change_rank($rank)
	{
		$query = "SELECT b_rank FROM book WHERE b_id = $this->id";
		$p_rank = $this->connection->query( "$query" );
		$p_rank = $p_rank->fetch_assoc();
		$p_rank = $p_rank['b_rank'];
		$value = ($rank - $p_rank);

		$query = "UPDATE book SET b_rank = $rank WHERE b_id = $this->id";
		$this->connection->query( "$query" );

		$a_id = $this->author['a_id'];
		$this->author_rank_update($a_id, $value, $p_rank);
	}

	function valid($name, $max)
	{
		if ( !is_string($name) ) return false;
		if ( strlen($name) == 0  ) return false;
		if ( strlen($name) > $max ) return false;
		return true;
	}

	function change($b_name, $a_name, $c_name, $file)
	{
		$point = $this->change_book_name($b_name);
		if ( !$point ) return false;

		$a_id = $this->change_author_name($a_name);
		if ( is_null($a_id) ) return false;
		$query = "UPDATE book SET a_ref = $a_id WHERE b_id = $this->id";
		$result = $this->connection->query( "$query" );
		if ( is_null($result) ) return false;

		$c_id = $this->change_category_name($c_name);
		if ( is_null($c_id) ) return false;
		$query = "UPDATE book SET c_ref = $c_id WHERE b_id = $this->id";
		$result = $this->connection->query( "$query" );
		if ( is_null($result) ) return false;

		$point = $this->change_file($file);
		if ( !$point ) return false;

		return true;
	}

	function change_book_name($name)
	{
		if ( !$this->valid($name, 300) ) return null;
		if ( $this->book['b_name'] === $name) return true;
		$query = "UPDATE book SET b_name = '$name' WHERE b_id = $this->id";
		$this->connection->query( "$query" );
		return true;
	}

	function change_author_name($name)
	{
		if ( !$this->valid($name, 200) ) return null;
		if ( $this->author['a_name'] === $name) return $this->author['a_id'];

		$query = "SELECT a_id FROM author WHERE a_name like '$name'";
		$exists = $this->connection->query( "$query" );
		if ($exists->num_rows==0)
		{
			$query = "INSERT INTO author (a_name) VALUES ('$name')";
			$result = $this->connection->query( "$query" );
			if ( is_null($result) ) return null;
			$id = $this->connection->insert_id;
		}
		else
		{
			$result = $exists->fetch_assoc();
			$id = $result['a_id'];
		}

		$this->author_rank_update( $this->author['a_id'], -($this->book['b_rank']) );
		$this->author_rank_update( $id, $this->book['b_rank'] );
		$this->author_count_dec( $this->author['a_id'] );
		$this->author_count_inc( $id );
		return $id;
	}

	function change_category_name($name)
	{
		if ( !$this->valid($name, 100) ) return false;
		if ( $this->category['c_name'] === $name) return $this->category['c_id'];

		$query = "SELECT c_id FROM category WHERE c_name like '$name'";
		$exists = $this->connection->query( "$query" );
		if ($exists->num_rows==0)
		{
			$query = "INSERT INTO category (c_name) VALUES ('$name')";
			$result = $this->connection->query( "$query" );
			if ( is_null($result) ) return false;
			$id = $this->connection->insert_id;
		}
		else
		{
			$result = $exists->fetch_assoc();
			$id = $result['c_id'];
		}

		$this->category_count_book_dec($this->category['c_id']);
		$this->category_count_book_inc($id);
		return $id;
	}

	function change_file( $file )
	{
		if ($file['error'] == 4) return true;
		if ($file['error'] != 0) return false;
		if ($file['size'] > 104857600) return false;

		$name = $this->book['path'];
		$dir = '/var/www/mars/books/';
		$filename = "$dir" . "$name";

		$saved = move_uploaded_file($file['tmp_name'], $filename);
		if ($saved) return true;
		return false;
	}

	function author_count_book($a_id)
	{
		$query = "SELECT a_count_b FROM author WHERE a_id = $a_id";
		$count = $this->connection->query("$query");
		$count = $count->fetch_assoc();
		$count = $count['a_count_b'];
		return $count;
	}

	function author_count_inc($a_id)
	{
		if ( !$a_id ) return false;
		$a_count_b = $this->author_count_book($a_id);
		$a_count_b++;

		$query = "	UPDATE author SET a_count_b = $a_count_b
					WHERE a_id = $a_id";
		$result = $this->connection->query("$query");
		return true;
	}

	function author_count_dec($a_id)
	{
		if ( !$a_id ) return false;
		$a_count_b = $this->author_count_book($a_id);
		$a_count_b--;

		$query = "	UPDATE author SET a_count_b = $a_count_b
					WHERE a_id = $a_id";
		$result = $this->connection->query("$query");
		return true;
	}

	function category_count_book($c_id)
	{
		$query = "SELECT c_count_b FROM category WHERE c_id = $c_id";
		$count = $this->connection->query("$query");
		$count = $count->fetch_assoc();
		$count = $count['c_count_b'];
		return $count;
	}

	function category_count_book_inc($c_id)
	{
		if ( !$c_id ) return false;
		$c_count_b = $this->category_count_book($c_id);
		$c_count_b++;

		$query = "	UPDATE category SET c_count_b = $c_count_b
					WHERE c_id = $c_id";
		$result = $this->connection->query("$query");
		return true;
	}

	function category_count_book_dec($c_id)
	{
		if ( !$c_id ) return false;
		$c_count_b = $this->category_count_book($c_id);
		$c_count_b--;

		$query = "	UPDATE category SET c_count_b = $c_count_b
					WHERE c_id = $c_id";
		$result = $this->connection->query("$query");
		return true;
	}

	function delete()
	{
		$query = "DELETE FROM book WHERE b_id = $this->id";
		$this->connection->query( "$query" );
		$a_id = $this->author['a_id'];
		$this->author_count_dec($a_id);
		$c_id = $this->category['c_id'];
		$this->category_count_book_dec($c_id);
	}

	function __destruct()
	{
		$this->connection->close();
	}
}

?>