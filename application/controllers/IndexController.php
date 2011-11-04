<?php
class IndexController extends PhworkController {
	////////////////////////////////////////////////////////////////////////
	/// Views /////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////
	/**
	 * This view is the default landing page
	 * of the website
	 * @access public
	 * @return IndexController $this
	**/
	public function indexView() {
		// Set the page title
		$this->oPage->setTitle('Welcome!');
		// Set a global property
		$this->oView->sSomething = 'Something';
		// Return instance
		return $this;
	}
	/**
	 * This view displays the default phpinfo() output
	 * @access public
	 * @return IndexController $this
	**/
	public function infoView() {
		// Return instance
		return $this;
	}
}
