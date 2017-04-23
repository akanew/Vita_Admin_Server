<?php

	include_once("utils.php");

	class Teams extends ProcessOpers  {
		
		// Номер отряда
		private $teamId = "id";
		// Название отряда
		private $teamName = "name";
		// Номер санатория
		private $sanatoriumId = "sanatoriumId";	
		

		// Получить данные клиента или список клиентов
		protected function getObject()
		{
			if ($this->paramArray[$this->teamId] != null)
			{
				$db = new DB;
				
				if($this->paramArray[$this->teamId] != 0)
					$result = $db->GetResult('CALL getTeam("'.$this->paramArray[$this->teamId].'")');
				else if ($this->paramArray[$this->sanatoriumId] != null)
					$result = $db->GetResult('CALL getTeamList("'.$this->paramArray[$this->sanatoriumId].'")');
				
				if ($result['status'] == 1){
					$this->error = 0;
					$this->resultArray = $result['result'];
				}
				else
					$this->createError($this->sqlError);
			}
			else
				$this->createError($this->notCorrectParameters);
			
			if($this->error > 0)
				echo $this->getStatus();
			else echo $this->getResultArray();
		}

		private function createError($mes)
		{
			echo "GET\n";
			echo $this->paramArray;
		}

		// Создать клиента
		protected function createObject()
		{				
			if (($this->paramArray[$this->teamName] != null)  &&  (strlen($this->paramArray[$this->teamName]) > 0)  &&  ($this->paramArray[$this->sanatoriumId] != null))		
			{
				$db = new DB;
				$result = $db->ExecQueryWithoutResult("CALL addTeam('".$this->paramArray[$this->teamName]."', ".$this->paramArray[$this->sanatoriumId].")");
				if ($result['status'] == 1)
					$this->error = 0;
				else
					$this->createError($this->sqlError);
			}
			else
				$this->createError($this->notCorrectParameters);
			echo $this->getStatus();
		}

		// Редактировать клиента
		protected function editObject()
		{
			if (($this->paramArray[$this->teamId] != null)  && ($this->paramArray[$this->teamName] != null)  &&  (strlen($this->paramArray[$this->teamName]) > 0)  &&  ($this->paramArray[$this->sanatoriumId] != null))		
			{
				$db = new DB;
				$result = $db->ExecQueryWithoutResult("CALL editTeam(".$this->paramArray[$this->teamId].", '".$this->paramArray[$this->teamName]."', ".$this->paramArray[$this->sanatoriumId].")");
				if ($result['status'] == 1)
					$this->error = 0;
				else
					$this->createError($this->sqlError);
			}
			else
				$this->createError($this->notCorrectParameters);
			echo $this->getStatus();
		}

		// Удалить данные клиента
		protected function deleteObject()
		{
			if ($this->paramArray[$this->teamId] != null)
			{
				$db = new DB;
				$result = $db->ExecQueryWithoutResult('CALL deleteTeam("'.$this->paramArray[$this->teamId].'")');
				if ($result['status'] == 1)
					$this->error = 0;
				else
					$this->createError($this->sqlError);
			}
			else
				$this->createError($this->notCorrectParameters);
			echo $this->getStatus();
		}

		// Ошибка - неизвестный протокол
		protected function errorObject()
		{
			echo "ERROR\n";
			echo $this->paramArray;
		}

	}

	$teams = new Teams;
	$teams->processRequest();	

?>