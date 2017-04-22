<?php

	define("GET", 0);
	define("CREATE", 1);
	define("EDIT", 2);
	define("DELETE", 3);
	define("UNKNOWN", 4);

	abstract class ProcessOpers {

		protected $paramArray;
		protected $jsonObj;
		protected $error;
		protected $errorName = "";
		// Ошибки
		protected $notCorrectParameters = "Not correct parameters!";
		protected $sqlError = "SQL error!";

		private $method;


		public function processRequest()
		{		
			$this->paramArray = $this->getParamsArray();
			$this->jsonObj = json_decode($this->paramArray);
			$op = $this->splitMethod();

			switch($op) {
				case GET:
					$this->getObject();
					break;
				case CREATE:
					$this->createObject();
					break;
				case EDIT:
					$this->editObject();
					break;
				case DELETE:
					$this->deleteObject();
					break;
				case UNKNOWN:
					$this->errorObject();
					break;
			}
		}

		abstract protected function getObject();
		abstract protected function createObject();
		abstract protected function editObject();
		abstract protected function deleteObject();
		abstract protected function errorObject();

		protected function getStatus()
		{
			$status = $this->error > 0 ? "error" : "ok";
			$arr = array('status' => $status, 'errorName' => $this->errorName);
			return json_encode($arr);
		}

		public function splitMethod()
		{
			$operations = Array ("add" => CREATE, "update" => EDIT, "get" => GET, "delete" => DELETE);
			foreach($operations as $operation => $opId){
				if($this->jsonObj->$operation != NULL){
					$this->jsonObj = $this->jsonObj->$operation;
					return $opId;
				}
			}
			return UNKNOWN;
		}

		public function getParamsArray()		
		{
			return file_get_contents('php://input'); 
		}

	}

	class DB {

		// Link к БД
		private $db_link = null; 

		protected  $query_set_names = "SET NAMES utf8";

		// Массив с результатом выполнения запроса
		private $query_results = array(); 

		// Массив с настройками доступа к БД
		protected $config = array(
		
			'user' => "root",
		
			'password' => "",
		
			'host' => 'localhost',
		
			'db' => "vitaland",
		
			'type' => 'mysql',
		
			'charset' => null,
	
		);
		

		// Подключиться к серверу и выбрать БД
		public function DB_connect_and_DB_choose()
		{
			$this->DB_connect();
			if ($this->db_link)
				return $this->DB_choose();
			return 0;
			
		}
		
		// Инициализация массива config
		public function Init($user, $password, $host, $db, $type, $charset)			
		{
			$this->config['user']=$user;
			$this->config['password']=$password;
			$this->config['host']=$host;
			$this->config['db']=$db;
			$this->config['type']=$type;
			$this->config['charset']=$charset;
		}

		// Покдлючение к серверу
		public function DB_connect()
		{
			$this->db_link = mysql_connect($this->config['host'], $this->config['user'], $this->config['password']);			
		}

		// Выбор БД
		public function DB_choose()
		{
			return mysql_select_db($this->config['db'], $this->db_link);
		}

		// Закрытие соединения с БД
		public function DB_close()
		{
				mysql_close($this->db_link);
		}

		// Выполнение запроса $query - строка запроса $is_select - нужно ли возвращать результат
		public function ExecQuery($query, $is_select=null)
		{					
			if ($query)
			{					
				if (($result = mysql_query($query)) && ($is_select))
				{	
					$i=0;
					while ($row = mysql_fetch_assoc($result))
					{
						$this->query_results[$i] = $row;
						$i++;
					}
				}				
			}
			return $this->query_results;
		}

		// Выполнить запрос, возвращаюший результат
		public function GetResult($sql)
		{
			$result = array();
			if ($this->DB_connect_and_DB_choose())
			{
				$this->ExecQuery($query_set_names, 0);
				$result['sresult'] = $this->ExecQuery($sql, 1);
				$result['status'] = 1;
			}
			else				
				$result['status'] = 0;
			return $res;
		}

		// Выполнить запрос, не возвращаюший результат
		public function ExecQueryWithoutResult($sql)
		{
			$result = array();
			if ($this->DB_connect_and_DB_choose())
			{
				$this->ExecQuery($query_set_names, 0);
				$this->ExecQuery($sql);
				$result['status'] = 1;
			}
			else
				$result['status'] = 0;
			return $result;
		}

	}
?>