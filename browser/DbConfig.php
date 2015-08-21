<?php


/**
 * 
 * @author martinsnijders
 *
 */
class DbConfig {
	
	private static $theInstance = null;
	
	private static $host;
	private static $username;
	private static $password;
	private static $dbname;
	
	public static function getInstance()
    {
        if (!isset(static::$theInstance)) {
            static::$theInstance = new static;
        }
        return static::$theInstance;
    }
	
	protected function __construct() {
		$myPath = realpath(dirname(__FILE__));
		$iniPath = substr($myPath, 0, strlen($myPath) - 7) . "/application/configs/application.ini"; // cut away 'browser'
		$ini_array = parse_ini_file($iniPath);
		
		try {
			self::$host = $ini_array["resources.db.params.host"];
			self::$username = $ini_array["resources.db.params.username"];
			self::$password = $ini_array["resources.db.params.password"];
			self::$dbname = $ini_array["resources.db.params.dbname"];
		}
		catch(Exception $ex) {
			trigger_error("Exception while accessing config members for database.", $ex->getTraceAsString());
		}
		
		if (!isset(self::$host) OR !isset(self::$username) OR !isset(self::$password) OR !isset(self::$dbname)) {
			trigger_error("Database configuration could not be (fully) read. host= " .self::$host." ,username= " . self::$username . " ,password= " . self::$password . " ,dbname= ".self::$dbname, E_USER_ERROR);
		}
	}
	
	public function getHost() {
		return self::$host;
	}
	
	public function getUserName() {
		return self::$username;
	}
	
	public function getPassword() {
		return self::$password;
	}
	
	public function getDbName() {
		return self::$dbname;
	}
	
}