<?php
/*------------------------------------------------------------------------
# com_zhyandexmap - Zh YandexMap
# ------------------------------------------------------------------------
# author    Dmitry Zhuk
# copyright Copyright (C) 2011 zhuk.cc. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
# Websites: http://zhuk.cc
# Technical Support Forum: http://forum.zhuk.cc/
-------------------------------------------------------------------------*/
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * View class for the ZhYandexMap Component
 */
class ZhYandexMapViewZhYandexMap extends JView
{
	// Overwriting JView display method
	function display($tpl = null) 
	{
		// Assign data to the view
		$this->item = $this->get('Item');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) 
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}


		// Map API Key
		$mapapikey = $this->get('APIKey');
		$this->assignRef( 'mapapikey',	$mapapikey );
		$mapapiversion = $this->get('APIVersion');
		$this->assignRef( 'mapapiversion',	$mapapiversion );

		// Map markers
		$markers = $this->get('Markers');
		$this->assignRef( 'markers',	$markers );

		// Map markergroups
		$markergroups = $this->get('MarkerGroups');
		$this->assignRef( 'markergroups',	$markergroups );

		// Map routers
		$routers = $this->get('Routers');
		$this->assignRef( 'routers',	$routers );

		$licenseinfo = $this->get('LicenseInfo');
		$this->assignRef( 'licenseinfo',	$licenseinfo );

		// Map paths
		$paths = $this->get('Paths');
		$this->assignRef( 'paths',	$paths );

		$mapcompatiblemode = $this->get('CompatibleMode');
		$this->assignRef( 'mapcompatiblemode',	$mapcompatiblemode );
		
		$mapcompatiblemodersf = $this->get('CompatibleModeRSF');
		$this->assignRef( 'mapcompatiblemodersf',	$mapcompatiblemodersf );

		// Map types
		$maptypes = $this->get('MapTypes');
		$this->assignRef( 'maptypes',	$maptypes );

		$externalmarkerlink = $this->get('ExternalMarkerLink');
		$this->assignRef( 'externalmarkerlink',	$externalmarkerlink );
		
		// Display the view
		parent::display($tpl);
	}
}
