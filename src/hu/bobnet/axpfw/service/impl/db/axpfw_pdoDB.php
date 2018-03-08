<?php
/**
 * Created by PhpStorm.
 * User: Bobesz
 * Date: 3/7/2018
 * Time: 12:05 PM
 */

namespace HU\BOBNET\AXPFW\SERVICE\IMPL\DB;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( !class_exists( '\\HU\\BOBNET\\AXPFW\\SERVICE\\IMPL\\DB\\axpfw_pdoDB' ) ) :

class axpfw_pdoDB extends axpfw_db{
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
endif; // class_exists

return new axpfw_pdoDB();