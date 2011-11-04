<?php
/**
 * @name Phwork
 * @package Phwork
 * @description This is the primary class in which t(mb)^Phwork operates
**/
class Phwork {
	////////////////////////////////////////////////////////////////////////
	/// Constants /////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////
	const BERKLEY   = 1;
	const MONGO     = 2;
	const MYSQL     = 3;
	const ORACLE    = 4;
	const PGSQL     = 5;
	const SQLITE    = 6;
	const SQLITE2   = 7;
	const SQLITE3   = 8;
	const SQLITEMEM = 9;
	////////////////////////////////////////////////////////////////////////
	/// Static Properties /////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////
	protected static $aConfiguration = array();
	protected static $aErrors        = array();
	protected static $oInstance      = null;
	protected static $aInstances     = array();
	////////////////////////////////////////////////////////////////////////
	/// Properties ////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////
	protected $oPage                 = null;
	protected $aQuery                = array();
	protected $oRouter               = null;
	////////////////////////////////////////////////////////////////////////
	/// Singleton /////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////
	/**
	 * This sets the singleton pattern instance
	 * @return Phwork self
	**/
	public static function setInstance() {
		// Try to set an instance
		try {
			// Set instance to new self
			self::$oInstance = new self();
		// Catch any exceptions
		} catch (Exception $oException) {
			// Set error string
			sef::AddError($oException->getMessage());
		}
		// Return instance of class
		return self::$oInstance;
	}
	/**
	 * This gets the singleton instance
	 * @return Phwork self
	**/
	public static function getInstance() {
		// Check to see if an instance has already
		// been created
		if (is_null(self::$oInstance)) {
			// If not, return a new instance
			return self::setInstance();
		} else {
			// If so, return the previously created
			// instance
			return self::$oInstance;
		}
	}
	////////////////////////////////////////////////////////////////////////
	/// Public Static Methods /////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////
	/**
	 * This method adds an error message to the
	 * pool of error messages
	 * @static
	 * @access public
	 * @param string $sText
	 * @return self
	**/
	public static function AddError($sText) {
		// Add the error to the list
		self::$aErrors[] = $sText;
		// Return instance
		return self::$oInstance;
	}
	/**
	 * This method loads the configuration and
	 * returns the caller specified keys
	 * @param string [$sKey]
	 * @return mixed
	 **/
	public static function Config() {
		// Check to see if the configuration
		// has already been loaded into memory
		if (empty(self::$aConfiguration) === true) {
			// Load the configuration file
			self::$aConfiguration = (string) file_get_contents(APPLICATIONPATH.'/configurations/application.json');
			// Decode the configuration file
			self::$aConfiguration = (array) json_decode(self::$aConfiguration, true);
		}
		// Check for arguments
		if (func_num_args() > 0) {
			// Set our configuration placeholder
			$aConfiguration = self::$aConfiguration;
			// Loop through the arguments
			foreach (func_get_args() as $sKey) {
				// Make sure the key exists
				if (isset($aConfiguration[$sKey]) === true) {
					// Set the configuration
					$aConfiguration = $aConfiguration[$sKey];
				}
			}
			// Return the configuration
			return $aConfiguration;
		}
		// Return the configurations
		return self::$aConfiguration;
	}
	/**
	 * This method prints out a variable 
	 * in a pretty, human readable, format
	 * @static
	 * @access public
	 * @param mixed [$mVariable]
	 * @return Phwork self
	**/
	public static function Debug() {
		// Check for arguments
		if (func_num_args() > 0) {
			// Echo the HTML
			echo('<pre>');
			// Loop through the arguemnts
			foreach (func_get_args() as $mVariable) {
				// Dump the data
				var_dump($mVariable);
			}
			// Close the HTML
			echo('</pre>');
		}
		// Return instance
		return self::$oInstance;
	}
	/**
	 * This method runs all the actions necessary
	 * to decrypt the encrypted string.
	 * @static
	 * @access public
	 * @param string $sHash
	 * @return string
	**/
	public static function Decrypt($sHash) {
		// Decode the hash
		$sHash = base64_decode($sHash);
		// Decrypt the hash
		$sSource = (string) rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5(self::Config('SystemSettings', 'SecretHash')), $sHash, MCRYPT_MODE_CBC, md5(md5(self::Config('SystemSettings', 'SecretHash')))), "\0");
		// Return the decrypted string
		return $sSource;
	}
	/**
	 * This method runs all the actions necessary
	 * to encrypt the string
	 * @static
	 * @access public
	 * @param string $sSource
	 * @return string
	**/
	public static function Encrypt($sSource) {
		// Encrypt the source
		$sHash = (string) mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5(self::Config('SystemSettings', 'SecretHash')), $sSource, MCRYPT_MODE_CBC, md5(md5(self::Config('SystemSettings', 'SecretHash'))));
		// Encode and return the hash
		return base64_encode($sHash);
	}
	/**
	 * This method grabs an instance of the desired class
	 * and stored it for quick access later, this serves
	 * as a quick and dirty auto-singleton
	 * @static
	 * @access public
	 * @param string $sClass
	 * @param array [$aArguments]
	 * @return object|bool
	**/
	public static function Instance() {
		// Make sure we have at least one
		// argument for the class name
		if (func_num_args() < 1) {
			// Set the system error
			return false;
		}
		// Set the arguments
		$aArguments = func_get_args();
		// Set the class name
		$sClass     = $aArguments[0];
		// Remove the class name from the arguments
		array_shift($aArguments);
		// Check for an instance
		if (empty(self::$aInstances[$sClass]) === false) {
			// Return the existing instance
			return self::$aInstances[$sClass];
		}
		// Make sure the class exists
		if (class_exists($sClass)) {
			// Check for arguments that need to be passed
			if (empty($aArguments[0]) === false) {
				// Set the instance
				$oReflect = new ReflectionClass($sClass);
				// Call the method and sent the arguments
				self::$aInstances[$sClass] = $oReflect->newInstanceArgs($aArguments[0]);
			} else {
				// Set the instance
				self::$aInstances[$sClass] = new $sClass();
			}
			// Return the instance
			return self::$aInstances[$sClass];
		}
		// Inevitably return failure
		return false;
	}
	////////////////////////////////////////////////////////////////////////
	/// Public Methods ////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////
	/**
	 * This method auto-loads classes
	 * @access public
	 * @param string $sClass
	 * @return bool
	**/
	public function AutoLoadClass($sClass) {
		// Correct the class name, replace
		// spaces with / to indicate subdirectories
		$sClass = str_replace('_','/', $sClass);
		// Set the full file path
		$sFile  = LIBRARYPATH."/{$sClass}.php";
		// Double check that the
		// file actually exists
		if (file_exists($sFile)) {
			// If so, load it
			require_once($sFile);
			// All is well,
			// return true
			return true;
		}
		// Check for controllers to load
		$sFile = APPLICATIONPATH."/controllers/{$sClass}.php";
		// Double check that the
		// file actually exists
		if (file_exists($sFile)) {
			// If so, load it
			require_once($sFile);
			// All is well,
			// return true
			return true;
		}
		// Check for Models to load
		$sFile = APPLICATIONPATH."/models/{$sClass}.php";
		// Double check that the
		// file actually exists
		if (file_exists($sFile)) {
			// If so, load it
			require_once($sFile);
			// All is well,
			// return true
			return true;
		}
		// There was an error,
		// return false
		return false;
	}
	/**
	 * This method loads and parses
	 * a file and stores the results 
	 * into a variable as a string
	 * @access public
	 * @param string $sFile
	 * @param bool [$bPrint]
	 * @return string
	**/
	public function loadFile($sFile, $bPrint = false) {
		// Make sure the file exists
		if ((file_exists($sFile) === true) && (is_file($sFile) === true)) {
			// Start the output buffer
			ob_start();
			// Load the file
			require_once($sFile);
			// Check to see if we need to
			// print the output or just
			// return it to the caller
			if ($bPrint === true) {
				// Print the output
				return print(ob_get_clean());
			} else {
				// Return the contents
				// and close the buffer
				return ob_get_clean();
			}
		}
		// Inevitably return failure
		return false;
	}
	/**
	 * This method starts and runs the application
	 * Phwork, bootstrapping the system
	 * @access public
	 * @param string [$sBaseUriPath]
	 * @return Phwork $this
	**/
	public function run($sBaseUriPath = null) {
		// Instantiate the router
		// and route the request
		$this->oRouter = self::Instance('PhworkRouter', array($sBaseUriPath));	
		// Start the next step
		return $this->runController();
	}
	/**
	 * This method executes a controller class and
	 * stores the object into the system
	 * @access public
	 * @return Phwork $this
	**/
	public function runController() {
		// Set the controller class
		$sController = (string) "{$this->oRouter->getController()}Controller";
		// Check for the controller class
		if (class_exists($sController) === true) {
			// Instantiate the controller
			self::Instance($sController, $this);
			// Now execute the view
			return $this->runView();
		} else {
			// Add the error
			self::AddError(str_replace(':sController', $sController, self::Config('ErrorMessages', 'InvalidController')));
		}
	}
	public function runError() {

	}
	/**
	 * This method renders the layout 
	 * and the view to the user
	 * @access public
	 * @return Phwork $this
	**/
	public function runView() {
		// Set the layout path
		$sLayout     = (string) APPLICATIONPATH.'/layouts/'.$this->getLayout();
		// Make sure the layout exists
		if (file_exists($sLayout) === true) {
			// Set the controller class
			$sController = (string) "{$this->oRouter->getController()}Controller";
			// Set the view file
			$sView = (string) APPLICATIONPATH.'/views/'.strtolower($this->oRouter->getController()).'/'.$this->oRouter->getView().self::Config('SystemSettings', 'DefaultExtension');
			// Set the view method
			$sViewMethod = (string) $this->oRouter->getView().'View';
			// Make sure the controller method exits
			if (method_exists(self::$aInstances[$sController], $sViewMethod) === true) {
				// Try to execute the method
				if (call_user_func(array(self::$aInstances[$sController], $sViewMethod))) {
					// Set the view
					$this->oView = self::$aInstances[$sController]->getView();
					// Loop through the view properties
					foreach ($this->oView as $sName => $mValue) {
						// Add the property to this scope
						$this->{$sName} = $mValue;
					}
					// Set the page
					$this->oPage = self::$aInstances[$sController]->getPage();
					// Set the page body
					$this->oPage->setContent($this->loadFile($sView, false));
					// Render the layout
					$this->loadFile($sLayout, true);
				}
			}
		}
		// Return instance
		return $this;
	}
	////////////////////////////////////////////////////////////////////////
	/// Protected Methods /////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////
	
	////////////////////////////////////////////////////////////////////////
	/// Getters ///////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////
	/**
	 * This method grabs the controller
	 * name from the current instance
	 * @access public
	 * @return string
	**/
	public function getController() {
		// Return the controller
		return (string) $this->oRouter->getController();
	}
	/**
	 * This method grabs the controller
	 * class from the current instance
	 * @return string
	**/
	public function getControllerClass() {
		// Return the controller class
		return (string) "{$this->oRouter->getController()}Controller";
	}
	/**
	 * This method grabs the layout
	 * file name from the current instance
	 * @access public
	 * @return string
	**/
	public function getLayout() {
		// Return the layout
		return (string) self::Config('SystemSettings', 'DefaultLayout').self::Config('SystemSettings', 'DefaultExtension');
	}
	/**
	 * This method returns the query parameters
	 * in an array format to the caller
	 * @access public
	 * @return array
	**/
	public function getQuery() {
		// Return our parameters
		return $this->oRouter->getQuery();
	}
	/**
	 * This method returns the router
	 * object from the current instance
	 * @access public
	 * @return PhworkRouter
	**/
	public function getRouter() {
		// Return the router object
		return $this->oRouter;
	}
	/**
	 * This method grabs the view
	 * name from the current instance
	 * @access public
	 * @return string
	**/
	public function getView() {
		// Return the view
		return (string) $this->oRouter->getView();
	}
	/**
	 * This method grabs the view controller
	 * method from the current instance
	 * @access public
	 * @return string
	**/
	public function getViewMethod() {
		// Return the view method
		return (string) "{$this->oRouter->getView()}View";
	}
	////////////////////////////////////////////////////////////////////////
	/// Setters ///////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////

}
