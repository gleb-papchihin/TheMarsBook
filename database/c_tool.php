<?php

class Category
{
	public $id;
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
		$query = "SELECT * FROM category WHERE c_id = $this->id";
		$category = $this->connection->query( "$query" );
		$category = $category->fetch_assoc();
		$this->category = $category;
	}

	function content()
	{
		$c_name = $this->category['c_name'];
		return $c_name;
	}

	function change($name)
	{
		if ( !is_string($name) ) return false;
		if ( strlen($name) > 100 ) return false;
		if ( strlen($name) == 0 ) return false;
		if ( $this->category['c_name'] === $name ) return true;
		$query = "UPDATE category SET c_name = '$name' WHERE c_id = $this->id";
		$this->connection->query( "$query" );
		return true;
	}

	function delete()
	{
		$query = "DELETE FROM category WHERE c_id = $this->id";
		$this->connection->query( "$query" );
	}

	function __destruct()
	{
		$this->connection->close();
	}
}

?>