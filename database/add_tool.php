<?php

class Add
{
	public $post;
	public $file;
	public $connection;
	public $allowed = ['fb2', 'pdf', 'epub', 'djvu'];

	function __construct($post, $file, $connection)
	{
		$this->post = $post;
		$this->file = $file;
		$this->connection = $connection;
	}

	function save_book()
	{
		$count = $this->connection->query(' SELECT COUNT(*) FROM book');
		$count = $count->fetch_assoc();
		$count = $count['COUNT(*)'] + 1;
		$name = "book_" . "$count";

		$info = pathinfo( $this->file['name'] );
		$ext = $info['extension'];

		$name = $name."."."$ext";
		$dir = '/var/www/mars/books/';
		$filename = "$dir" . "$name"; 

		$saved = move_uploaded_file($this->file['tmp_name'], $filename);
		if ($saved) return $name;
		return false;
	}

	function valid()
	{
		$book_title = $this->post['name'];
		$category = $this->post['category'];
		$author = $this->post['author'];

		$info = pathinfo( $this->file['name'] );
		$ext = strtolower( $info['extension'] );

		if (strlen($book_title) > 300 or strlen($book_title) == 0) return false;

		if (strlen($category) > 100 or strlen($category) == 0) return false;

		if (strlen($author) > 200 or strlen($author) == 0) return false;

		if ($this->file['error'] != 0 or $this->file['size'] > 104857600) return false; #100MB

		if (!in_array($ext, $this->allowed)) return false;

		if ( strlen($this->file['name']) > 200 ) return false;

		return true;
	}

	function get_or_create_author()
	{
		$author = $this->post['author'];
		$result = $this->connection->query("SELECT a_id FROM author WHERE a_name like '$author'");
		
		if ( $result->num_rows == 0 )
		{
			$result = $this->connection->query("INSERT INTO author (a_name) VALUES ('$author')");
			if ( !$result ) return null;
			$id = $this->connection->insert_id;
			return $id;
		}

		$id = $result->fetch_assoc();
		return $id['a_id'];
	}

	function get_or_create_category()
	{
		$category = $this->post['category'];
		$result = $this->connection->query("SELECT c_id FROM category WHERE c_name like '$category'");
		
		if ( $result->num_rows == 0 )
		{
			$result = $this->connection->query("INSERT INTO category (c_name) VALUES ('$category')");
			if ( !$result ) return null;
			$id = $this->connection->insert_id;
			return $id;
		}

		$id = $result->fetch_assoc();
		return $id['c_id'];
	}

	function author_new_book($a_id)
	{
		$query = "SELECT a_new_b FROM author WHERE a_id = $a_id";
		$count = $this->connection->query("$query");
		$count = $count->fetch_assoc();
		$count = $count['a_new_b'];
		return $count;
	}

	function author_new_inc($a_id)
	{
		$a_new_b = $this->author_new_book($a_id);
		$a_new_b++;

		$query = "	UPDATE author SET a_new_b = $a_new_b
					WHERE a_id = $a_id";
		$result = $this->connection->query("$query");
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
	}

	function create()
	{
		if (!$this->valid($this->post, $this->file)) return false;
		$category = $this->post['category'];
		$author = $this->post['author'];
		$book = $this->post['name'];

		$filename = $this->save_book();
		if (!$filename) return false;

		$a_id = $this->get_or_create_author();
		if (is_null($a_id)) return false;

		$c_id = $this->get_or_create_category();
		if (is_null($c_id)) return false;

		$query = "	INSERT INTO book (b_name, b_path, a_ref, c_ref) 
					VALUES ('$book', '$filename', '$a_id', '$c_id')";
		$result = $this->connection->query("$query");
		if (!$result) return false;

		$this->category_count_author_inc($c_id);
		$this->category_count_book_inc($c_id);
		$this->author_new_inc($a_id);
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

	function category_count_author_inc($c_id)
	{
		if ( !$c_id ) return false;
		$c_count_a = $this->category_count_author($c_id);
		$c_count_a++;

		$query = "	UPDATE category SET c_count_a = $c_count_a
					WHERE c_id = $c_id";
		$result = $this->connection->query("$query");
	}

	function author_create()
	{
		$name = $this->post['author'];
		if (strlen($name) > 200) return false;
		if (strlen($name) == 0) return false;
		$query = "	SELECT COUNT(*) as total FROM author
					WHERE a_name LIKE '$name'";
		$count = $this->connection->query( $query );
		$count = $count->fetch_assoc();
		$count = $count['total'];
		if ($count > 0) return true;
		$query = "	INSERT INTO author (a_name) VALUES ('$name')";
		$count = $this->connection->query( $query );
		return true;
	}

	function category_create()
	{
		$name = $this->post['category'];
		if (strlen($name) > 100) return false;
		if (strlen($name) == 0) return false;
		$query = "	SELECT COUNT(*) as total FROM category
					WHERE c_name LIKE '$name'";
		$count = $this->connection->query( $query );
		$count = $count->fetch_assoc();
		$count = $count['total'];
		if ($count > 0) return true;
		$query = "	INSERT INTO category (c_name) VALUES ('$name')";
		$count = $this->connection->query( $query );
		return true;
	}

	function __destruct()
	{
		$this->connection->close();
	}
}

?>