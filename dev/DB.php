<?php
	/**
	* @author Floris Bollen <floris@florisweb.tk>
	* @version 1.0
	* @license MIT
	*/


	/**
	* This packet includes a database wrapper under the name of '$DB', containing the following methods:
	* 
	* @method connect
	* String @param DBName
	* @return 							Database wrapper object
	*/

	/**
	* @method execute
	* String @param sql:				Your SQL-statement, containing '?', for parameters
	* Array @param parameters:			The parameters referenced in the sql-statement. 
	* SQLConnectionobject @param conn:	The connection with the DB on which you want to execute the given quarry
	* @return 							Returns the SQL return value of your quarry
	*/

	/**
	* @var connections:					Conntains the DBWrapperObjects in an array, accessable via connections["DBName"]
	*/

		/** 
		* DBWrapperObject
		*
		* @method execute
		* String @param sql:				Your SQL-statement, containing '?', for parameters
		* Array @param parameters:			The parameters referenced in the sql-statement. 
		* @return 							Returns the SQL return value of your quarry
		*/

		/**
		* @method getTableNames
		* Array @return 					Returns the names of all tables in the current database.
		*/

		/**
		* @method tableExists
		* String @param tableName:			The name of the Table from which you want to know the columns.
		* Boolean @return 					Returns whether the table exists or not.
		*/

		/**
		* @method getColumnNames
		* String @param tableName:			The name of the Table from which you want to know the columns.
		* Array @return 					Returns the names of all columns in the give table.
		*/







  	global $DB;
  	$DB = new _DB();

	class _DB 
	{
		private $USER 		= "eelekweb_pcmcweb";
		private $PASSWORD 	= "Ad15db62453681f581d9ea90a9f54526";
		private $HOST		= "localhost";

		public function connect($_DBName) {
			if (!$_DBName) return false;
			$_DBName = (String)$_DBName;

			$conn = new mysqli($this->HOST, $this->USER, $this->PASSWORD, $_DBName);
			if ($conn->connect_error) {
                error_log("MySQL connection error " . $conn->connect_error);
                return false;  
            } 

			$connectionObj = new _DB_connectionClass($conn, $this);
			return $connectionObj;
		}


		public function execute($_sql, $_parameters, $_conn) {
			$bind_paramParams = $this->verifyParameters($_sql, $_parameters);
			if (!$_conn || !$bind_paramParams) return false;

			if ($stmt = $_conn->prepare($_sql))
			{
				if (sizeof($bind_paramParams) > 1) 
				{
					$bind_paramParams = array_merge([$stmt], $bind_paramParams);
					for ($i = 2; $i < sizeof($bind_paramParams); $i++) $bind_paramParams[$i] = &$bind_paramParams[$i];
					call_user_func_array("mysqli_stmt_bind_param", $bind_paramParams);
				}

				$stmt->execute();
				$returnArr = array();
				$result = $stmt->get_result();
				$error = $stmt->error;
				$stmt->free_result();
				$stmt->close();
				
				if ($error) return $error;
				if (!$result) return true; // It's a set command and does not return any data
				while ($row = $result->fetch_assoc()) array_push($returnArr, $row);

				return $returnArr;
			}

			return false;
		}

			private function verifyParameters($_sql, $_parameters) {
				$paramCount = sizeof(explode("?", $_sql)) - 1;

				if ($paramCount !== sizeof($_parameters)) return false;
				$type = "";
				for ($i = 0; $i < $paramCount; $i++) $type .= "s";

				return array_merge([$type], $_parameters);
			}
	}


	class _DB_connectionClass
	{
	    private $conn;
	    private $DB;

	    public function __construct($_conn, $_DB) {
	        $this->conn = $_conn;
	        $this->DB = $_DB;
	    }
        
        public function close() {
            $this->conn->close();
        }

	    public function execute($_sql, $_parameters = array()) {return $this->DB->execute($_sql, $_parameters, $this->conn);}

	   	public function getTableNames() {
			$tableNames = array();
			$inpTableNames = $this->execute("SHOW TABLES");
			for ($i = 0; $i < sizeof($inpTableNames); $i++)
			{
				$tableObj = $inpTableNames[$i];
				$tableName = $tableObj[key($tableObj)];
				array_push($tableNames, $tableName);
			}

			return $tableNames;
		}

		public function tableExists($_tableName) {
			$tableNames = $this->getTableNames();
			return in_array((string)$_tableName, $tableNames);
		}
	
		public function getColumnNames($_tableName) {
			$columnNames = array();

			if (!$this->tableExists($_tableName)) return false;

			$inpColumns = $this->execute("SHOW COLUMNS FROM $DBName." . (string)$_tableName);

			for ($i = 0; $i < sizeof($inpColumns); $i++)
			{
				$columnName = (string)$inpColumns[$i]["Field"];
				array_push($columnNames, $columnName);
			}

			return $columnNames;
		}
	}


?>