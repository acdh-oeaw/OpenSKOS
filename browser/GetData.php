<?php

/**
 * 
 * @author martinsnijders
 *
 */
class GetData {
	
	private static $theInstance = null;
	private static $collections = null;
	private static $users = null;
	private static $tenants = null;
	
	public static function getInstance()
	{
		if (!isset(static::$theInstance)) {
			static::$theInstance = new static;
		}
		return static::$theInstance;
	}
	
	protected function __construct() {
	}
	
	/*
	 * Returns an key value array of collection id's with collection codes
	 */
	public function getCollections($host, $user, $password, $dbname) {
		if (!isset(self::$collections)) {
			$con=mysqli_connect($host,$user,$password,$dbname);
			// Check connection
			if (mysqli_connect_errno()) {
			  trigger_error("Failed to connect to MySQL: " . mysqli_connect_error());
			}
			
			$result = mysqli_query($con,"SELECT * FROM collection");
			
			$toReturn = array();
			while($row = mysqli_fetch_array($result)) {
	 		  $toReturn[$row['id']] = $row['code'];
			}
	
			mysqli_close($con);
			self::$collections = $toReturn;
		}
		return self::$collections;
	}
	
	/*
	 * Returns an key value array of user id's with user names
	 */
	public function getUsers($host, $user, $password, $dbname) {
		if (!isset(self::$users)) {
			$con=mysqli_connect($host,$user,$password,$dbname);
			// Check connection
			if (mysqli_connect_errno()) {
				trigger_error("Failed to connect to MySQL: " . mysqli_connect_error());
			}
		
			$result = mysqli_query($con,"SELECT * FROM user");
		
			$toReturn = array();
			while($row = mysqli_fetch_array($result)) {
				$toReturn[$row['id']] = $row['name'];
			}
		
			mysqli_close($con);
			self::$users = $toReturn;
		}
		return self::$users;
	}
	
	/*
	 * Returns an key value array of user id's with user names
	 */
	public function getTenants($host, $user, $password, $dbname) {
		if (!isset(self::$tenants)) {
			$con=mysqli_connect($host,$user,$password,$dbname);
			// Check connection
			if (mysqli_connect_errno()) {
				trigger_error("Failed to connect to MySQL: " . mysqli_connect_error());
			}
		
			$result = mysqli_query($con,"SELECT * FROM tenant");
		
			$toReturn = array();
			while($row = mysqli_fetch_array($result)) {
				$toReturn[$row['code']] = $row['name'];
			}
		
			mysqli_close($con);
			self::$tenants = $toReturn;
		}
		return self::$tenants;
	}

}