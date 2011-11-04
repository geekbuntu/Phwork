<?php
	/**
	 * @name PhworkPage
	 * @package Phwork
	 * @description This class sets up page data
	**/
	class PhworkPage {
		////////////////////////////////////////////////////////////////////////
		//////////      The Properties    /////////////////////////////////////
		//////////////////////////////////////////////////////////////////////
		protected $sBodyClass   = null;
		protected $sContent     = null;
		protected $aJavaScripts = null;
		protected $aMetaTags    = null;
		protected $aStyleSheets = null;
		protected $sTitle       = null;
		////////////////////////////////////////////////////////////////////////
		//////////      Constructor    ////////////////////////////////////////
		//////////////////////////////////////////////////////////////////////
		/**
		 * This is our constructor
		 * @return PhworkPage $this
		**/
		public function __construct() {
			// Return instance
			return $this;
		}
		////////////////////////////////////////////////////////////////////////
		//////////      Setters    ////////////////////////////////////////////
		//////////////////////////////////////////////////////////////////////
		/**
		 * This method sets the classes to the body tag
		 * @param string $sClass is the class(es) to append to the body
		 * @return PhworkPage $this
		**/
		public function setBodyClass($sClass) {
			// Set the body's class
			$this->sBodyClass = (string) $sClass;
			// Return instance
			return $this;
		}
		/**
		 * This method sets the content body of the page
		 * @access public
		 * @param string $sContent
		 * @return PhworkPage $this
		**/
		public function setContent($sContent) {
			// Set the content body
			$this->sContent = (string) $sContent;
			// Return instance
			return $this;
		}
		/**
		 * This methos sets the javascripts the caller
		 * wishes to load when the page loads
		 * @param array $aJavaScripts
		 * @return PhworkPage $this
		**/
		public function setJavascript(array $aJavaScripts) {
			// Set our javascripts
			$this->aJavaScripts = (array) $aJavaScripts;
			// Return instance
			return $this;
		}
		/**
		 * This methos sets the meta tags the caller
		 * wishes to load when the page loads
		 * @param array $aMetaTags
		 * @return PhworkPage $this
		**/
		public function setMetaTags(array $aMetaTags) {
			// Set our javascripts
			$this->aMetaTags = (array) $aMetaTags;
			// Return instance
			return $this;
		}
		/**
		 * This method sets the stylesheets the caller
		 * wishes to load when the page loads
		 * @param array $aStyleSheets
		 * @return PhworkPage $this
		**/
		public function setStylesheets(array $aStyleSheets) {
			// Set our stylesheets
			$this->aStyleSheets = (array) $aStyleSheets;
			// Return instance
			return $this;
		}
		/**
		 * This method sets the page title to what the caller
		 * wishes it to be
		 * @param string $sPageTitle
		 * @return PhworkPage $this
		**/
		public function setTitle($sPageTitle) {
			// Set our page's title
			$this->sTitle = (string) '.:'.Phwork::Config('SystemSettings', 'Namespace').' - '.$sPageTitle.':.';
			// Return Instance
			return $this;
		}
		////////////////////////////////////////////////////////////////////////
		//////////      Getters    ////////////////////////////////////////////
		//////////////////////////////////////////////////////////////////////
		/**
		 * This method retreives the class(es) to append to the body tag
		 * @return string
		**/
		public function getBodyClass() {
			// Return the body class(es)
			return $this->sBodyClass;
		}
		/**
		 * This method grabs the current page's content
		 * @access public
		 * @return string
		**/
		public function getContent() {
			// Return the content body
			return $this->sContent;
		}
		/**
		 * This method generates the html for
		 * each of the javascript files to load
		 * @return string
		**/
		public function getJavascript() {
			// Check to see if we need to load
			// any javascripts
			if (is_null($this->aJavaScripts)) {
				// Return null
				return null;
			// Load stylesheets
			} else {
				// Html placeholder
				$sHtml = (string) '';
				// Loop through each of the scripts
				foreach ($this->aJavaScripts as $sScript) {
					// Generate the html and append
					// it to the return string
					$sHtml .= (string) Phwork::Instance('PhworkHtml')->generateScript('text/javascript', $sScript)->getHtml();
				}
				// Return the html string
				return $sHtml;
			}
		}
		/**
		 * This method generates the html for
		 * each of the meta tags to load
		 * @return string
		**/
		public function getMetaTags() {
			// Check to see if we need to load
			// any meta tags
			if (is_null($this->aMetaTags)) {
				// Return null
				return null;
			// Load stylesheets
			} else {
				// Html placeholder
				$sHtml = (string) '';
				// Loop through each of the meta tags
				foreach ($this->aMetaTags as $sName => $sContent) {
					// Generate the html and append
					// it to the return string
					$sHtml .= (string) Phwork::Instance('PhworkHtml')->generateMetaTag($sName, $sContent)->getHtml();
				}
				// Return the html string
				return $sHtml;
			}
		}
		/**
		 * This method generates the html for
		 * each of the stylesheets to load
		 * @return string
		**/
		public function getStylesheets() {
			// Check to see if we need to load
			// any stylesheets
			if (is_null($this->aStyleSheets)) {
				// Return null
				return null;
			// Load stylesheets
			} else {
				// Html placeholder
				$sHtml = (string) '';
				// Loop through each of the stylesheets
				foreach ($this->aStyleSheets as $sStyleSheet) {
					// Generate the html and append
					// it to the return string
					$sHtml .= (string) Phwork::Instance('PhworkHtml')->generateLink('stylesheet', 'text/css', $sStyleSheet)->getHtml();
				}
				// Return the html string
				return $sHtml;
			}
		}
		/**
		 * This method returns the current page title
		 *
		 * @return string @property sTitle is the current page title
		**/
		public function getTitle() {
			// Return the page title
			return $this->sTitle;
		}
	}
