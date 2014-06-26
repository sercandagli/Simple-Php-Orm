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
	
	/*
	 * Get fields with given single property
	 * 
	 * @param string $model
	 * @param string $field
	 * @param $value
	 */
	
	public function find($model, $field, $value){
		if($this->table_exists($model)){
			$query = $this->connection->prepare("SELECT * FROM $model WHERE $field=?");
			if($query->execute(array($value)))
				return $query->fetchAll(PDO::FETCH_ASSOC);
			
			return false;
		}
	}
} 