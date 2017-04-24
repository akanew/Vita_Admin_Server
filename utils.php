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
		// ������
		protected $notCorrectParameters = "Not correct parameters!";
		protected $sqlError = "SQL error!";

		private $method;


		public function processRequest()
		{		
			$op = $this->checkMethod();
			$this->paramArray = $this->getParamsArray();
			$this->jsonObj = json_decode($this->paramArray);
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
					$this->deleteClient();
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

		public function checkMethod()		
		{
			$this->method = $_SERVER['REQUEST_METHOD'];
			switch ($this->method) {
				case 'GET':
					return GET;
					break;
				case 'POST':
					return CREATE;
					break;
				case 'PUT':
					return EDIT;
					break;
				case 'DELETE':
					return DELETE;
					break;
				default:
					return UNKNOWN;
					break;
			}
		}

		public function getParamsArray()		
		{
			return file_get_contents('php://input'); 
		}

	}

	class DB {

		// Link � ��
		private $db_link = null; 

		protected  $query_set_names = "SET NAMES utf8";

		// ������ � ����������� ���������� �������
		private $query_results = array(); 

		// ������ � ����������� ������� � ��
		protected $config = array(
		
			'user' => "root",
		
			'password' => "",
		
			'host' => 'localhost',
		
			'db' => "vitaland",
		
			'type' => 'mysql',
		
			'charset' => null,
	
		);
		

		// ������������ � ������� � ������� ��
		public function DB_connect_and_DB_choose()
		{
			$this->DB_connect();
			if ($this->db_link)
				return $this->DB_choose();
			return 0;
			
		}
		
		// ������������� ������� config
		public function Init($user, $password, $host, $db, $type, $charset)			
		{
			$this->config['user']=$user;
			$this->config['password']=$password;
			$this->config['host']=$host;
			$this->config['db']=$db;
			$this->config['type']=$type;
			$this->config['charset']=$charset;
		}

		// ����������� � �������
		public function DB_connect()
		{
			$this->db_link = mysql_connect($this->config['host'], $this->config['user'], $this->config['password']);			
		}

		// ����� ��
		public function DB_choose()
		{
			return mysql_select_db($this->config['db'], $this->db_link);
		}

		// �������� ���������� � ��
		public function DB_close()
		{
				mysql_close($this->db_link);
		}

		// ���������� ������� $query - ������ ������� $is_select - ����� �� ���������� ���������
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

		// ��������� ������, ������������ ���������
		public function GetResult($sql)
		{

			$result = array();
			if ($this->DB_connect_and_DB_choose())
			{
				$this->ExecQuery($query_set_names, 0);
				$result['result'] = $this->ExecQuery($sql, 1);
				$result['status'] = 1;
			}
			else				
				$result['status'] = 0;
			return $result;
		}

		// ��������� ������, �� ������������ ���������
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