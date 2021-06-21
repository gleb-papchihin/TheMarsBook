<?php
	
	include 'connect.php';

	$author 	= "	CREATE TABLE IF NOT EXISTS author (
					a_id INT NOT NULL AUTO_INCREMENT,
					a_name VARCHAR(200) NOT NULL,
					a_rank_sum INT DEFAULT 0,
					a_count_b INT DEFAULT 0,
					a_new_b INT DEFAULT 0,
					PRIMARY KEY ( a_id ))";

	$category 	= "	CREATE TABLE IF NOT EXISTS category (
					c_id INT NOT NULL AUTO_INCREMENT,
					c_name VARCHAR(100) NOT NULL,
					c_count_a INT DEFAULT 0,
					c_count_b INT DEFAULT 0,
					PRIMARY KEY ( c_id ))";

	$book 		= "	CREATE TABLE IF NOT EXISTS book (
					b_id INT NOT NULL AUTO_INCREMENT,
					b_path VARCHAR(200) NOT NULL,
					b_name VARCHAR(300) NOT NULL,
					b_rank INT DEFAULT 0,
					a_ref INT,
					c_ref INT,
					FOREIGN KEY ( c_ref ) REFERENCES category (c_id)
					ON DELETE SET NULL,
					FOREIGN KEY ( a_ref ) REFERENCES author (a_id)
					ON DELETE SET NULL,
					PRIMARY KEY ( b_id ))";

	if ( !$storage->connect_errno )
	{
		$table = $storage->query( "$author" );
		$table = $storage->query( "$category" );
		$table = $storage->query( "$book" );
	}

	$storage->close();

?>