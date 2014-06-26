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
	
	/*
	 * Connect database with given config information
	 */
	public function __construct(){
		$connection_string = "mysql:host=" . HOST . ";dbname=" . DBNAME . ";";
		try{
			$this->connection = new PDO($connection_string, USERNAME, PASSWORD);
		}catch (PDOException $e){
			echo $e;
		}	
	}
	/*
	 * Check if exists table 
	 * 
	 * @param string $model
	 * @return boolean
	 */
	protected function table_exists($model){
		$query = $this->connection->prepare("SELECT 1 FROM $model");
		if($query->execute())
			return true;
		
		return false;
	}
} 