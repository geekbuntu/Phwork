<?php
abstract class PhworkObjectMapper {
	////////////////////////////////////////////////////////////////////////
	/// Properties ////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////
	
	////////////////////////////////////////////////////////////////////////
	/// Constructor ///////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////
	
	////////////////////////////////////////////////////////////////////////
	/// Public ////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////
	/**
	 * This method takes an array
	 * and translates it into a
	 * new object map
	 * @access public
	 * @param array $aOptions
	 * @return PhworkObjectMapper $this
	**/
	public function autoSetup($aOptions) {
		// Grab the methods in this class
		$aMethods = get_class_methods($this);
		// Iterate through the options
		foreach ($aOptions as $sName => $mValue) {
			// Setup the method
			$sMethod = 'get'.substr($sName, 1);
			// Verify that the method exists
			if (in_array($sMethod, $aMethods)) {
				// Call the method
				$this->$sMethod($mValue);
			}
		}
		// Return instance
		return $this;
	}
	////////////////////////////////////////////////////////////////////////
	/// Getters ///////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////
	/**
	 * This is our automagic getter
	 * @throws Exception
	 * @access public
	 * @param string $sName
	 * @return mixed
	**/
	public function __get($sName) {
		// Setup the method name
		$sMethod = 'get'.substr($sName, 1);
		// Make sure the method exists
		if (method_exists($this, $sMethod) === false) {
			// Throw an exception
			throw new Exception('Invalid Message property');
		}
		// Call the method
		return $this->$sMethod();
	}
	////////////////////////////////////////////////////////////////////////
	/// Setters ///////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////
	/**
	 * This is our automagic setter
	 * @throws Exception
	 * @access public
	 * @param string $sName
	 * @param array|bool|float|integer|mixed|object $mValue
	 * @return mixed
	**/
	public function __set($sName, $mValue) {
		// Setup the method name
		$sMethod = 'get'.substr($sName, 1);
		// Make sure the method exists
		if (method_exists($this, $sMethod) === false) {
			// Throw an exception
			throw new Exception('Invalid Message property');
		}
		// Call the method
		return $this->$sMethod($mValue);
	}
}
