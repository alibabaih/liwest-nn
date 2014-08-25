<?php

/**
* GK Cache plugin
* @Copyright (C) 2009-2011 Gavick.com
* @ All rights reserved
* @ Joomla! is Free Software
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version $Revision: GK4 1.0 $
**/

defined( '_JEXEC' ) or die();
jimport( 'joomla.plugin.plugin' );

class plgSystemPlg_GKCache extends JPlugin {
	
	function __construct( &$subject ) {
		parent::__construct( $subject );
	}
	// Create a trigger of the onBeforeCache event
	function onAfterRender() {
		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger("onBeforeCache");
	}
}

/* EOF */