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
			if (isset($_GET['id']))
				$sql = "CALL getTeam(".$_GET['id'].")";
			else
				$sql = "CALL getTeamList()";
			$db = new DB;
			$result = $db->GetResult($sql);	
			echo json_encode($result['result']);
		}

		private function createError($mes)
		{
			$this->error = 1;
			echo $this->errorName = $mes;
		}

		// Создать клиента
		protected function createObject()
		{				
			if (($this->jsonObj->{$this->teamName} != null)  &&  (strlen($this->jsonObj->{$this->teamName}) > 0)  &&  ($this->jsonObj->{$this->sanatoriumId} != null))		
			{
				$db = new DB;
				$result = $db->ExecQueryWithoutResult("CALL addTeam('".$this->jsonObj->{$this->teamName}."', ".$this->jsonObj->{$this->sanatoriumId}.")");
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
			if (($this->jsonObj->{$this->teamId} != null)  && ($this->jsonObj->{$this->teamName} != null)  &&  (strlen($this->jsonObj->{$this->teamName}) > 0)  &&  ($this->jsonObj->{$this->sanatoriumId} != null))		
			{
				$db = new DB;
				$result = $db->ExecQueryWithoutResult("CALL editTeam(".$this->jsonObj->{$this->teamId}.", '".$this->jsonObj->{$this->teamName}."', ".$this->jsonObj->{$this->sanatoriumId}.")");
				if ($result['status'] == 1)
					$this->error = 0;
				else
					$this->createError($this->sqlError);
			}
			else
				$this->createError($this->notCorrectParameters);
			echo $this->getStatus();
		}

		// Удалить клиента
		protected function deleteObject()
		{
			echo "DELETE\n";
			echo $this->paramArray;
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