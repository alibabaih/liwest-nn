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

$document	= JFactory::getDocument();

$map = $this->item;

$divmapheader ="";
$divmapfooter ="";

$currentUserInfo ="";
$allowUserMarker = 0;
$currentUserID = 0;

$scripttext = '';

// Change translation language and load translation
if (isset($map->lang) && $map->lang != "")
{
	$currentLanguage = JFactory::getLanguage();
	$currentLangTag = $currentLanguage->getTag();

	$currentLanguage->load('com_zhyandexmap', JPATH_SITE, $map->lang, true);	
	$currentLanguage->load('com_zhyandexmap', JPATH_COMPONENT, $map->lang, true);	
}

$compatiblemodersf = $this->mapcompatiblemodersf;
$compatiblemode = $this->mapcompatiblemode;

$credits ='';

if ($compatiblemodersf == "")
{
  $compatiblemodersf = 0;
}

if ($compatiblemode == "")
{
  $compatiblemode = 0;
}

if ($compatiblemodersf == 0)
{
	$document->addStyleSheet(JURI::root() .'administrator/components/com_zhyandexmap/assets/css/common.css');
}
else
{
	$document->addStyleSheet(JURI::root() .'components/com_zhyandexmap/assets/css/common.css');
}

$licenseinfo = $this->licenseinfo;
if ($licenseinfo == "")
{
  $licenseinfo = 102;
}

$externalmarkerlink = (int)$this->externalmarkerlink;

$custMapTypeList = explode(";", $map->custommaptypelist);
if (count($custMapTypeList) != 0)
{
	$custMapTypeFirst = $custMapTypeList[0];
}
else
{
	$custMapTypeFirst = 0;
}

if (isset($map->usermarkers) && (int)$map->usermarkers == 1) 
{
    $currentUser = JFactory::getUser();

    if ($currentUser->id == 0)
    {
		$currentUserInfo .= JText::_( 'COM_ZHYANDEXMAP_MAP_USER_NOTLOGIN' );
		$allowUserMarker = 0;
		$currentUserID = 0;

    }
    else
    {
		$currentUserInfo .= JText::_( 'COM_ZHYANDEXMAP_MAP_USER_LOGIN' ) .' '. $currentUser->name;
		$allowUserMarker = 1;
		$currentUserID = $currentUser->id;
    }
    
}
else
{
	$allowUserMarker = 0;
	$currentUserID = 0;
}


