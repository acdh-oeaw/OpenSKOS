<?php


/**
 * 
 * @author martinsnijders
 *
 */
class SolrConfig {
	
	private static $theInstance = null;
	
	private static $host;
	private static $port;
	private static $context;
	
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
			self::$host = $ini_array["resources.solr.host"];
			self::$port = $ini_array["resources.solr.port"];
			self::$context = $ini_array["resources.solr.context"];
		}
		catch(Exception $ex) {
			trigger_error("Exception while accessing config members for solr.", $ex->getTraceAsString());
		}
		
		if (!isset(self::$host) OR !isset(self::$port) OR !isset(self::$context)) {
			trigger_error("SOLR configuration could not be (fully) read. host= " .self::$host." ,port= " . self::$port . " ,context= " . self::$context, E_USER_ERROR);
		}
	}
	
	public function getHost() {
		return self::$host;
	}
	
	public function getPort() {
		return self::$port;
	}
	
	public function getContext() {
		return self::$context;
	}
	
}