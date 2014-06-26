<?php
require_once 'config.php';
/*
 * Simple Php Orm For Mysql
 * 
 * @author Sercan Dağlı	<sdagli7@gmail.com>
 */

/*
 * ORM class for all actions 
 */
class ORM{
	private $connection;
	
	public function __construct(){
		$connection_string = "mysql:host=" . HOST . ";dbname=" . DBNAME . ";";
		try{
			$this->connection = new PDO($connection_string, USERNAME, PASSWORD);
		}catch (PDOException $e){
			echo $e;
		}	
	}
} 