<?php
ini_set("display_errors", 1);
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
	protected $query;
	protected $query_string;
	protected $paramArray = array();
	
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
		$this->query = $this->connection->prepare("SELECT 1 FROM $model");
		if($this->query->execute())
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
	
	public function find($model, $conditions = null, $condition_type = "AND"){
		if($this->table_exists($model)){
			$this->query_string = "SELECT * FROM $model";
			if($conditions != null){
				$this->create_condition($conditions, $condition_type);
			}
			$this->query = $this->connection->prepare($this->query_string);
			if($this->query->execute($this->paramArray))
				return $this->query->fetchAll(PDO::FETCH_ASSOC);
			
			return false;
		}
	}
	/*
	 * Save data method
	 * 
	 * @param string $model
	 * @param array $data
	 * @return boolean
	 */
	public function save($model, Array $data){
		if($this->table_exists($model)){
			if(count($data) > 0){
				$paramString = "(";
				$this->query_string = "INSERT INTO $model (";
				foreach ($data as $key => $value){
					$this->query_string .= "$key,";
					$paramString .= "?,";
					$this->paramArray[] = $value; 
				}
				$this->query_string = substr($this->query_string, 0, -1);
				$this->query_string .= ") VALUES " . substr($paramString, 0, -1) . ")"; 
				$this->query = $this->connection->prepare($this->query_string);
				if($this->query->execute($this->paramArray))
					$this->query_string = "";
					return true;
				
				return false;
			}else{
				return false;
			}
		}
	}
	
	/*
	 * Update method
	 * 
	 * @param string $model
	 * @param array $data
	 * @param array $condition
	 * @param string $condition_type
	 * @return boolean
	 */
	public function update($model, $data, $conditions = null, $condition_type = "AND"){
		if($conditions != null){
			if($this->table_exists($model)){
				$this->query_string = "UPDATE $model SET ";
				foreach ($data as $key => $value){
					$this->query_string .= $key . "=?,";
					$this->paramArray[] = $value;
				}
				$this->query_string = substr($this->query_string, 0, -1); 
				$this->create_condition($conditions, $condition_type); 
				$this->query = $this->connection->prepare($this->query_string);
				if($this->query->execute($this->paramArray)){
					$this->query_string = null;
					return true;
				}
				return false;
			}
		}
		return false;
	}
	
	/*
	 * Delete method
	 * 
	 * @param $model
	 * @param array $conditions
	 * @param string $condition_type
	 */
	
	public function delete($model, $conditions = null, $condition_type =  "AND"){
		if($conditions != null){
			$this->query_string = "DELETE FROM $model";
			$this->create_condition($conditions, $condition_type);
			$this->query = $this->connection->prepare($this->query_string);
			if($this->query->execute($this->paramArray)){
				$this->query_string = null;
				return true;
			}
			return false;
		}
		return false;
	}

	
	/*
	 * Create conditions
	 * Create conditions with given condition rule
	 * 
	 * @param array $conditions
	 * @param string $condition_type
	 */
	
	private function create_condition($conditions, $condition_type){
		$this->query_string .= " WHERE ";
		foreach ($conditions as $key => $value){
			$this->query_string .= $key . "=?" . $condition_type . " ";
			$this->paramArray[] = $value; // 
		}
		switch($condition_type){
			case "AND":
				$count = -4;
				break;
			case "OR":
				$count = -3;
				break;
			default:
				throw new Exception("Unexpected condition type");
		}
		$this->query_string = substr($this->query_string, 0, $count);
		return true;
	}
} 