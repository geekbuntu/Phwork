<?php

abstract class PhworkController {
	////////////////////////////////////////////////////////////////////////
	/// Properties ////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////
	protected $oFw     = null;
	protected $oPage   = null;
	protected $aParams = array();
	protected $oView   = null;
	////////////////////////////////////////////////////////////////////////
	/// Constructor ///////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////
	/**
	 * This method sets up the controller and
	 * provides it with an instance of Phwork
	 * @access public
	 * @param Phwork $oFw
	 * @return PhworkController $this
	**/
	public function __construct(Phwork $oFw) {
		// Set the instance of Phwork
		$this->oFw     = $oFw;
		// Set the page object
		$this->oPage   = Phwork::Instance('PhworkPage');
		// Set the parameters
		if (is_object(Phwork::getInstance()->getRouter())) {
			$this->aParams = Phwork::getInstance()->getQuery();
		}
		// Set the view object
		$this->oView   = Phwork::Instance('PhworkView');
		// Return instance
		return $this;
	}
	////////////////////////////////////////////////////////////////////////
	/// Getters ///////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////
	/**
	 * This method returns the page object
	 * @return PhworkPage
	**/
	public function getPage() {
		// Return the page object
		return $this->oPage;
	}
	/**
	 * This method grabs the query parameters from
	 * the base Phwork class instance and returns
	 * a specific parameter to the caller
	 * @access public
	 * @param string $sKey
	 * @return mixed
	**/
	public function getParameter($sKey) {
		// Check for the key
		if (empty($this->aParams[$sKey]) === false) {
			// Return the parameter
			return $this->aParams[$sKey];
		}
		// Inevitably return false
		return false;
	}
	/**
	 * This method returns to view object
	 * @access public
	 * @return PhworkView
	**/
	public function getView() {
		// Return the view object
		return $this->oView;
	}
}
