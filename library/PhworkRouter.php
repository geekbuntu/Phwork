<?php
/**
 * @name PhworkRouter
 * @package t(mb)^Phwork
 * @description This is the router class in t(mb)^Phwork
**/
class PhworkRouter {
	////////////////////////////////////////////////////////////////////////
	/// Properties ////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////
	protected $sBaseUriPath = null;
	protected $sController  = null;
	protected $aQuery       = array();
	protected $aRequest     = array();
	protected $sView        = null;
	////////////////////////////////////////////////////////////////////////
	/// Constructor ///////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////
	/**
	 * This is our constructor which tells the system
	 * whether to auto route or to static route
	 * @access public
	 * @param string [$sBaseUriPath]
	 * @param bool [$bAutoRoute]
	 * @return PhworkRouter $this;
	**/
	public function __construct($sBaseUriPath = null, $bAutoRoute = true) {
		// Check for a base path
		if (empty($sBaseUriPath) === false) {
			// Set the base path
			$this->sBaseUriPath = (string) $sBaseUriPath;
		}
		// Check to see if we are auto-routing
		if ($bAutoRoute === true) {
			// Auto-Route the request
			return $this->autoRoute();
		} else {
			// Return instance
			return $this;
		}
	}
	////////////////////////////////////////////////////////////////////////
	/// Public ////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////
	public function autoRoute() {
		// Grab the request array 
		$this->loadRequest();
		// Load the Controller and Action
		$this->loadControllerAction();
		// Load the query
		$this->loadQuery();
		// Return instance
		return $this;
	}
	/**
	 * This method loads the controller and action from 
	 * the request array and sets them into the system
	 * @access public
	 * @return PhworkRouter $this
	**/
	public function loadControllerAction() {
		// Check for an empty key
		if (empty($this->aRequest[0])) {
			// Remove the empty key
			array_shift($this->aRequest);
		}
		// Check for the controller
		if (class_exists("{$this->aRequest[0]}Controller")) { // The controller is set, now check for a view
			// Set the controller
			$this->sController = (string) ucwords($this->aRequest[0]);
			// Remove the controller
			array_shift($this->aRequest);
			// Check for the view
			if ((method_exists(Phwork::Instance(ucwords(Phwork::Config('SystemSettings', 'DefaultController')).'Controller'), "{$this->aRequest[0]}View"))) { // The view is specified
				// Set the view
				$this->sView = (string) strtolower($this->aRequest[0]);
				// Remove the view
				array_shift($this->aRequest);
			} else { // Use the default view
				// Set the view
				$this->sView = (string) strtolower(Phwork::Config('SystemSettings', 'DefaultView'));
			}
		} elseif (method_exists(Phwork::Instance(ucwords(Phwork::Config('SystemSettings', 'DefaultController')).'Controller'), "{$this->aRequest[0]}View")) { // The controller is default
			// Set the controller
			$this->sController = (string) ucwords(Phwork::Config('SystemSettings', 'DefaultController'));
			// Set the view
			$this->sView       = (string) strtolower($this->aRequest[0]);
			// Remove the view
			array_shift($this->aRequest);
		} else { // Neither are set, so run the default controller and vie
			// Set the controller
			$this->sController = (string) ucwords(Phwork::Config('SystemSettings', 'DefaultController'));
			// Set the view
			$this->sView       = (string) strtolower(Phwork::Config('SystemSettings', 'DefaultController'));
		}
		// Return instance
		return $this;
	}
	/**
	 * This method loads the leftover request array
	 * into a nice name=value pairs
	 * @access public
	 * @return PhworkRouter $this
	**/
	public function loadQuery() {
		// Make sure the we have an even number of 
		// Check for leftovers
		if (empty($this->aRequest) === false) {
			// Loop through the leftovers
			for ($iParameter = 0; $iParameter < count($this->aRequest); $iParameter += 2) {
				// Make sure we have a key
				if ((empty($this->aRequest[$iParameter]) === false) && (empty($this->aRequest[($iParameter + 1)]) === false)) {
					// Set the query parameter
					$this->aQuery[$this->aRequest[$iParameter]] = (isset($this->aRequest[($iParameter + 1)]) ? $this->aRequest[($iParameter + 1)] : null);
				}
			}
		}
		// Aet the GET array to the query array
		$_GET = $this->aQuery;
		// Check for POST
		if (empty($_POST) === false) {
			// Loop through the POST data
			foreach ($_POST as $sName => $mValue) {
				// Add the POST data to the query
				$this->aQuery[$sName] = $mValue;
			}
		}
		// Check for GET
		if (empty($_GET) === false) {
			// Loop through the GET data
			foreach ($_GET as $sName => $mValue) {
				// Add the GET data to the query
				$this->aQuery[$sName] = $mValue;
			}
		}
		// Reset POST
		$_POST = $this->aQuery;
		// Return instance
		return $this;
	}
	/**
	 * This method parses the REQUEST_URI into an array
	 * @access public
	 * @return PhworkRouter $this
	**/
	public function loadRequest() {
		// Check for a query string
		if (empty($_SERVER['QUERY_STRING']) === false) {
			// Remove the query string from the reques
			$_SERVER['REQUEST_URI'] = str_replace("?{$_SERVER['QUERY_STRING']}", null, $_SERVER['REQUEST_URI']);
		}
		// Check for a preset base path
		if (empty($this->sBaseUriPath) === false) {
			// Remove the the base path
			$sRequest = (string) str_replace($this->sBaseUriPath, null, $_SERVER['REQUEST_URI']);
		} else {
			// Set the request URI
			$sRequest = (string) $_SERVER['REQUEST_URI'];
		}
		// Load the URI into the system
		$this->aRequest = (array) explode('/', $sRequest);
		// Return the request
		return $this;
	}
	////////////////////////////////////////////////////////////////////////
	/// Getters ///////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////
	/**
	 * This method grabs the controller
	 * name from the current router
	 * @access public
	 * @return string
	**/
	public function getController() {
		// Return the controller
		return $this->sController;
	}
	/**
	 * This method grabs the query
	 * from the current router
	 * @access public
	 * @return array
	**/
	public function getQuery() {
		// Return the query
		return $this->aQuery;
	}
	/**
	 * This method grabs the view
	 * from the current router
	 * @access public
	 * @return string
	**/
	public function getView() {
		// Return the view
		return $this->sView;
	}
}
