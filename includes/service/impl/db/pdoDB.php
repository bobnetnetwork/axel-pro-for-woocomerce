<?php
/**
 * Created by PhpStorm.
 * User: Bobesz
 * Date: 3/7/2018
 * Time: 12:05 PM
 */

class pdoDB extends db{
	private $pdo, $dbengine;

	public function __construct($host, $port, $user, $pwd, $dbname, $charset, $dbengine, $prefix) {
		parent::__construct($host, $port, $user, $pwd, $dbname, $charset, $prefix);
		$this->dbengine = $dbengine;
	}

	public function openConnection() {
		try
		{

			$this->pdo = new PDO($this->dbengine.':host='.$this->host.';dbname='.$this->dbname, $this->username, $this->pwd, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES ".$this->charset));

		}
		catch (PDOException $e)
		{
			echo 'Error: ' . $e->getMessage();
			exit();
		}
	}

	public function query($sql) {
		$pd = new PDO($this->dbengine.':host='.$this->host.';dbname='.$this->dbname, $this->user, $this->pwd, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES ".$this->charset));

		$query = $pd->prepare($sql);
		$query->execute();
		return $query;
	}

	public function closeConnection() {
		$pdo = null;
	}
}