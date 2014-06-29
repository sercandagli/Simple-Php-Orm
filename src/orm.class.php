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
	public $query_string;
	
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
	
	public function find($model, $field, $value){
		if($this->table_exists($model)){
			$this->query = $this->connection->prepare("SELECT * FROM $model WHERE $field=?");
			if($query->execute(array($value)))
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
				$paramArray = array();
				$paramString = "(";
				$this->query_string = "INSERT INTO $model (";
				foreach ($data as $key => $value){
					$this->query_string .= "$key,";
					$paramString .= "?,";
					$paramArray[] = $value; 
				}
				$this->query_string = substr($this->query_string, 0, -1);
				$this->query_string .= ") VALUES " . substr($paramString, 0, -1) . ")"; 
				$this->query = $this->connection->prepare($this->query_string);
				if($this->query->execute($paramArray))
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
				$paramArray = array();
				foreach ($data as $key => $value){
					$this->query_string .= $key . "=?, ";
					$paramArray[] = $value;
				}
				$this->query_string = substr($this->query_string, 0, -2);
				$this->query_string .= " WHERE ";
				foreach ($conditions as $key => $value){
					$this->query_string .= $key . "='" . $value . "' $condition_type ";
				}
				if($condition_type == "AND")
					$count = -4;
				else 
					$count = -3;
				$this->query_string = substr($this->query_string, 0, $count);
				$this->query = $this->connection->prepare($this->query_string);
				if($this->query->execute($paramArray)){
					return true;
				}
				return false;
			}
		}
	}
} 