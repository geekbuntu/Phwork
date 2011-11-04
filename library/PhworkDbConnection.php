<?php 

class PhworkDbConnection {
	////////////////////////////////////////////////////////////////////////
	/// Properties ////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////
	protected $aAllResults       = array();
	protected $oCurrentStatement = null;
	protected $sDatabase         = null;
	protected $iDatabaseType     = 0;
	protected $oDbc              = null;
	protected $sHost             = null;
	protected $sPassword         = null;
	protected $iPort             = 0;
	protected $sUsername         = null;
	////////////////////////////////////////////////////////////////////////
	/// Constructor ///////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////
	/**
	 * This is our constructor, it aids in
	 * the setting up of the connection
	 * @access public
	 * @param string [$sDatabase]
	 * @param integer [$iDatabaseType]
	 * @param string [$sHost]
	 * @param string [$sPassword]
	 * @param integer [$iPort]
	 * @param string [$sUsername]
	 * @return PhworkDbConnection $this
	**/
	public function __construct() {
		// Check for arguments
		if (func_num_args() > 0) {
			// Set the database
			$this->sDatabase     = func_get_arg(0);
			// Set the database type
			$this->iDatabaseType = func_get_arg(1);
			// Set the host
			$this->sHost         = func_get_arg(2);
			// Set the Password
			$this->sPassword     = func_get_arg(3);
			// Set the Port
			$this->iPort         = func_get_arg(4);
			// Set the Username
			$this->sUsername     = func_get_arg(5);
		} else {	// Autoload the connection data
			// Set the database
			$this->sDatabase     = Phwork::Config('Database', 'Name');
			// Set the database type
			$this->iDatabaseType = constant('Phwork::'.Phwork::Config('Database', 'Type'));
			// Set the host
			$this->sHost         = Phwork::Config('Database', 'Host');
			// Set the Password
			$this->sPassword     = Phwork::Config('Database', 'Password');
			// Set the Port
			$this->iPort         = intval(Phwork::Config('Database', 'Port'));
			// Set the Username
			$this->sUsername     = Phwork::Config('Database', 'Username');
		}
		// Determine the DB Type
		switch ($this->iDatabaseType) {
			// MySQL
			case Phwork::MYSQL     : 
				// Set the DSN
				$sDsn = (string) "mysql:host={$this->sHost};dbname={$this->sDatabase}";
				// Done
				break;
			// Oracle
			case Phwork::ORACLE    : 
				// Set the DSN
				$sDsn = (string) "oci:dbname={$this->sDatabase}";
				// Done
				break;
			// PgSQL
			case Phwork::PGSQL     : 
				// Set the DSN
				$sDsn = (string) "pgsql:host={$this->sHost};dbname={$this->sDatabase}";
				// Done
				break; 
			// SQLite
			case Phwork::SQLITE    : 
				// Set the DSN
				$sDsn = (string) "sqlite:{$this->sDatabase}";
				// Done
				break;
			// SQLite 2
			case Phwork::SQLITE2   : 
				// Set the DSN
				$sDsn = (string) "sqlite2:{$this->sDatabase}";
				// Done
				break; 
			// SQLite 3
			case Phwork::SQLITE3   : 
				// Set the DSN
				$sDsn = (string) "sqlite3:{$this->sDatabase}";
				// Done
				break;
			// SQLite Memory
			case Phwork::SQLITEMEM : 
				// Set the DSN
				$sDsn = (string) "sqlite::memory:";
				// Done
				break;
		}
		// Try to create the connection
		try {
			// Set the connection
			$this->oDbc = new PDO($sDsn, $this->sUsername, $this->sPassword);
			// Return instance
			return $this;
		} catch (PDOException $oException) {
			// Handle the error
			Phwork::getInstance()->runError($oException);
		}
		// Inevitably return failure
		return false;
	}
	////////////////////////////////////////////////////////////////////////
	/// Public ////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////
	/**
	 * This method checks for and
	 * executes a prepared statement
	 * @access public
	 * @return PhworkDbConnection $this
	**/
	public function executeStatement() {
		// Check for a prepared statement
		if (empty($this->oCurrentStatement) === false) {
			// Execute the statement
			$this->oCurrentStatement->execute();
			// Return instance
			return $this;
		}
		// Inevitably return false
		return false;
	}
	/**
	 * This method grabs all records
	 * returned for the current 
	 * executed statement
	 * @access public
	 * @param bool [$bStore]
	 * @return PhworkDbConnection $this
	**/
	public function fetchAll($bStore = false) {
		// Check for a statement
		if ($this->executeStatement()) {
			// Check to see if we need
			// to store the results
			if ($bStore === true) {
				// Fetch the results
				$this->aAllResults = $this->oCurrentStatement->fetchAll(PDO::FETCH_ASSOC);
				// Return the results
				return $this->aAllResults;
			} else {
				// Return the results
				return $this->oCurrentStatement->fetchAll(PDO::FETCH_ASSOC);
			}
		}
		// Inevitably return failure
		return false;
	}
	/**
	 * This method finds a specific row
	 * @access public
	 * @param string $sColumn
	 * @param mixed $mValue
	 * @return array|bool
	**/
	public function find($sColumn, $mValue) {
		// Check for a previous fetchAll
		if (empty($this->aAllResults) === false) {
			// Loop through the resutls
			for ($iRow = 0; $iRow < count($this->aAllResults); $iRow ++) {
				// Check for a match
				if ($this->aAllResults[$iRow][$sColumn] == $mValue) {
					// Return the row
					return $this->aAllResults[$iRow];
				}
			}
		} else {
			// No previous results, execute
			// the statement and rerun this method
			if ($this->fetchAll(true)) {
				// Loop through the resutls
				for ($iRow = 0; $iRow < count($this->aAllResults); $iRow ++) {
					// Check for a match
					if ($this->aAllResults[$iRow][$sColumn] == $mValue) {
						// Return the row
						return $this->aAllResults[$iRow];
					}
				}
			}
		}
		// Inevitably return failure
		return false;
	}
	/**
	 * This method helps prepare a SQL string
	 * @access public
	 * @param string $sSql
	 * @param array [$aData]
	 * @return PhworkDbConnection $this
	**/
	public function prepareQuery($sSql, $aData = array()) {
		// Create the statement
		$this->oCurrentStatement = $this->oDbc->prepare($sSql);
		// Check for data
		if (empty($aData) === false) {
			// Loop through the parameters
			foreach ($aData as $sName => $mValue) {
				// Bind the value
				$this->oCurrentStatement->bindValue((strpos($sName, ':') ? $sName : ":{$sName}"), $mValue);
			}
		}
		// Return instance
		return $this;
	}
	////////////////////////////////////////////////////////////////////////
	/// Protected /////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////
	
	////////////////////////////////////////////////////////////////////////
	/// Getters ///////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////
	
	////////////////////////////////////////////////////////////////////////
	/// Setters ///////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////
}