// if post data to load
if ($allowUserMarker == 1
 && isset($_POST['marker_action']))
{		
$scripttext .= '<script type="text/javascript">';
		$db = JFactory::getDBO();

		if (isset($_POST['marker_action']) && 
			($_POST['marker_action'] == "insert") ||
			($_POST['marker_action'] == "update") 
			)
		{

			$title = substr($_POST["markername"], 0, 249);
			if ($title == "")
			{
				$title = 'Placemark';
			}

			$markericon = substr($_POST["markerimage"], 0, 100);
			if ($markericon == "")
			{
				$markericon ='default#';
			}
			
			$description = $_POST["markerdescription"];
			$latitude = substr($_POST["markerlat"], 0, 100);
			$longitude = substr($_POST["markerlng"], 0, 100);
			$group = substr($_POST["markergroup"], 0, 100);
			$markercatid = substr($_POST["markercatid"], 0, 100);
			$markerid = (int)substr($_POST["markerid"], 0, 100);
			$markerhrefimage = substr($_POST["markerhrefimage"], 0, 500);
			
			if (isset($map->usercontact) && (int)$map->usercontact == 1) 
			{
				$contactid = substr($_POST["contactid"], 0, 100);
			}
			else
			{
				$contactid = '';
			}
			
			$contactDoInsert = 0;
			
			if (isset($map->usercontact) && (int)$map->usercontact == 1) 
			{
				$contact_name = substr($_POST["contactname"], 0, 100);
				$contact_position = substr($_POST["contactposition"], 0, 100);
				$contact_phone = substr($_POST["contactphone"], 0, 100);
				$contact_mobile = substr($_POST["contactmobile"], 0, 100);
				$contact_fax = substr($_POST["contactfax"], 0, 100);
				$contact_address = substr($_POST["contactaddress"], 0, 100);
				
				if (($contact_name != "") 
				  ||($contact_position != "")
				  ||($contact_phone != "")
				  ||($contact_mobile != "")
				  ||($contact_fax != "")
				  ||($contact_address != "")
					)
				{
					$contactDoInsert = 1;
				}
			}

			$newRow = new stdClass;
			
			if ($_POST['marker_action'] == "insert")
			{
				$newRow->id = NULL;
				$newRow->userprotection = 0;
				$newRow->actionbyclick = 1;
				
				if ((isset($map->usercontact) && (int)$map->usercontact == 1) 
				 &&($contactDoInsert == 1))
				{				
					$newRow->showcontact = 2;
				}
				else
				{				
					$newRow->showcontact = 0;
				}
			}
			else
			{
				$newRow->id = $markerid;

				if ((isset($map->usercontact) && (int)$map->usercontact == 1) 
				 &&($contactDoInsert == 1) && ((int)$contactid == 0))
				{				
					$newRow->showcontact = 2;
				}
				
			}
			
			// Data for Contacts - begin
			if ((isset($map->usercontact) && (int)$map->usercontact == 1) 
			  &&($contactDoInsert == 1))
			{
				$newContactRow = new stdClass;
				
				if ($_POST['marker_action'] == "insert")
				{
					$newContactRow->id = NULL;
					$newContactRow->published = (int)$map->usercontactpublished;
					$newContactRow->language = '*';
					$newContactRow->access = 1;
				}
				else
				{
					if ((int)$contactid == 0)
					{
						$newContactRow->id = NULL;
						$newContactRow->published = (int)$map->usercontactpublished;
						$newContactRow->language = '*';
						$newContactRow->access = 1;
					}
					else
					{
						$newContactRow->id = $contactid;
					}
				}
				
			}			
			// Data for Contacts - end
			
			// because it (quotes) escaped
			$newRow->title = str_replace('\\','', htmlspecialchars($title, ENT_NOQUOTES, 'UTF-8'));
			$newRow->description = str_replace('\\','', htmlspecialchars($description, ENT_NOQUOTES, 'UTF-8'));
			// because it escaped
			$newRow->latitude = htmlspecialchars($latitude, ENT_QUOTES, 'UTF-8');
			$newRow->longitude = htmlspecialchars($longitude, ENT_QUOTES, 'UTF-8');
			$newRow->mapid = $map->id;
			$newRow->icontype = htmlspecialchars($markericon, ENT_QUOTES, 'UTF-8');
			$newRow->published = (int)$map->usermarkerspublished;
			$newRow->createdbyuser = $currentUser->id;
			$newRow->markergroup = htmlspecialchars($group, ENT_QUOTES, 'UTF-8');
			$newRow->catid = htmlspecialchars($markercatid, ENT_QUOTES, 'UTF-8');
			$newRow->hrefimage = htmlspecialchars($markerhrefimage, ENT_QUOTES, 'UTF-8');

			if ((isset($map->usercontact) && (int)$map->usercontact == 1) 
			  &&($contactDoInsert == 1))
			{
				$newContactRow->name = str_replace('\\','', htmlspecialchars($contact_name, ENT_NOQUOTES, 'UTF-8'));
				if ($newContactRow->name == "")
				{
					$newContactRow->name = $newRow->title;
				}
				$newContactRow->con_position = str_replace('\\','', htmlspecialchars($contact_position, ENT_NOQUOTES, 'UTF-8'));
				$newContactRow->telephone = str_replace('\\','', htmlspecialchars($contact_phone, ENT_NOQUOTES, 'UTF-8'));
				$newContactRow->mobile = str_replace('\\','', htmlspecialchars($contact_mobile, ENT_NOQUOTES, 'UTF-8'));
				$newContactRow->fax = str_replace('\\','', htmlspecialchars($contact_fax, ENT_NOQUOTES, 'UTF-8'));
				$newContactRow->address = str_replace('\\','', htmlspecialchars($contact_address, ENT_NOQUOTES, 'UTF-8'));
			}
			
			if ($_POST['marker_action'] == "insert")
			{
				if ((isset($map->usercontact) && (int)$map->usercontact == 1) 
				  &&($contactDoInsert == 1))
				{
					$dml_contact_result = $db->insertObject( '#__contact_details', $newContactRow, 'id' );
					
					$newRow->contactid = $newContactRow->id;
				}

				$dml_result = $db->insertObject( '#__zhyandexmaps_markers', $newRow, 'id' );
			}
			else
			{
				if ((isset($map->usercontact) && (int)$map->usercontact == 1) 
				  &&($contactDoInsert == 1))
				{
					if (isset($newContactRow->id))
					{
						$dml_contact_result = $db->updateObject( '#__contact_details', $newContactRow, 'id' );
					}
					else
					{
						$dml_contact_result = $db->insertObject( '#__contact_details', $newContactRow, 'id' );
						$newRow->contactid = $newContactRow->id;
					}
				}

				$dml_result = $db->updateObject( '#__zhyandexmaps_markers', $newRow, 'id' );
			}
			
			if ((!$dml_result) || 
			    (isset($map->usercontact) && (int)$map->usercontact == 1 && ($contactDoInsert == 1) && (!$dml_result))
				)
			{
				//$this->setError($db->getErrorMsg());
				$scripttext .= 'alert("Error (Insert New Marker or Update): " + "' . $db->getEscaped($db->getErrorMsg()).'");';
			}
			else
			{
				$scripttext .= 'window.location = "'.JURI::current().'";'."\n";
				
				$new_id = $newRow->id;

			}
		}
		else if (isset($_POST['marker_action']) && $_POST['marker_action'] == "delete") 
		{

			$contactid = substr($_POST["contactid"], 0, 100);
			$markerid = substr($_POST["markerid"], 0, 100);
		
			if (isset($map->usercontact) && (int)$map->usercontact == 1) 
			{
			
				if ((int)$contactid != 0)
				{
					$query = $db->getQuery(true);

					$db->setQuery( 'DELETE FROM `#__contact_details` '.
					'WHERE `id`='.(int)$contactid);
					
					if (!$db->query()) {
						//$this->setError($db->getErrorMsg());
						$scripttext .= 'alert("Error (Delete Exist Marker Contact): " + "' . $db->getEscaped($db->getErrorMsg()).'");';
					}
				}
			}


			$query = $db->getQuery(true);

			$db->setQuery( 'DELETE FROM `#__zhyandexmaps_markers` '.
			'WHERE `createdbyuser`='.$currentUser->id.
			' and `id`='.$markerid);

			
			if (!$db->query()) {
				//$this->setError($db->getErrorMsg());
				$scripttext .= 'alert("Error (Delete Exist Marker): " + "' . $db->getEscaped($db->getErrorMsg()).'");';
			}
			else
			{
				$scripttext .= 'window.location = "'.JURI::current().'";'."\n";
			}
		}

$scripttext .= '</script>';

echo $scripttext;
}
else
{
// main part where not post data
	$mapVersion = "2.0";

	$apikey = $this->mapapikey;

	if (isset($map->lang) && ($map->lang != ""))
	{
		$apilang = $map->lang;
	}
	else
	{
		$apilang = 'ru-RU';
	}
	
	$scriptlink	= 'http://api-maps.yandex.ru/'.$mapVersion.'/?coordorder=longlat&amp;load=package.full&amp;lang='.$apilang;
	$loadmodules	='';
	$loadmodules_pmap = 0;

	if ($compatiblemodersf == 0)
	{
		$directoryIcons = 'administrator/components/com_zhyandexmap/assets/icons/';
		$imgpathIcons = JURI::root() .'administrator/components/com_zhyandexmap/assets/icons/';
		$imgpathUtils = JURI::root() .'administrator/components/com_zhyandexmap/assets/utils/';	
		$imgpath4size = JPATH_ADMINISTRATOR .'/components/com_zhyandexmap/assets/icons/';		
	}
	else
	{
		$directoryIcons = 'components/com_zhyandexmap/assets/icons/';
		$imgpathIcons = JURI::root() .'components/com_zhyandexmap/assets/icons/';
		$imgpathUtils = JURI::root() .'components/com_zhyandexmap/assets/utils/';		
		$imgpath4size = JPATH_SITE .'/components/com_zhyandexmap/assets/icons/';
	}


$fullWidth = 0;
$fullHeight = 0;



function parse_route_by_markers($markerId)
{
	if ((int)$markerId != 0)
	{
		$dbMrk = JFactory::getDBO();

		$queryMrk = $dbMrk->getQuery(true);
		$queryMrk->select('h.*')
			->from('#__zhyandexmaps_markers as h')
			->where('h.id = '.(int) $markerId);
		$dbMrk->setQuery($queryMrk);        
		$myMarker = $dbMrk->loadObject();
		
		if (isset($myMarker))
		{
			if ($myMarker->latitude != "" && $myMarker->longitude != "")
			{
				return '['.$myMarker->longitude.', ' .$myMarker->latitude.']';
			}
			else
			{
				return 'geocode';
			}
		}
		else
		{
			return '';
		}	
	}
}



if ((int)$map->useruser != 0)
{
	function get_userinfo_for_marker($userId, $showuser, $imgpathIcons, $imgpathUtils, $directoryIcons)
	{
		
		if ((int)$userId != 0)
		{
			$cur_user_name = '';
			$cur_user_address = '';
			$cur_user_phone = '';
			
			$dbUsr = JFactory::getDBO();
			$queryUsr = $dbUsr->getQuery(true);
			
			$queryUsr->select('p.*, h.name as profile_username')
				->from('#__users as h')
				->leftJoin('#__user_profiles as p ON p.user_id=h.id')
				->where('h.id = '.(int)$userId);

			$dbUsr->setQuery($queryUsr);        
			$myUsr = $dbUsr->loadObjectList();
			
			if (isset($myUsr))
			{
				
				foreach ($myUsr as $key => $currentUsers) 
				{
					$cur_user_name = $currentUsers->profile_username;

					if ($currentUsers->profile_key == 'profile.address1')
					{
						$cur_user_address = $currentUsers->profile_value;
					}
					else if ($currentUsers->profile_key == 'profile.phone')
					{
						$cur_user_phone = $currentUsers->profile_value;
					}
					
					
				}
				
				$cur_scripttext = '';
				
				if (isset($showuser) && ((int)$showuser != 0))
				{
					switch ((int)$showuser) 
					{
						case 1:
							if ($cur_user_name != "") 
							{
								$cur_scripttext .= '\'<p class="placemarkBodyUser">'.JText::_('COM_ZHYANDEXMAP_MAP_USER_USER_NAME').' '.htmlspecialchars(str_replace('\\', '/', $cur_user_name), ENT_QUOTES, 'UTF-8').'</p>\'+'."\n";
							}
							if ($cur_user_address != "") 
							{
								$cur_scripttext .= '\'<p class="placemarkBodyUser">'.JText::_('COM_ZHYANDEXMAP_MAP_USER_USER_ADDRESS').' '.str_replace('<br /><br />', '<br />',str_replace(array("\r", "\r\n", "\n"), '<br />', htmlspecialchars(str_replace('\\', '/', $cur_user_address), ENT_QUOTES, 'UTF-8'))).'</p>\'+'."\n";
							}
							if ($cur_user_phone != "") 
							{
								$cur_scripttext .= '\'<p class="placemarkBodyUser">'.JText::_('COM_ZHYANDEXMAP_MAP_USER_USER_PHONE').' '.htmlspecialchars(str_replace('\\', '/', $cur_user_phone), ENT_QUOTES, 'UTF-8').'</p>\'+'."\n";
							}
						break;
						case 2:
							if ($cur_user_name != "") 
							{
								$cur_scripttext .= '\'<p class="placemarkBodyUser">'.htmlspecialchars(str_replace('\\', '/', $cur_user_name), ENT_QUOTES, 'UTF-8').'</p>\'+'."\n";
							}
							if ($cur_user_address != "") 
							{
								$cur_scripttext .= '\'<p class="placemarkBodyUser"><img src="'.$imgpathUtils.'address.png" alt="'.JText::_('COM_ZHYANDEXMAP_MAP_USER_USER_ADDRESS').'" />'.str_replace('<br /><br />', '<br />',str_replace(array("\r", "\r\n", "\n"), '<br />', htmlspecialchars(str_replace('\\', '/', $cur_user_address), ENT_QUOTES, 'UTF-8'))).'</p>\'+'."\n";
							}
							if ($cur_user_phone != "") 
							{
								$cur_scripttext .= '\'<p class="placemarkBodyUser"><img src="'.$imgpathUtils.'phone.png" alt="'.JText::_('COM_ZHYANDEXMAP_MAP_USER_USER_PHONE').'" />'.htmlspecialchars(str_replace('\\', '/', $cur_user_phone), ENT_QUOTES, 'UTF-8').'</p>\'+'."\n";
							}
						break;
						case 3:
							if ($cur_user_name != "") 
							{
								$cur_scripttext .= '\'<p class="placemarkBodyUser">'.htmlspecialchars(str_replace('\\', '/', $cur_user_name), ENT_QUOTES, 'UTF-8').'</p>\'+'."\n";
							}
							if ($cur_user_address != "") 
							{
								$cur_scripttext .= '\'<p class="placemarkBodyUser">'.str_replace('<br /><br />', '<br />',str_replace(array("\r", "\r\n", "\n"), '<br />', htmlspecialchars(str_replace('\\', '/', $cur_user_address), ENT_QUOTES, 'UTF-8'))).'</p>\'+'."\n";
							}
							if ($cur_user_phone != "") 
							{
								$cur_scripttext .= '\'<p class="placemarkBodyUser">'.htmlspecialchars(str_replace('\\', '/', $cur_user_phone), ENT_QUOTES, 'UTF-8').'</p>\'+'."\n";
							}
						break;
						default:
							if ($cur_user_name != "") 
							{
								$cur_scripttext .= '\'<p class="placemarkBodyUser">'.htmlspecialchars(str_replace('\\', '/', $cur_user_name), ENT_QUOTES, 'UTF-8').'</p>\'+'."\n";
							}
							if ($cur_user_address != "") 
							{
								$cur_scripttext .= '\'<p class="placemarkBodyUser">'.str_replace('<br /><br />', '<br />',str_replace(array("\r", "\r\n", "\n"), '<br />', htmlspecialchars(str_replace('\\', '/', $cur_user_address), ENT_QUOTES, 'UTF-8'))).'</p>\'+'."\n";
							}
							if ($cur_user_phone != "") 
							{
								$cur_scripttext .= '\'<p class="placemarkBodyUser">'.htmlspecialchars(str_replace('\\', '/', $cur_user_phone), ENT_QUOTES, 'UTF-8').'</p>\'+'."\n";
							}
						break;										
					}
				}
				
				return $cur_scripttext;
			}
			else
			{
				return '';
			}	
		}
		else
		{
			return '';
		}	
		
		
	}
}



if (isset($map->css2load) && ($map->css2load != ""))
{
	$loadCSSList = explode(';', str_replace(array("\r", "\r\n", "\n"), ';', $map->css2load));


	for($i = 0; $i < count($loadCSSList); $i++) 
	{
		$currCSS = trim($loadCSSList[$i]);
		if ($currCSS != "")
		{
			$document->addStyleSheet($currCSS);
		}
	}
}

		
if (isset($map->usermarkers) && (int)$map->usermarkers == 1) 
{
	if ($compatiblemodersf == 0)
	{
		$document->addStyleSheet(JURI::root() .'administrator/components/com_zhyandexmap/assets/css/usermarkers.css');
	}
	else
	{
		$document->addStyleSheet(JURI::root() .'components/com_zhyandexmap/assets/css/usermarkers.css');
	}
}



if ($map->headerhtml != "")
{
        $divmapheader .= '<div id="YMapInfoHeader">'.$map->headerhtml;
        if (isset($map->headersep) && (int)$map->headersep == 1) 
        {
            $divmapheader .= '<hr id="mapHeaderLine" />';
        }
        $divmapheader .= '</div>';
}

$divmap = "";

$divmapbefore = "";
$divmapafter = "";


if (isset($map->findcontrol) && (int)$map->findcontrol == 1) 
{
	switch ((int)$map->findpos) 
	{
		
		case 0:
			$divmapbefore .= '<div id="YMapFindAddress">'."\n";
			$divmapbefore .= '<form action="#" onsubmit="showAddressByGeocoding(this.findAddressField.value);return false;">'."\n";
			//$divmapbefore .= '<p>'."\n";
            $divmapbefore .= '<input id="findAddressField" type="text" value=""';
			if (isset($map->findwidth) && (int)$map->findwidth != 0)
			{
				$divmapbefore .= ' size="'.(int)$map->findwidth.'"';
			}
			$divmapbefore .=' />';
			
			$divmapbefore .= '<input id="findAddressButton" type="submit" value="';
			if (isset($map->findroute) && (int)$map->findroute == 1) 
			{
				$divmapbefore .= JText::_( 'COM_ZHYANDEXMAP_MAP_DOFINDBUTTONROUTE' );
			}
			else
			{
				$divmapbefore .= JText::_( 'COM_ZHYANDEXMAP_MAP_DOFINDBUTTON' );
			}
			$divmapbefore .= '" />'."\n";
			
			//$divmapbefore .= '</p>'."\n";
			$divmapbefore .= '</form>'."\n";
			$divmapbefore .= '</div>'."\n";
		break;
		case 101:
			$divmapbefore .= '<div id="YMapFindAddress">'."\n";
			$divmapbefore .= '<form action="#" onsubmit="showAddressByGeocoding(this.findAddressField.value);return false;">'."\n";
			//$divmapbefore .= '<p>'."\n";
            $divmapbefore .= '<input id="findAddressField" type="text" value=""';
			if (isset($map->findwidth) && (int)$map->findwidth != 0)
			{
				$divmapbefore .= ' size="'.(int)$map->findwidth.'"';
			}
			$divmapbefore .=' />';
			$divmapbefore .= '<input id="findAddressButton" type="submit" value="';
			if (isset($map->findroute) && (int)$map->findroute == 1) 
			{
				$divmapbefore .= JText::_( 'COM_ZHYANDEXMAP_MAP_DOFINDBUTTONROUTE' );
			}
			else
			{
				$divmapbefore .= JText::_( 'COM_ZHYANDEXMAP_MAP_DOFINDBUTTON' );
			}
			$divmapbefore .= '" />'."\n";
			//$divmapbefore .= '</p>'."\n";
			$divmapbefore .= '</form>'."\n";
			$divmapbefore .= '</div>'."\n";
		break;
		case 102:
			$divmapafter .= '<div id="YMapFindAddress">'."\n";
			$divmapafter .= '<form action="#" onsubmit="showAddressByGeocoding(this.findAddressField.value);return false;">'."\n";
			//$divmapafter .= '<p>'."\n";
            $divmapafter .= '<input id="findAddressField" type="text" value=""';
			if (isset($map->findwidth) && (int)$map->findwidth != 0)
			{
				$divmapafter .= ' size="'.(int)$map->findwidth.'"';
			}
			$divmapafter .=' />';
			$divmapafter .= '<input id="findAddressButton" type="submit" value="';
			if (isset($map->findroute) && (int)$map->findroute == 1) 
			{
				$divmapafter .= JText::_( 'COM_ZHYANDEXMAP_MAP_DOFINDBUTTONROUTE' );
			}
			else
			{
				$divmapafter .= JText::_( 'COM_ZHYANDEXMAP_MAP_DOFINDBUTTON' );
			}
			$divmapafter .= '" />'."\n";
			//$divmapafter .= '</p>'."\n";
			$divmapafter .= '</form>'."\n";
			$divmapafter .= '</div>'."\n";
		break;
		default:
		break;
	}
}

if (isset($map->autopositioncontrol) && (int)$map->autopositioncontrol == 2) 
{


	switch ((int)$map->autopositionpos) 
	{
		
		case 0:
			$divmapbefore .='<div id="geoLocation">';
			$divmapbefore .= '  <button id="geoLocationButton" onclick="findMyPosition(\'Button\');return false;">';

			switch ((int)$map->autopositionbutton) 
			{
				case 1:
					$divmapbefore .= '<img src="'.$imgpathUtils.'geolocation.png" alt="'.JText::_('COM_ZHYANDEXMAP_MAP_AUTOPOSITIONBUTTON').'" style="vertical-align: middle">';
				break;
				case 2:
					$divmapbefore .= JText::_('COM_ZHYANDEXMAP_MAP_AUTOPOSITIONBUTTON');
				break;
				case 3:
					$divmapbefore .= '<img src="'.$imgpathUtils.'geolocation.png" alt="'.JText::_('COM_ZHYANDEXMAP_MAP_AUTOPOSITIONBUTTON').'" style="vertical-align: middle">';
					$divmapbefore .= JText::_('COM_ZHYANDEXMAP_MAP_AUTOPOSITIONBUTTON');
				break;
				default:
					$divmapbefore .= '<img src="'.$imgpathUtils.'geolocation.png" alt="'.JText::_('COM_ZHYANDEXMAP_MAP_AUTOPOSITIONBUTTON').'" style="vertical-align: middle">';
				break;
			}
			
			$divmapbefore .= '</button>';
			$divmapbefore .='</div>'."\n";
		break;
		case 101:
			$divmapbefore .='<div id="geoLocation">';
			$divmapbefore .= '  <button id="geoLocationButton" onclick="findMyPosition(\'Button\');return false;">';

			switch ((int)$map->autopositionbutton) 
			{
				case 1:
					$divmapbefore .= '<img src="'.$imgpathUtils.'geolocation.png" alt="'.JText::_('COM_ZHYANDEXMAP_MAP_AUTOPOSITIONBUTTON').'" style="vertical-align: middle">';
				break;
				case 2:
					$divmapbefore .= JText::_('COM_ZHYANDEXMAP_MAP_AUTOPOSITIONBUTTON');
				break;
				case 3:
					$divmapbefore .= '<img src="'.$imgpathUtils.'geolocation.png" alt="'.JText::_('COM_ZHYANDEXMAP_MAP_AUTOPOSITIONBUTTON').'" style="vertical-align: middle">';
					$divmapbefore .= JText::_('COM_ZHYANDEXMAP_MAP_AUTOPOSITIONBUTTON');
				break;
				default:
					$divmapbefore .= '<img src="'.$imgpathUtils.'geolocation.png" alt="'.JText::_('COM_ZHYANDEXMAP_MAP_AUTOPOSITIONBUTTON').'" style="vertical-align: middle">';
				break;
			}
			
			$divmapbefore .= '</button>';
			$divmapbefore .='</div>'."\n";
		break;
		case 102:
			$divmapafter .='<div id="geoLocation">';
			$divmapafter .= '  <button id="geoLocationButton" onclick="findMyPosition(\'Button\');return false;">';

			switch ((int)$map->autopositionbutton) 
			{
				case 1:
					$divmapafter .= '<img src="'.$imgpathUtils.'geolocation.png" alt="'.JText::_('COM_ZHYANDEXMAP_MAP_AUTOPOSITIONBUTTON').'" style="vertical-align: middle">';
				break;
				case 2:
					$divmapafter .= JText::_('COM_ZHYANDEXMAP_MAP_AUTOPOSITIONBUTTON');
				break;
				case 3:
					$divmapafter .= '<img src="'.$imgpathUtils.'geolocation.png" alt="'.JText::_('COM_ZHYANDEXMAP_MAP_AUTOPOSITIONBUTTON').'" style="vertical-align: middle">';
					$divmapafter .= JText::_('COM_ZHYANDEXMAP_MAP_AUTOPOSITIONBUTTON');
				break;
				default:
					$divmapafter .= '<img src="'.$imgpathUtils.'geolocation.png" alt="'.JText::_('COM_ZHYANDEXMAP_MAP_AUTOPOSITIONBUTTON').'" style="vertical-align: middle">';
				break;
			}
			
			$divmapafter .= '</button>';
			$divmapafter .='</div>'."\n";
		break;
		default:
		break;
	}

	
}



if ($map->footerhtml != "")
{
       $divmapfooter .= '<div id="YMapInfoFooter">';
        if (isset($map->footersep) && (int)$map->footersep == 1) 
        {
            $divmapfooter .= '<hr id="mapFooterLine" />';
        }
       $divmapfooter .= $map->footerhtml.'</div>';
}

if ((!isset($map->width)) || (isset($map->width) && (int)$map->width < 1)) 
{
	$fullWidth = 1;
}
if ((!isset($map->height)) || (isset($map->height) && (int)$map->height < 1)) 
{
	$fullHeight = 1;
}



if (isset($map->markergroupcontrol) && (int)$map->markergroupcontrol != 0) 
{
	if ($compatiblemodersf == 0)
	{
		$document->addStyleSheet(JURI::root() .'administrator/components/com_zhyandexmap/assets/css/markergroups.css');
	}
	else
	{
		$document->addStyleSheet(JURI::root() .'components/com_zhyandexmap/assets/css/markergroups.css');
	}
	

	switch ((int)$map->markergroupcss) 
	{
		
		case 0:
			$markergroupcssstyle = '-simple';
		break;
		case 1:
			$markergroupcssstyle = '-advanced';
		break;
		case 2:
			$markergroupcssstyle = '-external';
		break;
		default:
			$markergroupcssstyle = '-simple';
		break;
	}

	$divmarkergroup = '<div id="YMapsMenu'.$markergroupcssstyle.'" style="margin:0;padding:0;width:100%;">';

        if ($map->markergrouptitle != "")
        {
            $divmarkergroup .= '<div id="groupList"><h2 id="groupListHeadTitle" class="groupListHead">'.htmlspecialchars($map->markergrouptitle , ENT_QUOTES, 'UTF-8').'</h2></div>';
        }
        
        if ($map->markergroupdesc1 != "")
        {
            $divmarkergroup .= '<div id="groupListBodyTopContent" class="groupListBodyTop">'.htmlspecialchars($map->markergroupdesc1 , ENT_QUOTES, 'UTF-8').'</div>';
        }

        if (isset($map->markergroupsep1) && (int)$map->markergroupsep1 == 1) 
        {
            $divmarkergroup .= '<hr id="groupListLineTop" />';
        }

        $divmarkergroup .= '<ul id="zhym-menu'.$markergroupcssstyle.'">'."\n";

        if (isset($this->markergroups) && !empty($this->markergroups)) 
		{

			foreach ($this->markergroups as $key => $currentmarkergroup) 
			{
				if (((int)$currentmarkergroup->published == 1) || ($allowUserMarker == 1))
				{
					$imgimg = $imgpathIcons.str_replace("#", "%23", $currentmarkergroup->icontype).'.png';

					$markergroupname = 'markergroup'. $currentmarkergroup->id;

					if ((int)$currentmarkergroup->activeincluster == 1)
					{
						$markergroupactive = 'class="active"';
					}
					else
					{
						$markergroupactive = 'class=""';
					}


					if (isset($map->markercluster) && (int)$map->markercluster == 1)
					{
						if ((isset($map->markerclustergroup) && (int)$map->markerclustergroup == 1))
						{

							switch ((int)$map->markergroupshowicon) 
							{
								
								case 0:
									$divmarkergroup .= '<li id="li-'.$markergroupname.'"><div id="zhym-markergroup-a-'.$markergroupname.'" class="zhym-markergroup-a'.$markergroupcssstyle.'"><a '.$markergroupactive.' id="a-'.$markergroupname.'" href="#" onclick="callMarkerChangeGroup(\'a-'.$markergroupname.'\', markerCluster'.$currentmarkergroup->id.', '.$currentmarkergroup->id.');return false;"><div id="zhym-markergroup-text-'.$markergroupname.'" class="zhym-markergroup-text'.$markergroupcssstyle.'">'.htmlspecialchars(str_replace('\\', '/',$currentmarkergroup->title), ENT_QUOTES, 'UTF-8').'</div></a></div></li>'."\n";
								break;
								case 1:
									$divmarkergroup .= '<li id="li-'.$markergroupname.'"><div id="zhym-markergroup-a-'.$markergroupname.'" class="zhym-markergroup-a'.$markergroupcssstyle.'"><a '.$markergroupactive.' id="a-'.$markergroupname.'" href="#" onclick="callMarkerChangeGroup(\'a-'.$markergroupname.'\', markerCluster'.$currentmarkergroup->id.', '.$currentmarkergroup->id.');return false;"><div id="zhym-markergroup-img-'.$markergroupname.'" class="zhym-markergroup-img'.$markergroupcssstyle.'"><img src="'.$imgimg.'" alt="" /></div><div id="zhym-markergroup-text-'.$markergroupname.'" class="zhym-markergroup-text'.$markergroupcssstyle.'">'.htmlspecialchars(str_replace('\\', '/',$currentmarkergroup->title), ENT_QUOTES, 'UTF-8').'</div></a></div></li>'."\n";
								break;
								case 2:
									$divmarkergroup .= '<li id="li-'.$markergroupname.'"><div id="zhym-markergroup-a-'.$markergroupname.'" class="zhym-markergroup-a'.$markergroupcssstyle.'"><a '.$markergroupactive.' id="a-'.$markergroupname.'" href="#" onclick="callMarkerChangeGroup(\'a-'.$markergroupname.'\', markerCluster'.$currentmarkergroup->id.', '.$currentmarkergroup->id.');return false;"><div id="zhym-markergroup-img-'.$markergroupname.'" class="zhym-markergroup-img'.$markergroupcssstyle.'"><img src="'.$imgimg.'" alt="" /></div></a></div></li>'."\n";
								break;
								default:
									$divmarkergroup .= '<li id="li-'.$markergroupname.'"><div id="zhym-markergroup-a-'.$markergroupname.'" class="zhym-markergroup-a'.$markergroupcssstyle.'"><a '.$markergroupactive.' id="a-'.$markergroupname.'" href="#" onclick="callMarkerChangeGroup(\'a-'.$markergroupname.'\', markerCluster'.$currentmarkergroup->id.', '.$currentmarkergroup->id.');return false;"><div id="zhym-markergroup-text-'.$markergroupname.'" class="zhym-markergroup-text'.$markergroupcssstyle.'">'.htmlspecialchars(str_replace('\\', '/',$currentmarkergroup->title), ENT_QUOTES, 'UTF-8').'</div></a></div></li>'."\n";
								break;
							}
						}   
						else
						{
							switch ((int)$map->markergroupshowicon) 
							{
								
								case 0:
									$divmarkergroup .= '<li id="li-'.$markergroupname.'"><div id="zhym-markergroup-a-'.$markergroupname.'" class="zhym-markergroup-a'.$markergroupcssstyle.'"><a '.$markergroupactive.' id="a-'.$markergroupname.'" href="#" onclick="callMarkerChange(\'a-'.$markergroupname.'\', clustermarkers'.$currentmarkergroup->id.');return false;"><div id="zhym-markergroup-text-'.$markergroupname.'" class="zhym-markergroup-text'.$markergroupcssstyle.'">'.htmlspecialchars(str_replace('\\', '/',$currentmarkergroup->title), ENT_QUOTES, 'UTF-8').'</div></a></div></li>'."\n";
								break;
								case 1:
									$divmarkergroup .= '<li id="li-'.$markergroupname.'"><div id="zhym-markergroup-a-'.$markergroupname.'" class="zhym-markergroup-a'.$markergroupcssstyle.'"><a '.$markergroupactive.' id="a-'.$markergroupname.'" href="#" onclick="callMarkerChange(\'a-'.$markergroupname.'\', clustermarkers'.$currentmarkergroup->id.');return false;"><div id="zhym-markergroup-img-'.$markergroupname.'" class="zhym-markergroup-img'.$markergroupcssstyle.'"><img src="'.$imgimg.'" alt="" /></div><div id="zhym-markergroup-text-'.$markergroupname.'" class="zhym-markergroup-text'.$markergroupcssstyle.'">'.htmlspecialchars(str_replace('\\', '/',$currentmarkergroup->title), ENT_QUOTES, 'UTF-8').'</div></a></div></li>'."\n";
								break;
								case 2:
									$divmarkergroup .= '<li id="li-'.$markergroupname.'"><div id="zhym-markergroup-a-'.$markergroupname.'" class="zhym-markergroup-a'.$markergroupcssstyle.'"><a '.$markergroupactive.' id="a-'.$markergroupname.'" href="#" onclick="callMarkerChange(\'a-'.$markergroupname.'\', clustermarkers'.$currentmarkergroup->id.');return false;"><div id="zhym-markergroup-img-'.$markergroupname.'" class="zhym-markergroup-img'.$markergroupcssstyle.'"><img src="'.$imgimg.'" alt="" /></div></a></div></li>'."\n";
								break;
								default:
									$divmarkergroup .= '<li id="li-'.$markergroupname.'"><div id="zhym-markergroup-a-'.$markergroupname.'" class="zhym-markergroup-a'.$markergroupcssstyle.'"><a '.$markergroupactive.' id="a-'.$markergroupname.'" href="#" onclick="callMarkerChange(\'a-'.$markergroupname.'\', clustermarkers'.$currentmarkergroup->id.');return false;"><div id="zhym-markergroup-text-'.$markergroupname.'" class="zhym-markergroup-text'.$markergroupcssstyle.'">'.htmlspecialchars(str_replace('\\', '/',$currentmarkergroup->title), ENT_QUOTES, 'UTF-8').'</div></a></div></li>'."\n";
								break;
							}
						}
					}   
					else
					{
						switch ((int)$map->markergroupshowicon) 
						{
							
							case 0:
								$divmarkergroup .= '<li id="li-'.$markergroupname.'"><div id="zhym-markergroup-a-'.$markergroupname.'" class="zhym-markergroup-a'.$markergroupcssstyle.'"><a '.$markergroupactive.' id="a-'.$markergroupname.'" href="#" onclick="callMarkerArrayChange(\'a-'.$markergroupname.'\', clustermarkers'.$currentmarkergroup->id.');return false;"><div id="zhym-markergroup-text-'.$markergroupname.'" class="zhym-markergroup-text'.$markergroupcssstyle.'">'.htmlspecialchars(str_replace('\\', '/',$currentmarkergroup->title), ENT_QUOTES, 'UTF-8').'</div></a></div></li>'."\n";
							break;
							case 1:
								$divmarkergroup .= '<li id="li-'.$markergroupname.'"><div id="zhym-markergroup-a-'.$markergroupname.'" class="zhym-markergroup-a'.$markergroupcssstyle.'"><a '.$markergroupactive.' id="a-'.$markergroupname.'" href="#" onclick="callMarkerArrayChange(\'a-'.$markergroupname.'\', clustermarkers'.$currentmarkergroup->id.');return false;"><div id="zhym-markergroup-img-'.$markergroupname.'" class="zhym-markergroup-img'.$markergroupcssstyle.'"><img src="'.$imgimg.'" alt="" /></div><div id="zhym-markergroup-text-'.$markergroupname.'" class="zhym-markergroup-text'.$markergroupcssstyle.'">'.htmlspecialchars(str_replace('\\', '/',$currentmarkergroup->title), ENT_QUOTES, 'UTF-8').'</div></a></div></li>'."\n";
							break;
							case 2:
								$divmarkergroup .= '<li id="li-'.$markergroupname.'"><div id="zhym-markergroup-a-'.$markergroupname.'" class="zhym-markergroup-a'.$markergroupcssstyle.'"><a '.$markergroupactive.' id="a-'.$markergroupname.'" href="#" onclick="callMarkerArrayChange(\'a-'.$markergroupname.'\', clustermarkers'.$currentmarkergroup->id.');return false;"><div id="zhym-markergroup-img-'.$markergroupname.'" class="zhym-markergroup-img'.$markergroupcssstyle.'"><img src="'.$imgimg.'" alt="" /></div></a></div></li>'."\n";
							break;
							default:
								$divmarkergroup .= '<li id="li-'.$markergroupname.'"><div id="zhym-markergroup-a-'.$markergroupname.'" class="zhym-markergroup-a'.$markergroupcssstyle.'"><a '.$markergroupactive.' id="a-'.$markergroupname.'" href="#" onclick="callMarkerArrayChange(\'a-'.$markergroupname.'\', clustermarkers'.$currentmarkergroup->id.');return false;"><div id="zhym-markergroup-text-'.$markergroupname.'" class="zhym-markergroup-text'.$markergroupcssstyle.'">'.htmlspecialchars(str_replace('\\', '/',$currentmarkergroup->title), ENT_QUOTES, 'UTF-8').'</div></a></div></li>'."\n";
							break;
						}
					} 
				}
			}
		}


        $divmarkergroup .= '</ul>'."\n";

        if (isset($map->markergroupsep2) && (int)$map->markergroupsep2 == 1) 
        {
            $divmarkergroup .= '<hr id="groupListLineBottom" />';
        }
        
        if ($map->markergroupdesc2 != "")
        {
            $divmarkergroup .= '<div id="groupListBodyBottomContent" class="groupListBodyBottom">'.htmlspecialchars($map->markergroupdesc2 , ENT_QUOTES, 'UTF-8').'</div>';
        }
        

	$divmarkergroup .= '</div>'."\n";
}


	$divwrapmapstyle = '';
	$divtabcolmapstyle = '';
	
	if ($fullWidth == 1)
	{
		$divwrapmapstyle .= 'width:100%;';
	}
	if ($fullHeight == 1)
	{
		$divwrapmapstyle .= 'height:100%;';
		$divtabcolmapstyle .= 'height:100%;';
	}
	if ($divwrapmapstyle != "")
	{
		$divwrapmapstyle = 'style="'.$divwrapmapstyle.'"';
	}
	if ($divtabcolmapstyle != "")
	{
		$divtabcolmapstyle = 'style="'.$divtabcolmapstyle.'"';
	}

// adding markerlist (div)
if (isset($map->markerlistpos) && (int)$map->markerlistpos != 0) 
{

	if ($compatiblemodersf == 0)
	{
		$document->addStyleSheet(JURI::root() .'administrator/components/com_zhyandexmap/assets/css/markerlists.css');
	}
	else
	{
		$document->addStyleSheet(JURI::root() .'components/com_zhyandexmap/assets/css/markerlists.css');
	}
	
	
	switch ((int)$map->markerlist) 
	{
		
		case 0:
			$markerlistcssstyle = 'markerList-simple';
		break;
		case 1:
			$markerlistcssstyle = 'markerList-advanced';
		break;
		case 2:
			$markerlistcssstyle = 'markerList-external';
		break;
		default:
			$markerlistcssstyle = 'markerList-simple';
		break;
	}


	$markerlistAddStyle ='';
	
	if ($map->markerlistbgcolor != "")
	{
		$markerlistAddStyle .= ' background: '.$map->markerlistbgcolor.';';
	}
	
	if ((int)$map->markerlistwidth == 0)
	{
		if ((int)$map->markerlistpos == 113
		  ||(int)$map->markerlistpos == 114
		  ||(int)$map->markerlistpos == 121)
		{
			$divMarkerlistWidth = '100%';
		}
		else
		{
			$divMarkerlistWidth = '200px';
		}
	}
	else
	{
		$divMarkerlistWidth = $map->markerlistwidth;
		$divMarkerlistWidth = $divMarkerlistWidth. 'px';
	}


	if ((int)$map->markerlistpos == 111
	  ||(int)$map->markerlistpos == 112)
	{
		if ($fullHeight == 1)
		{
			$divMarkerlistHeight = '100%';
		}
		else
		{
			$divMarkerlistHeight = $map->height;
			$divMarkerlistHeight = $divMarkerlistHeight. 'px';
		}
	}
	else
	{
		if ((int)$map->markerlistheight == 0)
		{
			$divMarkerlistHeight = 200;
		}
		else
		{
			$divMarkerlistHeight = $map->markerlistheight;
		}
		$divMarkerlistHeight = $divMarkerlistHeight. 'px';
	}		
	
	if ((int)$map->markerlistcontent < 100) 
	{
		$markerlisttag = '<ul id="YMapsMarkerUL" class="zhym-ul-'.$markerlistcssstyle.'"></ul>';
	}
	else 
	{
		$markerlisttag =  '<table id="YMapsMarkerTABLE" class="zhym-ul-table-'.$markerlistcssstyle.'" ';
		if (((int)$map->markerlistpos == 113) 
		|| ((int)$map->markerlistpos == 114) 
		|| ((int)$map->markerlistpos == 121))
		{
			if ($fullWidth == 1) 
			{
				$markerlisttag .= 'style="width:100%;" ';
			}
		}
		$markerlisttag .= '>';
		$markerlisttag .= '<tbody id="YMapsMarkerTABLEBODY" class="zhym-ul-tablebody-'.$markerlistcssstyle.'">';
		$markerlisttag .= '</tbody>';
		$markerlisttag .= '</table>';
	}
	
	switch ((int)$map->markerlistpos) 
	{
		case 0:
			// None
		break;
		case 1:
			$divmap .= '<div id="YMMapWrapper" '.$divwrapmapstyle.' class="zhym-wrap-'.$markerlistcssstyle.'">';
			$divmap .='<div id="YMapsMarkerList" class="zhym-list-'.$markerlistcssstyle.'" style="'.$markerlistAddStyle.' display: none; float: left; padding: 0; margin: 5px; width:'.$divMarkerlistWidth.'; height:'.$divMarkerlistHeight.';">'.$markerlisttag.'</div>';
		break;
		case 2:
			$divmap .= '<div id="YMMapWrapper" '.$divwrapmapstyle.' class="zhym-wrap-'.$markerlistcssstyle.'">';
			$divmap .='<div id="YMapsMarkerList" class="zhym-list-'.$markerlistcssstyle.'" style="'.$markerlistAddStyle.' display: none; float: left; padding: 0; margin: 5px; width:'.$divMarkerlistWidth.'; height:'.$divMarkerlistHeight.';">'.$markerlisttag.'</div>';
		break;
		case 3:
			$divmap .= '<div id="YMMapWrapper" '.$divwrapmapstyle.' class="zhym-wrap-'.$markerlistcssstyle.'">';
			$divmap .='<div id="YMapsMarkerList" class="zhym-list-'.$markerlistcssstyle.'" style="'.$markerlistAddStyle.' display: none; float: left; padding: 0; margin: 5px; width:'.$divMarkerlistWidth.'; height:'.$divMarkerlistHeight.';">'.$markerlisttag.'</div>';
		break;
		case 4:
			$divmap .= '<div id="YMMapWrapper" '.$divwrapmapstyle.' class="zhym-wrap-'.$markerlistcssstyle.'">';
			$divmap .='<div id="YMapsMarkerList" class="zhym-list-'.$markerlistcssstyle.'" style="'.$markerlistAddStyle.' display: none; float: left; padding: 0; margin: 5px; width:'.$divMarkerlistWidth.'; height:'.$divMarkerlistHeight.';">'.$markerlisttag.'</div>';
		break;
		case 5:
			$divmap .= '<div id="YMMapWrapper" '.$divwrapmapstyle.' class="zhym-wrap-'.$markerlistcssstyle.'">';
			$divmap .='<div id="YMapsMarkerList" class="zhym-list-'.$markerlistcssstyle.'" style="'.$markerlistAddStyle.' display: none; float: left; padding: 0; margin: 5px; width:'.$divMarkerlistWidth.'; height:'.$divMarkerlistHeight.';">'.$markerlisttag.'</div>';
		break;
		case 6:
			$divmap .= '<div id="YMMapWrapper" '.$divwrapmapstyle.' class="zhym-wrap-'.$markerlistcssstyle.'">';
			$divmap .='<div id="YMapsMarkerList" class="zhym-list-'.$markerlistcssstyle.'" style="'.$markerlistAddStyle.' display: none; float: left; padding: 0; margin: 5px; width:'.$divMarkerlistWidth.'; height:'.$divMarkerlistHeight.';">'.$markerlisttag.'</div>';
		break;
		case 7:
			$divmap .= '<div id="YMMapWrapper" '.$divwrapmapstyle.' class="zhym-wrap-'.$markerlistcssstyle.'">';
			$divmap .='<div id="YMapsMarkerList" class="zhym-list-'.$markerlistcssstyle.'" style="'.$markerlistAddStyle.' display: none; float: left; padding: 0; margin: 5px; width:'.$divMarkerlistWidth.'; height:'.$divMarkerlistHeight.';">'.$markerlisttag.'</div>';
		break;
		case 8:
			$divmap .= '<div id="YMMapWrapper" '.$divwrapmapstyle.' class="zhym-wrap-'.$markerlistcssstyle.'">';
			$divmap .='<div id="YMapsMarkerList" class="zhym-list-'.$markerlistcssstyle.'" style="'.$markerlistAddStyle.' display: none; float: left; padding: 0; margin: 5px; width:'.$divMarkerlistWidth.'; height:'.$divMarkerlistHeight.';">'.$markerlisttag.'</div>';
		break;
		case 9:
			$divmap .= '<div id="YMMapWrapper" '.$divwrapmapstyle.' class="zhym-wrap-'.$markerlistcssstyle.'">';
			$divmap .='<div id="YMapsMarkerList" class="zhym-list-'.$markerlistcssstyle.'" style="'.$markerlistAddStyle.' display: none; float: left; padding: 0; margin: 5px; width:'.$divMarkerlistWidth.'; height:'.$divMarkerlistHeight.';">'.$markerlisttag.'</div>';
		break;
		case 10:
			$divmap .= '<div id="YMMapWrapper" '.$divwrapmapstyle.' class="zhym-wrap-'.$markerlistcssstyle.'">';
			$divmap .='<div id="YMapsMarkerList" class="zhym-list-'.$markerlistcssstyle.'" style="'.$markerlistAddStyle.' display: none; float: left; padding: 0; margin: 5px; width:'.$divMarkerlistWidth.'; height:'.$divMarkerlistHeight.';">'.$markerlisttag.'</div>';
		break;
		case 11:
			$divmap .= '<div id="YMMapWrapper" '.$divwrapmapstyle.' class="zhym-wrap-'.$markerlistcssstyle.'">';
			$divmap .='<div id="YMapsMarkerList" class="zhym-list-'.$markerlistcssstyle.'" style="'.$markerlistAddStyle.' display: none; float: left; padding: 0; margin: 5px; width:'.$divMarkerlistWidth.'; height:'.$divMarkerlistHeight.';">'.$markerlisttag.'</div>';
		break;
		case 12:
			$divmap .= '<div id="YMMapWrapper" '.$divwrapmapstyle.' class="zhym-wrap-'.$markerlistcssstyle.'">';
			$divmap .='<div id="YMapsMarkerList" class="zhym-list-'.$markerlistcssstyle.'" style="'.$markerlistAddStyle.' display: none; float: left; padding: 0; margin: 5px; width:'.$divMarkerlistWidth.'; height:'.$divMarkerlistHeight.';">'.$markerlisttag.'</div>';
		break;
		case 111:
			if ($fullWidth == 1) 
			{
				$divmap .= '<table id="YMMapTable" class="zhym-table-'.$markerlistcssstyle.'" style="width:100%;" >';
			}
			else
			{
				$divmap .= '<table id="YMMapTable" class="zhym-table-'.$markerlistcssstyle.'" >';
			}
			$divmap .= '<tbody>';
			$divmap .= '<tr>';
			$divmap .= '<td style="width:'.$divMarkerlistWidth.'">';
			$divmap .='<div id="YMapsMarkerList" class="zhym-list-'.$markerlistcssstyle.'" style="'.$markerlistAddStyle.' float: left; padding: 0; margin: 0 10px 0 0; width:'.$divMarkerlistWidth.'; height:'.$divMarkerlistHeight.';">'.$markerlisttag.'</div>';
			$divmap .= '</td>';
			$divmap .= '<td>';
		break;
		case 112:
			if ($fullWidth == 1) 
			{
				$divmap .= '<table id="YMMapTable" class="zhym-table-'.$markerlistcssstyle.'" style="width:100%;" >';
			}
			else
			{
				$divmap .= '<table id="YMMapTable" class="zhym-table-'.$markerlistcssstyle.'" >';
			}
			$divmap .= '<tbody>';
			$divmap .= '<tr>';
			$divmap .= '<td>';
		break;
		case 113:
			$divmap .= '<div id="YMMapWrapper" '.$divwrapmapstyle.' class="zhym-wrap-'.$markerlistcssstyle.'" >';
			$divmap .='<div id="YMapsMarkerList" class="zhym-list-'.$markerlistcssstyle.'" style="'.$markerlistAddStyle.' display: none; float: left; padding: 0; margin: 0; width:'.$divMarkerlistWidth.'; height:'.$divMarkerlistHeight.';">'.$markerlisttag.'</div>';
		break;
		case 114:
			$divmap .= '<div id="YMMapWrapper" '.$divwrapmapstyle.' class="zhym-wrap-'.$markerlistcssstyle.'" >';
		break;
		case 121:
		break;
		default:
		break;
	}

	
}



$mapDivCSSClassName = "";
if (isset($map->cssclassname) && ($map->cssclassname != ""))
{
	$mapDivCSSClassName = ' class="'.$map->cssclassname.'"';
}


if ($fullWidth == 1) 
{
	if ($fullHeight == 1) 
	{
		$divmap .= '<div id="YMapsID" '.$mapDivCSSClassName.' style="margin:0;padding:0;width:100%;height:100%;"></div>';
	}
	else
	{
		$divmap .= '<div id="YMapsID" '.$mapDivCSSClassName.' style="margin:0;padding:0;width:100%;height:'.$map->height.'px;"></div>';
	}		

}
else
{
	if ($fullHeight == 1) 
	{
		$divmap .= '<div id="YMapsID" '.$mapDivCSSClassName.' style="margin:0;padding:0;width:'.$map->width.'px;height:100%;"></div>';			
	}
	else
	{
		$divmap .= '<div id="YMapsID" '.$mapDivCSSClassName.' style="margin:0;padding:0;width:'.$map->width.'px;height:'.$map->height.'px;"></div>';			
	}		
}



// adding markerlist (close div)
if (isset($map->markerlistpos) && (int)$map->markerlistpos != 0) 
{

	switch ((int)$map->markerlistpos) 
	{
		case 0:
			// None
		break;
		case 1:
			$divmap .='</div>';
		break;
		case 2:
			$divmap .='</div>';
		break;
		case 3:
			$divmap .='</div>';
		break;
		case 4:
			$divmap .='</div>';
		break;
		case 5:
			$divmap .='</div>';
		break;
		case 6:
			$divmap .='</div>';
		break;
		case 7:
			$divmap .='</div>';
		break;
		case 8:
			$divmap .='</div>';
		break;
		case 9:
			$divmap .='</div>';
		break;
		case 10:
			$divmap .='</div>';
		break;
		case 11:
			$divmap .='</div>';
		break;
		case 12:
			$divmap .='</div>';
		break;
		case 111:
			$divmap .= '</td>';
			$divmap .= '</tr>';
			$divmap .= '</tbody>';
			$divmap .='</table>';
		break;
		case 112:
			$divmap .= '</td>';
			$divmap .= '<td style="width:'.$divMarkerlistWidth.'">';
			$divmap .='<div id="YMapsMarkerList" class="zhym-list-'.$markerlistcssstyle.'" style="'.$markerlistAddStyle.' float: left; padding: 0; margin: 0 0 0 10px; width:'.$divMarkerlistWidth.'; height:'.$divMarkerlistHeight.';">'.$markerlisttag.'</div>';
			$divmap .= '</td>';
			$divmap .= '</tr>';
			$divmap .= '</tbody>';
			$divmap .='</table>';
		break;
		case 113:
			$divmap .='</div>';
		break;
		case 114:
			$divmap .='<div id="YMapsMarkerList" class="zhym-list-'.$markerlistcssstyle.'" style="'.$markerlistAddStyle.' display: none; float: left; padding: 0; margin: 0; width:'.$divMarkerlistWidth.'; height:'.$divMarkerlistHeight.';">'.$markerlisttag.'</div>';
			$divmap .='</div>';
		break;
		case 121:
		break;
		default:
		break;
	}


}

// Adding before and after sections

$divmap = $divmapbefore . $divmap . $divmapafter;


echo $divmapheader . $currentUserInfo;

	$divwrapmapstyle = '';
	$divtabcolmapstyle = '';
	
	if ($fullWidth == 1)
	{
		$divwrapmapstyle .= 'width:100%;';
	}
	if ($fullHeight == 1)
	{
		$divwrapmapstyle .= 'height:100%;';
		$divtabcolmapstyle .= 'height:100%;';
	}
	if ($divwrapmapstyle != "")
	{
		$divwrapmapstyle = 'style="'.$divwrapmapstyle.'"';
	}
	if ($divtabcolmapstyle != "")
	{
		$divtabcolmapstyle = 'style="'.$divtabcolmapstyle.'"';
	}

$divmap .= '<div id="YMapsCredit" class="zhym-credit"></div>';
	
	
if (isset($map->markergroupcontrol) && (int)$map->markergroupcontrol != 0) 
{
	switch ((int)$map->markergroupcontrol) 
	{
		
		case 1:
		       if ($fullWidth == 1) 
		       {
		          echo '<table class="zhym-group-manage" '.$divwrapmapstyle.'>';
		          echo '<tr align="left" >';
		          echo '<td valign="top" width="'.(int)$map->markergroupwidth.'%">';
        	          echo $divmarkergroup;
		          echo '</td>';
		          echo '<td '.$divtabcolmapstyle.'>';
		          echo $divmap;
		          echo '</td>';
		          echo '</tr>';
		       }
		       else
		       {
		          echo '<table class="zhym-group-manage" '.$divwrapmapstyle.'>';
                          echo '<tr>';
		          echo '<td valign="top">';
        	          echo $divmarkergroup;
		          echo '</td>';
		          echo '<td '.$divtabcolmapstyle.'>';
		          echo $divmap;
		          echo '</td>';
		          echo '</tr>';
                       }
		       echo '</table>';
		break;
		case 2:
		       if ($fullWidth == 1) 
		       {
		          echo '<table class="zhym-group-manage" '.$divwrapmapstyle.'>';
		       }
		       else
		       {
		          echo '<table class="zhym-group-manage" '.$divwrapmapstyle.'>';
		       }
		       echo '<tr>';
		       echo '<td valign="top">';
		       echo $divmarkergroup;
		       echo '</td>';
		       echo '</tr>';
		       echo '<tr>';
		       echo '<td '.$divtabcolmapstyle.'>';
		       echo $divmap;
		       echo '</td>';
		       echo '</tr>';
		       echo '</table>';

		break;
		case 3:
		       if ($fullWidth == 1) 
		       {
		          echo '<table class="zhym-group-manage" '.$divwrapmapstyle.'>';
		          echo '<tr>';
		          echo '<td '.$divtabcolmapstyle.'>';
		          echo $divmap;
		          echo '</td>';
		          echo '<td valign="top" width="'.(int)$map->markergroupwidth.'%">';
		          echo $divmarkergroup;
		          echo '</td>';
		          echo '</tr>';
		       }
		       else
		       {
		          echo '<table class="zhym-group-manage" '.$divwrapmapstyle.'>';
		          echo '<tr>';
		          echo '<td '.$divtabcolmapstyle.'>';
		          echo $divmap;
		          echo '</td>';
		          echo '<td valign="top">';
		          echo $divmarkergroup;
		          echo '</td>';
		          echo '</tr>';
		       }
		       echo '</table>';

		break;
		case 4:
		       if ($fullWidth == 1) 
		       {
		          echo '<table class="zhym-group-manage" '.$divwrapmapstyle.'>';
		       }
		       else
		       {
		          echo '<table class="zhym-group-manage" '.$divwrapmapstyle.'>';
		       }
		       echo '<tr>';
		       echo '<td '.$divtabcolmapstyle.'>';
		       echo $divmap;
		       echo '</td>';
		       echo '</tr>';
		       echo '<tr>';
		       echo '<td valign="top">';
		       echo $divmarkergroup;
		       echo '</td>';
		       echo '</tr>';
		       echo '</table>';
		break;
		case 5:
		       echo '<div id="zhym-wrapper" '.$divwrapmapstyle.'>';
		       echo $divmarkergroup;
		       echo $divmap;
		       echo '</div>';
		break;
		case 6:
		       echo '<div id="zhym-wrapper" '.$divwrapmapstyle.'>';
		       echo $divmap;
		       echo $divmarkergroup;
		       echo '</div>';
		break;
		default:
			echo $divmap;
		break;
	}


}
else
{
    echo $divmap;
}


$divmap4route = '<div id="YMapsMainRoutePanel"><div id="YMapsMainRoutePanel_Total"></div></div>';
$divmap4route .= '<div id="YMapsRoutePanel"><div id="YMapsRoutePanel_Description"></div><div id="YMapsRoutePanel_Total"></div><div id="YMapsRoutePanel_Steps"></div></div>';

echo $divmapfooter . $divmap4route;





//Script begin
$scripttext .= '<script type="text/javascript" >/*<![CDATA[*/' ."\n";

	$scripttext .= 'var map, mapcenter, geoResult, geoRoute;' ."\n";
	$scripttext .= 'var searchControl, searchControlPMAP;' ."\n";

	if ($externalmarkerlink == 1)
	{
		$scripttext .= 'var allPlacemarkArray = [];' ."\n";
	}
	
    // MarkerGroups
    if (
	     ((isset($map->markercluster) && (int)$map->markercluster == 1))
	     ||
	     ((isset($map->markerclustergroup) && (int)$map->markerclustergroup == 1))
	     ||
	     (isset($map->markergroupcontrol) && (int)$map->markergroupcontrol != 0) 
	     //||
	     //(isset($map->markermanager) && (int)$map->markermanager != 0) 
	   )
	{
	   $scripttext .= 'var clustermarkers0;' ."\n";
	   $scripttext .= 'var markerCluster0;' ."\n";

	   if (isset($this->markergroups) && !empty($this->markergroups)) 
	   {
			foreach ($this->markergroups as $key => $currentmarkergroup) 
			{
				$scripttext .= 'var clustermarkers'.$currentmarkergroup->id.';' ."\n";
				if ((isset($map->markerclustergroup) && (int)$map->markerclustergroup == 1))
				{				
					$scripttext .= 'var markerCluster'.$currentmarkergroup->id.';' ."\n";
				}
			}
	   }
    }
	
	$scripttext .= 'ymaps.ready(initialize);' ."\n";

	$scripttext .= 'function initialize () {' ."\n";

	// Begin initialize function
		
		$scripttext .= '    mapcenter = ['.$map->longitude.', ' .$map->latitude.'];' ."\n";
        $scripttext .= '    map = new ymaps.Map("YMapsID", {' ."\n";
		$scripttext .= '    center: mapcenter,' ."\n";
		$scripttext .= '    zoom: '.(int)$map->zoom .''."\n";
		$scripttext .= '    });' ."\n";

	$scripttext .= 'geoResult = new ymaps.GeoObjectCollection();'."\n";

    // MarkerGroups for Clusters
    if (
	     ((isset($map->markercluster) && (int)$map->markercluster == 1))
	     ||
	     ((isset($map->markerclustergroup) && (int)$map->markerclustergroup == 1))
	     ||
	     (isset($map->markergroupcontrol) && (int)$map->markergroupcontrol != 0) 
	     //||
	     //(isset($map->markermanager) && (int)$map->markermanager != 0) 
	   )
	{
	   $scripttext .= 'clustermarkers0 = [];' ."\n";

	   if (isset($this->markergroups) && !empty($this->markergroups)) 
	   {
			foreach ($this->markergroups as $key => $currentmarkergroup) 
			{
				$scripttext .= 'clustermarkers'.$currentmarkergroup->id.' = [];' ."\n";
			}
	   }
    }

    if (
	    ((isset($map->markercluster) && (int)$map->markercluster == 0))
	     &&(isset($map->markergroupcontrol) && (int)$map->markergroupcontrol == 0) 
	   )
	{
	   if (isset($this->markergroups) && !empty($this->markergroups)) 
	   {
			foreach ($this->markergroups as $key => $currentmarkergroup) 
			{
				$markergroupname = 'markergroup'. $currentmarkergroup->id;

				$imgimg = $imgpathIcons.str_replace("#", "%23", $currentmarkergroup->icontype).'.png';
				$imgimg4size = $imgpath4size.$currentmarkergroup->icontype.'.png';

				list ($imgwidth, $imgheight) = getimagesize($imgimg4size);

				// For clusterer collection doesn't need yet :(
				
				if ((isset($map->markercluster) && (int)$map->markercluster == 0)
					&& (int)$currentmarkergroup->overridemarkericon == 1
					&& (int)$currentmarkergroup->published == 1)
				{
					$scripttext .= ' var '.$markergroupname.' = new ymaps.GeoObjectCollection();'."\n";
					
					$imgimg = $imgpathIcons.str_replace("#", "%23", $currentmarkergroup->icontype).'.png';
					$imgimg4size = $imgpath4size.$currentmarkergroup->icontype.'.png';

					list ($imgwidth, $imgheight) = getimagesize($imgimg4size);

					$scripttext .= $markergroupname.'.options.set("iconImageHref", "'.$imgimg.'");' ."\n";
					$scripttext .= $markergroupname.'.options.set("iconImageSize", ['.$imgwidth.','.$imgheight.']);' ."\n";
					if (isset($currentmarkergroup->iconofsetx) 
					 && isset($currentmarkergroup->iconofsety) 
					// Write offset all time
					// && ((int)$currentmarkergroup->iconofsetx !=0
					//  || (int)$currentmarkergroup->iconofsety !=0)
					 )
					{
						// This is for compatibility
						$ofsX = (int)$currentmarkergroup->iconofsetx - 7;
						$ofsY = (int)$currentmarkergroup->iconofsety - $imgheight;
						$scripttext .= $markergroupname.'.options.set("iconImageOffset", ['.$ofsX.','.$ofsY.']);' ."\n";
					}
					
					$scripttext .= 'map.geoObjects.add('.$markergroupname.');' ."\n";
					
				}
				
			}
	   }
   }
	
	// Creating Clusters in the beginning 
	if ((isset($map->markercluster) && (int)$map->markercluster == 1))
	{      

		$scripttext .= 'markerCluster0 = new ymaps.Clusterer({ maxZoom: '.$map->clusterzoom."\n";
		if ((isset($map->clusterdisableclickzoom) && (int)$map->clusterdisableclickzoom == 1))
		{
			$scripttext .= '  , clusterDisableClickZoom: true' ."\n";
		}
		else
		{
			$scripttext .= '  , clusterDisableClickZoom: false' ."\n";
		}
		if ((isset($map->clustersynchadd) && (int)$map->clustersynchadd == 1))
		{
			$scripttext .= '  , synchAdd: true' ."\n";
		}
		else
		{
			$scripttext .= '  , synchAdd: false' ."\n";
		}
		if ((isset($map->clusterorderalphabet) && (int)$map->clusterorderalphabet == 1))
		{
			$scripttext .= '  , showInAlphabeticalOrder: true' ."\n";
		}
		else
		{
			$scripttext .= '  , showInAlphabeticalOrder: false' ."\n";
		}

		if (isset($map->clustergridsize))
		{
			$scripttext .= '  , gridSize: '.(int)$map->clustergridsize ."\n";
		}
		if (isset($map->clusterminclustersize))
		{
			$scripttext .= '  , minClusterSize: '.(int)$map->clusterminclustersize ."\n";
		}
		
		$scripttext .= '});' ."\n";
		
		$scripttext .= 'map.geoObjects.add(markerCluster0);' ."\n";
		

        if ((isset($map->markerclustergroup) && (int)$map->markerclustergroup == 1))
		{
			if ($compatiblemodersf == 0)
			{
				$imgpath4size = JPATH_ADMINISTRATOR .'/components/com_zhyandexmap/assets/icons/';
			}
			else
			{
				$imgpath4size = JPATH_SITE .'/components/com_zhyandexmap/assets/icons/';
			}

			if (isset($this->markergroups) && !empty($this->markergroups)) 
			{
				foreach ($this->markergroups as $key => $currentmarkergroup) 
				{
					$scripttext .= 'markerCluster'.$currentmarkergroup->id.' = new ymaps.Clusterer({ maxZoom: '.$map->clusterzoom."\n";
					if ((isset($map->clusterdisableclickzoom) && (int)$map->clusterdisableclickzoom == 1))
					{
						$scripttext .= '  , clusterDisableClickZoom: true' ."\n";
					}
					else
					{
						$scripttext .= '  , clusterDisableClickZoom: false' ."\n";
					}
					if ((isset($map->clustersynchadd) && (int)$map->clustersynchadd == 1))
					{
						$scripttext .= '  , synchAdd: true' ."\n";
					}
					else
					{
						$scripttext .= '  , synchAdd: false' ."\n";
					}
					if ((isset($map->clusterorderalphabet) && (int)$map->clusterorderalphabet == 1))
					{
						$scripttext .= '  , showInAlphabeticalOrder: true' ."\n";
					}
					else
					{
						$scripttext .= '  , showInAlphabeticalOrder: false' ."\n";
					}

					if (isset($map->clustergridsize))
					{
						$scripttext .= '  , gridSize: '.(int)$map->clustergridsize ."\n";
					}
					if (isset($map->clusterminclustersize))
					{
						$scripttext .= '  , minClusterSize: '.(int)$map->clusterminclustersize ."\n";
					}
					$scripttext .= '});' ."\n";
					$scripttext .= 'map.geoObjects.add(markerCluster'.$currentmarkergroup->id.');' ."\n";
				}
			}

		}
		
	}

	
	//Double Click Zoom
	if (isset($map->doubleclickzoom) && (int)$map->doubleclickzoom == 1) 
	{
		$scripttext .= 'map.behaviors.enable(\'dblClickZoom\');' ."\n";
	} 
	else 
	{
		$scripttext .= 'if (map.behaviors.isEnabled(\'dblClickZoom\'))' ."\n";
		$scripttext .= 'map.behaviors.disable(\'dblClickZoom\');' ."\n";
	}


	//Scroll Wheel Zoom		
	if (isset($map->scrollwheelzoom) && (int)$map->scrollwheelzoom == 1) 
	{
		$scripttext .= 'map.behaviors.enable(\'scrollZoom\');' ."\n";
	} 
	else 
	{
		$scripttext .= 'if (map.behaviors.isEnabled(\'scrollZoom\'))' ."\n";
		$scripttext .= 'map.behaviors.disable(\'scrollZoom\');' ."\n";
	}
		

	//Zoom Control
	if (isset($map->zoomcontrol)) 
	{
                $ctrlPosition = "";
                $ctrlPositionFullText ="";
                
                if (isset($map->zoomcontrolpos)) 
                {
                    switch ($map->zoomcontrolpos)
                    {
                        case 1:
                            // TOP_LEFT
							$ctrlPosition = "{ top: ".(int)$map->zoomcontrolofsy.", left: ".(int)$map->zoomcontrolofsx."}";
                        break;
                        case 2:
                            // TOP_RIGHT
							$ctrlPosition = "{ top: ".(int)$map->zoomcontrolofsy.", right: ".(int)$map->zoomcontrolofsx."}";
                        break;
                        case 3:
                            // BOTTOM_RIGHT
							$ctrlPosition = "{ bottom: ".(int)$map->zoomcontrolofsy.", right: ".(int)$map->zoomcontrolofsx."}";
                        break;
                        case 4:
                            // BOTTOM_LEFT
							$ctrlPosition = "{ bottom: ".(int)$map->zoomcontrolofsy.", left: ".(int)$map->zoomcontrolofsx."}";
                        break;
                        default:
                            $ctrlPosition = "";
                        break;
                    }
                    if ($ctrlPosition != "")
                    {
                        $ctrlPositionFullText = ', '.$ctrlPosition;
                    }
                    else
                    {
                        $ctrlPositionFullText ="";
                    }
                }
                else
                {
                    $ctrlPositionFullText ="";
                }
            
		switch ($map->zoomcontrol) 
		{
			case 1:
				$scripttext .= 'map.controls.add(new ymaps.control.ZoomControl()'.$ctrlPositionFullText.');' ."\n";
			break;
			case 2:
				$scripttext .= 'map.controls.add(new ymaps.control.SmallZoomControl()'.$ctrlPositionFullText.');' ."\n";
			break;
			default:
				$scripttext .= '' ."\n";
			break;
		}
	}

	//Scale Control
	if (isset($map->scalecontrol) && (int)$map->scalecontrol == 1) 
	{
                $ctrlPosition = "";
                $ctrlPositionFullText ="";
                
                if (isset($map->scalecontrolpos)) 
                {
                    switch ($map->scalecontrolpos)
                    {
                        case 1:
                            // TOP_LEFT
							$ctrlPosition = "{ top: ".(int)$map->scalecontrolofsy.", left: ".(int)$map->scalecontrolofsx."}";
                        break;
                        case 2:
                            // TOP_RIGHT
							$ctrlPosition = "{ top: ".(int)$map->scalecontrolofsy.", right: ".(int)$map->scalecontrolofsx."}";
                        break;
                        case 3:
                            // BOTTOM_RIGHT
							$ctrlPosition = "{ bottom: ".(int)$map->scalecontrolofsy.", right: ".(int)$map->scalecontrolofsx."}";
                        break;
                        case 4:
                            // BOTTOM_LEFT
							$ctrlPosition = "{ bottom: ".(int)$map->scalecontrolofsy.", left: ".(int)$map->scalecontrolofsx."}";
                        break;
                        default:
                            $ctrlPosition = "";
                        break;
                    }
                    if ($ctrlPosition != "")
                    {
                        $ctrlPositionFullText = ', '.$ctrlPosition;
                    }
                    else
                    {
                        $ctrlPositionFullText ="";
                    }
                }
                else
                {
                    $ctrlPositionFullText ="";
                }
            
        $scripttext .= 'var scaleline = new ymaps.control.ScaleLine();' ."\n";
		$scripttext .= 'map.controls.add(scaleline'.$ctrlPositionFullText.');' ."\n";
	}

	if ((int)$map->openstreet == 1)
	{
		$scripttext .= 'osmMapType = function () { return new ymaps.Layer(' ."\n";
		$scripttext .= '\'http://tile.openstreetmap.org/%z/%x/%y.png\', {' ."\n";
		$scripttext .= '	projection: ymaps.projection.sphericalMercator' ."\n";
		$scripttext .= '});' ."\n";
		$scripttext .= '};' ."\n";

		$scripttext .= 'ymaps.mapType.storage.add(\'osmMapType\', new ymaps.MapType(' ."\n";
		$scripttext .= '	\'OSM\',' ."\n";
		$scripttext .= '	[\'osmMapType\']' ."\n";
		$scripttext .= '));' ."\n";

		$scripttext .= 'ymaps.layer.storage.add(\'osmMapType\', osmMapType);' ."\n";

		if ($credits != '')
		{
			$credits .= '<br />';
		}
		$credits .= 'OSM '.JText::_('COM_ZHYANDEXMAP_MAP_POWEREDBY').': ';
		$credits .= '<a href="http://www.openstreetmap.org/" target="_blank">OpenStreetMap</a>';
		
	}
	
	// Add Custom MapTypes - Begin
	if ((int)$map->custommaptype != 0)
	{
		foreach ($this->maptypes as $key => $currentmaptype) 
		{
			for ($i=0; $i < count($custMapTypeList); $i++)
			{
				if ($currentmaptype->id == (int)$custMapTypeList[$i])
				{
					$scripttext .= 'customMapLayer'.$currentmaptype->id.' = new ymaps.Layer(' ."\n";
					$scripttext .= '\'\', {' ."\n";

                    switch ($currentmaptype->projection)
                    {
                        case 0:
							$scripttext .= '  projection: ymaps.projection.sphericalMercator' ."\n";
                        break;
                        case 1:
							$scripttext .= '  projection: ymaps.projection.wgs84Mercator' ."\n";
                        break;
                        case 2:
							$scripttext .= '  projection: ymaps.projection.Cartesian' ."\n";
                        break;
                        default:
							$scripttext .= '  projection: ymaps.projection.sphericalMercator' ."\n";
                        break;
                    }
                    if ($currentmaptype->opacity != "")
					{
						$scripttext .= ', brightness: '.$currentmaptype->opacity ."\n";
					}

					$scripttext .= ', tileSize: ['.$currentmaptype->tilewidth.','.$currentmaptype->tileheight.']'."\n";

                    if ((int)$currentmaptype->transparent == 0)
					{
						$scripttext .= ', tileTransparent: false' ."\n";
					}
					else
					{
						$scripttext .= ', tileTransparent: true' ."\n";
					}
					
					$scripttext .= '});' ."\n";
					
					$scripttext .= 'customMapLayer'.$currentmaptype->id.'.getTileUrl = '.$currentmaptype->gettileurl ."\n";

					$scripttext .= 'customMapType'.$currentmaptype->id.' = function () { return customMapLayer'.$currentmaptype->id.';';
					$scripttext .= '};' ."\n";
					
					switch ($currentmaptype->overlay) 
					{
						case 0:
							$scripttext .= 'ymaps.mapType.storage.add(\'customMapType'.$currentmaptype->id.'\', new ymaps.MapType(' ."\n";
							$scripttext .= '	\''.str_replace('\'','\\\'', $currentmaptype->title).'\',' ."\n";
							$scripttext .= '	[\'customMapType'.$currentmaptype->id.'\']' ."\n";
							$scripttext .= '));' ."\n";
						break;
						case 1:
							$scripttext .= 'ymaps.mapType.storage.add(\'customMapType'.$currentmaptype->id.'\', new ymaps.MapType(' ."\n";
							$scripttext .= '	\''.str_replace('\'','\\\'', $currentmaptype->title).'\',' ."\n";
							$scripttext .= '	ymaps.mapType.storage.get(\'yandex#map\').getLayers().concat([\'customMapType'.$currentmaptype->id.'\'])' ."\n";
							$scripttext .= '));' ."\n";
						break;
						case 2:
							$scripttext .= 'ymaps.mapType.storage.add(\'customMapType'.$currentmaptype->id.'\', new ymaps.MapType(' ."\n";
							$scripttext .= '	\''.str_replace('\'','\\\'', $currentmaptype->title).'\',' ."\n";
							$scripttext .= '	ymaps.mapType.storage.get(\'yandex#satellite\').getLayers().concat([\'customMapType'.$currentmaptype->id.'\'])' ."\n";
							$scripttext .= '));' ."\n";
						break;
						case 3:
							$scripttext .= 'ymaps.mapType.storage.add(\'customMapType'.$currentmaptype->id.'\', new ymaps.MapType(' ."\n";
							$scripttext .= '	\''.str_replace('\'','\\\'', $currentmaptype->title).'\',' ."\n";
							$scripttext .= '	ymaps.mapType.storage.get(\'yandex#hybrid\').getLayers().concat([\'customMapType'.$currentmaptype->id.'\'])' ."\n";
							$scripttext .= '));' ."\n";
						break;
						case 4:
							$scripttext .= 'ymaps.mapType.storage.add(\'customMapType'.$currentmaptype->id.'\', new ymaps.MapType(' ."\n";
							$scripttext .= '	\''.str_replace('\'','\\\'', $currentmaptype->title).'\',' ."\n";
							$scripttext .= '	ymaps.mapType.storage.get(\'yandex#publicMap\').getLayers().concat([\'customMapType'.$currentmaptype->id.'\'])' ."\n";
							$scripttext .= '));' ."\n";
						break;
						case 5:
							$scripttext .= 'ymaps.mapType.storage.add(\'customMapType'.$currentmaptype->id.'\', new ymaps.MapType(' ."\n";
							$scripttext .= '	\''.str_replace('\'','\\\'', $currentmaptype->title).'\',' ."\n";
							$scripttext .= '	ymaps.mapType.storage.get(\'yandex#publicMapHybrid\').getLayers().concat([\'customMapType'.$currentmaptype->id.'\'])' ."\n";
							$scripttext .= '));' ."\n";
						break;
						case 6:
							if ((int)$map->openstreet == 1)
							{
								$scripttext .= 'ymaps.mapType.storage.add(\'customMapType'.$currentmaptype->id.'\', new ymaps.MapType(' ."\n";
								$scripttext .= '	\''.str_replace('\'','\\\'', $currentmaptype->title).'\',' ."\n";
								$scripttext .= '	ymaps.mapType.storage.get(\'osmMapType\').getLayers().concat([\'customMapType'.$currentmaptype->id.'\'])' ."\n";
								$scripttext .= '));' ."\n";
							}
							else
							{
								$scripttext .= 'ymaps.mapType.storage.add(\'customMapType'.$currentmaptype->id.'\', new ymaps.MapType(' ."\n";
								$scripttext .= '	\''.str_replace('\'','\\\'', $currentmaptype->title).'\',' ."\n";
								$scripttext .= '	[\'customMapType'.$currentmaptype->id.'\']' ."\n";
								$scripttext .= '));' ."\n";
							}
						break;
						default:
							$scripttext .= 'ymaps.mapType.storage.add(\'customMapType'.$currentmaptype->id.'\', new ymaps.MapType(' ."\n";
							$scripttext .= '	\''.str_replace('\'','\\\'', $currentmaptype->title).'\',' ."\n";
							$scripttext .= '	[\'customMapType'.$currentmaptype->id.'\']' ."\n";
							$scripttext .= '));' ."\n";
						break;
					}

					$scripttext .= 'ymaps.layer.storage.add(\'customMapType'.$currentmaptype->id.'\', customMapType'.$currentmaptype->id.');' ."\n";

				}
			}
			// End loop by Enabled CustomMapTypes
			
		}
		// End loop by All CustomMapTypes
		
	}
		
	if ((isset($map->maptypecontrol) && (int)$map->maptypecontrol == 1) 
	  || (isset($map->pmaptypecontrol) && (int)$map->pmaptypecontrol == 1) 
	  || (isset($map->custommaptype) && (int)$map->custommaptype == 2) )
	{
		$ctrlPosition = "";
		$ctrlPositionFullText ="";
		
		if (isset($map->maptypecontrolpos)) 
		{
			switch ($map->maptypecontrolpos)
			{
				case 1:
					// TOP_LEFT
					$ctrlPosition = "{ top: ".(int)$map->maptypecontrolofsy.", left: ".(int)$map->maptypecontrolofsx."}";
				break;
				case 2:
					// TOP_RIGHT
					$ctrlPosition = "{ top: ".(int)$map->maptypecontrolofsy.", right: ".(int)$map->maptypecontrolofsx."}";
				break;
				case 3:
					// BOTTOM_RIGHT
					$ctrlPosition = "{ bottom: ".(int)$map->maptypecontrolofsy.", right: ".(int)$map->maptypecontrolofsx."}";
				break;
				case 4:
					// BOTTOM_LEFT
					$ctrlPosition = "{ bottom: ".(int)$map->maptypecontrolofsy.", left: ".(int)$map->maptypecontrolofsx."}";
				break;
				default:
					$ctrlPosition = "";
				break;
			}
			if ($ctrlPosition != "")
			{
				$ctrlPositionFullText = ', '.$ctrlPosition;
			}
			else
			{
				$ctrlPositionFullText ="";
			}
		}
		else
		{
			$ctrlPositionFullText ="";
		}

		$ctrlMapType = "";
		
		if (isset($map->maptypecontrol) && (int)$map->maptypecontrol == 1) 
		{
			if ($ctrlMapType == "")
			{
				$ctrlMapType .= '"yandex#map", "yandex#satellite", "yandex#hybrid"';
			}
			else
			{
				$ctrlMapType .= ', "yandex#map", "yandex#satellite", "yandex#hybrid"';
			}
		}
		if (isset($map->pmaptypecontrol) && (int)$map->pmaptypecontrol == 1) 
		{
			if ($ctrlMapType == "")
			{
				$ctrlMapType .= '"yandex#publicMap", "yandex#publicMapHybrid"';
			}
			else
			{
				$ctrlMapType .= ', "yandex#publicMap", "yandex#publicMapHybrid"';
			}
		}

		if ((int)$map->openstreet == 1)
		{
			if ($ctrlMapType == "")
			{
				$ctrlMapType .= '"osmMapType"' ."\n";
			}
			else
			{
				$ctrlMapType .= ', "osmMapType"' ."\n";
			}
		}
		
		// Add Custom MapTypes - Begin
		if ((int)$map->custommaptype == 2)
		{
			foreach ($this->maptypes as $key => $currentmaptype) 
			{
				for ($i=0; $i < count($custMapTypeList); $i++)
				{
					if ($currentmaptype->id == (int)$custMapTypeList[$i])
					{
						if ($ctrlMapType == "")
						{
							$ctrlMapType .= '"customMapType'.$currentmaptype->id.'"' ."\n";
						}
						else
						{
							$ctrlMapType .= ', "customMapType'.$currentmaptype->id.'"' ."\n";
						}
					}
				}
				// End loop by Enabled CustomMapTypes
				
			}
			// End loop by All CustomMapTypes
			
		}
								
		$scripttext .= 'map.controls.add(new ymaps.control.TypeSelector(['.$ctrlMapType.'])'.$ctrlPositionFullText.');' ."\n";
	}


	// Map type
	if (isset($map->maptype)) 
	{
		switch ($map->maptype) 
		{
			
			case 1:
				$scripttext .= 'map.setType("yandex#map");' ."\n";
			break;
			case 2:
				$scripttext .= 'map.setType("yandex#satellite");' ."\n";
			break;
			case 3:
				$scripttext .= 'map.setType("yandex#hybrid");' ."\n";
			break;
			case 4:
				$scripttext .= 'map.setType("yandex#publicMap");' ."\n";
			break;
			case 5:
				$scripttext .= 'map.setType("yandex#publicMapHybrid");' ."\n";
			break;
			case 6:
				if ((int)$map->openstreet == 1)
				{
					$scripttext .= 'map.setType("osmMapType");' ."\n";
				}
			break;
			case 7:
			if ((int)$map->custommaptype != 0)
			{
				foreach ($this->maptypes as $key => $currentmaptype) 	
				{
					for ($i=0; $i < count($custMapTypeList); $i++)
					{
						if ($currentmaptype->id == (int)$custMapTypeList[$i])
						{
							if (((int)$custMapTypeFirst != 0) && ((int)$custMapTypeFirst == $currentmaptype->id))
							{
								$scripttext .= ' map.setType(\'customMapType'.$currentmaptype->id.'\');' ."\n";
							}
						}
					}
					// End loop by Enabled CustomMapTypes
					
				}
				// End loop by All CustomMapTypes
			}
			break;
			default:
				$scripttext .= '' ."\n";
			break;
		}
	}


	// MiniMap type
	if (isset($map->minimap) && (int)$map->minimap != 0) 
	{
		if (isset($map->minimaptype)) 
		{
			switch ($map->minimaptype) 
			{
				
				case 1:
					//MAP';
					$scripttextMiniMap = 'yandex#map';
				break;
				case 2:
					//SATELLITE';
					$scripttextMiniMap = 'yandex#satellite';
				break;
				case 3:
					//HYBRID';
					$scripttextMiniMap = 'yandex#hybrid';
				break;
				case 4:
					//PMAP';
					$scripttextMiniMap = 'yandex#publicMap';
				break;
				case 5:
					//PHYBRID';
					$scripttextMiniMap = 'yandex#publicMapHybrid';
				break;
				default:
					$scripttextMiniMap = '';
				break;
			}
		}
	}

	
	// Because that we set map type
	if ((int)$map->zoom != 200)
	{
		$scripttext .= '    map.setZoom('.(int)$map->zoom.');' ."\n";
	}
	else
	{
		//$scripttext .= '    map.setZoom(map.options.get("maxZoom"));' ."\n";
		$scripttext .= '  map.zoomRange.get(map.getCenter()).then(function (range) {' ."\n";
		$scripttext .= '    map.setZoom(range[1]);' ."\n";
		$scripttext .= '});' ."\n";
	}
	
	$scripttext .= '    map.options.set("minZoom",'.(int)$map->minzoom.');' ."\n";
	if ((int)$map->maxzoom != 200)
	{
		$scripttext .= '    map.options.set("maxZoom", '.(int)$map->maxzoom.');' ."\n";
	}
	
	// When changed maptype max zoom level can be other
	$scripttext .= 'map.events.add("typechange", function (e) {' ."\n";
	//$scripttext .= '     alert("Change Type!");' ."\n";
	$scripttext .= '  map.zoomRange.get(map.getCenter()).then(function (range) {' ."\n";
	//$scripttext .= '  alert("range"+range[1]);';
	
	$scripttext .= '  if (map.getZoom() > range[1] )' ."\n";
	$scripttext .= '  {	' ."\n";
	//$scripttext .= '     alert("Change Zoom!");' ."\n";
	$scripttext .= '    map.setZoom(range[1]);' ."\n";
	$scripttext .= '  }' ."\n";
	$scripttext .= '});' ."\n";
	$scripttext .= '});' ."\n";

	if (isset($map->mapbounds) && $map->mapbounds != "")
	{
		$mapBoundsArray = explode(";", str_replace(',',';',$map->mapbounds));
		if (count($mapBoundsArray) != 4)
		{
			$scripttext .= 'alert("'.JText::_('COM_ZHYANDEXMAP_MAP_ERROR_MAPBOUNDS').'");'."\n";
		}
		else
		{
			

			$scripttext .= '   var allowedBounds = [' ."\n";
			$scripttext .= '	 ['.$mapBoundsArray[0].', '.$mapBoundsArray[1].'],' ."\n";
			$scripttext .= '	 ['.$mapBoundsArray[2].', '.$mapBoundsArray[3].']];' ."\n";
			
			// Listen for the event
			$scripttext .= '  map.events.add("boundschange", function() {' ."\n";

			// Out of bounds - Move the map back within the bounds
			$scripttext .= '	 var c = map.getCenter(),' ."\n";
			$scripttext .= '		 y = c[0],' ."\n";
			$scripttext .= '		 x = c[1],' ."\n";
			$scripttext .= '		 maxY = allowedBounds[1][0],' ."\n";
			$scripttext .= '		 maxX = allowedBounds[1][1],' ."\n";
			$scripttext .= '		 minY = allowedBounds[0][0],' ."\n";
			$scripttext .= '		 minX = allowedBounds[0][1];' ."\n";

			$scripttext .= '	 if (maxX < minX)' ."\n";
			$scripttext .= '	{' ."\n";
			$scripttext .= '	  	minX = allowedBounds[1][1];' ."\n";
			$scripttext .= '	  	maxX = allowedBounds[0][1];' ."\n";
			$scripttext .= '	}' ."\n";
			$scripttext .= '	 if (maxY < minY)' ."\n";
			$scripttext .= '	{' ."\n";
			$scripttext .= '	  	minY = allowedBounds[1][0];' ."\n";
			$scripttext .= '		maxY = allowedBounds[0][0];' ."\n";
			$scripttext .= '	}' ."\n";
			$scripttext .= '	 if ((x <= maxX && x >= minX) && (y <= maxY && y >= minY)) return;' ."\n";
			
			$scripttext .= '	 if (x < minX) x = minX;' ."\n";
			$scripttext .= '	 if (x > maxX) x = maxX;' ."\n";
			$scripttext .= '	 if (y < minY) y = minY;' ."\n";
			$scripttext .= '	 if (y > maxY) y = maxY;' ."\n";

			$scripttext .= '	 var newCenter = [];' ."\n";
			$scripttext .= '	    newCenter.push(y);' ."\n";
			$scripttext .= '	    newCenter.push(x);' ."\n";
			$scripttext .= '	 map.setCenter(newCenter);' ."\n";
			$scripttext .= '   });' ."\n";
		}
	}	
	

	//MiniMap
	if (isset($map->minimap) && (int)$map->minimap != 0) 
	{
                $ctrlPosition = "";
                $ctrlPositionFullText ="";
                
                if (isset($map->minimappos)) 
                {
                    switch ($map->minimappos)
                    {
                        case 1:
                            // TOP_LEFT
							$ctrlPosition = "{ top: ".(int)$map->minimapofsy.", left: ".(int)$map->minimapofsx."}";
                        break;
                        case 2:
                            // TOP_RIGHT
							$ctrlPosition = "{ top: ".(int)$map->minimapofsy.", right: ".(int)$map->minimapofsx."}";
                        break;
                        case 3:
                            // BOTTOM_RIGHT
							$ctrlPosition = "{ bottom: ".(int)$map->minimapofsy.", right: ".(int)$map->minimapofsx."}";
                        break;
                        case 4:
                            // BOTTOM_LEFT
							$ctrlPosition = "{ bottom: ".(int)$map->minimapofsy.", left: ".(int)$map->minimapofsx."}";
                        break;
                        default:
                            $ctrlPosition = "";
                        break;
                    }
                    if ($ctrlPosition != "")
                    {
                        $ctrlPositionFullText = ', '.$ctrlPosition;
                    }
                    else
                    {
                        $ctrlPositionFullText ="";
                    }
                }
                else
                {
                    $ctrlPositionFullText ="";
                }

            $scripttext .= 'minimap = new ymaps.control.MiniMap();' ."\n";

			if ((int)$map->minimap == 1)
			{
				$scripttext .= 'minimap.expand();' ."\n";
			}
			else
			{
				$scripttext .= 'minimap.collapse();' ."\n";
			}

			if ($scripttextMiniMap != "")
			{
				$scripttext .= 'minimap.setType("'.$scripttextMiniMap.'");' ."\n";
			}

			
            $scripttext .= 'map.controls.add(minimap'.$ctrlPositionFullText.');' ."\n";
	}
	
	//Toolbar
	if (isset($map->toolbar) && (int)$map->toolbar == 1) 
	{
                $ctrlPosition = "";
                $ctrlPositionFullText ="";
                
                if (isset($map->toolbarpos)) 
                {
                    switch ($map->toolbarpos)
                    {
                        case 1:
                            // TOP_LEFT
							$ctrlPosition = "{ top: ".(int)$map->toolbarofsy.", left: ".(int)$map->toolbarofsx."}";
                        break;
                        case 2:
                            // TOP_RIGHT
							$ctrlPosition = "{ top: ".(int)$map->toolbarofsy.", right: ".(int)$map->toolbarofsx."}";
                        break;
                        case 3:
                            // BOTTOM_RIGHT
							$ctrlPosition = "{ bottom: ".(int)$map->toolbarofsy.", right: ".(int)$map->toolbarofsx."}";
                        break;
                        case 4:
                            // BOTTOM_LEFT
							$ctrlPosition = "{ bottom: ".(int)$map->toolbarofsy.", left: ".(int)$map->toolbarofsx."}";
                        break;
                        default:
                            $ctrlPosition = "";
                        break;
                    }
                    if ($ctrlPosition != "")
                    {
                        $ctrlPositionFullText = ', '.$ctrlPosition;
                    }
                    else
                    {
                        $ctrlPositionFullText ="";
                    }
                }
                else
                {
                    $ctrlPositionFullText ="";
                }

                $scripttext .= 'var toolbar = new ymaps.control.MapTools();' ."\n";
                $scripttext .= 'map.controls.add(toolbar'.$ctrlPositionFullText.');' ."\n";


				
		if (isset($map->autopositioncontrol) && (int)$map->autopositioncontrol == 1) 
		{
				switch ((int)$map->autopositionbutton) 
				{
					case 1:
						$scripttext .= 'var btnGeoPosition = new ymaps.control.Button({ data: { image: "'.$imgpathUtils.'geolocation.png", content: "", title: "'.JText::_('COM_ZHYANDEXMAP_MAP_AUTOPOSITIONBUTTON').'"}});' ."\n";
					break;
					case 2:
						$scripttext .= 'var btnGeoPosition = new ymaps.control.Button({ data: { content: "'.JText::_('COM_ZHYANDEXMAP_MAP_AUTOPOSITIONBUTTON').'", title: "'.JText::_('COM_ZHYANDEXMAP_MAP_AUTOPOSITIONBUTTON').'"}});' ."\n";
					break;
					case 3:
						$scripttext .= 'var btnGeoPosition = new ymaps.control.Button({ data: { image: "'.$imgpathUtils.'geolocation.png", content: "'.JText::_('COM_ZHYANDEXMAP_MAP_AUTOPOSITIONBUTTON').'", title: "'.JText::_('COM_ZHYANDEXMAP_MAP_AUTOPOSITIONBUTTON').'"}});' ."\n";
					break;
					default:
						$scripttext .= 'var btnGeoPosition = new ymaps.control.Button({ data: { image: "'.$imgpathUtils.'geolocation.png", content: "", title: "'.JText::_('COM_ZHYANDEXMAP_MAP_AUTOPOSITIONBUTTON').'"}});' ."\n";
					break;
				}

				$scripttext .= 'btnGeoPosition.events.add("click", function (e) {' ."\n";
				$scripttext .= '	findMyPosition("Button");' ."\n";
				$scripttext .= '}, toolbar);' ."\n";
				$scripttext .= 'toolbar.add(btnGeoPosition);' ."\n";
		}
		
		
		if (isset($map->markerlistpos) && (int)$map->markerlistpos != 0) 
		{
		
			if ((int)$map->markerlistpos == 111
			  ||(int)$map->markerlistpos == 112
			  ||(int)$map->markerlistpos == 121
			  ) 
			{
				// Do not create button when table or external
			}
			else
			{
				if ((int)$map->markerlistbuttontype == 0)
				{
					// Skip creation for non-button
				}
				else
				{
						
						switch ($map->markerlistbuttontype) 
						{
							case 0:
								$btnPlacemarkListOptions ="" ;
							break;
							case 1:
								$btnPlacemarkListOptions ="" ;
							break;
							case 2:
								$btnPlacemarkListOptions ="" ;
							break;
							case 11:
								$btnPlacemarkListOptions ="btnPlacemarkList.select();" ."\n";
							break;
							case 12:
								$btnPlacemarkListOptions ="btnPlacemarkList.select();" ."\n";
							break;
							default:
								$btnPlacemarkListOptions ="" ;
							break;
						}		
						
						switch ((int)$map->markerlistbuttontype) 
						{
							case 1:
								$scripttext .= 'var btnPlacemarkList = new ymaps.control.Button({ data: { image: "'.$imgpathUtils.'star.png", content: "", title: "'.JText::_('COM_ZHYANDEXMAP_MAP_PLACEMARKLIST').'"}});' ."\n";
							break;
							case 2:
								$scripttext .= 'var btnPlacemarkList = new ymaps.control.Button({ data: { content: "'.JText::_('COM_ZHYANDEXMAP_MAP_PLACEMARKLIST').'", title: "'.JText::_('COM_ZHYANDEXMAP_MAP_PLACEMARKLIST').'"}});' ."\n";
							break;
							case 11:
								$scripttext .= 'var btnPlacemarkList = new ymaps.control.Button({ data: { image: "'.$imgpathUtils.'star.png", content: "", title: "'.JText::_('COM_ZHYANDEXMAP_MAP_PLACEMARKLIST').'"}});' ."\n";
							break;
							case 2:
								$scripttext .= 'var btnPlacemarkList = new ymaps.control.Button({ data: { content: "'.JText::_('COM_ZHYANDEXMAP_MAP_PLACEMARKLIST').'", title: "'.JText::_('COM_ZHYANDEXMAP_MAP_PLACEMARKLIST').'"}});' ."\n";
							default:
								$scripttext .= 'var btnPlacemarkList = new ymaps.control.Button({ data: { image: "'.$imgpathUtils.'star.png", content: "", title: "'.JText::_('COM_ZHYANDEXMAP_MAP_PLACEMARKLIST').'"}});' ."\n";
							break;
						}

						$scripttext .= 'btnPlacemarkList.events.add("select", function (e) {' ."\n";
						$scripttext .= '		var toHideDiv = document.getElementById("YMapsMarkerList");' ."\n";
						$scripttext .= '		toHideDiv.style.display = "block";' ."\n";
						$scripttext .= '}, toolbar);' ."\n";

						$scripttext .= 'btnPlacemarkList.events.add("deselect", function (e) {' ."\n";
						$scripttext .= '		var toHideDiv = document.getElementById("YMapsMarkerList");' ."\n";
						$scripttext .= '		toHideDiv.style.display = "none";' ."\n";
						$scripttext .= '}, toolbar);' ."\n";

						
						$scripttext .= $btnPlacemarkListOptions;
						
						$scripttext .= 'toolbar.add(btnPlacemarkList);' ."\n";
					
				}
			}
		
		}
		
	}

	if (isset($licenseinfo) && (int)$licenseinfo != 0) 
	{
	
		if ((int)$licenseinfo == 102 // Map-License (into credits)
		  ) 
		{
			// Do not create button when L-M, M-L or external
			if ($credits != '')
			{
				$credits .= '<br />';
			}
			$credits .= ''.JText::_('COM_ZHYANDEXMAP_MAP_POWEREDBY').': ';
			$credits .= '<a href="http://www.zhuk.cc/" target="_blank" alt="'.JText::_('COM_ZHYANDEXMAP_MAP_POWEREDBY').'">zhuk.cc</a>';
		}
		else
		{
		}
	}
	
	if ($credits != '')
	{
		$scripttext .= '  document.getElementById("YMapsCredit").innerHTML = \''.$credits.'\';'."\n";
	}

	//Search
	if (isset($map->search) && (int)$map->search == 1) 
	{
                $ctrlPosition = "";
                $ctrlPositionFullText ="";
                
                if (isset($map->searchpos)) 
                {
                    switch ($map->searchpos)
                    {
                        case 1:
                            // TOP_LEFT
							$ctrlPosition = "{ top: ".(int)$map->searchofsy.", left: ".(int)$map->searchofsx."}";
                        break;
                        case 2:
                            // TOP_RIGHT
							$ctrlPosition = "{ top: ".(int)$map->searchofsy.", right: ".(int)$map->searchofsx."}";
                        break;
                        case 3:
                            // BOTTOM_RIGHT
							$ctrlPosition = "{ bottom: ".(int)$map->searchofsy.", right: ".(int)$map->searchofsx."}";
                        break;
                        case 4:
                            // BOTTOM_LEFT
							$ctrlPosition = "{ bottom: ".(int)$map->searchofsy.", left: ".(int)$map->searchofsx."}";
                        break;
                        default:
                            $ctrlPosition = "";
                        break;
                    }
                    if ($ctrlPosition != "")
                    {
                        $ctrlPositionFullText = ', '.$ctrlPosition;
                    }
                    else
                    {
                        $ctrlPositionFullText ="";
                    }
                }
                else
                {
                    $ctrlPositionFullText ="";
                }

				
                $scripttext .= 'searchControl = new ymaps.control.SearchControl();' ."\n";
                $scripttext .= 'searchControlPMAP = new ymaps.control.SearchControl({provider: "yandex#publicMap"});' ."\n";
				$scripttext .= '   if ((map.getType() == "yandex#publicMap") || (map.getType() == "yandex#publicMapHybrid"))';
				$scripttext .= '   {';
				//$scripttext .= '	  alert("PMAP");' ."\n";
                $scripttext .= '	  map.controls.add(searchControlPMAP'.$ctrlPositionFullText.');' ."\n";
				$scripttext .= '   }';
				$scripttext .= '   else';
				$scripttext .= '   {';
				//$scripttext .= '	  alert("MAP");' ."\n";
                $scripttext .= '	  map.controls.add(searchControl'.$ctrlPositionFullText.');' ."\n";
				$scripttext .= '   }';
				

				$scripttext .= 'map.events.add("typechange", function (e) {' ."\n";
				$scripttext .= '   if ((map.getType() == "yandex#publicMap") || (map.getType() == "yandex#publicMapHybrid"))';
				$scripttext .= '   {';
				//$scripttext .= '	  alert("PMAP");' ."\n";
				$scripttext .= '	  map.controls.remove(searchControl);' ."\n";
				$scripttext .= '	  map.controls.add(searchControlPMAP'.$ctrlPositionFullText.');' ."\n";
				$scripttext .= '   }';
				$scripttext .= '   else';
				$scripttext .= '   {';
				//$scripttext .= '	  alert("Map");' ."\n";
				$scripttext .= '	  map.controls.remove(searchControlPMAP);' ."\n";
				$scripttext .= '	  map.controls.add(searchControl'.$ctrlPositionFullText.');' ."\n";
				$scripttext .= '   }';
				$scripttext .= '});' ."\n";
				
	}



	//Traffic
	if (isset($map->traffic) && (int)$map->traffic == 1) 
	{
                $ctrlPosition = "";
                $ctrlPositionFullText ="";
                
                if (isset($map->trafficpos)) 
                {
                    switch ($map->trafficpos)
                    {
                        case 1:
                            // TOP_LEFT
							$ctrlPosition = "{ top: ".(int)$map->trafficofsy.", left: ".(int)$map->trafficofsx."}";
                        break;
                        case 2:
                            // TOP_RIGHT
							$ctrlPosition = "{ top: ".(int)$map->trafficofsy.", right: ".(int)$map->trafficofsx."}";
                        break;
                        case 3:
                            // BOTTOM_RIGHT
							$ctrlPosition = "{ bottom: ".(int)$map->trafficofsy.", right: ".(int)$map->trafficofsx."}";
                        break;
                        case 4:
                            // BOTTOM_LEFT
							$ctrlPosition = "{ bottom: ".(int)$map->trafficofsy.", left: ".(int)$map->trafficofsx."}";
                        break;
                        default:
                            $ctrlPosition = "";
                        break;
                    }
                    if ($ctrlPosition != "")
                    {
                        $ctrlPositionFullText = ', '.$ctrlPosition;
                    }
                    else
                    {
                        $ctrlPositionFullText ="";
                    }
                }
                else
                {
                    $ctrlPositionFullText ="";
                }

				if (isset($map->trafficprovider) && (int)$map->trafficprovider == 1) 
				{
					$trafficProvider = 'providerKey: \'traffic#archive\'';
				}
				else
				{
					$trafficProvider = 'providerKey: \'traffic#actual\'';
				}
				
				if (isset($map->trafficlayer) && (int)$map->trafficlayer == 1) 
				{
					$scripttext .= 'map.controls.add(new ymaps.control.TrafficControl({'.$trafficProvider.', shown: true})'.$ctrlPositionFullText.');' ."\n";
				}
				else
				{
					$scripttext .= 'map.controls.add(new ymaps.control.TrafficControl({'.$trafficProvider.'})'.$ctrlPositionFullText.');' ."\n";
				}
	}


	/*
	if (isset($map->markermanager) && (int)$map->markermanager == 1) 
	{
		$scripttext .= 'var objectManager = new YMaps.ObjectManager();'."\n";
		$scripttext .= 'map.addOverlay(objectManager);'."\n";
	}
	*/
	
	if (isset($map->rightbuttonmagnifier) && (int)$map->rightbuttonmagnifier == 1) 
	{
		$scripttext .= 'map.behaviors.enable(\'rightMouseButtonMagnifier\');' ."\n";
	} 
	else 
	{
		$scripttext .= 'if (map.behaviors.isEnabled(\'rightMouseButtonMagnifier\'))' ."\n";
		$scripttext .= 'map.behaviors.disable(\'rightMouseButtonMagnifier\');' ."\n";
	}


	if (isset($map->magnifier)) 
	{
		switch ((int)$map->magnifier)
		{
			case 0:
			break;
			case 1:
				$scripttext .= 'map.behaviors.enable(\'leftMouseButtonMagnifier\');'."\n";
			break;
			case 2:
				$scripttext .= 'map.behaviors.enable(\'ruler\');'."\n";
			break;
			default:
			break;
		}
	}

	if (isset($map->draggable) && (int)$map->draggable == 1) 
	{
		$scripttext .= 'map.behaviors.enable(\'drag\');' ."\n";
	} 
	else 
	{
		$scripttext .= 'if (map.behaviors.isEnabled(\'drag\'))' ."\n";
		$scripttext .= 'map.behaviors.disable(\'drag\');' ."\n";
	}

	/*
	
	//Grid Coordinates		
	if (isset($map->gridcoordinates) && (int)$map->gridcoordinates == 1) 
	{
		$scripttext .= 'map.addLayer(new YMaps.Layer(new YMaps.TileDataSource("http://lrs.maps.yandex.net/tiles/?l=grd&v=1.0&%c", true, false)));' ."\n";
	}
	*/
	
	

	//UserMarker - begin
	if ($allowUserMarker == 1)
	{
		$db = JFactory::getDBO();
		
		$scripttext .= 'if (ymaps.geolocation) {' ."\n";
		$scripttext .= '  var insertPlacemarkLocation = [ymaps.geolocation.longitude, ymaps.geolocation.latitude];' ."\n";
		$scripttext .= '}else {' ."\n";
		$scripttext .= '  var insertPlacemarkLocation = [30.3158, 59.9388];' ."\n";
		$scripttext .= '}' ."\n";

		$scripttext .= 'var insertPlacemark = new ymaps.Placemark(insertPlacemarkLocation);' ."\n";

		$scripttext .= 'insertPlacemark.options.set("draggable", true);' ."\n";

		$scripttext .= 'insertPlacemark.properties.set("balloonContentHeader", "'.JText::_( 'COM_ZHYANDEXMAP_MAP_USER_NEWMARKER' ).'");' ."\n";
		$scripttext .= 'insertPlacemark.properties.set("balloonContentBody", "'.JText::_( 'COM_ZHYANDEXMAP_MAP_USER_NEWMARKER_DESC' ).'");' ."\n";
		

		$scripttext .= 'map.geoObjects.add(insertPlacemark);' ."\n";

		$query = $db->getQuery(true);
		
		$query->select('h.title as text, h.id as value ');
		$query->from('#__zhyandexmaps_markergroups as h');
		$query->leftJoin('#__categories as c ON h.catid=c.id');
		$query->where('1=1');
		// get all groups, because you can add marker and disable group
		//$query->where('h.published=1');
		$query->order('h.title');
		
		$db->setQuery($query);    

		if (!$db->query())
		{
			$scripttext .= 'alert("Error (Load Group List Item): " + "' . $db->getEscaped($db->getErrorMsg()).'");';
		}
		else
		{
			$newMarkerGroupList = $db->loadObjectList();
		}
		

		$scripttext .= 'var contentInsertPlacemarkPart1 = \'<div id="contentInsertPlacemark">\' +' ."\n";
		$scripttext .= '\'<h1 id="headContentInsertPlacemark" class="insertPlacemarkHead">'.
			'<img src="'.$imgpathUtils.'published'.(int)$map->usermarkerspublished.'.png" alt="" /> '.
			JText::_( 'COM_ZHYANDEXMAP_MAP_USER_NEWMARKER' ).'</h1>\'+' ."\n";
		$scripttext .= '\'<div id="bodyContentInsertPlacemark"  class="insertPlacemarkBody">\'+'."\n";
		//$scripttext .= '    \''.JText::_( 'COM_ZHYANDEXMAP_MAP_USER_LNG' ).' \'+insertPlacemarkLocation.getLng() + ' ."\n";
		//$scripttext .= '    \'<br />'.JText::_( 'COM_ZHYANDEXMAP_MAP_USER_LAT' ).' \'+insertPlacemarkLocation.getLat() + ' ."\n";
		$scripttext .= '    \'<form id="insertPlacemarkForm" action="'.JURI::current().'" method="post">\'+'."\n";

		// Begin Placemark Properties
		$scripttext .= '\'<div id="bodyInsertPlacemarkDivA"  class="bodyInsertProperties">\'+'."\n";
		$scripttext .= '\'<a id="bodyInsertPlacemarkA" href="javascript:showonlyone(\\\'Placemark\\\',\\\'\\\');" ><img src="'.$imgpathUtils.'collapse.png">'.JText::_( 'COM_ZHYANDEXMAP_MAP_USER_BASIC_PROPERTIES' ).'</a>\'+'."\n";
		$scripttext .= '\'</div>\'+'."\n";
		$scripttext .= '\'<div id="bodyInsertPlacemark"  class="bodyInsertPlacemarkProperties">\'+'."\n";
		$scripttext .= '    \''.JText::_( 'COM_ZHYANDEXMAP_MAP_USER_NAME' ).' \'+' ."\n";
		$scripttext .= '    \'<br />\'+' ."\n";
		$scripttext .= '    \'<input name="markername" type="text" maxlength="250" size="50" />\'+' ."\n";
		$scripttext .= '    \'<br />\'+' ."\n";
		$scripttext .= '    \''.JText::_( 'COM_ZHYANDEXMAP_MAP_USER_DESCRIPTION' ).' \'+' ."\n";
		$scripttext .= '    \'<br />\'+' ."\n";
		$scripttext .= '    \'<input name="markerdescription" type="text" maxlength="250" size="50" />\'+' ."\n";

		$scripttext .= '    \'<br />\'+' ."\n";
		$scripttext .= '    \''.JText::_( 'COM_ZHYANDEXMAP_MAPMARKER_DETAIL_HREFIMAGE_LABEL' ).' \'+' ."\n";
		$scripttext .= '    \'<br />\'+' ."\n";
		$scripttext .= '    \'<input name="markerhrefimage" type="text" maxlength="500" size="50" value="" />\'+' ."\n";
		$scripttext .= '    \'<br />\'+' ."\n";

		$scripttext .= '    \'<br />\';' ."\n";

		// icon type
		$scripttext .= 'var contentInsertPlacemarkIcon = "" +' ."\n";
		if (isset($map->usermarkersicon) && (int)$map->usermarkersicon == 1) 
		{
			$iconTypeJS = " onchange=\"javascript: ";
			$iconTypeJS .= " if (document.forms.insertPlacemarkForm.markerimage.options[selectedIndex].value!=\'\') ";
			$iconTypeJS .= " {document.markericonimage.src=\'".$imgpathIcons."\' + document.forms.insertPlacemarkForm.markerimage.options[selectedIndex].value.replace(/#/g,\'%23\') + \'.png\'}";
			$iconTypeJS .= " else ";
			$iconTypeJS .= " {document.markericonimage.src=\'\'}\"";
			
			$scripttext .= '    \''.JText::_( 'COM_ZHYANDEXMAP_MAP_USER_ICON_TYPE' ).' \'+' ."\n";
			$scripttext .= ' \'';
			$scripttext .= '<img name="markericonimage" src="" alt="" />';
			$scripttext .= '\'+' ."\n";
			$scripttext .= '    \'<br />\'+' ."\n";
			$scripttext .= ' \'';
			$scripttext .= str_replace('.png<', '<', 
								str_replace('.png"', '"', 
									str_replace('JOPTION_SELECT_IMAGE', JText::_('COM_ZHYANDEXMAP_MAP_USER_IMAGESELECT'),
										str_replace(array("\r", "\r\n", "\n"),'', JHTML::_('list.images',  'markerimage', $active =  "", $iconTypeJS, $directoryIcons, $extensions =  "png")))));
			$scripttext .= '\'+' ."\n";
			$scripttext .= '    \'<br />\';' ."\n";	

		}
		else
		{
			$scripttext .= '    \'<input name="markerimage" type="hidden" value="default#" />\'+' ."\n";	
		}
		$scripttext .= '    \'\';' ."\n";


		$scripttext .= 'var contentInsertPlacemarkPart2 = "" +' ."\n";
		
		$scripttext .= '    \'<br />\'+' ."\n";

		$scripttext .= '\'</div>\'+'."\n";
		// End Placemark Properties

		// Begin Placemark Group Properties
		$scripttext .= '\'<div id="bodyInsertPlacemarkGrpDivA"  class="bodyInsertProperties">\'+'."\n";
		$scripttext .= '\'<a id="bodyInsertPlacemarkGrpA" href="javascript:showonlyone(\\\'PlacemarkGroup\\\',\\\'\\\');" ><img src="'.$imgpathUtils.'expand.png">'.JText::_( 'COM_ZHYANDEXMAP_MAP_USER_BASIC_GROUP_PROPERTIES' ).'</a>\'+'."\n";
		$scripttext .= '\'</div>\'+'."\n";
		$scripttext .= '\'<div id="bodyInsertPlacemarkGrp"  class="bodyInsertPlacemarkGrpProperties">\'+'."\n";
		$scripttext .= '    \''.JText::_( 'COM_ZHYANDEXMAP_MAP_USER_GROUP' ).' \'+' ."\n";
		$scripttext .= '    \'<br />\'+' ."\n";
		
		$scripttext .= '    \' <select name="markergroup" > \'+' ."\n";
		$scripttext .= '    \' <option value="" selected="selected">'.JText::_( 'COM_ZHYANDEXMAP_MAPMARKER_FILTER_PLACEMARK_GROUP').'</option> \'+' ."\n";
		foreach ($newMarkerGroupList as $key => $newGrp) 
		{
			$scripttext .= '    \' <option value="'.$newGrp->value.'">'.$newGrp->text.'</option> \'+' ."\n";
		}
		$scripttext .= '    \' </select> \'+' ."\n";
		
		$scripttext .= '    \'<br />\'+' ."\n";

		$scripttext .= '    \''.JText::_( 'COM_ZHYANDEXMAP_MAP_USER_CATEGORY' ).' \'+' ."\n";
		$scripttext .= '    \'<br />\'+' ."\n";
		$scripttext .= '    \' <select name="markercatid" > \'+' ."\n";
		$scripttext .= '    \' <option value="" selected="selected">'.JText::_( 'COM_ZHYANDEXMAP_MAP_FILTER_CATEGORY').'</option> \'+' ."\n";
		$scripttext .= '    \''.str_replace(array("\r", "\r\n", "\n"),'', 
		                       JHtml::_('select.options', JHtml::_('category.options', 'com_zhyandexmap'), 'value', 'text', '')) .
							   '\'+' ."\n";
		$scripttext .= '    \' </select> \'+' ."\n";
		$scripttext .= '    \'<br />\'+' ."\n";
		$scripttext .= '    \'<br />\'+' ."\n";
		$scripttext .= '\'</div>\'+'."\n";
		// End Placemark Group Properties
		
		// Begin Contact Properties
		if (isset($map->usercontact) && (int)$map->usercontact == 1) 
		{

				$scripttext .= '\'<div id="bodyInsertContactDivA"  class="bodyInsertProperties">\'+'."\n";
				$scripttext .= '\'<a id="bodyInsertContactA" href="javascript:showonlyone(\\\'Contact\\\',\\\'\\\');" ><img src="'.$imgpathUtils.'expand.png">'.JText::_( 'COM_ZHYANDEXMAP_MAP_USER_CONTACT_PROPERTIES' ).'</a>\'+'."\n";
				$scripttext .= '\'</div>\'+'."\n";
				$scripttext .= '\'<div id="bodyInsertContact"  class="bodyInsertContactProperties">\'+'."\n";
				$scripttext .= '\'<img src="'.$imgpathUtils.'published'.(int)$map->usercontactpublished.'.png" alt="" /> \'+'."\n";
				$scripttext .= '    \''.JText::_( 'COM_ZHYANDEXMAP_MAP_USER_CONTACT_NAME' ).' \'+' ."\n";
				$scripttext .= '    \'<br />\'+' ."\n";
				$scripttext .= '    \'<input name="contactname" type="text" maxlength="250" size="50" />\'+' ."\n";
				$scripttext .= '    \'<br />\'+' ."\n";
				$scripttext .= '    \''.JText::_( 'COM_ZHYANDEXMAP_MAP_USER_CONTACT_POSITION' ).' \'+' ."\n";
				$scripttext .= '    \'<br />\'+' ."\n";
				$scripttext .= '    \'<input name="contactposition" type="text" maxlength="250" size="50" />\'+' ."\n";
				$scripttext .= '    \'<br />\'+' ."\n";
				$scripttext .= '    \''.JText::_( 'COM_ZHYANDEXMAP_MAP_USER_CONTACT_PHONE' ).' \'+' ."\n";
				$scripttext .= '    \'<br />\'+' ."\n";
				$scripttext .= '    \'<input name="contactphone" type="text" maxlength="250" size="50" />\'+' ."\n";
				$scripttext .= '    \'<br />\'+' ."\n";
				$scripttext .= '    \''.JText::_( 'COM_ZHYANDEXMAP_MAP_USER_CONTACT_MOBILE' ).' \'+' ."\n";
				$scripttext .= '    \'<br />\'+' ."\n";
				$scripttext .= '    \'<input name="contactmobile" type="text" maxlength="250" size="50" />\'+' ."\n";
				$scripttext .= '    \'<br />\'+' ."\n";
				$scripttext .= '    \''.JText::_( 'COM_ZHYANDEXMAP_MAP_USER_CONTACT_FAX' ).' \'+' ."\n";
				$scripttext .= '    \'<br />\'+' ."\n";
				$scripttext .= '    \'<input name="contactfax" type="text" maxlength="250" size="50" />\'+' ."\n";
				$scripttext .= '    \'<br />\'+' ."\n";
				$scripttext .= '    \'<input name="contactid" type="hidden" value="" />\'+' ."\n";
				$scripttext .= '\'</div>\'+'."\n";
				// Contact Address
				$scripttext .= '\'<div id="bodyInsertContactAdrDivA"  class="bodyInsertProperties">\'+'."\n";
				$scripttext .= '\'<a id="bodyInsertContactAdrA" href="javascript:showonlyone(\\\'ContactAddress\\\',\\\'\\\');" ><img src="'.$imgpathUtils.'expand.png">'.JText::_( 'COM_ZHYANDEXMAP_MAP_USER_CONTACT_ADDRESS_PROPERTIES' ).'</a>\'+'."\n";
				$scripttext .= '\'</div>\'+'."\n";
				$scripttext .= '\'<div id="bodyInsertContactAdr"  class="bodyInsertContactAdrProperties">\'+'."\n";
				$scripttext .= '    \''.JText::_( 'COM_ZHYANDEXMAP_MAP_USER_CONTACT_ADDRESS' ).' \'+' ."\n";
				$scripttext .= '    \'<br />\'+' ."\n";
				$scripttext .= '    \'<textarea name="contactaddress" cols="35" rows="4"></textarea>\'+' ."\n";
				$scripttext .= '    \'<br />\'+' ."\n";
				$scripttext .= '    \'<br />\'+' ."\n";
				$scripttext .= '\'</div>\'+'."\n";
		}
		// End Contact Properties
		$scripttext .= '\'\';'."\n";



		$scripttext .= 'insertPlacemark.events.add("drag", function (e) {' ."\n";

		$scripttext .= '    insertPlacemark.balloon.close();' ."\n";
		$scripttext .= '    insertPlacemarkLocation = insertPlacemark.geometry.getCoordinates();' ."\n";

		$scripttext .= '  contentInsertPlacemarkButtons = \'<div id="contentInsertPlacemarkButtons">\' +' ."\n";
		$scripttext .= '    \'<hr />\'+' ."\n";					
		$scripttext .= '    \'<input name="markerlat" type="hidden" value="\'+insertPlacemarkLocation[1] + \'" />\'+' ."\n";
		$scripttext .= '    \'<input name="markerlng" type="hidden" value="\'+insertPlacemarkLocation[0] + \'" />\'+' ."\n";
		$scripttext .= '    \'<input name="markerid" type="hidden" value="" />\'+' ."\n";
		$scripttext .= '    \'<input name="contactid" type="hidden" value="" />\'+' ."\n";
		$scripttext .= '    \'<input name="marker_action" type="hidden" value="insert" />\'+' ."\n";	
		$scripttext .= '    \'<input name="markersubmit" type="submit" value="'.JText::_( 'COM_ZHYANDEXMAP_MAP_USER_BUTTON_ADD' ).'" />\'+' ."\n";
		$scripttext .= '    \'</form>\'+' ."\n";		
		$scripttext .= '\'</div>\'+'."\n";
		$scripttext .= '\'</div>\';'."\n";
		
		$scripttext .= 'insertPlacemark.properties.set("balloonContentHeader", "");' ."\n";
		$scripttext .= 'insertPlacemark.properties.set("balloonContentBody", "");' ."\n";

		$scripttext .= '    insertPlacemark.properties.set("balloonContent", contentInsertPlacemarkPart1+';
		$scripttext .= 'contentInsertPlacemarkIcon+';
		$scripttext .= 'contentInsertPlacemarkPart2+';
		$scripttext .= 'contentInsertPlacemarkButtons);'."\n";

		$scripttext .= '});' ."\n";


		//$scripttext .= 'YMaps.Events.observe(insertPlacemark, insertPlacemark.Events.Click, function (obj) {' ."\n";
		//$scripttext .= '    insertPlacemark.closeBalloon();' ."\n";
		//$scripttext .= '    insertPlacemarkLocation = obj.getGeoPoint().copy();' ."\n";
		//$scripttext .= '    YMaps.Events.notify(insertPlacemark, insertPlacemark.Events.BalloonOpen);' ."\n";
		//$scripttext .= '});' ."\n";
		
		$scripttext .= 'map.events.add("click", function (e) {' ."\n";
		$scripttext .= '    insertPlacemark.balloon.close();' ."\n";
		$scripttext .= '    insertPlacemarkLocation = e.get(\'coordPosition\');' ."\n";
		$scripttext .= '    insertPlacemark.geometry.setCoordinates(insertPlacemarkLocation);' ."\n";

		$scripttext .= '  contentInsertPlacemarkButtons = \'<div id="contentInsertPlacemarkButtons">\' +' ."\n";
		$scripttext .= '    \'<hr />\'+' ."\n";					
		$scripttext .= '    \'<input name="markerlat" type="hidden" value="\'+insertPlacemarkLocation[1] + \'" />\'+' ."\n";
		$scripttext .= '    \'<input name="markerlng" type="hidden" value="\'+insertPlacemarkLocation[0] + \'" />\'+' ."\n";
		$scripttext .= '    \'<input name="markerid" type="hidden" value="" />\'+' ."\n";
		$scripttext .= '    \'<input name="contactid" type="hidden" value="" />\'+' ."\n";
		$scripttext .= '    \'<input name="marker_action" type="hidden" value="insert" />\'+' ."\n";	
		$scripttext .= '    \'<input name="markersubmit" type="submit" value="'.JText::_( 'COM_ZHYANDEXMAP_MAP_USER_BUTTON_ADD' ).'" />\'+' ."\n";
		$scripttext .= '    \'</form>\'+' ."\n";		
		$scripttext .= '\'</div>\'+'."\n";
		$scripttext .= '\'</div>\';'."\n";
		
		$scripttext .= 'insertPlacemark.properties.set("balloonContentHeader", "");' ."\n";
		$scripttext .= 'insertPlacemark.properties.set("balloonContentBody", "");' ."\n";

		$scripttext .= '    insertPlacemark.properties.set("balloonContent", contentInsertPlacemarkPart1+';
		$scripttext .= 'contentInsertPlacemarkIcon+';
		$scripttext .= 'contentInsertPlacemarkPart2+';
		$scripttext .= 'contentInsertPlacemarkButtons);'."\n";
		
		$scripttext .= '});' ."\n";
		
	}
	// New Marker - End
        
		

	//Balloon	
	if (isset($map->balloon)) 
	{
		switch ($map->balloon) 
		{
			case 0:
			break;
			case 1:
				$scripttext .= 'map.balloon.open(['.$map->longitude.', ' .$map->latitude.'], { contentBody: "'.htmlspecialchars(str_replace('\\', '/', $map->title), ENT_QUOTES, 'UTF-8').'"});' ."\n";
			break;
			case 2:
				$scripttext .= 'var placemark = new ymaps.Placemark(['.$map->longitude.', ' .$map->latitude.']);' ."\n";
				if ($map->preseticontype != "")
				{
					$scripttext .= 'placemark.options.set("preset", "'.$map->preseticontype.'");' ."\n";
				}
				else
				{
					$scripttext .= 'placemark.options.set("preset", "twirl#blueStretchyIcon");' ."\n";
				}
				$scripttext .= 'placemark.properties.set("balloonContentHeader", "' .htmlspecialchars(str_replace('\\', '/', $map->title), ENT_QUOTES, 'UTF-8').'");' ."\n";
				$scripttext .= 'placemark.properties.set("balloonContentBody", "' .htmlspecialchars(str_replace('\\', '/', $map->description), ENT_QUOTES, 'UTF-8').'");' ."\n";
				$scripttext .= 'map.geoObjects.add(placemark);' ."\n";
				$scripttext .= 'placemark.balloon.open();' ."\n";
			break;
			case 3:
				$scripttext .= 'var placemark = new ymaps.Placemark(['.$map->longitude.', ' .$map->latitude.']);' ."\n";
				
				if ($map->preseticontype != "")
				{
					$scripttext .= 'placemark.options.set("preset", "'.$map->preseticontype.'");' ."\n";
				}
				else
				{
					$scripttext .= 'placemark.options.set("preset", "twirl#blueStretchyIcon");' ."\n";
				}
				$scripttext .= 'placemark.properties.set("balloonContentHeader", "' .htmlspecialchars(str_replace('\\', '/', $map->title), ENT_QUOTES, 'UTF-8').'");' ."\n";
				$scripttext .= 'placemark.properties.set("balloonContentBody", "' .htmlspecialchars(str_replace('\\', '/', $map->description), ENT_QUOTES, 'UTF-8').'");' ."\n";
				$scripttext .= 'placemark.properties.set("iconContent", "' .htmlspecialchars(str_replace('\\', '/', $map->title), ENT_QUOTES, 'UTF-8').'");' ."\n";
				$scripttext .= 'map.geoObjects.add(placemark);' ."\n";
			break;
			default:
				$scripttext .= '' ."\n";
			break;
		}
	}


	if (isset($map->markerlistpos) && (int)$map->markerlistpos != 0) 
	{
		if ((int)$map->markerlistcontent < 100) 
		{
			$scripttext .= 'var markerUL = document.getElementById("YMapsMarkerUL");'."\n";
			$scripttext .= 'if (!markerUL)'."\n";
			$scripttext .= '{'."\n";
			$scripttext .= ' alert("'.JText::_('COM_ZHYANDEXMAP_MAP_MARKERUL_NOTFIND').'");'."\n";
			$scripttext .= '}'."\n";
		}
		else
		{
			$scripttext .= 'var markerUL = document.getElementById("YMapsMarkerTABLEBODY");'."\n";
			$scripttext .= 'if (!markerUL)'."\n";
			$scripttext .= '{'."\n";
			$scripttext .= ' alert("'.JText::_('COM_ZHYANDEXMAP_MAP_MARKERTABLE_NOTFIND').'");'."\n";
			$scripttext .= '}'."\n";
		}
		
	}
		
		
	
	// Markers
	$doAddToListCount = 0;
	
	if (isset($this->markers) && !empty($this->markers)) 
	{

		foreach ($this->markers as $key => $currentmarker) 
		{
				// Main IF Begin
				if ( 
					((($currentmarker->markergroup != 0)
					    && ((int)$currentmarker->published == 1)
						&& ((int)$currentmarker->publishedgroup == 1)) || ($allowUserMarker == 1)
					) || 
					((($currentmarker->markergroup == 0)
					    && ((int)$currentmarker->published == 1)) || ($allowUserMarker == 1)
					) 
				   )
				{
					$markername ='';
					$markername = 'placemark'. $currentmarker->id;

					$scripttext .= 'var latlng'.$currentmarker->id.'= ['.$currentmarker->longitude.', ' .$currentmarker->latitude.'];' ."\n";
					
					$scripttext .= 'var '.$markername.'= new ymaps.Placemark(latlng'.$currentmarker->id.');'."\n";

					if ($externalmarkerlink == 1)
					{
						$scripttext .= 'PlacemarkByIDAdd('. $currentmarker->id.', '.$currentmarker->latitude.', ' .$currentmarker->longitude.',  placemark'. $currentmarker->id.', latlng'. $currentmarker->id.');'."\n";
					}
					
					if ((int)$currentmarker->actionbyclick == 1)
					{
						$scripttext .= $markername.'.options.set("hasBalloon", true);'."\n";;
					}
					else
					{
						$scripttext .= $markername.'.options.set("hasBalloon", false);'."\n";;
					}
					

					if ((isset($map->markercluster) && (int)$map->markercluster == 0)
					  &&(isset($map->markergroupcontrol) && (int)$map->markergroupcontrol == 0)
					  && ((int)$currentmarker->overridemarkericon == 1)
					  && ((int)$currentmarker->publishedgroup == 1)
					   )
					{
						// later when pushing into arrays
					}
					else
					{
						if (((int)$currentmarker->overridemarkericon == 1)
						  && ((int)$currentmarker->publishedgroup == 1)
						)
						{
								$imgimg = $imgpathIcons.str_replace("#", "%23", $currentmarker->groupicontype).'.png';
								$imgimg4size = $imgpath4size.$currentmarker->groupicontype.'.png';

								list ($imgwidth, $imgheight) = getimagesize($imgimg4size);

								$scripttext .= $markername.'.options.set("iconImageHref", "'.$imgimg.'");' ."\n";
								$scripttext .= $markername.'.options.set("iconImageSize", ['.$imgwidth.','.$imgheight.']);' ."\n";
								if (isset($currentmarker->groupiconofsetx) 
								 && isset($currentmarker->groupiconofsety) 
								// Write offset all time
								// && ((int)$currentmarker->groupiconofsetx !=0
								//  || (int)$currentmarker->groupiconofsety !=0)
								 )
								{
									// This is for compatibility
									$ofsX = (int)$currentmarker->groupiconofsetx - 7;
									$ofsY = (int)$currentmarker->groupiconofsety - $imgheight;
									$scripttext .= $markername.'.options.set("iconImageOffset", ['.$ofsX.','.$ofsY.']);' ."\n";
								}
						}
						else
						{
							if ((int)$currentmarker->showiconcontent == 0)
							{
								$imgimg = $imgpathIcons.str_replace("#", "%23", $currentmarker->icontype).'.png';
								$imgimg4size = $imgpath4size.$currentmarker->icontype.'.png';

								list ($imgwidth, $imgheight) = getimagesize($imgimg4size);

								$scripttext .= $markername.'.options.set("iconImageHref", "'.$imgimg.'");' ."\n";
								$scripttext .= $markername.'.options.set("iconImageSize", ['.$imgwidth.','.$imgheight.']);' ."\n";
								if (isset($currentmarker->iconofsetx) 
								 && isset($currentmarker->iconofsety) 
								// Write offset all time
								// && ((int)$currentmarker->iconofsetx !=0
								//  || (int)$currentmarker->iconofsety !=0)
								 )
								{
									// This is for compatibility
									$ofsX = (int)$currentmarker->iconofsetx - 7;
									$ofsY = (int)$currentmarker->iconofsety - $imgheight;
									$scripttext .= $markername.'.options.set("iconImageOffset", ['.$ofsX.','.$ofsY.']);' ."\n";
								}
							}
							else
							{
								if ($currentmarker->preseticontype != "")
								{
									$scripttext .= $markername.'.options.set("preset", "'.$currentmarker->preseticontype.'");' ."\n";
								}
								else
								{
									$scripttext .= $markername.'.options.set("preset", "twirl#blueStretchyIcon");' ."\n";
								}
								
								if ((int)$currentmarker->showiconcontent == 1)
								{
									if ($currentmarker->presettitle != "")
									{
										$scripttext .= $markername.'.properties.set("iconContent", "' .htmlspecialchars(str_replace('\\', '/', $currentmarker->presettitle), ENT_QUOTES, 'UTF-8').'");' ."\n";
									}
									else
									{
										$scripttext .= $markername.'.properties.set("iconContent", "' .htmlspecialchars(str_replace('\\', '/', $currentmarker->title), ENT_QUOTES, 'UTF-8').'");' ."\n";
									}
								}
							}

						}
					}

						
					//}

					if (($allowUserMarker == 0)
					 || (isset($currentmarker->userprotection) && (int)$currentmarker->userprotection == 1)
					 || ($currentUserID == 0)
					 || (isset($currentmarker->createdbyuser) 
						&& (((int)$currentmarker->createdbyuser != $currentUserID )
						   || ((int)$currentmarker->createdbyuser == 0)))
					 )
					{
					
						$scripttext .= 'var contentStringHead'. $currentmarker->id.' = \'<div id="placemarkContent'. $currentmarker->id.'">\' +' ."\n";
						if (isset($currentmarker->markercontent) &&
							(((int)$currentmarker->markercontent == 0) ||
							 ((int)$currentmarker->markercontent == 1))
							)
						{
							$scripttext .= '\'<h1 id="headContent'. $currentmarker->id.'" class="placemarkHead">'.htmlspecialchars(str_replace('\\', '/', $currentmarker->title), ENT_QUOTES, 'UTF-8').'</h1>\'+' ."\n";
						}
						$scripttext .= '\'</div>\';'."\n";
						
						$scripttext .= 'var contentStringHeadCluster'. $currentmarker->id.' = \'<div id="placemarkContentCluster'. $currentmarker->id.'">\' +' ."\n";
						$scripttext .= '\'<span id="headContentCluster'. $currentmarker->id.'" class="placemarkHeadCluster">'.htmlspecialchars(str_replace('\\', '/', $currentmarker->title), ENT_QUOTES, 'UTF-8').'</span>\'+' ."\n";
						$scripttext .= '\'</div>\';'."\n";

						$scripttext .= 'var contentStringBody'. $currentmarker->id.' = \'<div id="bodyContent'. $currentmarker->id.'"  class="placemarkBody">\'+'."\n";

								if ($currentmarker->hrefimage!="")
								{
									 $scripttext .= '\'<img src="'.$currentmarker->hrefimage.'" alt="" />\'+'."\n";
								}

								if (isset($currentmarker->markercontent) &&
									(((int)$currentmarker->markercontent == 0) ||
									 ((int)$currentmarker->markercontent == 2))
									)
								{
									$scripttext .= '\''.htmlspecialchars(str_replace('\\', '/', $currentmarker->description), ENT_QUOTES, 'UTF-8').'\'+'."\n";
								}
								$scripttext .= '\''.str_replace("'", "\'", str_replace(array("\r", "\r\n", "\n"), '', $currentmarker->descriptionhtml)).'\'+'."\n";

								//$scripttext .= ' latlng'. $currentmarker->id. '.toString()+'."\n";

								// Contact info - begin
								if (isset($map->usercontact) && ((int)$map->usercontact != 0))
								{
									if (isset($currentmarker->showcontact) && ((int)$currentmarker->showcontact != 0))
									{
										switch ((int)$currentmarker->showcontact) 
										{
											case 1:
												if ($currentmarker->contact_name != "") 
												{
													$scripttext .= '\'<p class="placemarkBodyContact">'.JText::_('COM_ZHYANDEXMAP_MAP_USER_CONTACT_NAME').' '.htmlspecialchars(str_replace('\\', '/', $currentmarker->contact_name), ENT_QUOTES, 'UTF-8').'</p>\'+'."\n";
												}
												if ($currentmarker->contact_position != "") 
												{
													$scripttext .= '\'<p class="placemarkBodyContact">'.JText::_('COM_ZHYANDEXMAP_MAP_USER_CONTACT_POSITION').' '.htmlspecialchars(str_replace('\\', '/', $currentmarker->contact_position), ENT_QUOTES, 'UTF-8').'</p>\'+'."\n";
												}
												if ($currentmarker->contact_address != "") 
												{
													$scripttext .= '\'<p class="placemarkBodyContact">'.JText::_('COM_ZHYANDEXMAP_MAP_USER_CONTACT_ADDRESS').' '.str_replace('<br /><br />', '<br />', str_replace(array("\r", "\r\n", "\n"), '<br />', htmlspecialchars(str_replace('\\', '/', $currentmarker->contact_address), ENT_QUOTES, 'UTF-8'))).'</p>\'+'."\n";
												}
												if ($currentmarker->contact_phone != "") 
												{
													$scripttext .= '\'<p class="placemarkBodyContact">'.JText::_('COM_ZHYANDEXMAP_MAP_USER_CONTACT_PHONE').' '.htmlspecialchars(str_replace('\\', '/', $currentmarker->contact_phone), ENT_QUOTES, 'UTF-8').'</p>\'+'."\n";
												}
												if ($currentmarker->contact_mobile != "") 
												{
													$scripttext .= '\'<p class="placemarkBodyContact">'.JText::_('COM_ZHYANDEXMAP_MAP_USER_CONTACT_MOBILE').' '.htmlspecialchars(str_replace('\\', '/', $currentmarker->contact_mobile), ENT_QUOTES, 'UTF-8').'</p>\'+'."\n";
												}
												if ($currentmarker->contact_fax != "") 
												{
													$scripttext .= '\'<p class="placemarkBodyContact">'.JText::_('COM_ZHYANDEXMAP_MAP_USER_CONTACT_FAX').' '.htmlspecialchars(str_replace('\\', '/', $currentmarker->contact_fax), ENT_QUOTES, 'UTF-8').'</p>\'+'."\n";
												}
											break;
											case 2:
												if ($currentmarker->contact_name != "") 
												{
													$scripttext .= '\'<p class="placemarkBodyContact">'.htmlspecialchars(str_replace('\\', '/', $currentmarker->contact_name), ENT_QUOTES, 'UTF-8').'</p>\'+'."\n";
												}
												if ($currentmarker->contact_position != "") 
												{
													$scripttext .= '\'<p class="placemarkBodyContact">'.htmlspecialchars(str_replace('\\', '/', $currentmarker->contact_position), ENT_QUOTES, 'UTF-8').'</p>\'+'."\n";
												}
												if ($currentmarker->contact_address != "") 
												{
													$scripttext .= '\'<p class="placemarkBodyContact"><img src="'.$imgpathUtils.'address.png" alt="'.JText::_('COM_ZHYANDEXMAP_MAP_USER_CONTACT_ADDRESS').'" />'.str_replace('<br /><br />', '<br />', str_replace(array("\r", "\r\n", "\n"), '<br />', htmlspecialchars(str_replace('\\', '/', $currentmarker->contact_address), ENT_QUOTES, 'UTF-8'))).'</p>\'+'."\n";
												}
												if ($currentmarker->contact_phone != "") 
												{
													$scripttext .= '\'<p class="placemarkBodyContact"><img src="'.$imgpathUtils.'phone.png" alt="'.JText::_('COM_ZHYANDEXMAP_MAP_USER_CONTACT_PHONE').'" />'.htmlspecialchars(str_replace('\\', '/', $currentmarker->contact_phone), ENT_QUOTES, 'UTF-8').'</p>\'+'."\n";
												}
												if ($currentmarker->contact_mobile != "") 
												{
													$scripttext .= '\'<p class="placemarkBodyContact"><img src="'.$imgpathUtils.'mobile.png" alt="'.JText::_('COM_ZHYANDEXMAP_MAP_USER_CONTACT_MOBILE').'" />'.htmlspecialchars(str_replace('\\', '/', $currentmarker->contact_mobile), ENT_QUOTES, 'UTF-8').'</p>\'+'."\n";
												}
												if ($currentmarker->contact_fax != "") 
												{
													$scripttext .= '\'<p class="placemarkBodyContact"><img src="'.$imgpathUtils.'fax.png" alt="'.JText::_('COM_ZHYANDEXMAP_MAP_USER_CONTACT_FAX').'" />'.htmlspecialchars(str_replace('\\', '/', $currentmarker->contact_fax), ENT_QUOTES, 'UTF-8').'</p>\'+'."\n";
												}
											break;
											case 3:
												if ($currentmarker->contact_name != "") 
												{
													$scripttext .= '\'<p class="placemarkBodyContact">'.htmlspecialchars(str_replace('\\', '/', $currentmarker->contact_name), ENT_QUOTES, 'UTF-8').'</p>\'+'."\n";
												}
												if ($currentmarker->contact_position != "") 
												{
													$scripttext .= '\'<p class="placemarkBodyContact">'.htmlspecialchars(str_replace('\\', '/', $currentmarker->contact_position), ENT_QUOTES, 'UTF-8').'</p>\'+'."\n";
												}
												if ($currentmarker->contact_address != "") 
												{
													$scripttext .= '\'<p class="placemarkBodyContact">'.str_replace('<br /><br />', '<br />', str_replace(array("\r", "\r\n", "\n"), '<br />', htmlspecialchars(str_replace('\\', '/', $currentmarker->contact_address), ENT_QUOTES, 'UTF-8'))).'</p>\'+'."\n";
												}
												if ($currentmarker->contact_phone != "") 
												{
													$scripttext .= '\'<p class="placemarkBodyContact">'.htmlspecialchars(str_replace('\\', '/', $currentmarker->contact_phone), ENT_QUOTES, 'UTF-8').'</p>\'+'."\n";
												}
												if ($currentmarker->contact_mobile != "") 
												{
													$scripttext .= '\'<p class="placemarkBodyContact">'.htmlspecialchars(str_replace('\\', '/', $currentmarker->contact_mobile), ENT_QUOTES, 'UTF-8').'</p>\'+'."\n";
												}
												if ($currentmarker->contact_fax != "") 
												{
													$scripttext .= '\'<p class="placemarkBodyContact">'.htmlspecialchars(str_replace('\\', '/', $currentmarker->contact_fax), ENT_QUOTES, 'UTF-8').'</p>\'+'."\n";
												}
											break;
											default:
												if ($currentmarker->contact_name != "") 
												{
													$scripttext .= '\'<p class="placemarkBodyContact">'.htmlspecialchars(str_replace('\\', '/', $currentmarker->contact_name), ENT_QUOTES, 'UTF-8').'</p>\'+'."\n";
												}
												if ($currentmarker->contact_position != "") 
												{
													$scripttext .= '\'<p class="placemarkBodyContact">'.htmlspecialchars(str_replace('\\', '/', $currentmarker->contact_position), ENT_QUOTES, 'UTF-8').'</p>\'+'."\n";
												}
												if ($currentmarker->contact_address != "") 
												{
													$scripttext .= '\'<p class="placemarkBodyContact">'.str_replace('<br /><br />', '<br />', str_replace(array("\r", "\r\n", "\n"), '<br />', htmlspecialchars(str_replace('\\', '/', $currentmarker->contact_address), ENT_QUOTES, 'UTF-8'))).'</p>\'+'."\n";
												}
												if ($currentmarker->contact_phone != "") 
												{
													$scripttext .= '\'<p class="placemarkBodyContact">'.htmlspecialchars(str_replace('\\', '/', $currentmarker->contact_phone), ENT_QUOTES, 'UTF-8').'</p>\'+'."\n";
												}
												if ($currentmarker->contact_mobile != "") 
												{
													$scripttext .= '\'<p class="placemarkBodyContact">'.htmlspecialchars(str_replace('\\', '/', $currentmarker->contact_mobile), ENT_QUOTES, 'UTF-8').'</p>\'+'."\n";
												}
												if ($currentmarker->contact_fax != "") 
												{
													$scripttext .= '\'<p class="placemarkBodyContact">'.htmlspecialchars(str_replace('\\', '/', $currentmarker->contact_fax), ENT_QUOTES, 'UTF-8').'</p>\'+'."\n";
												}
											break;										
										}
									}
								}
								// Contact info - end
								// User info - begin
								if (isset($map->useruser) && ((int)$map->useruser != 0))
								{
									$scripttext .= get_userinfo_for_marker($currentmarker->createdbyuser, $currentmarker->showuser, 
																			$imgpathIcons, $imgpathUtils, $directoryIcons);
								}
								if ($currentmarker->hrefsite!="")
								{
										$scripttext .= '\'<p><a class="placemarkHREF" href="'.$currentmarker->hrefsite.'" target="_blank">';
										if ($currentmarker->hrefsitename != "")
										{
											$scripttext .= htmlspecialchars($currentmarker->hrefsitename, ENT_QUOTES, 'UTF-8');
										}
										else
										{
											$scripttext .= $currentmarker->hrefsite;
										}
								
										$scripttext .= '</a></p>\'+'."\n";
								}

								
						$scripttext .= '\'</div>\';'."\n";

						
						// Action By Click - Begin							
						switch ((int)$currentmarker->actionbyclick)
						{
							// None
							case 0:
								if ((int)$currentmarker->zoombyclick != 100)
								{
									$scripttext .= $markername.'.events.add("click", function (e) {' ."\n";
									$scripttext .= '  map.setCenter(latlng'. $currentmarker->id.');' ."\n";
									$scripttext .= '  map.setZoom('.(int)$currentmarker->zoombyclick.');' ."\n";
									$scripttext .= '});' ."\n";
								}
							break;
							// Info
							case 1:
								// Moved out trigger, because cluster get info into its balloon
								$scripttext .= $markername.'.properties.set("balloonContentHeader", contentStringHead'. $currentmarker->id.');' ."\n";
								$scripttext .= $markername.'.properties.set("balloonContentBody", contentStringBody'. $currentmarker->id.');' ."\n";

								$scripttext .= $markername.'.events.add("click", function (e) {' ."\n";
								if ((int)$currentmarker->zoombyclick != 100)
								{
									$scripttext .= '  map.setCenter(latlng'. $currentmarker->id.');' ."\n";
									$scripttext .= '  map.setZoom('.(int)$currentmarker->zoombyclick.');' ."\n";
								}

								// In this API there is no need to fire
								//
								//$scripttext .= '    YMaps.Events.notify('.$markername.', '.$markername.'.Events.BalloonOpen);' ."\n";
								/*
								$scripttext .= $markername.'.events.fire("", new ymaps.Event('."\n";
								$scripttext .= ''.$markername.','."\n";
								$scripttext .= ' true));' ."\n";
								*/
								//$scripttext .= '    '.$markername.'.balloon.open();' ."\n";
								
								$scripttext .= '  });' ."\n";
							break;
							// Link
							case 2:
								if ($currentmarker->hrefsite != "")
								{
									$scripttext .= $markername.'.events.add("click", function (e) {' ."\n";
									if ((int)$currentmarker->zoombyclick != 100)
									{
										$scripttext .= '  map.setCenter(latlng'. $currentmarker->id.');' ."\n";
										$scripttext .= '  map.setZoom('.(int)$currentmarker->zoombyclick.');' ."\n";
									}
									$scripttext .= '  window.open("'.$currentmarker->hrefsite.'");' ."\n";
									$scripttext .= '  });' ."\n";
								}
								else
								{
									if ((int)$currentmarker->zoombyclick != 100)
									{
										$scripttext .= $markername.'.events.add("click", function (e) {' ."\n";
										$scripttext .= '  map.setCenter(latlng'. $currentmarker->id.');' ."\n";
										$scripttext .= '  map.setZoom('.(int)$currentmarker->zoombyclick.');' ."\n";
										$scripttext .= '  });' ."\n";
									}
								}
							break;
							// Link in self
							case 3:
								if ($currentmarker->hrefsite != "")
								{
									$scripttext .= $markername.'.events.add("click", function (e) {' ."\n";
									if ((int)$currentmarker->zoombyclick != 100)
									{
										$scripttext .= '  map.setCenter(latlng'. $currentmarker->id.');' ."\n";
										$scripttext .= '  map.setZoom('.(int)$currentmarker->zoombyclick.');' ."\n";
									}
									$scripttext .= '  window.location = "'.$currentmarker->hrefsite.'";' ."\n";
									$scripttext .= '  });' ."\n";
								}
								else
								{
									if ((int)$currentmarker->zoombyclick != 100)
									{
										$scripttext .= $markername.'.events.add("click", function (e) {' ."\n";
										$scripttext .= '  map.setCenter(latlng'. $currentmarker->id.');' ."\n";
										$scripttext .= '  map.setZoom('.(int)$currentmarker->zoombyclick.');' ."\n";
										$scripttext .= '  });' ."\n";
									}
								}
							break;
							default:
								$scripttext .= '' ."\n";
							break;
						}
						
						// Action By Click - End

					}
					else
					{
						// Change UserMarker - begin
						$scripttext .= $markername.'.options.set("draggable", true);' ."\n";
						
						//$scripttext .= 'contentString'.$currentmarker->id.' = contentString'.$currentmarker->id.'+' ."\n";
						// replace content
						$scripttext .= 'var contentStringHeadCluster'. $currentmarker->id.' = \'<div id="placemarkContentCluster'. $currentmarker->id.'">\' +' ."\n";
						$scripttext .= '\'<span id="headContentCluster'. $currentmarker->id.'" class="placemarkHeadCluster">'.htmlspecialchars(str_replace('\\', '/', $currentmarker->title), ENT_QUOTES, 'UTF-8').'</span>\'+' ."\n";
						$scripttext .= '\'</div>\';'."\n";

						$scripttext .= 'contentStringPart1'.$currentmarker->id.' = "" +' ."\n";
						$scripttext .= '\'<div id="contentUpdatePlacemark">\'+'."\n";
						//$scripttext .= '    \'<br />\'+' ."\n";
						//$scripttext .= '    \''.JText::_( 'COM_ZHYANDEXMAP_MAP_USER_LNG' ).' \'+current.getLng() + ' ."\n";
						//$scripttext .= '    \'<br />'.JText::_( 'COM_ZHYANDEXMAP_MAP_USER_LAT' ).' \'+current.getLat() + ' ."\n";
						// Form Update
						$scripttext .= '    \'<form id="updatePlacemarkForm'.$currentmarker->id.'" action="'.JURI::current().'" method="post">\'+'."\n";
						$scripttext .= '    \''.'<img src="'.$imgpathUtils.'published'.(int)$currentmarker->published.'.png" alt="" />  \'+' ."\n";
						$scripttext .= '    \'<br />\'+' ."\n";

						// Begin Placemark Properties
						$scripttext .= '\'<div id="bodyInsertPlacemarkDivA'.$currentmarker->id.'"  class="bodyInsertProperties">\'+'."\n";
						$scripttext .= '\'<a id="bodyInsertPlacemarkA'.$currentmarker->id.'" href="javascript:showonlyone(\\\'Placemark\\\',\\\''.$currentmarker->id.'\\\');" ><img src="'.$imgpathUtils.'collapse.png">'.JText::_( 'COM_ZHYANDEXMAP_MAP_USER_BASIC_PROPERTIES' ).'</a>\'+'."\n";
						$scripttext .= '\'</div>\'+'."\n";
						$scripttext .= '\'<div id="bodyInsertPlacemark'.$currentmarker->id.'"  class="bodyInsertPlacemarkProperties">\'+'."\n";
							$scripttext .= '    \''.JText::_( 'COM_ZHYANDEXMAP_MAP_USER_NAME' ).' \'+' ."\n";
							$scripttext .= '    \'<br />\'+' ."\n";
							$scripttext .= '    \'<input name="markername" type="text" maxlength="250" size="50" value="'. htmlspecialchars($currentmarker->title, ENT_QUOTES, 'UTF-8').'" />\'+' ."\n";
							$scripttext .= '    \'<br />\'+' ."\n";
							//$scripttext .= '    \'<br />\'+' ."\n";
							$scripttext .= '    \''.JText::_( 'COM_ZHYANDEXMAP_MAP_USER_DESCRIPTION' ).' \'+' ."\n";
							$scripttext .= '    \'<br />\'+' ."\n";
							$scripttext .= '    \'<input name="markerdescription" type="text" maxlength="250" size="50" value="'. htmlspecialchars($currentmarker->description, ENT_QUOTES, 'UTF-8').'" />\'+' ."\n";

							$scripttext .= '    \'<br />\'+' ."\n";
							$scripttext .= '    \''.JText::_( 'COM_ZHYANDEXMAP_MAPMARKER_DETAIL_HREFIMAGE_LABEL' ).' \'+' ."\n";
							$scripttext .= '    \'<br />\'+' ."\n";
							$scripttext .= '    \'<input name="markerhrefimage" type="text" maxlength="500" size="50" value="'. htmlspecialchars($currentmarker->hrefimage, ENT_QUOTES, 'UTF-8').'" />\'+' ."\n";
							$scripttext .= '    \'<br />\'+' ."\n";

							$scripttext .= '    \'<br />\';' ."\n";

							// icon type
							
							$scripttext .= 'contentStringPart2'.$currentmarker->id.' = "" +' ."\n";
							$scripttext .= '    \'<br />\'+' ."\n";

						$scripttext .= '\'</div>\'+'."\n";
						// End Placemark Properties
						
						// Begin Placemark Group Properties
						$scripttext .= '\'<div id="bodyInsertPlacemarkGrpDivA'.$currentmarker->id.'"  class="bodyInsertProperties">\'+'."\n";
						$scripttext .= '\'<a id="bodyInsertPlacemarkGrpA'.$currentmarker->id.'" href="javascript:showonlyone(\\\'PlacemarkGroup\\\',\\\''.$currentmarker->id.'\\\');" ><img src="'.$imgpathUtils.'expand.png">'.JText::_( 'COM_ZHYANDEXMAP_MAP_USER_BASIC_GROUP_PROPERTIES' ).'</a>\'+'."\n";
						$scripttext .= '\'</div>\'+'."\n";
						$scripttext .= '\'<div id="bodyInsertPlacemarkGrp'.$currentmarker->id.'"  class="bodyInsertPlacemarkGrpProperties">\'+'."\n";
							$scripttext .= '    \''.JText::_( 'COM_ZHYANDEXMAP_MAP_USER_GROUP' ).' \'+' ."\n";
							$scripttext .= '    \'<br />\'+' ."\n";
							
							$scripttext .= '    \' <select name="markergroup" > \'+' ."\n";
							if ($currentmarker->markergroup == 0)
							{
								$scripttext .= '    \' <option value="" selected="selected">'.JText::_( 'COM_ZHYANDEXMAP_MAPMARKER_FILTER_PLACEMARK_GROUP').'</option> \'+' ."\n";
							}
							else
							{
								$scripttext .= '    \' <option value="">'.JText::_( 'COM_ZHYANDEXMAP_MAPMARKER_FILTER_PLACEMARK_GROUP').'</option> \'+' ."\n";
							}
							foreach ($newMarkerGroupList as $key => $newGrp) 
							{
								if ($currentmarker->markergroup == $newGrp->value)
								{
									$scripttext .= '    \' <option value="'.$newGrp->value.'" selected="selected">'.$newGrp->text.'</option> \'+' ."\n";
								}
								else
								{
									$scripttext .= '    \' <option value="'.$newGrp->value.'">'.$newGrp->text.'</option> \'+' ."\n";
								}
							}
							$scripttext .= '    \' </select> \'+' ."\n";
							$scripttext .= '    \'<br />\'+' ."\n";


						$scripttext .= '    \''.JText::_( 'COM_ZHYANDEXMAP_MAP_USER_CATEGORY' ).' \'+' ."\n";
						$scripttext .= '    \'<br />\'+' ."\n";
						$scripttext .= '    \' <select name="markercatid" > \'+' ."\n";
						$scripttext .= '    \' <option value="" selected="selected">'.JText::_( 'COM_ZHYANDEXMAP_MAP_FILTER_CATEGORY').'</option> \'+' ."\n";
						$scripttext .= '    \''.str_replace(array("\r", "\r\n", "\n"),'', 
											   JHtml::_('select.options', JHtml::_('category.options', 'com_zhyandexmap'), 'value', 'text', $currentmarker->catid)) .
											   '\'+' ."\n";
						$scripttext .= '    \' </select> \'+' ."\n";
						$scripttext .= '    \'<br />\'+' ."\n";

						$scripttext .= '    \'<br />\'+' ."\n";
						$scripttext .= '\'</div>\'+'."\n";
						// End Placemark Group Properties

						// Begin Contact Properties
						if (isset($map->usercontact) && (int)$map->usercontact == 1) 
						{

								$scripttext .= '\'<div id="bodyInsertContactDivA'.$currentmarker->id.'"  class="bodyInsertProperties">\'+'."\n";
								$scripttext .= '\'<a id="bodyInsertContactA'.$currentmarker->id.'" href="javascript:showonlyone(\\\'Contact\\\',\\\''.$currentmarker->id.'\\\');" ><img src="'.$imgpathUtils.'expand.png">'.JText::_( 'COM_ZHYANDEXMAP_MAP_USER_CONTACT_PROPERTIES' ).'</a>\'+'."\n";
								$scripttext .= '\'</div>\'+'."\n";
								$scripttext .= '\'<div id="bodyInsertContact'.$currentmarker->id.'"  class="bodyInsertContactProperties">\'+'."\n";
								$scripttext .= '    \''.JText::_( 'COM_ZHYANDEXMAP_MAP_USER_CONTACT_NAME' ).' \'+' ."\n";
								$scripttext .= '    \'<br />\'+' ."\n";
								$scripttext .= '    \'<input name="contactname" type="text" maxlength="250" size="50" value="'. htmlspecialchars($currentmarker->contact_name, ENT_QUOTES, 'UTF-8').'" />\'+' ."\n";
								$scripttext .= '    \'<br />\'+' ."\n";
								$scripttext .= '    \''.JText::_( 'COM_ZHYANDEXMAP_MAP_USER_CONTACT_POSITION' ).' \'+' ."\n";
								$scripttext .= '    \'<br />\'+' ."\n";
								$scripttext .= '    \'<input name="contactposition" type="text" maxlength="250" size="50" value="'. htmlspecialchars($currentmarker->contact_position, ENT_QUOTES, 'UTF-8').'" />\'+' ."\n";
								$scripttext .= '    \'<br />\'+' ."\n";
								$scripttext .= '    \''.JText::_( 'COM_ZHYANDEXMAP_MAP_USER_CONTACT_PHONE' ).' \'+' ."\n";
								$scripttext .= '    \'<br />\'+' ."\n";
								$scripttext .= '    \'<input name="contactphone" type="text" maxlength="250" size="50" value="'. htmlspecialchars($currentmarker->contact_phone, ENT_QUOTES, 'UTF-8').'" />\'+' ."\n";
								$scripttext .= '    \'<br />\'+' ."\n";
								$scripttext .= '    \''.JText::_( 'COM_ZHYANDEXMAP_MAP_USER_CONTACT_MOBILE' ).' \'+' ."\n";
								$scripttext .= '    \'<br />\'+' ."\n";
								$scripttext .= '    \'<input name="contactmobile" type="text" maxlength="250" size="50" value="'. htmlspecialchars($currentmarker->contact_mobile, ENT_QUOTES, 'UTF-8').'" />\'+' ."\n";
								$scripttext .= '    \'<br />\'+' ."\n";
								$scripttext .= '    \''.JText::_( 'COM_ZHYANDEXMAP_MAP_USER_CONTACT_FAX' ).' \'+' ."\n";
								$scripttext .= '    \'<br />\'+' ."\n";
								$scripttext .= '    \'<input name="contactfax" type="text" maxlength="250" size="50" value="'. htmlspecialchars($currentmarker->contact_fax, ENT_QUOTES, 'UTF-8').'" />\'+' ."\n";
								$scripttext .= '\'</div>\'+'."\n";
								// Contact Address
								$scripttext .= '\'<div id="bodyInsertContactAdrDivA'.$currentmarker->id.'"  class="bodyInsertProperties">\'+'."\n";
								$scripttext .= '\'<a id="bodyInsertContactAdrA'.$currentmarker->id.'" href="javascript:showonlyone(\\\'ContactAddress\\\',\\\''.$currentmarker->id.'\\\');" ><img src="'.$imgpathUtils.'expand.png">'.JText::_( 'COM_ZHYANDEXMAP_MAP_USER_CONTACT_ADDRESS_PROPERTIES' ).'</a>\'+'."\n";
								$scripttext .= '\'</div>\'+'."\n";
								$scripttext .= '\'<div id="bodyInsertContactAdr'.$currentmarker->id.'"  class="bodyInsertContactAdrProperties">\'+'."\n";
								$scripttext .= '    \''.JText::_( 'COM_ZHYANDEXMAP_MAP_USER_CONTACT_ADDRESS' ).' \'+' ."\n";
								$scripttext .= '    \'<br />\'+' ."\n";
								$scripttext .= '    \'<textarea name="contactaddress" cols="35" rows="4" >'. str_replace("\n\n", "'+'\\n'+'", str_replace(array("\r", "\r\n", "\n"), "\n",htmlspecialchars($currentmarker->contact_address, ENT_QUOTES, 'UTF-8'))).'</textarea>\'+' ."\n";
								$scripttext .= '    \'<br />\'+' ."\n";
								$scripttext .= '    \'<br />\'+' ."\n";
								$scripttext .= '\'</div>\'+'."\n";
						}
						// End Contact Properties

						$scripttext .= '\'\';'."\n";
						
						
						
						$scripttext .= $markername.'.events.add("click", function (e) {' ."\n";

						$scripttext .= 'var contentStringButtons'.$currentmarker->id.' = "" +' ."\n";
						$scripttext .= '    \'<hr />\'+' ."\n";					
						$scripttext .= '    \'<input name="markerlat" type="hidden" value="\'+latlng'. $currentmarker->id.'[1] + \'" />\'+' ."\n";
						$scripttext .= '    \'<input name="markerlng" type="hidden" value="\'+latlng'. $currentmarker->id.'[0] + \'" />\'+' ."\n";
						$scripttext .= '    \'<input name="marker_action" type="hidden" value="update" />\'+' ."\n";
						$scripttext .= '    \'<input name="markerid" type="hidden" value="'.$currentmarker->id.'" />\'+' ."\n";
						$scripttext .= '    \'<input name="contactid" type="hidden" value="'.$currentmarker->contactid.'" />\'+' ."\n";
						$scripttext .= '    \'<input name="markersubmit" type="submit" value="'.JText::_( 'COM_ZHYANDEXMAP_MAP_USER_BUTTON_UPDATE' ).'" />\'+' ."\n";
						$scripttext .= '    \'</form>\'+' ."\n";		
						$scripttext .= '\'</div>\'+'."\n";
						// Form Delete
						$scripttext .= '\'<div id="contentDeletePlacemark">\'+'."\n";
						$scripttext .= '    \'<form id="deletePlacemarkForm'.$currentmarker->id.'" action="'.JURI::current().'" method="post">\'+'."\n";
						$scripttext .= '    \'<input name="marker_action" type="hidden" value="delete" />\'+' ."\n";
						$scripttext .= '    \'<input name="markerid" type="hidden" value="'.$currentmarker->id.'" />\'+' ."\n";
						$scripttext .= '    \'<input name="contactid" type="hidden" value="'.$currentmarker->contactid.'" />\'+' ."\n";
						$scripttext .= '    \'<input name="markersubmit" type="submit" value="'.JText::_( 'COM_ZHYANDEXMAP_MAP_USER_BUTTON_DELETE' ).'" />\'+' ."\n";
						$scripttext .= '    \'</form>\'+' ."\n";		
						$scripttext .= '\'</div>\';'."\n";

						$scripttext .= $markername.'.properties.set("balloonContent", contentStringPart1'.$currentmarker->id.'+';
						$scripttext .= 'contentInsertPlacemarkIcon.replace(/insertPlacemarkForm/g,"updatePlacemarkForm'. $currentmarker->id.'")';
						$scripttext .= '.replace(\'"markericonimage" src="\', \'"markericonimage" src="'.$imgpathIcons.str_replace("#", "%23", $currentmarker->icontype).'.png"\')';
						$scripttext .= '.replace(\'<option value="'.$currentmarker->icontype.'">'.$currentmarker->icontype.'</option>\', \'<option value="'.$currentmarker->icontype.'" selected="selected">'.$currentmarker->icontype.'</option>\')';
						if (isset($map->usermarkersicon) && (int)$map->usermarkersicon == 0) 
						{
							$scripttext .= '.replace(\'<input name="markerimage" type="hidden" value="default#" />\', \'<input name="markerimage" type="hidden" value="'.$currentmarker->icontype.'" />\')';	
						}

						$scripttext .= '+';
						$scripttext .= 'contentStringPart2'.$currentmarker->id.'+';
						$scripttext .= 'contentStringButtons'.$currentmarker->id;
						$scripttext .= ');' ."\n";
						
						// In this API there is no need to fire
						//
						//$scripttext .= '    YMaps.Events.notify('.$markername.', '.$markername.'.Events.BalloonOpen);' ."\n";
						/*
						$scripttext .= $markername.'.events.fire("", new ymaps.Event('."\n";
						$scripttext .= ''.$markername.','."\n";
						$scripttext .= ' true));' ."\n";
						//$scripttext .= '    '.$markername.'.balloon.open();' ."\n";
						*/
						
						$scripttext .= '});' ."\n";
						
						
						$scripttext .= $markername.'.events.add("drag", function (e) {' ."\n";
						$scripttext .= '    latlng'. $currentmarker->id.' = '.$markername.'.geometry.getCoordinates();' ."\n";
						$scripttext .= '});' ."\n";

						// Change UserMarker - end
					}
					
					// Placemark Content - End
					
					if ((isset($map->markergroupcontrol) && (int)$map->markergroupcontrol != 0))
					{
						$scripttext .= 'clustermarkers'.$currentmarker->markergroup.'.push(placemark'. $currentmarker->id.');' ."\n";
					}
					else
					{
						if ((isset($map->markercluster) && (int)$map->markercluster == 1))
						{
							if ((isset($map->markerclustergroup) && (int)$map->markerclustergroup == 1))
							{
								$scripttext .= 'clustermarkers'.$currentmarker->markergroup.'.push(placemark'. $currentmarker->id.');' ."\n";
							}
							else
							{
								$scripttext .= 'clustermarkers0.push(placemark'. $currentmarker->id.');' ."\n";
							}
						}
						else
						{
							if ((isset($map->markerclustergroup) && (int)$map->markerclustergroup == 1))
							{
								$scripttext .= 'clustermarkers'.$currentmarker->markergroup.'.push(placemark'. $currentmarker->id.');' ."\n";
							}
						}
					}
					
					
					if ((isset($map->markergroupcontrol) && ((int)$map->markergroupcontrol != 0)
						&& ($currentmarker->markergroup != 0))
					|| (isset($map->markercluster) && ((int)$map->markercluster != 0))
					)
					{

						if ((isset($map->markercluster) && (int)$map->markercluster == 0)
						  //&& (isset($map->markermanager) && (int)$map->markermanager == 0)
						  )
						{
								$scripttext .= 'map.geoObjects.add('.$markername.');' ."\n";
						}
						
						$scripttext .= $markername.'.properties.set("clusterCaption", contentStringHeadCluster'. $currentmarker->id.');' ."\n";

						/*
						// Open only if disabled editing
						if (($allowUserMarker == 0)
						 || (isset($currentmarker->userprotection) && (int)$currentmarker->userprotection == 1)
						 || ($currentUserID == 0)
						 || (isset($currentmarker->createdbyuser) 
							&& (((int)$currentmarker->createdbyuser != $currentUserID )
							   || ((int)$currentmarker->createdbyuser == 0)))
						 )
						{
							if ((int)$currentmarker->actionbyclick == 1)
							{
								$scripttext .= $markername.'.properties.set("balloonContentHeader", contentStringHead'. $currentmarker->id.');' ."\n";
								$scripttext .= $markername.'.properties.set("balloonContentBody", contentStringBody'. $currentmarker->id.');' ."\n";
							}
						}
						*/

					}
					else
					{
						/*
						if (isset($map->markermanager) && (int)$map->markermanager == 1) 
						{                            
							if (($currentmarker->markergroup != 0) &&
								((int)$currentmarker->markermanagerminzoom != 0) &&
								((int)$currentmarker->markermanagermaxzoom != 0) 
								)
							{
							   $scripttext .= 'objectManager.add('.$markername.','.$currentmarker->markermanagerminzoom.','.$currentmarker->markermanagermaxzoom.');' ."\n";
							}
							else
							{
							   $scripttext .= 'objectManager.add('.$markername.', 1, 17);' ."\n";
							}
						}
						else
						{
						*/
						if ((isset($map->markercluster) && (int)$map->markercluster == 0)
						  &&(isset($map->markergroupcontrol) && (int)$map->markergroupcontrol == 0)
						  && ((int)$currentmarker->overridemarkericon == 1)
						  && ((int)$currentmarker->publishedgroup == 1)
						   )
						{
							$markergroupname = 'markergroup'. $currentmarker->markergroup;
							$scripttext .= $markergroupname.'.add('.$markername.');'."\n";
						}
						else
						{
							$scripttext .= 'map.geoObjects.add('.$markername.');' ."\n";
						}
						/*
						}
						*/
					}

					if ($currentmarker->openbaloon == '1')
					{
						//$scripttext .= $markername.'.events.fire("click", new ymaps.Event({'."\n";
						//$scripttext .= 'target: '.$markername.','."\n";
						//$scripttext .= '}, true));' ."\n";
						// Action By Click - For Placemark Open Balloon Property - Begin	
						// Because there is a problem with Notify propagation

						switch ((int)$currentmarker->actionbyclick)
						{
							// None
							case 0:
								if ((int)$currentmarker->zoombyclick != 100)
								{
									$scripttext .= '  map.setCenter(latlng'. $currentmarker->id.');' ."\n";
									$scripttext .= '  map.setZoom('.(int)$currentmarker->zoombyclick.');' ."\n";
								}
							break;
							// Info
							case 1:
								if ((int)$currentmarker->zoombyclick != 100)
								{
									$scripttext .= '  map.setCenter(latlng'. $currentmarker->id.');' ."\n";
									$scripttext .= '  map.setZoom('.(int)$currentmarker->zoombyclick.');' ."\n";
								}
								// Open only if disabled editing
								if (($allowUserMarker == 0)
								 || (isset($currentmarker->userprotection) && (int)$currentmarker->userprotection == 1)
								 || ($currentUserID == 0)
								 || (isset($currentmarker->createdbyuser) 
									&& (((int)$currentmarker->createdbyuser != $currentUserID )
									   || ((int)$currentmarker->createdbyuser == 0)))
								 )
								{
									// I set it on previous level action by click
									//$scripttext .= $markername.'.properties.set("balloonContentHeader", contentStringHead'. $currentmarker->id.');' ."\n";
									//$scripttext .= $markername.'.properties.set("balloonContentBody", contentStringBody'. $currentmarker->id.');' ."\n";

									// if clusterer is enabled - do not display, because placemark is not on map yet
									if (isset($map->markercluster) && (int)$map->markercluster == 0)
									{
										$scripttext .= '    '.$markername.'.balloon.open();' ."\n";
									}
									
								}

								
							break;
							// Link
							case 2:
								if ($currentmarker->hrefsite != "")
								{
									if ((int)$currentmarker->zoombyclick != 100)
									{
										$scripttext .= '  map.setCenter(latlng'. $currentmarker->id.');' ."\n";
										$scripttext .= '  map.setZoom('.(int)$currentmarker->zoombyclick.');' ."\n";
									}
									$scripttext .= '  window.open("'.$currentmarker->hrefsite.'");' ."\n";
								}
								else
								{
									if ((int)$currentmarker->zoombyclick != 100)
									{
										$scripttext .= '  map.setCenter(latlng'. $currentmarker->id.');' ."\n";
										$scripttext .= '  map.setZoom('.(int)$currentmarker->zoombyclick.');' ."\n";
									}
								}
							break;
							// Link in self
							case 3:
								if ($currentmarker->hrefsite != "")
								{
									if ((int)$currentmarker->zoombyclick != 100)
									{
										$scripttext .= '  map.setCenter(latlng'. $currentmarker->id.');' ."\n";
										$scripttext .= '  map.setZoom('.(int)$currentmarker->zoombyclick.');' ."\n";
									}
									$scripttext .= '  window.location = "'.$currentmarker->hrefsite.'";' ."\n";
								}
								else
								{
									if ((int)$currentmarker->zoombyclick != 100)
									{
										$scripttext .= '  map.setCenter(latlng'. $currentmarker->id.');' ."\n";
										$scripttext .= '  map.setZoom('.(int)$currentmarker->zoombyclick.');' ."\n";
									}
								}
							break;
							default:
								$scripttext .= '' ."\n";
							break;
						}
						
						// Action By Click - For For Placemark Open Balloon Property - End
					}


						//
						// Generate list elements for each marker.
						if (isset($map->markerlistpos) && (int)$map->markerlistpos != 0) 
						{						
							$doAddToList = 1;							 
							
							if ($doAddToList == 1)
							{
								$doAddToListCount += 1;
								$scripttext .= 'if (markerUL)'."\n";
								$scripttext .= '{'."\n";
								if ((int)$map->markerlistcontent < 100) 
								{								
										$scripttext .= ' var markerLI = document.createElement(\'li\');'."\n";
										$scripttext .= ' markerLI.className = "zhym-li-'.$markerlistcssstyle.'";'."\n";
										$scripttext .= ' var markerLIWrp = document.createElement(\'div\');'."\n";
										$scripttext .= ' markerLIWrp.className = "zhym-li-wrp-'.$markerlistcssstyle.'";'."\n";
										$scripttext .= ' var markerASelWrp = document.createElement(\'div\');'."\n";
										$scripttext .= ' markerASelWrp.className = "zhym-li-wrp-a-'.$markerlistcssstyle.'";'."\n";
										$scripttext .= ' var markerASel = document.createElement(\'a\');'."\n";
										$scripttext .= ' markerASel.className = "zhym-li-a-'.$markerlistcssstyle.'";'."\n";
										$scripttext .= ' markerASel.href = \'javascript:void(0);\';'."\n";
										if ((int)$map->markerlistcontent == 0) 
										{
											$scripttext .= ' markerASel.innerHTML = \'<div id="markerASel'. $currentmarker->id.'" class="zhym-0-li-'.$markerlistcssstyle.'">'.htmlspecialchars(str_replace('\\', '/', $currentmarker->title), ENT_QUOTES, 'UTF-8').'</div>\';'."\n";
										}
										else if ((int)$map->markerlistcontent == 1) 
										{
											$scripttext .= ' markerASel.innerHTML = \'<div id="markerASel'. $currentmarker->id.'" class="zhym-1-lit-'.$markerlistcssstyle.'">'.htmlspecialchars(str_replace('\\', '/', $currentmarker->title), ENT_QUOTES, 'UTF-8').'</div>\';'."\n";
											$scripttext .= ' var markerDSel = document.createElement(\'div\');'."\n";
											$scripttext .= ' markerDSel.className = "zhym-1-liw-'.$markerlistcssstyle.'";'."\n";
											$scripttext .= ' markerDSel.innerHTML = ';
											$scripttext .= ' \'<div id="markerDDesc'. $currentmarker->id.'" class="zhym-1-lid-'.$markerlistcssstyle.'">'.htmlspecialchars(str_replace('\\', '/', $currentmarker->description), ENT_QUOTES, 'UTF-8').'</div>\';'."\n";
										}
										else if ((int)$map->markerlistcontent == 2) 
										{
											$scripttext .= ' markerASel.innerHTML = ';
											$scripttext .= ' \'<div id="markerDWrp'. $currentmarker->id.'" class="zhym-2-liw-icon-'.$markerlistcssstyle.'">\'+'."\n";
											$scripttext .= ' \'<div id="markerDIcon'. $currentmarker->id.'" class="zhym-2-lii-icon-'.$markerlistcssstyle.'"><img src="'.$imgpathIcons.str_replace("#", "%23", $currentmarker->icontype).'.png" alt="" /></div>\'+'."\n";
											$scripttext .= ' \'<div id="markerASel'. $currentmarker->id.'" class="zhym-2-lit-icon-'.$markerlistcssstyle.'">'.htmlspecialchars(str_replace('\\', '/', $currentmarker->title), ENT_QUOTES, 'UTF-8').'\'+'."\n";
											$scripttext .= ' \'</div></div>\';'."\n";
										}
										else if ((int)$map->markerlistcontent == 3) 
										{
											$scripttext .= ' markerASel.innerHTML = ';
											$scripttext .= ' \'<div id="markerDWrp'. $currentmarker->id.'" class="zhym-3-liw-icon-'.$markerlistcssstyle.'">\'+'."\n";
											$scripttext .= ' \'<div id="markerDIcon'. $currentmarker->id.'" class="zhym-3-lii-icon-'.$markerlistcssstyle.'"><img src="'.$imgpathIcons.str_replace("#", "%23", $currentmarker->icontype).'.png" alt="" /></div>\'+'."\n";
											$scripttext .= ' \'<div id="markerASel'. $currentmarker->id.'" class="zhym-3-lit-icon-'.$markerlistcssstyle.'">'.htmlspecialchars(str_replace('\\', '/', $currentmarker->title), ENT_QUOTES, 'UTF-8').'</div>\';'."\n";
											$scripttext .= ' var markerDSel = document.createElement(\'div\');'."\n";
											$scripttext .= ' markerDSel.className = "zhym-3-liwd-icon-'.$markerlistcssstyle.'";'."\n";
											$scripttext .= ' markerDSel.innerHTML = ';
											$scripttext .= ' \'<div id="markerDDesc'. $currentmarker->id.'" class="zhym-3-lid-icon-'.$markerlistcssstyle.'">'.htmlspecialchars(str_replace('\\', '/', $currentmarker->description), ENT_QUOTES, 'UTF-8').'</div>\'+'."\n";
											$scripttext .= ' \'</div>\';'."\n";
										}
										else if ((int)$map->markerlistcontent == 4) 
										{
											$scripttext .= ' markerASel.innerHTML = ';
											$scripttext .= ' \'';									
											$scripttext .= '<table class="zhym-4-table-icon-'.$markerlistcssstyle.'">';
											$scripttext .= '<tbody>';
											$scripttext .= '<tr class="zhym-4-row-icon-'.$markerlistcssstyle.'">';
											$scripttext .= '<td rowspan=2 class="zhym-4-tdicon-icon-'.$markerlistcssstyle.'">';
											$scripttext .= '<img src="'.$imgpathIcons.str_replace("#", "%23", $currentmarker->icontype).'.png" alt="" />';
											$scripttext .= '</td>';
											$scripttext .= '<td class="zhym-4-tdtitle-icon-'.$markerlistcssstyle.'">';
											$scripttext .= htmlspecialchars(str_replace('\\', '/', $currentmarker->title), ENT_QUOTES, 'UTF-8');
											$scripttext .= '</td>';
											$scripttext .= '</tr>';
											$scripttext .= '<tr>';
											$scripttext .= '<td class="zhym-4-tddesc-icon-'.$markerlistcssstyle.'">';
											$scripttext .= htmlspecialchars(str_replace('\\', '/', $currentmarker->description), ENT_QUOTES, 'UTF-8');
											$scripttext .= '</td>';
											$scripttext .= '</tr>';
											$scripttext .= '</tbody>';
											$scripttext .= '</table>';
											$scripttext .= ' \';'."\n";
										}
										else if ((int)$map->markerlistcontent == 11) 
										{
											$scripttext .= ' markerASel.innerHTML = ';
											$scripttext .= ' \'<div id="markerDWrp'. $currentmarker->id.'" class="zhym-11-liw-image-'.$markerlistcssstyle.'">\'+'."\n";
											$scripttext .= ' \'<div id="markerASel'. $currentmarker->id.'" class="zhym-11-lit-image-'.$markerlistcssstyle.'">'.htmlspecialchars(str_replace('\\', '/', $currentmarker->title), ENT_QUOTES, 'UTF-8').'</div>\'+'."\n";
											$scripttext .= ' \'<div id="markerDImage'. $currentmarker->id.'" class="zhym-11-lii-image-'.$markerlistcssstyle.'"><img src="'.$currentmarker->hrefimagethumbnail.'" alt="" />\'+'."\n";
											$scripttext .= ' \'</div></div>\';'."\n";
										}
										else if ((int)$map->markerlistcontent == 12) 
										{
											$scripttext .= ' markerASel.innerHTML = ';
											$scripttext .= ' \'<div id="markerDWrp'. $currentmarker->id.'" class="zhym-12-liw-image-'.$markerlistcssstyle.'">\'+'."\n";
											$scripttext .= ' \'<div id="markerASel'. $currentmarker->id.'" class="zhym-12-lit-image-'.$markerlistcssstyle.'">'.htmlspecialchars(str_replace('\\', '/', $currentmarker->title), ENT_QUOTES, 'UTF-8').'</div>\'+'."\n";
											$scripttext .= ' \'<div id="markerDImage'. $currentmarker->id.'" class="zhym-12-lii-image-'.$markerlistcssstyle.'"><img src="'.$currentmarker->hrefimagethumbnail.'" alt="" /></div>\';'."\n";
											$scripttext .= ' var markerDSel = document.createElement(\'div\');'."\n";
											$scripttext .= ' markerDSel.className = "zhym-12-liwd-image-'.$markerlistcssstyle.'";'."\n";
											$scripttext .= ' markerDSel.innerHTML = ';
											$scripttext .= ' \'<div id="markerDDesc'. $currentmarker->id.'" class="zhym-12-lid-image-'.$markerlistcssstyle.'">'.htmlspecialchars(str_replace('\\', '/', $currentmarker->description), ENT_QUOTES, 'UTF-8').'</div>\'+'."\n";
											$scripttext .= ' \'</div>\';'."\n";
										}
										else if ((int)$map->markerlistcontent == 13) 
										{
											$scripttext .= ' markerASel.innerHTML = ';
											$scripttext .= ' \'<div id="markerDWrp'. $currentmarker->id.'" class="zhym-13-liw-image-'.$markerlistcssstyle.'">\'+'."\n";
											$scripttext .= ' \'<div id="markerDImage'. $currentmarker->id.'" class="zhym-13-lii-image-'.$markerlistcssstyle.'"><img src="'.$currentmarker->hrefimagethumbnail.'" alt="" /></div>\'+'."\n";
											$scripttext .= ' \'<div id="markerASel'. $currentmarker->id.'" class="zhym-13-lit-image-'.$markerlistcssstyle.'">'.htmlspecialchars(str_replace('\\', '/', $currentmarker->title), ENT_QUOTES, 'UTF-8').'\'+'."\n";
											$scripttext .= ' \'</div></div>\';'."\n";
										}
										else if ((int)$map->markerlistcontent == 14) 
										{
											$scripttext .= ' markerASel.innerHTML = ';
											$scripttext .= ' \'<div id="markerDWrp'. $currentmarker->id.'" class="zhym-14-liw-image-'.$markerlistcssstyle.'">\'+'."\n";
											$scripttext .= ' \'<div id="markerDImage'. $currentmarker->id.'" class="zhym-14-lii-image-'.$markerlistcssstyle.'"><img src="'.$currentmarker->hrefimagethumbnail.'" alt="" /></div>\'+'."\n";
											$scripttext .= ' \'<div id="markerASel'. $currentmarker->id.'" class="zhym-14-lit-image-'.$markerlistcssstyle.'">'.htmlspecialchars(str_replace('\\', '/', $currentmarker->title), ENT_QUOTES, 'UTF-8').'</div>\';'."\n";
											$scripttext .= ' var markerDSel = document.createElement(\'div\');'."\n";
											$scripttext .= ' markerDSel.className = "zhym-14-liwd-image-'.$markerlistcssstyle.'";'."\n";
											$scripttext .= ' markerDSel.innerHTML = ';
											$scripttext .= ' \'<div id="markerDDesc'. $currentmarker->id.'" class="zhym-14-lid-image-'.$markerlistcssstyle.'">'.htmlspecialchars(str_replace('\\', '/', $currentmarker->description), ENT_QUOTES, 'UTF-8').'</div>\'+'."\n";
											$scripttext .= ' \'</div>\';'."\n";
										}
										else if ((int)$map->markerlistcontent == 15) 
										{
											$scripttext .= ' markerASel.innerHTML = ';
											$scripttext .= ' \'';									
											$scripttext .= '<table class="zhym-15-table-image-'.$markerlistcssstyle.'">';
											$scripttext .= '<tbody>';
											$scripttext .= '<tr class="zhym-15-row-image-'.$markerlistcssstyle.'">';
											$scripttext .= '<td rowspan=2 class="zhym-15-tdicon-image-'.$markerlistcssstyle.'">';
											$scripttext .= '<img src="'.$currentmarker->hrefimagethumbnail.'" alt="" />';
											$scripttext .= '</td>';
											$scripttext .= '<td class="zhym-15-tdtitle-image-'.$markerlistcssstyle.'">';
											$scripttext .= htmlspecialchars(str_replace('\\', '/', $currentmarker->title), ENT_QUOTES, 'UTF-8');
											$scripttext .= '</td>';
											$scripttext .= '</tr>';
											$scripttext .= '<tr>';
											$scripttext .= '<td class="zhym-15-tddesc-image-'.$markerlistcssstyle.'">';
											$scripttext .= htmlspecialchars(str_replace('\\', '/', $currentmarker->description), ENT_QUOTES, 'UTF-8');
											$scripttext .= '</td>';
											$scripttext .= '</tr>';
											$scripttext .= '</tbody>';
											$scripttext .= '</table>';
											$scripttext .= ' \';'."\n";
										}
										else
										{
											$scripttext .= ' markerASel.innerHTML = \'<div id="markerASel'. $currentmarker->id.'" class="zhym-0-li-'.$markerlistcssstyle.'">'.htmlspecialchars(str_replace('\\', '/', $currentmarker->title), ENT_QUOTES, 'UTF-8').'</div>\';'."\n";
										}


										if ((int)$map->markerlistaction == 0
											|| ($allowUserMarker == 1)) 
										{
											$scripttext .= ' markerASel.onclick = function(){ map.setCenter(latlng'. $currentmarker->id.')};'."\n";
										}
										else if ((int)$map->markerlistaction == 1) 
										{
											$scripttext .= ' markerASel.onclick = function(){ ';
											// $scripttext .= 'YMaps.Events.notify('.$markername.', '.$markername.'.Events.Click);';
											// Action By Click - For PlacemarkList - Begin	
											// Because there is a problem with Notify propagation

											switch ((int)$currentmarker->actionbyclick)
											{
												// None
												case 0:
													if ((int)$currentmarker->zoombyclick != 100)
													{
														$scripttext .= '  map.setCenter(latlng'. $currentmarker->id.');' ."\n";
														$scripttext .= '  map.setZoom('.(int)$currentmarker->zoombyclick.');' ."\n";
													}
												break;
												// Info
												case 1:
													if ((int)$currentmarker->zoombyclick != 100)
													{
														$scripttext .= '  map.setCenter(latlng'. $currentmarker->id.');' ."\n";
														$scripttext .= '  map.setZoom('.(int)$currentmarker->zoombyclick.');' ."\n";
													}
													// Open only if disabled editing
													if (($allowUserMarker == 0)
													 || (isset($currentmarker->userprotection) && (int)$currentmarker->userprotection == 1)
													 || ($currentUserID == 0)
													 || (isset($currentmarker->createdbyuser) 
														&& (((int)$currentmarker->createdbyuser != $currentUserID )
														   || ((int)$currentmarker->createdbyuser == 0)))
													 )
													{
														$scripttext .= $markername.'.properties.set("balloonContentHeader", contentStringHead'. $currentmarker->id.');' ."\n";
														$scripttext .= $markername.'.properties.set("balloonContentBody", contentStringBody'. $currentmarker->id.');' ."\n";

														$scripttext .= '    '.$markername.'.balloon.open();' ."\n";
													}
													
												break;
												// Link
												case 2:
													if ($currentmarker->hrefsite != "")
													{
														if ((int)$currentmarker->zoombyclick != 100)
														{
															$scripttext .= '  map.setCenter(latlng'. $currentmarker->id.');' ."\n";
															$scripttext .= '  map.setZoom('.(int)$currentmarker->zoombyclick.');' ."\n";
														}
														$scripttext .= '  window.open("'.$currentmarker->hrefsite.'");' ."\n";
													}
													else
													{
														if ((int)$currentmarker->zoombyclick != 100)
														{
															$scripttext .= '  map.setCenter(latlng'. $currentmarker->id.');' ."\n";
															$scripttext .= '  map.setZoom('.(int)$currentmarker->zoombyclick.');' ."\n";
														}
													}
												break;
												// Link in self
												case 3:
													if ($currentmarker->hrefsite != "")
													{
														if ((int)$currentmarker->zoombyclick != 100)
														{
															$scripttext .= '  map.setCenter(latlng'. $currentmarker->id.');' ."\n";
															$scripttext .= '  map.setZoom('.(int)$currentmarker->zoombyclick.');' ."\n";
														}
														$scripttext .= '  window.location = "'.$currentmarker->hrefsite.'";' ."\n";
													}
													else
													{
														if ((int)$currentmarker->zoombyclick != 100)
														{
															$scripttext .= '  map.setCenter(latlng'. $currentmarker->id.');' ."\n";
															$scripttext .= '  map.setZoom('.(int)$currentmarker->zoombyclick.');' ."\n";
														}
													}
												break;
												default:
													$scripttext .= '' ."\n";
												break;
											}
											
											// Action By Click - For PlacemarkList - End

											$scripttext .= '};'."\n";
										}
										else
										{
											$scripttext .= ' markerASel.onclick = function(){ map.setCenter(latlng'. $currentmarker->id.');};'."\n";
										}

										$scripttext .= ' markerASelWrp.appendChild(markerASel);'."\n";
										$scripttext .= ' markerLIWrp.appendChild(markerASelWrp);'."\n";
										if ((int)$map->markerlistcontent == 1) 
										{
											$scripttext .= ' markerLIWrp.appendChild(markerDSel);'."\n";
										}
										else if ((int)$map->markerlistcontent == 3) 
										{
											$scripttext .= ' markerLIWrp.appendChild(markerDSel);'."\n";
										}
										else if ((int)$map->markerlistcontent == 12) 
										{
											$scripttext .= ' markerLIWrp.appendChild(markerDSel);'."\n";
										}
										else if ((int)$map->markerlistcontent == 14) 
										{
											$scripttext .= ' markerLIWrp.appendChild(markerDSel);'."\n";
										}
										
										
										$scripttext .= ' markerLI.appendChild(markerLIWrp);'."\n";
										$scripttext .= ' markerUL.appendChild(markerLI);'."\n";
								}
								else
								{
										$scripttext .= ' var markerLI = document.createElement(\'tr\');'."\n";
										$scripttext .= ' markerLI.className = "zhym-li-table-tr-'.$markerlistcssstyle.'";'."\n";
										$scripttext .= ' var markerLI_C1 = document.createElement(\'td\');'."\n";
										$scripttext .= ' markerLI_C1.className = "zhym-li-table-c1-'.$markerlistcssstyle.'";'."\n";
										$scripttext .= ' var markerASelWrp = document.createElement(\'div\');'."\n";
										$scripttext .= ' markerASelWrp.className = "zhym-li-table-a-wrp-'.$markerlistcssstyle.'";'."\n";
										$scripttext .= ' var markerASel = document.createElement(\'a\');'."\n";
										$scripttext .= ' markerASel.className = "zhym-li-table-a-'.$markerlistcssstyle.'";'."\n";
										$scripttext .= ' markerASel.href = \'javascript:void(0);\';'."\n";
										if ((int)$map->markerlistcontent == 101) 
										{
											$scripttext .= ' markerASel.innerHTML = \'<div id="markerASelTable'. $currentmarker->id.'" class="zhym-101-td-'.$markerlistcssstyle.'">'.htmlspecialchars(str_replace('\\', '/', $currentmarker->title), ENT_QUOTES, 'UTF-8').'</div>\';'."\n";
										}
										else if ((int)$map->markerlistcontent == 102) 
										{
											$scripttext .= ' markerASel.innerHTML = \'<div id="markerASelTable'. $currentmarker->id.'" class="zhym-102-td1-'.$markerlistcssstyle.'">'.htmlspecialchars(str_replace('\\', '/', $currentmarker->title), ENT_QUOTES, 'UTF-8').'</div>\';'."\n";

											$scripttext .= ' var markerLI_C2 = document.createElement(\'td\');'."\n";
											$scripttext .= ' markerLI_C2.className = "zhym-li-table-c2-'.$markerlistcssstyle.'";'."\n";
											$scripttext .= ' var markerDSel = document.createElement(\'div\');'."\n";
											$scripttext .= ' markerDSel.className = "zhym-li-table-desc-'.$markerlistcssstyle.'";'."\n";
											$scripttext .= ' markerDSel.innerHTML = ';
											$scripttext .= ' \'<div id="markerDDescTable'. $currentmarker->id.'" class="zhym-102-td2-'.$markerlistcssstyle.'">'.htmlspecialchars(str_replace('\\', '/', $currentmarker->description), ENT_QUOTES, 'UTF-8').'</div>\';'."\n";
										}
										
										if ((int)$map->markerlistaction == 0
											|| ($allowUserMarker == 1)) 
										{
											$scripttext .= ' markerASel.onclick = function(){ map.setCenter(latlng'. $currentmarker->id.')};'."\n";
										}
										else if ((int)$map->markerlistaction == 1) 
										{
											$scripttext .= ' markerASel.onclick = function(){ ';
											// $scripttext .= 'YMaps.Events.notify('.$markername.', '.$markername.'.Events.Click);';
											// Action By Click - For PlacemarkList - Begin	
											// Because there is a problem with Notify propagation
											
											switch ((int)$currentmarker->actionbyclick)
											{
												// None
												case 0:
													if ((int)$currentmarker->zoombyclick != 100)
													{
														$scripttext .= '  map.setCenter(latlng'. $currentmarker->id.');' ."\n";
														$scripttext .= '  map.setZoom('.(int)$currentmarker->zoombyclick.');' ."\n";
													}
												break;
												// Info
												case 1:
													if ((int)$currentmarker->zoombyclick != 100)
													{
														$scripttext .= '  map.setCenter(latlng'. $currentmarker->id.');' ."\n";
														$scripttext .= '  map.setZoom('.(int)$currentmarker->zoombyclick.');' ."\n";
													}
													$scripttext .= $markername.'.properties.set("balloonContentHeader", contentStringHead'. $currentmarker->id.');' ."\n";
													$scripttext .= $markername.'.properties.set("balloonContentBody", contentStringBody'. $currentmarker->id.');' ."\n";

													$scripttext .= '    '.$markername.'.balloon.open();' ."\n";
													
												break;
												// Link
												case 2:
													if ($currentmarker->hrefsite != "")
													{
														if ((int)$currentmarker->zoombyclick != 100)
														{
															$scripttext .= '  map.setCenter(latlng'. $currentmarker->id.');' ."\n";
															$scripttext .= '  map.setZoom('.(int)$currentmarker->zoombyclick.');' ."\n";
														}
														$scripttext .= '  window.open("'.$currentmarker->hrefsite.'");' ."\n";
													}
													else
													{
														if ((int)$currentmarker->zoombyclick != 100)
														{
															$scripttext .= '  map.setCenter(latlng'. $currentmarker->id.');' ."\n";
															$scripttext .= '  map.setZoom('.(int)$currentmarker->zoombyclick.');' ."\n";
														}
													}
												break;
												// Link in self
												case 3:
													if ($currentmarker->hrefsite != "")
													{
														if ((int)$currentmarker->zoombyclick != 100)
														{
															$scripttext .= '  map.setCenter(latlng'. $currentmarker->id.');' ."\n";
															$scripttext .= '  map.setZoom('.(int)$currentmarker->zoombyclick.');' ."\n";
														}
														$scripttext .= '  window.location = "'.$currentmarker->hrefsite.'";' ."\n";
													}
													else
													{
														if ((int)$currentmarker->zoombyclick != 100)
														{
															$scripttext .= '  map.setCenter(latlng'. $currentmarker->id.');' ."\n";
															$scripttext .= '  map.setZoom('.(int)$currentmarker->zoombyclick.');' ."\n";
														}
													}
												break;
												default:
													$scripttext .= '' ."\n";
												break;
											}
											
											// Action By Click - For PlacemarkList - End
											
											$scripttext .= '};'."\n";
										}
										else
										{
											$scripttext .= ' markerASel.onclick = function(){ map.setCenter(latlng'. $currentmarker->id.');};'."\n";
										}

										$scripttext .= ' markerASelWrp.appendChild(markerASel);'."\n";
										$scripttext .= ' markerLI_C1.appendChild(markerASelWrp);'."\n";
										if ((int)$map->markerlistcontent == 102) 
										{
											$scripttext .= ' markerLI_C2.appendChild(markerDSel);'."\n";
										}
										
										
										$scripttext .= ' markerLI.appendChild(markerLI_C1);'."\n";
										if ((int)$map->markerlistcontent == 102) 
										{
											$scripttext .= ' markerLI.appendChild(markerLI_C2);'."\n";
										}
										$scripttext .= ' markerUL.appendChild(markerLI);'."\n";
								}
								$scripttext .= '}'."\n";
							}
						}
						// Generating Placemark List - End

					
			}
			// Main IF End
				
		} 
		// End foreach
                
	}


    if ((isset($map->markergroupcontrol) && (int)$map->markergroupcontrol != 0))
     {
             if ((isset($map->markercluster) && (int)$map->markercluster == 1))
             {      
                $scripttext .= 'markerCluster0.add(clustermarkers0);' ."\n";
                 
                if ((isset($map->markerclustergroup) && (int)$map->markerclustergroup == 1))
                {
					if (isset($this->markergroups) && !empty($this->markergroups)) 
					{
						foreach ($this->markergroups as $key => $currentmarkergroup) 
						{
							if (((int)$currentmarkergroup->published == 1) || ($allowUserMarker == 1))
							{
									if ((int)$currentmarkergroup->activeincluster == 1)
									{
											$scripttext .= 'callClusterFill('.$currentmarkergroup->id.');' ."\n";
									}
							}
						}
					}
                }
                else
                {
					if (isset($this->markergroups) && !empty($this->markergroups)) 
					{
						foreach ($this->markergroups as $key => $currentmarkergroup) 
						{
							if (((int)$currentmarkergroup->published == 1) || ($allowUserMarker == 1))
							{
									if ((int)$currentmarkergroup->activeincluster == 1)
									{
											$scripttext .= 'markerCluster0.add(clustermarkers'.$currentmarkergroup->id.');' ."\n";
									}
							}
						}
					}
                }
            }
            else
            {
				if (isset($this->markergroups) && !empty($this->markergroups)) 
				{
                    foreach ($this->markergroups as $key => $currentmarkergroup) 
                    {
						if (((int)$currentmarkergroup->published == 1) || ($allowUserMarker == 1))
						{
                            if ((int)$currentmarkergroup->activeincluster == 0)
                            {
                                    $scripttext .= 'callMarkerDisable(clustermarkers'.$currentmarkergroup->id.');' ."\n";
                            }
						}
                    }
				}
            }
     }
     else
     {
             if ((isset($map->markercluster) && (int)$map->markercluster == 1))
             {      
                $scripttext .= 'markerCluster0.add(clustermarkers0);' ."\n";
                 
                if ((isset($map->markerclustergroup) && (int)$map->markerclustergroup == 1))
                {
					if (isset($this->markergroups) && !empty($this->markergroups)) 
					{
						foreach ($this->markergroups as $key => $currentmarkergroup) 
						{
							if (((int)$currentmarkergroup->published == 1) || ($allowUserMarker == 1))
							{
								//if ((int)$currentmarkergroup->activeincluster == 1)
								//{
										$scripttext .= 'callClusterFill('.$currentmarkergroup->id.');' ."\n";
								//}
							}
						}
					}
                }
                else
                {
					if (isset($this->markergroups) && !empty($this->markergroups)) 
					{
						foreach ($this->markergroups as $key => $currentmarkergroup) 
						{
							if (((int)$currentmarkergroup->published == 1) || ($allowUserMarker == 1))
							{
								//if ((int)$currentmarkergroup->activeincluster == 1)
								//{
										$scripttext .= 'markerCluster0.add(clustermarkers'.$currentmarkergroup->id.');' ."\n";
								//}
							}
						}
					}
                }
            }
			/*
            else
            {
                if ((isset($map->markerclustergroup) && (int)$map->markerclustergroup == 1))
                {      
					if (isset($this->markergroups) && !empty($this->markergroups)) 
					{
						foreach ($this->markergroups as $key => $currentmarkergroup) 
						{
							if ((int)$currentmarkergroup->published == 1)
							{
								if ((int)$currentmarkergroup->activeincluster == 0)
								{
										$scripttext .= 'callMarkerDisable(clustermarkers'.$currentmarkergroup->id.');' ."\n";
								}
							}
						}
					}
                }
            }
			*/
     }


	// Routers
	if (isset($this->routers) && !empty($this->routers)) 
	{
		$routepanelcount = 0;
		$routepaneltotalcount = 0;

		$routeHTMLdescription ='';
		
		//Begin for each Route
		foreach ($this->routers as $key => $currentrouter) 
		{
			$routername = 'route'. $currentrouter->id;
			$routererror = 'routeError'. $currentrouter->id;
			if ($currentrouter->route != "")
			{
				$scripttext .= 'ymaps.route(['.$currentrouter->route.'],'."\n";
					$scripttext .=  '  { ';
					if (isset($currentrouter->showtype) && (int)$currentrouter->showtype == 1)
					{
						$scripttext .=       ' mapStateAutoApply: false ';
					}
					else
					{
						$scripttext .=       ' mapStateAutoApply: true ';
					}
					if (isset($currentrouter->checktraffic) && (int)$currentrouter->checktraffic == 1)
					{
						$scripttext .=       ', avoidTrafficJams: true ';
					}
					else
					{
						$scripttext .=       ', avoidTrafficJams: false ';
					}
					$scripttext .= '  }).then('."\n";
					$scripttext .= '  function('.$routername.'){'."\n";
					$scripttext .= '     map.geoObjects.add('.$routername.');'."\n";

					if (isset($currentrouter->showpanel) && (int)$currentrouter->showpanel == 1) 
					{
						$scripttext .= '     var segCounter = 0;'."\n";
						$scripttext .= '     var moveList = \'<table class="zhym-route-table">\';'."\n";
						$scripttext .= '         moveList += \'<tbody class="zhym-route-tablebody">\';'."\n";
						$scripttext .= '     for (var j = 0; j < '.$routername.'.getPaths().getLength(); j++) {'."\n";
						$scripttext .= '         moveList += \'<tr class="zhym-route-table-tr">\''."\n";
						$scripttext .= '         moveList += \'<td class="zhym-route-table-td-waypoint" colspan="2">\''."\n";
						$scripttext .= '         segCounter += 1;'."\n";
						$scripttext .= '         if (segCounter == 1)'."\n";
						$scripttext .= '         {'."\n";
						$scripttext .= '        	 moveList += segCounter + \' '.JText::_('COM_ZHYANDEXMAP_MAP_FIND_GEOCODING_START_POINT').'</br>\';'."\n";
						$scripttext .= '         }'."\n";
						$scripttext .= '         else'."\n";
						$scripttext .= '         {'."\n";
						$scripttext .= '         	moveList += segCounter + \' '.JText::_('COM_ZHYANDEXMAP_MAP_FIND_GEOCODING_WAY_POINT').'</br>\';'."\n";
						$scripttext .= '         }'."\n";
						$scripttext .= '         moveList += \'</td>\''."\n";
						$scripttext .= '         moveList += \'</tr>\''."\n";
						$scripttext .= '       var way = '.$routername.'.getPaths().get(j);'."\n";
						$scripttext .= '       var segments = way.getSegments();'."\n";
						$scripttext .= '       var segmentlength = 0.;'."\n";

						$scripttext .= '         moveList += \'<tr class="zhym-route-table-tr-step">\''."\n";
						$scripttext .= '         moveList += \'<td class="zhym-route-table-td"  colspan="2">\''."\n";
						
						$scripttext .= ' var total_km = way.getHumanLength();'."\n";
						if (isset($currentrouter->checktraffic) && (int)$currentrouter->checktraffic == 1)
						{
							$scripttext .= ' var total_time = way.getHumanJamsTime();'."\n";
						}
						else
						{
							$scripttext .= ' var total_time = way.getHumanTime();'."\n";
						}

						$scripttext .= '         moveList += \'';
						$scripttext .= JText::_('COM_ZHYANDEXMAP_MAPROUTER_DETAIL_SHOWPANEL_HDR_TOTAL_KM');
						$scripttext .= ' \'+ total_km + \' ';
						//$scripttext .= JText::_('COM_ZHYANDEXMAP_MAPROUTER_DETAIL_SHOWPANEL_HDR_KM');
						$scripttext .= ', ';
						$scripttext .= JText::_('COM_ZHYANDEXMAP_MAPROUTER_DETAIL_SHOWPANEL_HDR_TOTAL_TIME');
						if (isset($currentrouter->checktraffic) && (int)$currentrouter->checktraffic == 1)
						{
							$scripttext .= ' '.JText::_('COM_ZHYANDEXMAP_MAPROUTER_DETAIL_SHOWPANEL_HDR_JAM');
						}
						$scripttext .= ' \' + total_time;';
						//$scripttext .= JText::_('COM_ZHYANDEXMAP_MAPROUTER_DETAIL_SHOWPANEL_HDR_TIME');
						$scripttext .= '         moveList += \'</td>\''."\n";
						$scripttext .= '         moveList += \'</tr>\''."\n";
						
						$scripttext .= '       for (var i = 0; i < segments.length; i++) {'."\n";


						$scripttext .= '         moveList += \'<tr class="zhym-route-table-tr-step">\''."\n";
						$scripttext .= '         var street = segments[i].getStreet();'."\n";
						$scripttext .= '         moveList += \'<td class="zhym-route-table-td">\''."\n";
						$scripttext .= '         moveList += \''.JText::_('COM_ZHYANDEXMAP_MAP_FIND_GEOCODING_MOVE').' <b>\' + segments[i].getHumanAction() + \'</b>\'+(street ? \' '.JText::_('COM_ZHYANDEXMAP_MAP_FIND_GEOCODING_ON').' <b>\' + street + \'</b>\': \'\');'."\n";
						$scripttext .= '         moveList += \'</td>\''."\n";
						$scripttext .= '         moveList += \'<td class="zhym-route-table-td">\''."\n";
						$scripttext .= '         segmentlength = segments[i].getLength();'."\n";
						$scripttext .= '         if (segmentlength > 500)'."\n";
						$scripttext .= '         {'."\n";
						$scripttext .= '            segmentlength = segmentlength/1000.;'."\n";
						$scripttext .= '         	moveList += segmentlength.toFixed(1) + \' '.JText::_('COM_ZHYANDEXMAP_MAP_FIND_GEOCODING_KILOMETERS').'\';'."\n";
						$scripttext .= '         }'."\n";
						$scripttext .= '         else'."\n";
						$scripttext .= '         {'."\n";
						$scripttext .= '         	moveList += segmentlength.toFixed(0) + \' '.JText::_('COM_ZHYANDEXMAP_MAP_FIND_GEOCODING_METERS').'\';'."\n";
						$scripttext .= '         }'."\n";
						$scripttext .= '         moveList += \'</td>\''."\n";
						$scripttext .= '         moveList += \'</tr>\''."\n";
						$scripttext .= '       }'."\n";
						$scripttext .= '     }'."\n";
						$scripttext .= '         moveList += \'<tr class="zhym-route-table-tr">\''."\n";
						$scripttext .= '         moveList += \'<td class="zhym-route-table-td-waypoint" colspan="2">\''."\n";
						$scripttext .= '         segCounter += 1;'."\n";
						$scripttext .= '      moveList += segCounter + \' '.JText::_('COM_ZHYANDEXMAP_MAP_FIND_GEOCODING_END_POINT').'\';'."\n";
						$scripttext .= '         moveList += \'</td>\''."\n";
						$scripttext .= '         moveList += \'</tr>\''."\n";
						$scripttext .= '      moveList += \'</tbody>\';'."\n";
						$scripttext .= '      moveList += \'</table>\';'."\n";
						$scripttext .= '  document.getElementById("YMapsRoutePanel_Steps").innerHTML = \'\'+moveList+\'\';' ."\n";


						$scripttext .= ' var total_km = '.$routername.'.getHumanLength();'."\n";
						if (isset($currentrouter->checktraffic) && (int)$currentrouter->checktraffic == 1)
						{
							$scripttext .= ' var total_time = '.$routername.'.getHumanJamsTime();'."\n";
						}
						else
						{
							$scripttext .= ' var total_time = '.$routername.'.getHumanTime();'."\n";
						}

						$scripttext .= '  document.getElementById("YMapsRoutePanel_Total").innerHTML = "<p>';
						$scripttext .= JText::_('COM_ZHYANDEXMAP_MAPROUTER_DETAIL_SHOWPANEL_HDR_TOTAL_KM');
						$scripttext .= ' " + total_km + " ';
						//$scripttext .= JText::_('COM_ZHYANDEXMAP_MAPROUTER_DETAIL_SHOWPANEL_HDR_KM');
						$scripttext .= ', ';
						$scripttext .= JText::_('COM_ZHYANDEXMAP_MAPROUTER_DETAIL_SHOWPANEL_HDR_TOTAL_TIME');
						if (isset($currentrouter->checktraffic) && (int)$currentrouter->checktraffic == 1)
						{
							$scripttext .= ' '.JText::_('COM_ZHYANDEXMAP_MAPROUTER_DETAIL_SHOWPANEL_HDR_JAM');
						}
						$scripttext .= ' " + total_time + " ';
						//$scripttext .= JText::_('COM_ZHYANDEXMAP_MAPROUTER_DETAIL_SHOWPANEL_HDR_TIME');
						$scripttext .= '</p>";' ."\n";
						
					}
					

					$scripttext .= '  }, '."\n";
					$scripttext .= '  function('.$routererror.'){'."\n";
					$scripttext .= '     alert(\''.JText::_('COM_ZHYANDEXMAP_MAP_ERROR_GEOCODING').'\' + '.$routererror.'.message);'."\n";
					$scripttext .= '  }'."\n";
					$scripttext .= ');'."\n";
					

				if (isset($currentrouter->showpanel) && (int)$currentrouter->showpanel == 1) 
				{
					$routepanelcount++;
					if (isset($currentrouter->showpaneltotal) && (int)$currentrouter->showpaneltotal == 1) 
					{
						$routepaneltotalcount++;
					}
				}
				
			}
			
			
			
			
			if ($currentrouter->routebymarker != "")
			{
				$router2name = 'routeByMarker'. $currentrouter->id;
				$router2error = 'routeByMarkerError'. $currentrouter->id;
				
				$cs = explode(";", $currentrouter->routebymarker);
				$cs_total = count($cs)-1;
				$cs_idx = 0;
				$wp_list = '';
				$skipRouteCreation = 0;
				foreach($cs as $curroute)
				{	
					$currouteLatLng = parse_route_by_markers($curroute);
					//$scripttext .= 'alert("'.$currouteLatLng.'");'."\n";

					if ($currouteLatLng != "")
					{
						if ($currouteLatLng == "geocode")
						{
							$scripttext .= 'alert(\''.JText::_('COM_ZHYANDEXMAP_MAPROUTER_FINDMARKER_ERROR_GEOCODE').' '.$curroute.'\');'."\n";
							$skipRouteCreation = 1;
						}
						else
						{
							if ($cs_idx == 0)
							{
								$wp_start .= ' '.$currouteLatLng.''."\n";
							}
							else if ($cs_idx == $cs_total)
							{
								$wp_end .= ', '.$currouteLatLng.' '."\n";
							}
							else
							{
								if ($wp_list == '')
								{
									$wp_list .= ', '.$currouteLatLng;
								}
								else
								{
									$wp_list .= ', '.$currouteLatLng;
								}
							}
						}
					}
					else
					{
						$scripttext .= 'alert(\''.JText::_('COM_ZHYANDEXMAP_MAPROUTER_FINDMARKER_ERROR_REASON').' '.$curroute.'\');'."\n";
						$skipRouteCreation = 1;
					}

					$cs_idx += 1;
				}

				if ($skipRouteCreation == 0)
				{
					$routeToDraw = $wp_start . $wp_list . $wp_end;
					
					$scripttext .= 'ymaps.route(['.$routeToDraw.'],'."\n";
					$scripttext .=       '{ ';
					//strokeColor: 
					//opacity:
					if (isset($currentrouter->showtype) && (int)$currentrouter->showtype == 1)
					{
						$scripttext .=       ' mapStateAutoApply: false ';
					}
					else
					{
						$scripttext .=       ' mapStateAutoApply: true ';
					}
					if (isset($currentrouter->checktraffic) && (int)$currentrouter->checktraffic == 1)
					{
						$scripttext .=       ', avoidTrafficJams: true ';
					}
					else
					{
						$scripttext .=       ', avoidTrafficJams: false ';
					}
					$scripttext .= '  }).then('."\n";
					$scripttext .= '  function('.$router2name.'){'."\n";
					$scripttext .= '     map.geoObjects.add('.$router2name.');'."\n";
					
					if (isset($currentrouter->showpanel) && (int)$currentrouter->showpanel == 1) 
					{
						$scripttext .= '     var segCounter = 0;'."\n";
						$scripttext .= '     var moveList = \'<table class="zhym-route-table">\';'."\n";
						$scripttext .= '         moveList += \'<tbody class="zhym-route-tablebody">\';'."\n";
						$scripttext .= '     for (var j = 0; j < '.$router2name.'.getPaths().getLength(); j++) {'."\n";
						$scripttext .= '         moveList += \'<tr class="zhym-route-table-tr">\''."\n";
						$scripttext .= '         moveList += \'<td class="zhym-route-table-td-waypoint" colspan="2">\''."\n";
						$scripttext .= '         segCounter += 1;'."\n";
						$scripttext .= '         if (segCounter == 1)'."\n";
						$scripttext .= '         {'."\n";
						$scripttext .= '        	 moveList += segCounter + \' '.JText::_('COM_ZHYANDEXMAP_MAP_FIND_GEOCODING_START_POINT').'</br>\';'."\n";
						$scripttext .= '         }'."\n";
						$scripttext .= '         else'."\n";
						$scripttext .= '         {'."\n";
						$scripttext .= '         	moveList += segCounter + \' '.JText::_('COM_ZHYANDEXMAP_MAP_FIND_GEOCODING_WAY_POINT').'</br>\';'."\n";
						$scripttext .= '         }'."\n";
						$scripttext .= '         moveList += \'</td>\''."\n";
						$scripttext .= '         moveList += \'</tr>\''."\n";
						$scripttext .= '       var way = '.$router2name.'.getPaths().get(j);'."\n";
						$scripttext .= '       var segments = way.getSegments();'."\n";
						$scripttext .= '       var segmentlength = 0.;'."\n";

						$scripttext .= '         moveList += \'<tr class="zhym-route-table-tr-step">\''."\n";
						$scripttext .= '         moveList += \'<td class="zhym-route-table-td"  colspan="2">\''."\n";
						
						$scripttext .= ' var total_km = way.getHumanLength();'."\n";
						if (isset($currentrouter->checktraffic) && (int)$currentrouter->checktraffic == 1)
						{
							$scripttext .= ' var total_time = way.getHumanJamsTime();'."\n";
						}
						else
						{
							$scripttext .= ' var total_time = way.getHumanTime();'."\n";
						}

						$scripttext .= '         moveList += \'';
						$scripttext .= JText::_('COM_ZHYANDEXMAP_MAPROUTER_DETAIL_SHOWPANEL_HDR_TOTAL_KM');
						$scripttext .= ' \'+ total_km + \' ';
						//$scripttext .= JText::_('COM_ZHYANDEXMAP_MAPROUTER_DETAIL_SHOWPANEL_HDR_KM');
						$scripttext .= ', ';
						$scripttext .= JText::_('COM_ZHYANDEXMAP_MAPROUTER_DETAIL_SHOWPANEL_HDR_TOTAL_TIME');
						if (isset($currentrouter->checktraffic) && (int)$currentrouter->checktraffic == 1)
						{
							$scripttext .= ' '.JText::_('COM_ZHYANDEXMAP_MAPROUTER_DETAIL_SHOWPANEL_HDR_JAM');
						}
						$scripttext .= ' \' + total_time;';
						//$scripttext .= JText::_('COM_ZHYANDEXMAP_MAPROUTER_DETAIL_SHOWPANEL_HDR_TIME');
						$scripttext .= '         moveList += \'</td>\''."\n";
						$scripttext .= '         moveList += \'</tr>\''."\n";
						
						$scripttext .= '       for (var i = 0; i < segments.length; i++) {'."\n";


						$scripttext .= '         moveList += \'<tr class="zhym-route-table-tr-step">\''."\n";
						$scripttext .= '         var street = segments[i].getStreet();'."\n";
						$scripttext .= '         moveList += \'<td class="zhym-route-table-td">\''."\n";
						$scripttext .= '         moveList += \''.JText::_('COM_ZHYANDEXMAP_MAP_FIND_GEOCODING_MOVE').' <b>\' + segments[i].getHumanAction() + \'</b>\'+(street ? \' '.JText::_('COM_ZHYANDEXMAP_MAP_FIND_GEOCODING_ON').' <b>\' + street + \'</b>\': \'\');'."\n";
						$scripttext .= '         moveList += \'</td>\''."\n";
						$scripttext .= '         moveList += \'<td class="zhym-route-table-td">\''."\n";
						$scripttext .= '         segmentlength = segments[i].getLength();'."\n";
						$scripttext .= '         if (segmentlength > 500)'."\n";
						$scripttext .= '         {'."\n";
						$scripttext .= '            segmentlength = segmentlength/1000.;'."\n";
						$scripttext .= '         	moveList += segmentlength.toFixed(1) + \' '.JText::_('COM_ZHYANDEXMAP_MAP_FIND_GEOCODING_KILOMETERS').'\';'."\n";
						$scripttext .= '         }'."\n";
						$scripttext .= '         else'."\n";
						$scripttext .= '         {'."\n";
						$scripttext .= '         	moveList += segmentlength.toFixed(0) + \' '.JText::_('COM_ZHYANDEXMAP_MAP_FIND_GEOCODING_METERS').'\';'."\n";
						$scripttext .= '         }'."\n";
						$scripttext .= '         moveList += \'</td>\''."\n";
						$scripttext .= '         moveList += \'</tr>\''."\n";
						$scripttext .= '       }'."\n";
						$scripttext .= '     }'."\n";
						$scripttext .= '         moveList += \'<tr class="zhym-route-table-tr">\''."\n";
						$scripttext .= '         moveList += \'<td class="zhym-route-table-td-waypoint" colspan="2">\''."\n";
						$scripttext .= '         segCounter += 1;'."\n";
						$scripttext .= '      moveList += segCounter + \' '.JText::_('COM_ZHYANDEXMAP_MAP_FIND_GEOCODING_END_POINT').'\';'."\n";
						$scripttext .= '         moveList += \'</td>\''."\n";
						$scripttext .= '         moveList += \'</tr>\''."\n";
						$scripttext .= '      moveList += \'</tbody>\';'."\n";
						$scripttext .= '      moveList += \'</table>\';'."\n";
						$scripttext .= '  document.getElementById("YMapsRoutePanel_Steps").innerHTML = \'\'+moveList+\'\';' ."\n";


						$scripttext .= ' var total_km = '.$router2name.'.getHumanLength();'."\n";
						if (isset($currentrouter->checktraffic) && (int)$currentrouter->checktraffic == 1)
						{
							$scripttext .= ' var total_time = '.$router2name.'.getHumanJamsTime();'."\n";
						}
						else
						{
							$scripttext .= ' var total_time = '.$router2name.'.getHumanTime();'."\n";
						}

						$scripttext .= '  document.getElementById("YMapsRoutePanel_Total").innerHTML = "<p>';
						$scripttext .= JText::_('COM_ZHYANDEXMAP_MAPROUTER_DETAIL_SHOWPANEL_HDR_TOTAL_KM');
						$scripttext .= ' " + total_km + " ';
						//$scripttext .= JText::_('COM_ZHYANDEXMAP_MAPROUTER_DETAIL_SHOWPANEL_HDR_KM');
						$scripttext .= ', ';
						$scripttext .= JText::_('COM_ZHYANDEXMAP_MAPROUTER_DETAIL_SHOWPANEL_HDR_TOTAL_TIME');
						if (isset($currentrouter->checktraffic) && (int)$currentrouter->checktraffic == 1)
						{
							$scripttext .= ' '.JText::_('COM_ZHYANDEXMAP_MAPROUTER_DETAIL_SHOWPANEL_HDR_JAM');
						}
						$scripttext .= ' " + total_time + " ';
						//$scripttext .= JText::_('COM_ZHYANDEXMAP_MAPROUTER_DETAIL_SHOWPANEL_HDR_TIME');
						$scripttext .= '</p>";' ."\n";
						
					}
					
					$scripttext .= '  }, '."\n";
					$scripttext .= '  function('.$router2error.'){'."\n";
					$scripttext .= '     alert(\''.JText::_('COM_ZHYANDEXMAP_MAP_ERROR_GEOCODING').'\' + '.$router2error.'.message);'."\n";
					$scripttext .= '  }'."\n";
					$scripttext .= ');'."\n";

					if (isset($currentrouter->showpanel) && (int)$currentrouter->showpanel == 1) 
					{
						$routepanelcount++;
						if (isset($currentrouter->showpaneltotal) && (int)$currentrouter->showpaneltotal == 1) 
						{
							$routepaneltotalcount++;
						}
					}

				}

			}
			
			
			
			if (isset($currentrouter->showdescription) && (int)$currentrouter->showdescription == 1) 
			{
				if ($currentrouter->description != "")
				{
					$routeHTMLdescription .= '<h2>';
					$routeHTMLdescription .= htmlspecialchars($currentrouter->description, ENT_QUOTES, 'UTF-8');
					$routeHTMLdescription .= '</h2>';
				}
				if ($currentrouter->descriptionhtml != "")
				{
					$routeHTMLdescription .= str_replace("'", "\'", str_replace(array("\r", "\r\n", "\n"), '', $currentrouter->descriptionhtml));
				}
			}

			if ($currentrouter->kmllayerymapsml != "")
			{
				$kml1 = 'YMapsML'.$routername;
				$scripttext .= 'ymaps.geoXml.load(\''.$currentrouter->kmllayerymapsml.'\').then(' ."\n";
				$scripttext .= '	function('.$kml1.') {' ."\n";
				$scripttext .= '		map.geoObjects.add('.$kml1.'.geoObjects);' ."\n";
				$scripttext .= '		if ('.$kml1.'.mapState) ' ."\n";
				$scripttext .= '		{' ."\n";
				$scripttext .= '			'.$kml1.'.mapState.applyToMap(map);' ."\n";
				$scripttext .= '		}' ."\n";
				$scripttext .= '	},' ."\n";
				$scripttext .= '	function(error) {' ."\n";
				$scripttext .= '    alert(\''.JText::_('COM_ZHYANDEXMAP_MAP_ERROR_YMAPSML').'\' + error.message);' ."\n";
				$scripttext .= '	}' ."\n";
				$scripttext .= ');	' ."\n";
			}


			if ($currentrouter->kmllayerkml != "")
			{
				$kml2 = 'KML'.$routername;
				$scripttext .= 'ymaps.geoXml.load(\''.$currentrouter->kmllayerkml.'\').then(' ."\n";
				$scripttext .= '	function('.$kml2.') {' ."\n";
				$scripttext .= '		map.geoObjects.add('.$kml2.'.geoObjects);' ."\n";
				$scripttext .= '		if ('.$kml2.'.mapState) ' ."\n";
				$scripttext .= '		{' ."\n";
				$scripttext .= '			'.$kml2.'.mapState.applyToMap(map);' ."\n";
				$scripttext .= '		}' ."\n";
				$scripttext .= '	},' ."\n";
				$scripttext .= '	function(error) {' ."\n";
				$scripttext .= '    alert(\''.JText::_('COM_ZHYANDEXMAP_MAP_ERROR_KML').'\' + error.message);' ."\n";
				$scripttext .= '	}' ."\n";
				$scripttext .= ');	' ."\n";
			}

			if ($currentrouter->kmllayergpx != "")
			{
				$kml3 = 'GPX'.$routername;
				$scripttext .= 'ymaps.geoXml.load(\''.$currentrouter->kmllayergpx.'\').then(' ."\n";
				$scripttext .= '	function('.$kml3.') {' ."\n";
				$scripttext .= '		map.geoObjects.add('.$kml3.'.geoObjects);' ."\n";
				$scripttext .= '		if ('.$kml3.'.mapState) ' ."\n";
				$scripttext .= '		{' ."\n";
				$scripttext .= '			'.$kml3.'.mapState.applyToMap(map);' ."\n";
				$scripttext .= '		}' ."\n";
				$scripttext .= '	},' ."\n";
				$scripttext .= '	function(error) {' ."\n";
				$scripttext .= '    alert(\''.JText::_('COM_ZHYANDEXMAP_MAP_ERROR_GPX').'\' + error.message);' ."\n";
				$scripttext .= '	}' ."\n";
				$scripttext .= ');	' ."\n";
			}
			
			
		}
		// End for each Route
		
		if ($routepanelcount > 1 || $routepanelcount == 0 || $routepaneltotalcount == 0)
		{
			$scripttext .= 'var toHideRouteDiv = document.getElementById("YMapsRoutePanel_Total");' ."\n";
			$scripttext .= 'toHideRouteDiv.style.display = "none";' ."\n";
			//$scripttext .= 'alert("Hide because > 1 or = 0");';
		}

		if ($routeHTMLdescription != "")
		{
			$scripttext .= '  document.getElementById("YMapsRoutePanel_Description").innerHTML =  "<p>'. $routeHTMLdescription .'</p>";'."\n";
		}
		
		
	}
	// Routes End
	
	

	// Paths
	if (isset($this->paths) && !empty($this->paths)) 
	{
		foreach ($this->paths as $key => $currentpath) 
		{

			$scripttext .= 'var plProperties'.$currentpath->id.' = {'."\n";
			$scripttext .= ' hintContent: "'.htmlspecialchars(str_replace('\\', '/', $currentpath->title), ENT_QUOTES, 'UTF-8').'"' ."\n";			
			$scripttext .= '};'."\n";
		
			$scripttext .= ' var plOptions'.$currentpath->id.' = {'."\n";
			$scripttext .= ' strokeColor: \''.$currentpath->color.'\''."\n";
			if ($currentpath->opacity != "")
			{
				$scripttext .= ', strokeOpacity: \''.$currentpath->opacity.'\''."\n";
			}
			$scripttext .= ', strokeWidth: \''.$currentpath->width.'\''."\n";

			if ((int)$currentpath->objecttype == 1
			 || (int)$currentpath->objecttype == 2)
			{
				if ($currentpath->fillcolor != "")
				{
					$scripttext .= ', fillColor: \''.$currentpath->fillcolor.'\''."\n";
				}
				if ($currentpath->fillopacity != "")
				{
					$scripttext .= ', fillOpacity: \''.$currentpath->fillopacity.'\''."\n";
				}
			}
			
			if ((int)$currentpath->geodesic == 1)
			{
				$scripttext .= ', geodesic: true '."\n";
			}
			else
			{
				$scripttext .= ', geodesic: false '."\n";
			}
			$scripttext .= ' };'."\n";

			if ((int)$currentpath->actionbyclick == 1)
			{
				
				$scripttext .= 'var contentPathStringHead'. $currentpath->id.' = \'<div id="contentHeadPathContent'. $currentpath->id.'">\' +' ."\n";
				if (isset($currentpath->infowincontent) &&
					(((int)$currentpath->infowincontent == 0) ||
					 ((int)$currentpath->infowincontent == 1))
					)
				{
					$scripttext .= '\'<h1 id="headPathContent'. $currentpath->id.'" class="pathHead">'.htmlspecialchars(str_replace('\\', '/', $currentpath->title), ENT_QUOTES, 'UTF-8').'</h1>\'+' ."\n";
				}
				$scripttext .= '\'</div>\';'."\n";
				
				$scripttext .= 'var contentPathStringBody'. $currentpath->id.' = \'<div id="contentBodyPathContent'. $currentpath->id.'"  class="pathBody">\'+'."\n";


						if (isset($currentpath->infowincontent) &&
							(((int)$currentpath->infowincontent == 0) ||
							 ((int)$currentpath->infowincontent == 2))
							)
						{
							$scripttext .= '\''.htmlspecialchars(str_replace('\\', '/', $currentpath->description), ENT_QUOTES, 'UTF-8').'\'+'."\n";
						}
						$scripttext .= '\''.str_replace("'", "\'", str_replace(array("\r", "\r\n", "\n"), '', $currentpath->descriptionhtml)).'\'+'."\n";

						
				$scripttext .= '\'</div>\';'."\n";
				
			}
			

			if ((int)$currentpath->objecttype == 0)
			{
			
				$scripttext .= ' var plGeometry'.$currentpath->id.' = ['."\n";
				$scripttext .= '['.str_replace(";","],[", $currentpath->path).']'."\n";
				$scripttext .= ' ];'."\n";
				
				$curpathname = 'pl'.$currentpath->id;
				
				$scripttext .= ' var '.$curpathname.' = new ymaps.Polyline(plGeometry'.$currentpath->id.', plProperties'.$currentpath->id.', plOptions'.$currentpath->id.');'."\n";

				if ((int)$currentpath->actionbyclick == 1)
				{
					$scripttext .= $curpathname.'.properties.set("balloonContentHeader", contentPathStringHead'. $currentpath->id.');' ."\n";
					$scripttext .= $curpathname.'.properties.set("balloonContentBody", contentPathStringBody'. $currentpath->id.');' ."\n";
				}
				
				$scripttext .= 'map.geoObjects.add('.$curpathname.');'."\n";
			}
			else if ((int)$currentpath->objecttype == 1)
			{
				$scripttext .= ' var plGeometry'.$currentpath->id.' = ['."\n";
				$scripttext .= '[['.str_replace(";","],[", $currentpath->path).']]'."\n";
				$scripttext .= ' ,[]];'."\n";
				
				$curpathname = 'pl'.$currentpath->id;
				$scripttext .= ' var '.$curpathname.' = new ymaps.Polygon(plGeometry'.$currentpath->id.', plProperties'.$currentpath->id.', plOptions'.$currentpath->id.');'."\n";

				if ((int)$currentpath->actionbyclick == 1)
				{
					$scripttext .= $curpathname.'.properties.set("balloonContentHeader", contentPathStringHead'. $currentpath->id.');' ."\n";
					$scripttext .= $curpathname.'.properties.set("balloonContentBody", contentPathStringBody'. $currentpath->id.');' ."\n";
				}

				$scripttext .= 'map.geoObjects.add('.$curpathname.');'."\n";
			}
			else if ((int)$currentpath->objecttype == 2)
			{
				if ($currentpath->radius != "")
				{
					$arrayPathCoords = explode(';', $currentpath->path);
					$arrayPathIndex = 0;
					foreach ($arrayPathCoords as $currentpathcoordinates) 
					{
						$arrayPathIndex += 1;
						$scripttext .= ' var plGeometry'.$currentpath->id.'_'.$arrayPathIndex.' = ['."\n";
						$scripttext .= '['.$currentpathcoordinates.']'."\n";
						$scripttext .= ', '.$currentpath->radius."\n";
						$scripttext .= ' ];'."\n";
						
						$curpathname = 'pl'.$currentpath->id.'_'.$arrayPathIndex;
						$scripttext .= ' var '.$curpathname.' = new ymaps.Circle(plGeometry'.$currentpath->id.'_'.$arrayPathIndex.', plProperties'.$currentpath->id.', plOptions'.$currentpath->id.');'."\n";

						if ((int)$currentpath->actionbyclick == 1)
						{
							$scripttext .= $curpathname.'.properties.set("balloonContentHeader", contentPathStringHead'. $currentpath->id.');' ."\n";
							$scripttext .= $curpathname.'.properties.set("balloonContentBody", contentPathStringBody'. $currentpath->id.');' ."\n";
						}
						
						$scripttext .= 'map.geoObjects.add('.$curpathname.');'."\n";
					}
				}
			}
			
		}
	}

	
	$context_suffix = 'map';

	if ($map->kmllayer != "")
	{
		$kml1 = 'YMapsML'.$context_suffix;
		$scripttext .= 'ymaps.geoXml.load(\''.$map->kmllayer.'\').then(' ."\n";
		$scripttext .= '	function('.$kml1.') {' ."\n";
		$scripttext .= '		map.geoObjects.add('.$kml1.'.geoObjects);' ."\n";
		$scripttext .= '		if ('.$kml1.'.mapState) ' ."\n";
		$scripttext .= '		{' ."\n";
		$scripttext .= '			'.$kml1.'.mapState.applyToMap(map);' ."\n";
		$scripttext .= '		}' ."\n";
		$scripttext .= '	},' ."\n";
		$scripttext .= '	function(error) {' ."\n";
		$scripttext .= '    alert(\''.JText::_('COM_ZHYANDEXMAP_MAP_ERROR_YMAPSML').'\' + error.message);' ."\n";
		$scripttext .= '	}' ."\n";
		$scripttext .= ');	' ."\n";
	}

	if ($map->kmllayerkml != "")
	{
		$kml2 = 'KML'.$context_suffix;
		$scripttext .= 'ymaps.geoXml.load(\''.$map->kmllayerkml.'\').then(' ."\n";
		$scripttext .= '	function('.$kml2.') {' ."\n";
		$scripttext .= '		map.geoObjects.add('.$kml2.'.geoObjects);' ."\n";
		$scripttext .= '		if ('.$kml2.'.mapState) ' ."\n";
		$scripttext .= '		{' ."\n";
		$scripttext .= '			'.$kml2.'.mapState.applyToMap(map);' ."\n";
		$scripttext .= '		}' ."\n";
		$scripttext .= '	},' ."\n";
		$scripttext .= '	function(error) {' ."\n";
		$scripttext .= '    alert(\''.JText::_('COM_ZHYANDEXMAP_MAP_ERROR_KML').'\' + error.message);' ."\n";
		$scripttext .= '	}' ."\n";
		$scripttext .= ');	' ."\n";
	}

	if ($map->kmllayergpx != "")
	{
		$kml3 = 'GPX'.$context_suffix;
		$scripttext .= 'ymaps.geoXml.load(\''.$map->kmllayergpx.'\').then(' ."\n";
		$scripttext .= '	function('.$kml3.') {' ."\n";
		$scripttext .= '		map.geoObjects.add('.$kml3.'.geoObjects);' ."\n";
		$scripttext .= '		if ('.$kml3.'.mapState) ' ."\n";
		$scripttext .= '		{' ."\n";
		$scripttext .= '			'.$kml3.'.mapState.applyToMap(map);' ."\n";
		$scripttext .= '		}' ."\n";
		$scripttext .= '	},' ."\n";
		$scripttext .= '	function(error) {' ."\n";
		$scripttext .= '    alert(\''.JText::_('COM_ZHYANDEXMAP_MAP_ERROR_GPX').'\' + error.message);' ."\n";
		$scripttext .= '	}' ."\n";
		$scripttext .= ');	' ."\n";
	}
	
	
	if ((isset($map->autoposition) && (int)$map->autoposition == 1))
	{
			$scripttext .= 'findMyPosition("Map");' ."\n";
	}

	
	// Do open list if preset to yes
	if (isset($map->markerlistpos) && (int)$map->markerlistpos != 0) 
	{
		if ((int)$map->markerlistpos == 111
		  ||(int)$map->markerlistpos == 112
		  ||(int)$map->markerlistpos == 121
		  ) 
		{
			// We don't have to do in any case when table or external
			// because it displayed		
		}
		else
		{
			if ((int)$map->markerlistbuttontype == 0)
			{
				// Open because for non-button
				$scripttext .= '	var toShowDiv = document.getElementById("YMapsMarkerList");' ."\n";
				$scripttext .= '	toShowDiv.style.display = "block";' ."\n";
			}
			else
			{
				switch ($map->markerlistbuttontype) 
				{
					case 0:
						$scripttext .= '	var toShowDiv = document.getElementById("YMapsMarkerList");' ."\n";
						$scripttext .= '	toShowDiv.style.display = "block";' ."\n";
					break;
					case 1:
						$scripttext .= '';
					break;
					case 2:
						$scripttext .= '';
					break;
					case 11:
						//$scripttext .= '	var toShowDiv = document.getElementById("YMapsMarkerList");' ."\n";
						//$scripttext .= '	toShowDiv.style.display = "block";' ."\n";
						$scripttext .= 'btnPlacemarkList.events.fire("click", new ymaps.Event({'."\n";
						$scripttext .= ' target: btnPlacemarkList'."\n";
						$scripttext .= '}, true));' ."\n";
						
					break;
					case 12:
						//$scripttext .= '	var toShowDiv = document.getElementById("YMapsMarkerList");' ."\n";
						//$scripttext .= '	toShowDiv.style.display = "block";' ."\n";
						$scripttext .= 'btnPlacemarkList.events.fire("click", new ymaps.Event({'."\n";
						$scripttext .= ' target: btnPlacemarkList'."\n";
						$scripttext .= '}, true));' ."\n";
					break;
					default:
						$scripttext .= '';
					break;
				}
			}
								
		}	
	}
	// Open Placemark List Presets
	
	

	$scripttext .= '}'."\n";
	// End initialize function
	
//
	$scripttext .= 'function PlacemarkByIDShow(p_id, p_action, p_zoom) {' ."\n";
	if ($externalmarkerlink == 1)
	{
		$scripttext .= '  if (p_zoom != undefined && p_zoom != "")' ."\n";
		$scripttext .= '  {' ."\n";
		$scripttext .= '  	map.setZoom(p_zoom);' ."\n";
		$scripttext .= '  }' ."\n";

		$scripttext .= '  if( allPlacemarkArray[p_id] === undefined ) ' ."\n";
		$scripttext .= '  {' ."\n";
		$scripttext .= '  	alert("Unable to find placemark with ID = " + p_id);' ."\n";
		$scripttext .= '  }' ."\n";
		$scripttext .= '  else' ."\n";
		$scripttext .= '  {' ."\n";
		$scripttext .= '    cur_action = p_action.toLowerCase().split(",");' ."\n";
		$scripttext .= '    for (i = 0; i < cur_action.length; i++) {' ."\n";
		$scripttext .= '      if (cur_action[i] == "click")' ."\n";
		$scripttext .= '      {' ."\n";
		$scripttext .= '		allPlacemarkArray[p_id].markerobject.events.fire("click", new ymaps.Event({'."\n";
		$scripttext .= ' 			target: allPlacemarkArray[p_id].markerobject'."\n";
		$scripttext .= '		}, true));' ."\n";
		$scripttext .= '      }' ."\n";
		$scripttext .= '      else if (cur_action[i] == "center")' ."\n";
		$scripttext .= '      {' ."\n";
		$scripttext .= '  	    map.setCenter(allPlacemarkArray[p_id].latlngobject);' ."\n";
		$scripttext .= '      }' ."\n";
		$scripttext .= '    }' ."\n";
		$scripttext .= '  }' ."\n";
	}
	else
	{
		$scripttext .= '  	alert("This feature is supported only when you enable it in map menu item property!");' ."\n";
	}
	$scripttext .= '}' ."\n";
	

	if ($externalmarkerlink == 1)
	{
		$scripttext .= 'function PlacemarkByID(p_id, p_lat, p_lng, p_obj, p_ll) {' ."\n";
		$scripttext .= 'this.id = p_id;' ."\n";
		$scripttext .= 'this.lat = p_lat;' ."\n";
		$scripttext .= 'this.lng = p_lng;' ."\n";
		$scripttext .= 'this.markerobject = p_obj;' ."\n";
		$scripttext .= 'this.latlngobject = p_ll;' ."\n";
		$scripttext .= '}' ."\n";
		
		$scripttext .= 'function PlacemarkByIDAdd(p_id, p_lat, p_lng, p_obj, p_ll) {' ."\n";
		$scripttext .= '	allPlacemarkArray[p_id] = new PlacemarkByID(p_id, p_lat, p_lng, p_obj, p_ll);' ."\n";
		$scripttext .= '}' ."\n";
	}
	
//
//
if ($compatiblemode == 1)
{
	// for IE under 8
	$scripttext .= '		function myHasClass(ele,cls) {' ."\n";
	$scripttext .= '		    var reg = new RegExp(\'(\\s|^)\'+cls+\'(\\s|$)\');' ."\n";
	$scripttext .= '			return ele.className.match(reg);' ."\n";
	$scripttext .= '		}' ."\n";

	$scripttext .= '		function myAddClass(ele,cls) {' ."\n";
	$scripttext .= '			if (!myHasClass(ele,cls)) {' ."\n";
	$scripttext .= '			   if (ele.className == "")' ."\n";
	$scripttext .= '		   	   {' ."\n";
	$scripttext .= '			    ele.className += cls;' ."\n";
	$scripttext .= '			   }' ."\n";
	$scripttext .= '			   else' ."\n";
	$scripttext .= '			   {' ."\n";
	$scripttext .= '			    ele.className += " "+cls;' ."\n";
	$scripttext .= '			   }' ."\n";
	$scripttext .= '		    }' ."\n";
	$scripttext .= '		 }' ."\n";
			  
	$scripttext .= '		function myRemoveClass(ele,cls) {' ."\n";
	$scripttext .= '			if (myHasClass(ele,cls)) {' ."\n";
	$scripttext .= '				var reg = new RegExp(\'(\\s|^)\'+cls+\'(\\s|$)\');' ."\n";
	$scripttext .= '				ele.className=ele.className.replace(reg,\' \');' ."\n";
	$scripttext .= '				ele.className=ele.className.replace(/\s+/g,\' \');' ."\n";
	$scripttext .= '				ele.className=ele.className.replace(/^\s|\s$/,\'\');' ."\n";
	$scripttext .= '				if (ele.className == " ")' ."\n";
	$scripttext .= '				{' ."\n";
	$scripttext .= '				  ele.className ="";' ."\n";
	$scripttext .= '				}' ."\n";
	$scripttext .= '			}' ."\n";
	$scripttext .= '		}' ."\n";
					 
	$scripttext .= '	    function myToggleClass(elem, cls){' ."\n";
	$scripttext .= '	        if(myHasClass(elem, cls)){' ."\n";
	$scripttext .= '	            myRemoveClass(elem, cls);' ."\n";
	$scripttext .= '	        } else  {' ."\n";
	$scripttext .= '	            myAddClass(elem, cls);' ."\n";
	$scripttext .= '	        }' ."\n";
	$scripttext .= '	    }' ."\n";
}
//
//

// For Marker Cluster Support Functions - begin
if (
    ((isset($map->markercluster) && (int)$map->markercluster == 1))            
    ||
    ((isset($map->markerclustergroup) && (int)$map->markerclustergroup == 1))
    ||
    (isset($map->markergroupcontrol) && (int)$map->markergroupcontrol != 0)             
   )
{          
    if ((isset($map->markercluster) && (int)$map->markercluster == 1))
    {

        if ((isset($map->markerclustergroup) && (int)$map->markerclustergroup == 1))
		{

			$scripttext .= 'function callClusterFill (clusterid){   ' ."\n";

				if (isset($this->markergroups) && !empty($this->markergroups)) 
				{

                    foreach ($this->markergroups as $key => $currentmarkergroup) 
                    {
                        $scripttext .= 'if ('.$currentmarkergroup->id.' == clusterid)' ."\n";
                        $scripttext .= '{'."\n";

                        if ((int)$map->clusterzoom == 0)
                        {
                            if ((int)$currentmarkergroup->overridegroupicon == 1)
                            {
                                $scripttext .= 'markerCluster'.$currentmarkergroup->id.'.add(clustermarkers'.$currentmarkergroup->id.');' ."\n";
                            }
                            else
                            {
                                $scripttext .= 'markerCluster'.$currentmarkergroup->id.'.add(clustermarkers'.$currentmarkergroup->id.');' ."\n";
                            }
                        }
                        else
                        {
                            if ((int)$currentmarkergroup->overridegroupicon == 1)
                            {
                                $scripttext .= 'markerCluster'.$currentmarkergroup->id.'.add(clustermarkers'.$currentmarkergroup->id.');' ."\n";
                            }
                            else
                            {
                                $scripttext .= 'markerCluster'.$currentmarkergroup->id.'.add(clustermarkers'.$currentmarkergroup->id.');' ."\n";
                            }
                        }
						
                        $scripttext .= '}'."\n";
                    }
				}
			$scripttext .= '};' ."\n";


                if (isset($map->markergroupcontrol) && (int)$map->markergroupcontrol != 0) 
                {
                    $scripttext .= 'function callMarkerChangeGroup (ancorname, clustername, groupid){   ' ."\n";
                    $scripttext .= 'var link = document.getElementById(ancorname);' ."\n";
					if ($compatiblemode == 1)
					{
						$scripttext .= '       if (myHasClass(link, "active")) ' ."\n";
					}
					else
					{
						$scripttext .= '       if (link.hasClass("active"))' ."\n";
					}
                    $scripttext .= '           { ' ."\n";
                    $scripttext .= '           clustername.removeAll();' ."\n";
                    $scripttext .= '           clustername.refresh();' ."\n";
                    $scripttext .= '       } else ' ."\n";
                    $scripttext .= '           {    ' ."\n";
                    $scripttext .= '           if (clustername) ' ."\n";
                    $scripttext .= '           {' ."\n";
                    $scripttext .= '               clustername.removeAll();' ."\n";
                    $scripttext .= '               clustername.refresh();' ."\n";
                    $scripttext .= '           }' ."\n";
                    $scripttext .= '           callClusterFill(groupid);' ."\n";
                    $scripttext .= '       }' ."\n";
					if ($compatiblemode == 1)
					{
						$scripttext .= ' myToggleClass(link, "active"); ' ."\n";
					}
					else
					{
						$scripttext .= ' link.toggleClass("active"); ' ."\n";
					}
                    $scripttext .= '};' ."\n";
                }
		}
		else
		{
            if (isset($map->markergroupcontrol) && (int)$map->markergroupcontrol != 0) 
            {
			$scripttext .= 'function callMarkerChange (ancorname, markersArray){   ' ."\n";
			$scripttext .= 'var link = document.getElementById(ancorname);' ."\n";
			if ($compatiblemode == 1)
			{
				$scripttext .= '       if (myHasClass(link, "active")) {' ."\n";
			}
			else
			{
				$scripttext .= '       if (link.hasClass("active")) { ' ."\n";
			}
	        $scripttext .= '             markerCluster0.remove(markersArray);' ."\n";
	        $scripttext .= '             markerCluster0.refresh();' ."\n";
			//
	        $scripttext .= '       } else {    ' ."\n";
	        $scripttext .= '             markerCluster0.add(markersArray);' ."\n";
        	$scripttext .= '       }' ."\n";
			if ($compatiblemode == 1)
			{
				$scripttext .= ' myToggleClass(link, "active"); ' ."\n";
			}
			else
			{
				$scripttext .= ' link.toggleClass("active"); ' ."\n";
			}
			$scripttext .= '};' ."\n";
            }
		}
     }
     else
     {
        if (isset($map->markergroupcontrol) && (int)$map->markergroupcontrol != 0) 
        {
            $scripttext .= 'function callMarkerArrayChange (ancorname, markersArray){   ' ."\n";
            $scripttext .= 'var link = document.getElementById(ancorname);' ."\n";
			if ($compatiblemode == 1)
			{
				$scripttext .= '       if (myHasClass(link, "active")) {' ."\n";
			}
			else
			{
				$scripttext .= '       if (link.hasClass("active")) { ' ."\n";
			}
            $scripttext .= '   	     for (var i=0; i<markersArray.length; i++)' ."\n";
            $scripttext .= '   	     {' ."\n";
            $scripttext .= '               map.geoObjects.remove(markersArray[i]);' ."\n";		
            $scripttext .= '             }' ."\n";
            $scripttext .= '       } else {    ' ."\n";
            $scripttext .= '   	     for (var i=0; i<markersArray.length; i++)' ."\n";
            $scripttext .= '   	     {' ."\n";
            $scripttext .= '               map.geoObjects.add(markersArray[i]);' ."\n";
            $scripttext .= '             }' ."\n";
            $scripttext .= '       }' ."\n";
			if ($compatiblemode == 1)
			{
				$scripttext .= ' myToggleClass(link, "active"); ' ."\n";
			}
			else
			{
				$scripttext .= ' link.toggleClass("active"); ' ."\n";
			}
            $scripttext .= '};' ."\n";
        }
        
        $scripttext .= 'function callMarkerDisable (markersArray){   ' ."\n";
        $scripttext .= ' for (var i=0; i<markersArray.length; i++)' ."\n";
        $scripttext .= ' {' ."\n";
        $scripttext .= '   map.geoObjects.remove(markersArray[i]);' ."\n";		
        $scripttext .= ' }' ."\n";
        $scripttext .= '};' ."\n";
        
     }

}
// For Marker Cluster Support Functions - end
	
	
	// Geo Position - Begin
	if ((isset($map->autoposition) && (int)$map->autoposition == 1)
	 || (isset($map->autopositioncontrol) && (int)$map->autopositioncontrol != 0))
	{
			$scripttext .= 'function findMyPosition(AutoPosition) {' ."\n";
			$scripttext .= '     if (AutoPosition == "Button")' ."\n";
			$scripttext .= '     {' ."\n";
        	$scripttext .= '        if (ymaps.geolocation) ' ."\n";
			$scripttext .= '        {' ."\n";
	        $scripttext .= '        	p_center = [ymaps.geolocation.longitude, ymaps.geolocation.latitude];' ."\n";
			if (isset($map->findroute) && (int)$map->findroute == 1) 
			{
				$scripttext .= '    		getMyMapRoute(p_center);' ."\n";
			}
			else
			{
				$scripttext .= '    		map.setCenter(p_center);' ."\n";
			}
			//$scripttext .= '        	alert("Find");';
        	$scripttext .= '        } ' ."\n";
			$scripttext .= '        else ' ."\n";
			$scripttext .= '        {' ."\n";
			//$scripttext .= '        	alert("Not find");';
	        $scripttext .= '    	}' ."\n";
			$scripttext .= '     }' ."\n";
			$scripttext .= '     else' ."\n";
			$scripttext .= '     {' ."\n";
        	$scripttext .= '        if (ymaps.geolocation) ' ."\n";
			$scripttext .= '        {' ."\n";
	        $scripttext .= '        	p_center = [ymaps.geolocation.longitude, ymaps.geolocation.latitude];' ."\n";
	        $scripttext .= '    		map.setCenter(p_center);' ."\n";
			//$scripttext .= '        	alert("Find");';
        	$scripttext .= '        } ' ."\n";
			$scripttext .= '        else ' ."\n";
			$scripttext .= '        {' ."\n";
			//$scripttext .= '        	alert("Not find");';
	        $scripttext .= '    	}' ."\n";
			$scripttext .= '     }' ."\n";
			$scripttext .= '}' ."\n";
	}
	
	
/*	
	if (isset($map->markergroupcontrol) && (int)$map->markergroupcontrol != 0) 
	{
        // Add menu uten in list
        $scripttext .= 'function addMenuItem (group, map, menuContainer, imgname, activeincluster) {    ' ."\n";

        // Show/Hide group
		$scripttext .= 'if (activeincluster == 1) ' ."\n";
		$scripttext .= '{' ."\n";

		$scripttext .= 'YMaps.jQuery("<a class=\"active zhym-menutitle'.$markergroupcssstyle.'\" href=\"#\"><img src=\"" + imgname + "\" alt=\"\" />" + group.title + "</a>") ' ."\n";
        	$scripttext .= '    .bind("click", function () {   ' ."\n";
	        $scripttext .= '        var link = YMaps.jQuery(this);' ."\n";

        	// If menuitem is not active - add group to map,
	        // else - remove
        	$scripttext .= '       if (link.hasClass("active")) { ' ."\n";
	        $scripttext .= '           map.removeOverlay(group);' ."\n";
        	$scripttext .= '       } else {    ' ."\n";
	        $scripttext .= '           map.addOverlay(group);' ."\n";
        	$scripttext .= '       }' ."\n";

	        // change flag on menuitem
        	$scripttext .= '       link.toggleClass("active"); ' ."\n";

	        $scripttext .= '       return false; ' ."\n";
        	$scripttext .= '   })' ."\n";

	        // add new menuitem into list
        	$scripttext .= '   .appendTo(    ' ."\n";
	        $scripttext .= '       YMaps.jQuery("<li></li>").appendTo(menuContainer) ' ."\n";
        	$scripttext .= '   ) ' ."\n";


		$scripttext .= '}' ."\n";
		$scripttext .= 'else' ."\n";
		$scripttext .= '{' ."\n";

		$scripttext .= 'YMaps.jQuery("<a class=\"zhym-menutitle'.$markergroupcssstyle.'\" href=\"#\"><img src=\"" + imgname + "\" alt=\"\" />" + group.title + "</a>") ' ."\n";
        	$scripttext .= '    .bind("click", function () {   ' ."\n";
	        $scripttext .= '        var link = YMaps.jQuery(this);' ."\n";

        	// If menuitem is not active - add group to map,
	        // else - remove
        	$scripttext .= '       if (link.hasClass("active")) { ' ."\n";
	        $scripttext .= '           map.removeOverlay(group);' ."\n";
        	$scripttext .= '       } else {    ' ."\n";
	        $scripttext .= '           map.addOverlay(group);' ."\n";
        	$scripttext .= '       }' ."\n";

	        // change flag on menuitem
        	$scripttext .= '       link.toggleClass("active"); ' ."\n";

	        $scripttext .= '       return false; ' ."\n";
        	$scripttext .= '   })' ."\n";

	        // add new menuitem into list
        	$scripttext .= '   .appendTo(    ' ."\n";
	        $scripttext .= '       YMaps.jQuery("<li></li>").appendTo(menuContainer) ' ."\n";
        	$scripttext .= '   ) ' ."\n";

		$scripttext .= '}' ."\n";


        $scripttext .= '};' ."\n";
	}
// End Marker Group List Control

*/

// Find option Begin
	if (isset($map->findcontrol) && (int)$map->findcontrol == 1) 
	{
        $scripttext .= 'function showAddressByGeocoding(value) {' ."\n";
        // Delete Previous Result
		$scripttext .= '  if (geoResult)' ."\n";
		$scripttext .= '  {' ."\n";
        $scripttext .= '    map.geoObjects.remove(geoResult);' ."\n";
		$scripttext .= '  }' ."\n";

        // Geocoding
		$scripttext .= '   if ((map.getType() == "yandex#publicMap") || (map.getType() == "yandex#publicMapHybrid"))';
		$scripttext .= '   {';
        $scripttext .= '     var geocoderOpts = {results: 1, boundedBy: map.getBounds(), provider:"yandex#publicMap"};' ."\n";
		$scripttext .= '   }';
		$scripttext .= '   else';
		$scripttext .= '   {';
        $scripttext .= '     var geocoderOpts = {results: 1, boundedBy: map.getBounds()};' ."\n";
		$scripttext .= '   }';
        $scripttext .= '   ymaps.geocode(value, geocoderOpts).then(function (res) {' ."\n";
        // if find then add to map
        // set center map
		$scripttext .= '     cnt = res.geoObjects.getLength();'."\n";
        $scripttext .= '        if (cnt > 0) ' ."\n";
		$scripttext .= '		{' ."\n";
        $scripttext .= '            geoResult = res.geoObjects.get(0);' ."\n";
		$scripttext .= '     		geoResult.properties.set(\'balloonContentHeader\', \'\');'."\n";
		$scripttext .= '    		geoResult.properties.set(\'balloonContentBody\', \'\');'."\n";
        $scripttext .= '            map.geoObjects.add(geoResult);' ."\n";
        $scripttext .= '            map.setCenter(geoResult.geometry.getCoordinates());' ."\n";
		// add route
		if (isset($map->findroute) && (int)$map->findroute == 1) 
		{
			$scripttext .= '            getMyMapRoute(geoResult.geometry.getCoordinates()); '."\n";
		}
		// end add route
        $scripttext .= '        }' ."\n";
		$scripttext .= '		else ' ."\n";
		$scripttext .= '		{' ."\n";
        $scripttext .= '            alert("'.JText::_('COM_ZHYANDEXMAP_MAP_ERROR_FIND_GEOCODING').'");' ."\n";
        $scripttext .= '        }' ."\n";
        $scripttext .= '    },' ."\n";

        // Failure geocoding
        $scripttext .= '    function (err) {' ."\n";
        $scripttext .= '        alert("'.JText::_('COM_ZHYANDEXMAP_MAP_ERROR_FIND_GEOCODING_ERROR').'" + err.message);' ."\n";
        $scripttext .= '    });' ."\n";
        $scripttext .= '};' ."\n";
	}
// Find option End


// Add Map Route
		if (isset($map->findroute) && (int)$map->findroute == 1) 
		{
			$scripttext .= 'function getMyMapRoute(curposition) {'."\n";
		
			$scripttext .= '  if (geoRoute)' ."\n";
			$scripttext .= '  {' ."\n";
			$scripttext .= '	map.geoObjects.remove(geoRoute);' ."\n";
			$scripttext .= '  }' ."\n";
			
			$scripttext .= '  ymaps.route([curposition, mapcenter],'."\n";
			$scripttext .= '       { mapStateAutoApply: true }'."\n";
			$scripttext .= '  ).then('."\n";
			$scripttext .= '  function(route){'."\n";
			$scripttext .= '    geoRoute = route;'."\n";
			$scripttext .= '    var points = route.getWayPoints();'."\n";
            $scripttext .= '    points.options.set(\'preset\', \'twirl#blueStretchyIcon\');'."\n";
			$scripttext .= '    points.get(0).properties.set(\'iconContent\', \''.JText::_('COM_ZHYANDEXMAP_MAP_FIND_GEOCODING_START_POINT').'\');'."\n";
			$scripttext .= '    points.get(1).properties.set(\'iconContent\', \''.JText::_('COM_ZHYANDEXMAP_MAP_FIND_GEOCODING_END_POINT').'\');'."\n";
			// Clear for do not open balloon
			$scripttext .= '    points.get(0).properties.set(\'balloonContentHeader\', \'\');'."\n";
			$scripttext .= '    points.get(0).properties.set(\'balloonContentBody\', \'\');'."\n";
			$scripttext .= '    points.get(1).properties.set(\'balloonContentHeader\', \'\');'."\n";
			$scripttext .= '    points.get(1).properties.set(\'balloonContentBody\', \'\');'."\n";
			
			$scripttext .= '     map.geoObjects.add(geoRoute);'."\n";
			$scripttext .= '  }, '."\n";
			$scripttext .= '  function(err){'."\n";
			$scripttext .= '     alert(\''.JText::_('COM_ZHYANDEXMAP_MAP_ERROR_GEOCODING').'\' + err.message);'."\n";
			$scripttext .= '  }'."\n";
			$scripttext .= ');'."\n";
			$scripttext .= '}'."\n";
		}


// Toggle for Insert Markers
if (isset($map->usermarkers) && (int)$map->usermarkers == 1) 
{
    if ($allowUserMarker == 1)
    {
			$scripttext .= 'function showonlyone(thename, theid) {'."\n";
			$scripttext .= '  var xPlacemarkA = document.getElementById("bodyInsertPlacemarkA"+theid);'."\n";
			$scripttext .= '  var xPlacemarkGrpA = document.getElementById("bodyInsertPlacemarkGrpA"+theid);'."\n";
		if (isset($map->usercontact) && (int)$map->usercontact == 1)
		{
			$scripttext .= '  var xContactA = document.getElementById("bodyInsertContactA"+theid);'."\n";
			$scripttext .= '  var xContactAdrA = document.getElementById("bodyInsertContactAdrA"+theid);'."\n";
		}
			$scripttext .= '  if (thename == \'Contact\')'."\n";
			$scripttext .= '  {'."\n";
			$scripttext .= '    var toHide2 = document.getElementById("bodyInsertPlacemark"+theid);'."\n";
			$scripttext .= '    var toHide3 = document.getElementById("bodyInsertPlacemarkGrp"+theid);'."\n";
		if (isset($map->usercontact) && (int)$map->usercontact == 1)
		{
			$scripttext .= '    var toHide1 = document.getElementById("bodyInsertContactAdr"+theid);'."\n";
			$scripttext .= '    var toShow = document.getElementById("bodyInsertContact"+theid);'."\n";
		}
			$scripttext .= '    xPlacemarkA.innerHTML = \'<img src="'.$imgpathUtils.'expand.png">'.JText::_( 'COM_ZHYANDEXMAP_MAP_USER_BASIC_PROPERTIES' ).'\';'."\n";
			$scripttext .= '    xPlacemarkGrpA.innerHTML = \'<img src="'.$imgpathUtils.'expand.png">'.JText::_( 'COM_ZHYANDEXMAP_MAP_USER_BASIC_GROUP_PROPERTIES' ).'\';'."\n";
		if (isset($map->usercontact) && (int)$map->usercontact == 1)
		{
			$scripttext .= '    xContactA.innerHTML = \'<img src="'.$imgpathUtils.'collapse.png">'.JText::_( 'COM_ZHYANDEXMAP_MAP_USER_CONTACT_PROPERTIES' ).'\';'."\n";
			$scripttext .= '    xContactAdrA.innerHTML = \'<img src="'.$imgpathUtils.'expand.png">'.JText::_( 'COM_ZHYANDEXMAP_MAP_USER_CONTACT_ADDRESS_PROPERTIES' ).'\';'."\n";
		}
			$scripttext .= '  }'."\n";
			$scripttext .= '  else if (thename == \'Placemark\')'."\n";
			$scripttext .= '  {'."\n";
			$scripttext .= '    var toHide1 = document.getElementById("bodyInsertPlacemarkGrp"+theid);'."\n";
			$scripttext .= '    var toShow = document.getElementById("bodyInsertPlacemark"+theid);'."\n";
		if (isset($map->usercontact) && (int)$map->usercontact == 1)
		{
			$scripttext .= '    var toHide2 = document.getElementById("bodyInsertContact"+theid);'."\n";
			$scripttext .= '    var toHide3 = document.getElementById("bodyInsertContactAdr"+theid);'."\n";
		}
			$scripttext .= '    xPlacemarkA.innerHTML = \'<img src="'.$imgpathUtils.'collapse.png">'.JText::_( 'COM_ZHYANDEXMAP_MAP_USER_BASIC_PROPERTIES' ).'\';'."\n";
			$scripttext .= '    xPlacemarkGrpA.innerHTML = \'<img src="'.$imgpathUtils.'expand.png">'.JText::_( 'COM_ZHYANDEXMAP_MAP_USER_BASIC_GROUP_PROPERTIES' ).'\';'."\n";
		if (isset($map->usercontact) && (int)$map->usercontact == 1)
		{
			$scripttext .= '    xContactA.innerHTML = \'<img src="'.$imgpathUtils.'expand.png">'.JText::_( 'COM_ZHYANDEXMAP_MAP_USER_CONTACT_PROPERTIES' ).'\';'."\n";
			$scripttext .= '    xContactAdrA.innerHTML = \'<img src="'.$imgpathUtils.'expand.png">'.JText::_( 'COM_ZHYANDEXMAP_MAP_USER_CONTACT_ADDRESS_PROPERTIES' ).'\';'."\n";
		}
			$scripttext .= '  }'."\n";
			$scripttext .= '  else if (thename == \'PlacemarkGroup\')'."\n";
			$scripttext .= '  {'."\n";
			$scripttext .= '    var toShow = document.getElementById("bodyInsertPlacemarkGrp"+theid);'."\n";
			$scripttext .= '    var toHide1 = document.getElementById("bodyInsertPlacemark"+theid);'."\n";
		if (isset($map->usercontact) && (int)$map->usercontact == 1)
		{
			$scripttext .= '    var toHide2 = document.getElementById("bodyInsertContact"+theid);'."\n";
			$scripttext .= '    var toHide3 = document.getElementById("bodyInsertContactAdr"+theid);'."\n";
		}
			$scripttext .= '    xPlacemarkA.innerHTML = \'<img src="'.$imgpathUtils.'expand.png">'.JText::_( 'COM_ZHYANDEXMAP_MAP_USER_BASIC_PROPERTIES' ).'\';'."\n";
			$scripttext .= '    xPlacemarkGrpA.innerHTML = \'<img src="'.$imgpathUtils.'collapse.png">'.JText::_( 'COM_ZHYANDEXMAP_MAP_USER_BASIC_GROUP_PROPERTIES' ).'\';'."\n";
		if (isset($map->usercontact) && (int)$map->usercontact == 1)
		{
			$scripttext .= '    xContactA.innerHTML = \'<img src="'.$imgpathUtils.'expand.png">'.JText::_( 'COM_ZHYANDEXMAP_MAP_USER_CONTACT_PROPERTIES' ).'\';'."\n";
			$scripttext .= '    xContactAdrA.innerHTML = \'<img src="'.$imgpathUtils.'expand.png">'.JText::_( 'COM_ZHYANDEXMAP_MAP_USER_CONTACT_ADDRESS_PROPERTIES' ).'\';'."\n";
		}
			$scripttext .= '  }'."\n";
			$scripttext .= '  else if (thename == \'ContactAddress\')'."\n";
			$scripttext .= '  {'."\n";
			$scripttext .= '    var toHide2 = document.getElementById("bodyInsertPlacemark"+theid);'."\n";
			$scripttext .= '    var toHide3 = document.getElementById("bodyInsertPlacemarkGrp"+theid);'."\n";
		if (isset($map->usercontact) && (int)$map->usercontact == 1)
		{
			$scripttext .= '    var toHide1 = document.getElementById("bodyInsertContact"+theid);'."\n";
			$scripttext .= '    var toShow = document.getElementById("bodyInsertContactAdr"+theid);'."\n";
		}
			$scripttext .= '    xPlacemarkA.innerHTML = \'<img src="'.$imgpathUtils.'expand.png">'.JText::_( 'COM_ZHYANDEXMAP_MAP_USER_BASIC_PROPERTIES' ).'\';'."\n";
			$scripttext .= '    xPlacemarkGrpA.innerHTML = \'<img src="'.$imgpathUtils.'expand.png">'.JText::_( 'COM_ZHYANDEXMAP_MAP_USER_BASIC_GROUP_PROPERTIES' ).'\';'."\n";
		if (isset($map->usercontact) && (int)$map->usercontact == 1)
		{
			$scripttext .= '    xContactA.innerHTML = \'<img src="'.$imgpathUtils.'expand.png">'.JText::_( 'COM_ZHYANDEXMAP_MAP_USER_CONTACT_PROPERTIES' ).'\';'."\n";
			$scripttext .= '    xContactAdrA.innerHTML = \'<img src="'.$imgpathUtils.'collapse.png">'.JText::_( 'COM_ZHYANDEXMAP_MAP_USER_CONTACT_ADDRESS_PROPERTIES' ).'\';'."\n";
		}
			$scripttext .= '  }'."\n";
			$scripttext .= '  toHide1.style.display = \'none\';'."\n";
			$scripttext .= '  toShow.style.display = \'block\';'."\n";
		if (isset($map->usercontact) && (int)$map->usercontact == 1)
		{
			$scripttext .= '  toHide2.style.display = \'none\';'."\n";
			$scripttext .= '  toHide3.style.display = \'none\';'."\n";
		}
			$scripttext .= '}'."\n";
    }   
}



$scripttext .= '/*]]>*/</script>' ."\n";
// Script end

$document->addScript($scriptlink . $loadmodules);

echo $scripttext;

}
// end of main part


