<?php
class Database
{
	protected $db = false;
		
	function __construct() //Konstruktor
	{
	}
	
	function selectOne($sql) //Abfrage starten und EIN Ergebnis sicherstellen
	{
		$result=$this->select($sql);
		
		if ($result->num_rows != 1)
		{
			$result->free();
			return false;
		}
		
		return $result->fetch_object();
	}
	
	function count($sql) //Abfrage starten und Ergebnisanzahl zurückgeben
	{
		$result=$this->select($sql);
		
		$num = $result->num_rows;
		$result->free();
		
		return $num;
	}
	
	function execute($sql) //Abfrage starten und affected Rows zählen
	{
		$this->assureConnection();

		$result = $this->send($sql);
		
		if (is_object($result))
		{
			trigger_error("SQL ERROR");
		}
		
		if (!$result)
		{
//			echo $sql;
			trigger_error("SQL ERROR");
		}
		
		$rows=mysqli_affected_rows($this->db);

		return $rows;
	}
	
	function select($sql) //Abfrage starten
	{
		$this->assureConnection();
		
		$result=$this->send($sql);
		
		if (!$result)
		{
//			echo $sql;
			trigger_error("SQL ERROR");
		}
		elseif (!is_object($result))
		{
			trigger_error("SQL ERROR");
		}
		
		return $result;
	}
	
	function escape($string) //String DB-Sicher escapen
	{
		$this->assureConnection();
		
		return $this->db->escape_string($string);
	}
	
	function getInsertedId() //return autogenerated id from last insert (0 if none)
	{
		$this->assureConnection();
		
		return mysqli_insert_id($this->db);
	}
	
	function assureConnection()
	{
		if (!$this->db)
		{
			$this->connect();
		}
	}

	function connect()
	{
		$path=dirname(realpath(__FILE__));
		require $path."/config.php";

		//Verbindung herstellen
		$this->db = @mysqli_connect($config["db_host"], $config["db_user"], $config["db_pwd"], $config["db_name"]);
		
		if (!$this->db)
		{
			trigger_error("Database connection failed. Message: ". mysqli_connect_error());
			die();
		}

		foreach ($_REQUEST as $key => $value)
		{
			if ($this->escape($key.$value) != $key.$value)
			{
				$_REQUEST[$this->escape($key)] = $this->escape($value);
			}
		}
	}
	
	function send($sql)
	{
		$result = $this->db->query($sql);
		return $result;
	}
}
?>