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

$map = $this->item;

$divmapheader ="";
$divmapfooter ="";

$currentUserInfo ="";
$allowUserMarker = 0;
$currentUserID = 0;
$credits ='';

$scripttext = '';

$compatiblemodersf = $this->mapcompatiblemodersf;
if ($compatiblemodersf == "")
{
  $compatiblemodersf = 0;
}

$licenseinfo = $this->licenseinfo;
if ($licenseinfo == "")
{
  $licenseinfo = 102;
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
	$mapVersion = "1.1";

	$apikey = $this->mapapikey;

	$document	= JFactory::getDocument();

	$scriptlink	= 'http://api-maps.yandex.ru/'.$mapVersion.'/index.xml?key='. $apikey ;
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
				return 'new YMaps.GeoPoint('.$myMarker->longitude.', ' .$myMarker->latitude.')';
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

		$divmarkergroup .= '<ul id="zhym-menu'.$markergroupcssstyle.'">';
		$divmarkergroup .= '</ul>';

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
$divmap4route .= '<div id="YMapsRoutePanel"><div id="YMapsRoutePanel_Description"></div><div id="YMapsRoutePanel_Total"></div></div>';

echo $divmapfooter . $divmap4route;





//Script begin
$scripttext .= '<script type="text/javascript" >/*<![CDATA[*/' ."\n";

	$scripttext .= 'var map, mapcenter, geoResult, geoRoute;' ."\n";
	$scripttext .= 'var searchControl, searchControlPMAP;' ."\n";

	// Begin initialize jquery function
	$scripttext .= 'YMaps.jQuery(function () {' ."\n";
		
        $scripttext .= '    map = new YMaps.Map(document.getElementById("YMapsID"));' ."\n";
		$scripttext .= '    mapcenter = new YMaps.GeoPoint( '.$map->longitude.', ' .$map->latitude.');' ."\n";
        $scripttext .= '    map.setCenter(mapcenter);' ."\n";


	//Double Click Zoom
	if (isset($map->doubleclickzoom) && (int)$map->doubleclickzoom == 1) 
	{
		$scripttext .= 'map.enableDblClickZoom();' ."\n";
	} 
	else 
	{
		$scripttext .= 'map.disableDblClickZoom();' ."\n";
	}


	//Scroll Wheel Zoom		
	if (isset($map->scrollwheelzoom) && (int)$map->scrollwheelzoom == 1) 
	{
		$scripttext .= 'map.enableScrollZoom();' ."\n";
	} 
	else 
	{
		$scripttext .= 'map.disableScrollZoom();' ."\n";
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
                            $ctrlPosition = "YMaps.ControlPosition.TOP_LEFT";
                        break;
                        case 2:
                            $ctrlPosition = "YMaps.ControlPosition.TOP_RIGHT";
                        break;
                        case 3:
                            $ctrlPosition = "YMaps.ControlPosition.BOTTOM_RIGHT";
                        break;
                        case 4:
                            $ctrlPosition = "YMaps.ControlPosition.BOTTOM_LEFT";
                        break;
                        default:
                            $ctrlPosition = "";
                        break;
                    }
                    if ($ctrlPosition != "")
                    {
                        $ctrlPositionFullText = ', new YMaps.ControlPosition('.
                                                    $ctrlPosition.
                                                    ', new YMaps.Point('.
                                                        (int)$map->zoomcontrolofsx.','.
                                                        (int)$map->zoomcontrolofsy.'))';
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
				$scripttext .= 'map.addControl(new YMaps.Zoom()'.$ctrlPositionFullText.');' ."\n";
			break;
			case 2:
				$scripttext .= 'map.addControl(new YMaps.SmallZoom()'.$ctrlPositionFullText.');' ."\n";
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
                            $ctrlPosition = "YMaps.ControlPosition.TOP_LEFT";
                        break;
                        case 2:
                            $ctrlPosition = "YMaps.ControlPosition.TOP_RIGHT";
                        break;
                        case 3:
                            $ctrlPosition = "YMaps.ControlPosition.BOTTOM_RIGHT";
                        break;
                        case 4:
                            $ctrlPosition = "YMaps.ControlPosition.BOTTOM_LEFT";
                        break;
                        default:
                            $ctrlPosition = "";
                        break;
                    }
                    if ($ctrlPosition != "")
                    {
                        $ctrlPositionFullText = ', new YMaps.ControlPosition('.
                                                    $ctrlPosition.
                                                    ', new YMaps.Point('.
                                                        (int)$map->scalecontrolofsx.','.
                                                        (int)$map->scalecontrolofsy.'))';
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
            
		$scripttext .= 'map.addControl(new YMaps.ScaleLine()'.$ctrlPositionFullText.');' ."\n";
	}
        
	if (isset($map->maptypecontrol) && (int)$map->maptypecontrol == 1) 
	{
                $ctrlPosition = "";
                $ctrlPositionFullText ="";
                
                if (isset($map->maptypecontrolpos)) 
                {
                    switch ($map->maptypecontrolpos)
                    {
                        case 1:
                            $ctrlPosition = "YMaps.ControlPosition.TOP_LEFT";
                        break;
                        case 2:
                            $ctrlPosition = "YMaps.ControlPosition.TOP_RIGHT";
                        break;
                        case 3:
                            $ctrlPosition = "YMaps.ControlPosition.BOTTOM_RIGHT";
                        break;
                        case 4:
                            $ctrlPosition = "YMaps.ControlPosition.BOTTOM_LEFT";
                        break;
                        default:
                            $ctrlPosition = "";
                        break;
                    }
                    if ($ctrlPosition != "")
                    {
                        $ctrlPositionFullText = ', new YMaps.ControlPosition('.
                                                    $ctrlPosition.
                                                    ', new YMaps.Point('.
                                                        (int)$map->maptypecontrolofsx.','.
                                                        (int)$map->maptypecontrolofsy.'))';
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
            
		$scripttext .= 'map.addControl(new YMaps.TypeControl()'.$ctrlPositionFullText.');' ."\n";
	}

	if (isset($map->pmaptypecontrol) && (int)$map->pmaptypecontrol == 1) 
	{
                $ctrlPosition = "";
                $ctrlPositionFullText ="";
                
                if (isset($map->pmaptypecontrolpos)) 
                {
                    switch ($map->pmaptypecontrolpos)
                    {
                        case 1:
                            $ctrlPosition = "YMaps.ControlPosition.TOP_LEFT";
                        break;
                        case 2:
                            $ctrlPosition = "YMaps.ControlPosition.TOP_RIGHT";
                        break;
                        case 3:
                            $ctrlPosition = "YMaps.ControlPosition.BOTTOM_RIGHT";
                        break;
                        case 4:
                            $ctrlPosition = "YMaps.ControlPosition.BOTTOM_LEFT";
                        break;
                        default:
                            $ctrlPosition = "";
                        break;
                    }
                    if ($ctrlPosition != "")
                    {
                        $ctrlPositionFullText = ', new YMaps.ControlPosition('.
                                                    $ctrlPosition.
                                                    ', new YMaps.Point('.
                                                        (int)$map->pmaptypecontrolofsx.','.
                                                        (int)$map->pmaptypecontrolofsy.'))';
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

                $scripttext .= 'map.addControl(new YMaps.TypeControl([YMaps.MapType.PMAP, YMaps.MapType.PHYBRID])'.$ctrlPositionFullText.');' ."\n";
                if (isset($map->maptype) && ($loadmodules_pmap == 0))
                {
                    if ($loadmodules =='') 
                    {
                            $loadmodules = '&amp;modules=pmap';
							$loadmodules_pmap = 1;
                    } 
                    else 
                    {
                            $loadmodules .= '~pmap';
							$loadmodules_pmap = 1;
                    }
                }
	}

	// Map type
	if (isset($map->maptype)) 
	{
		switch ($map->maptype) 
		{
			
			case 1:
				$scripttext .= 'map.setType(YMaps.MapType.MAP);' ."\n";
			break;
			case 2:
				$scripttext .= 'map.setType(YMaps.MapType.SATELLITE);' ."\n";
			break;
			case 3:
				$scripttext .= 'map.setType(YMaps.MapType.HYBRID);' ."\n";
			break;
			case 4:
				$scripttext .= 'map.setType(YMaps.MapType.PMAP);' ."\n";
				if ($loadmodules_pmap == 0)
				{
					if ($loadmodules =='') 
					{
							$loadmodules = '&amp;modules=pmap';
							$loadmodules_pmap = 1;
					} 
					else 
					{
							$loadmodules .= '~pmap';
							$loadmodules_pmap = 1;
					}
				}
			break;
			case 5:
				$scripttext .= 'map.setType(YMaps.MapType.PHYBRID);' ."\n";
				if ($loadmodules_pmap == 0)
				{
					if ($loadmodules =='') 
					{
							$loadmodules = '&amp;modules=pmap';
							$loadmodules_pmap = 1;
					} 
					else 
					{
							$loadmodules .= '~pmap';
							$loadmodules_pmap = 1;
					}
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
					$scripttextMiniMap = 'YMaps.MapType.MAP';
				break;
				case 2:
					$scripttextMiniMap = 'YMaps.MapType.SATELLITE';
				break;
				case 3:
					$scripttextMiniMap = 'YMaps.MapType.HYBRID';
				break;
				case 4:
					$scripttextMiniMap = 'YMaps.MapType.PMAP';
					if ($loadmodules_pmap == 0)
					{
						if ($loadmodules =='') 
						{
								$loadmodules = '&amp;modules=pmap';
								$loadmodules_pmap = 1;
						} 
						else 
						{
								$loadmodules .= '~pmap';
								$loadmodules_pmap = 1;
						}
					}
				break;
				case 5:
					$scripttextMiniMap = 'YMaps.MapType.PHYBRID';
					if ($loadmodules_pmap == 0)
					{
						if ($loadmodules =='') 
						{
								$loadmodules = '&amp;modules=pmap';
								$loadmodules_pmap = 1;
						} 
						else 
						{
								$loadmodules .= '~pmap';
								$loadmodules_pmap = 1;
						}
					}
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
		$scripttext .= '    map.setZoom(map.getMaxZoom());' ."\n";
	}
	$scripttext .= '    map.setMinZoom('.(int)$map->minzoom.');' ."\n";
	if ((int)$map->maxzoom != 200)
	{
		$scripttext .= '    map.setMaxZoom('.(int)$map->maxzoom.');' ."\n";
	}
	// When changed maptype max zoom level can be other
	$scripttext .= 'YMaps.Events.observe(map, map.Events.ZoomRangeChange, function (map, mEvent) {' ."\n";
	$scripttext .= '  if (map.getZoom() > map.getMaxZoom())' ."\n";
	$scripttext .= '  {	' ."\n";
	$scripttext .= '    map.setZoom(map.getMaxZoom());' ."\n";
	$scripttext .= '  }' ."\n";
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
			
			$scripttext .= '   var allowedBounds = new YMaps.GeoBounds(' ."\n";
			$scripttext .= '	 new YMaps.GeoPoint('.$mapBoundsArray[0].', '.$mapBoundsArray[1].'), ' ."\n";
			$scripttext .= '	 new YMaps.GeoPoint('.$mapBoundsArray[2].', '.$mapBoundsArray[3].'));' ."\n";

			// Listen for the event
			// dragend
			// bounds_changed
			$scripttext .= '  YMaps.Events.observe(map, map.Events.BoundsChange, function() {' ."\n";
			$scripttext .= '	 if (allowedBounds.contains(map.getCenter())) return;' ."\n";

			// Out of bounds - Move the map back within the bounds
			$scripttext .= '	 var c = map.getCenter(),' ."\n";
			$scripttext .= '		 y = c.getLng(),' ."\n";
			$scripttext .= '		 x = c.getLat(),' ."\n";
			$scripttext .= '		 maxY = allowedBounds.getRightTop().getLng(),' ."\n";
			$scripttext .= '		 maxX = allowedBounds.getRightTop().getLat(),' ."\n";
			$scripttext .= '		 minY = allowedBounds.getLeftBottom().getLng(),' ."\n";
			$scripttext .= '		 minX = allowedBounds.getLeftBottom().getLat();' ."\n";

			$scripttext .= '	 if (x < minX) x = minX;' ."\n";
			$scripttext .= '	 if (x > maxX) x = maxX;' ."\n";
			$scripttext .= '	 if (y < minY) y = minY;' ."\n";
			$scripttext .= '	 if (y > maxY) y = maxY;' ."\n";

			$scripttext .= '	 map.setCenter(new YMaps.GeoPoint(y, x));' ."\n";
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
                            $ctrlPosition = "YMaps.ControlPosition.TOP_LEFT";
                        break;
                        case 2:
                            $ctrlPosition = "YMaps.ControlPosition.TOP_RIGHT";
                        break;
                        case 3:
                            $ctrlPosition = "YMaps.ControlPosition.BOTTOM_RIGHT";
                        break;
                        case 4:
                            $ctrlPosition = "YMaps.ControlPosition.BOTTOM_LEFT";
                        break;
                        default:
                            $ctrlPosition = "";
                        break;
                    }
                    if ($ctrlPosition != "")
                    {
                        $ctrlPositionFullText = ', new YMaps.ControlPosition('.
                                                    $ctrlPosition.
                                                    ', new YMaps.Point('.
                                                        (int)$map->minimapofsx.','.
                                                        (int)$map->minimapofsy.'))';
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

            $scripttext .= 'minimap = new YMaps.MiniMap();' ."\n";

			if ((int)$map->minimap == 1)
			{
				$scripttext .= 'minimap.setVisible(true);' ."\n";
			}
			else
			{
				$scripttext .= 'minimap.setVisible(false);' ."\n";
			}

			if ($scripttextMiniMap != "")
			{
				$scripttext .= 'minimap.setType('.$scripttextMiniMap.');' ."\n";
			}
            $scripttext .= 'map.addControl(minimap'.$ctrlPositionFullText.');' ."\n";
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
                            $ctrlPosition = "YMaps.ControlPosition.TOP_LEFT";
                        break;
                        case 2:
                            $ctrlPosition = "YMaps.ControlPosition.TOP_RIGHT";
                        break;
                        case 3:
                            $ctrlPosition = "YMaps.ControlPosition.BOTTOM_RIGHT";
                        break;
                        case 4:
                            $ctrlPosition = "YMaps.ControlPosition.BOTTOM_LEFT";
                        break;
                        default:
                            $ctrlPosition = "";
                        break;
                    }
                    if ($ctrlPosition != "")
                    {
                        $ctrlPositionFullText = ', new YMaps.ControlPosition('.
                                                    $ctrlPosition.
                                                    ', new YMaps.Point('.
                                                        (int)$map->toolbarofsx.','.
                                                        (int)$map->toolbarofsy.'))';
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

                $scripttext .= 'var toolbar = new YMaps.ToolBar();' ."\n";
                $scripttext .= 'map.addControl(toolbar'.$ctrlPositionFullText.');' ."\n";


				
		if (isset($map->autopositioncontrol) && (int)$map->autopositioncontrol == 1) 
		{
				switch ((int)$map->autopositionbutton) 
				{
					case 1:
						$scripttext .= 'var btnGeoPosition = new YMaps.ToolBarButton({ icon: "'.$imgpathUtils.'geolocation.png", caption: "", hint: "'.JText::_('COM_ZHYANDEXMAP_MAP_AUTOPOSITIONBUTTON').'"});' ."\n";
					break;
					case 2:
						$scripttext .= 'var btnGeoPosition = new YMaps.ToolBarButton({ caption: "'.JText::_('COM_ZHYANDEXMAP_MAP_AUTOPOSITIONBUTTON').'", hint: "'.JText::_('COM_ZHYANDEXMAP_MAP_AUTOPOSITIONBUTTON').'"});' ."\n";
					break;
					case 3:
						$scripttext .= 'var btnGeoPosition = new YMaps.ToolBarButton({ icon: "'.$imgpathUtils.'geolocation.png", caption: "'.JText::_('COM_ZHYANDEXMAP_MAP_AUTOPOSITIONBUTTON').'", hint: "'.JText::_('COM_ZHYANDEXMAP_MAP_AUTOPOSITIONBUTTON').'"});' ."\n";
					break;
					default:
						$scripttext .= 'var btnGeoPosition = new YMaps.ToolBarButton({ icon: "'.$imgpathUtils.'geolocation.png", caption: "", hint: "'.JText::_('COM_ZHYANDEXMAP_MAP_AUTOPOSITIONBUTTON').'"});' ."\n";
					break;
				}

				$scripttext .= 'YMaps.Events.observe(btnGeoPosition, btnGeoPosition.Events.Click, function () {' ."\n";
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
								$btnPlacemarkListOptions =", {selected:true}" ;
							break;
							case 12:
								$btnPlacemarkListOptions =", {selected:true}" ;
							break;
							default:
								$btnPlacemarkListOptions ="" ;
							break;
						}		
						
						switch ((int)$map->markerlistbuttontype) 
						{
							case 1:
								$scripttext .= 'var btnPlacemarkList = new YMaps.ToolBarToggleButton({ icon: "'.$imgpathUtils.'star.png", caption: "", hint: "'.JText::_('COM_ZHYANDEXMAP_MAP_PLACEMARKLIST').'"}'.$btnPlacemarkListOptions.');' ."\n";
							break;
							case 2:
								$scripttext .= 'var btnPlacemarkList = new YMaps.ToolBarToggleButton({ caption: "'.JText::_('COM_ZHYANDEXMAP_MAP_PLACEMARKLIST').'", hint: "'.JText::_('COM_ZHYANDEXMAP_MAP_PLACEMARKLIST').'"}'.$btnPlacemarkListOptions.');' ."\n";
							break;
							case 11:
								$scripttext .= 'var btnPlacemarkList = new YMaps.ToolBarToggleButton({ icon: "'.$imgpathUtils.'star.png", caption: "", hint: "'.JText::_('COM_ZHYANDEXMAP_MAP_PLACEMARKLIST').'"}'.$btnPlacemarkListOptions.');' ."\n";
							break;
							case 2:
								$scripttext .= 'var btnPlacemarkList = new YMaps.ToolBarToggleButton({ caption: "'.JText::_('COM_ZHYANDEXMAP_MAP_PLACEMARKLIST').'", hint: "'.JText::_('COM_ZHYANDEXMAP_MAP_PLACEMARKLIST').'"}'.$btnPlacemarkListOptions.');' ."\n";
							default:
								$scripttext .= 'var btnPlacemarkList = new YMaps.ToolBarToggleButton({ icon: "'.$imgpathUtils.'star.png", caption: "", hint: "'.JText::_('COM_ZHYANDEXMAP_MAP_PLACEMARKLIST').'"}'.$btnPlacemarkListOptions.');' ."\n";
							break;
						}

						$scripttext .= 'YMaps.Events.observe(btnPlacemarkList, btnPlacemarkList.Events.Select, function () {' ."\n";
						$scripttext .= '		var toHideDiv = document.getElementById("YMapsMarkerList");' ."\n";
						$scripttext .= '		toHideDiv.style.display = "block";' ."\n";
						$scripttext .= '}, toolbar);' ."\n";
						
						$scripttext .= 'YMaps.Events.observe(btnPlacemarkList, btnPlacemarkList.Events.Deselect, function () {' ."\n";
						$scripttext .= '		var toHideDiv = document.getElementById("YMapsMarkerList");' ."\n";
						$scripttext .= '		toHideDiv.style.display = "none";' ."\n";
						$scripttext .= '}, toolbar);' ."\n";

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
                            $ctrlPosition = "YMaps.ControlPosition.TOP_LEFT";
                        break;
                        case 2:
                            $ctrlPosition = "YMaps.ControlPosition.TOP_RIGHT";
                        break;
                        case 3:
                            $ctrlPosition = "YMaps.ControlPosition.BOTTOM_RIGHT";
                        break;
                        case 4:
                            $ctrlPosition = "YMaps.ControlPosition.BOTTOM_LEFT";
                        break;
                        default:
                            $ctrlPosition = "";
                        break;
                    }
                    if ($ctrlPosition != "")
                    {
                        $ctrlPositionFullText = ', new YMaps.ControlPosition('.
                                                    $ctrlPosition.
                                                    ', new YMaps.Point('.
                                                        (int)$map->searchofsx.','.
                                                        (int)$map->searchofsy.'))';
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

				
                $scripttext .= 'searchControl = new YMaps.SearchControl();' ."\n";
                $scripttext .= 'searchControlPMAP = new YMaps.SearchControl({geocodeOptions: {geocodeProvider: "yandex#pmap"}});' ."\n";
				$scripttext .= '   if ((map.getType() == YMaps.MapType.PMAP) || (map.getType() == YMaps.MapType.PHYBRID))';
				$scripttext .= '   {';
				//$scripttext .= '	  alert("PMAP");' ."\n";
                $scripttext .= '	  map.addControl(searchControlPMAP'.$ctrlPositionFullText.');' ."\n";
				$scripttext .= '   }';
				$scripttext .= '   else';
				$scripttext .= '   {';
				//$scripttext .= '	  alert("MAP");' ."\n";
                $scripttext .= '	  map.addControl(searchControl'.$ctrlPositionFullText.');' ."\n";
				$scripttext .= '   }';
				

				$scripttext .= 'YMaps.Events.observe(map, map.Events.TypeChange, function (map, mEvent) {' ."\n";
				$scripttext .= '   if ((map.getType() == YMaps.MapType.PMAP) || (map.getType() == YMaps.MapType.PHYBRID))';
				$scripttext .= '   {';
				//$scripttext .= '	  alert("PMAP");' ."\n";
				$scripttext .= '	  map.removeControl(searchControl);' ."\n";
				$scripttext .= '	  map.addControl(searchControlPMAP'.$ctrlPositionFullText.');' ."\n";
				$scripttext .= '   }';
				$scripttext .= '   else';
				$scripttext .= '   {';
				//$scripttext .= '	  alert("Map");' ."\n";
				$scripttext .= '	  map.removeControl(searchControlPMAP);' ."\n";
				$scripttext .= '	  map.addControl(searchControl'.$ctrlPositionFullText.');' ."\n";
				$scripttext .= '   }';
				$scripttext .= '});' ."\n";
				
	}
	

	//Traffic
	if (isset($map->traffic) && (int)$map->traffic == 1) 
	{
		if ($loadmodules =='') 
		{
			$loadmodules = '&amp;modules=traffic';
		} 
		else 
		{
			$loadmodules .= '~traffic';
		}

                $ctrlPosition = "";
                $ctrlPositionFullText ="";
                
                if (isset($map->trafficpos)) 
                {
                    switch ($map->trafficpos)
                    {
                        case 1:
                            $ctrlPosition = "YMaps.ControlPosition.TOP_LEFT";
                        break;
                        case 2:
                            $ctrlPosition = "YMaps.ControlPosition.TOP_RIGHT";
                        break;
                        case 3:
                            $ctrlPosition = "YMaps.ControlPosition.BOTTOM_RIGHT";
                        break;
                        case 4:
                            $ctrlPosition = "YMaps.ControlPosition.BOTTOM_LEFT";
                        break;
                        default:
                            $ctrlPosition = "";
                        break;
                    }
                    if ($ctrlPosition != "")
                    {
                        $ctrlPositionFullText = ', new YMaps.ControlPosition('.
                                                    $ctrlPosition.
                                                    ', new YMaps.Point('.
                                                        (int)$map->trafficofsx.','.
                                                        (int)$map->trafficofsy.'))';
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

				if (isset($map->trafficlayer) && (int)$map->trafficlayer == 1) 
				{
					$scripttext .= 'map.addControl(new YMaps.Traffic.Control({}, {shown: true})'.$ctrlPositionFullText.');' ."\n";
				}
				else
				{
					$scripttext .= 'map.addControl(new YMaps.Traffic.Control()'.$ctrlPositionFullText.');' ."\n";
				}

	}


	if (isset($map->markermanager) && (int)$map->markermanager == 1) 
	{
		$scripttext .= 'var objectManager = new YMaps.ObjectManager();'."\n";
		$scripttext .= 'map.addOverlay(objectManager);'."\n";
	}
	
	if (isset($map->rightbuttonmagnifier) && (int)$map->rightbuttonmagnifier == 1) 
	{
		$scripttext .= 'map.enableRightButtonMagnifier();'."\n";
	}
	else
	{
		$scripttext .= 'map.disableRightButtonMagnifier();'."\n";
	}

	if (isset($map->magnifier)) 
	{
		switch ((int)$map->magnifier)
		{
			case 0:
			break;
			case 1:
				$scripttext .= 'map.enableMagnifier();'."\n";
			break;
			case 2:
				$scripttext .= 'map.enableRuler();'."\n";
			break;
			default:
			break;
		}
	}

	if (isset($map->draggable) && (int)$map->draggable == 1) 
	{
		$scripttext .= 'map.enableDragging();'."\n";
	}
	else
	{
		$scripttext .= 'map.disableDragging();'."\n";
	}

	
	//Grid Coordinates		
	if (isset($map->gridcoordinates) && (int)$map->gridcoordinates == 1) 
	{
		$scripttext .= 'map.addLayer(new YMaps.Layer(new YMaps.TileDataSource("http://lrs.maps.yandex.net/tiles/?l=grd&v=1.0&%c", true, false)));' ."\n";
	}
	
	// MarkerGroups
	if (isset($map->markergroupcontrol) && (int)$map->markergroupcontrol != 0) 
	{
   	   if (isset($this->markergroups) && !empty($this->markergroups)) 
	   {
		

		foreach ($this->markergroups as $key => $currentmarkergroup) 
		{
			$imgimg = $imgpathIcons.str_replace("#", "%23", $currentmarkergroup->icontype).'.png';
			$imgimg4size = $imgpath4size.$currentmarkergroup->icontype.'.png';

			list ($imgwidth, $imgheight) = getimagesize($imgimg4size);

			$markergroupname ='';
			$markergroupname = 'markergroup'. $currentmarkergroup->id;
			if ((int)$currentmarkergroup->overridemarkericon == 1
			  && (int)$currentmarkergroup->published == 1) 
			{
				$scripttext .= 'var smg'.$currentmarkergroup->id.' = new YMaps.Style();' ."\n";
				$scripttext .= 'smg'.$currentmarkergroup->id.'.iconStyle = new YMaps.IconStyle();' ."\n";
				$scripttext .= 'smg'.$currentmarkergroup->id.'.iconStyle.href ="'.$imgimg.'";' ."\n";
				$scripttext .= 'smg'.$currentmarkergroup->id.'.iconStyle.size = new YMaps.Point('.$imgwidth.','.$imgheight.');' ."\n";
				if (isset($currentmarkergroup->iconofsetx) 
				 && isset($currentmarkergroup->iconofsety) 
				 && ((int)$currentmarkergroup->iconofsetx !=0
				  || (int)$currentmarkergroup->iconofsety !=0)
				 )
				{
					$scripttext .= 'smg'. $currentmarkergroup->id.'.iconStyle.offset = new YMaps.Point('.(int)$currentmarkergroup->iconofsetx.','.(int)$currentmarkergroup->iconofsety.');' ."\n";
				}
				$scripttext .= ' YMaps.Styles.add("custom#MarkerGroup'.$currentmarkergroup->id.'", smg'.$currentmarkergroup->id.');'."\n";

				$scripttext .= ' var '.$markergroupname.' = new YMaps.GeoObjectCollection("custom#MarkerGroup'.$currentmarkergroup->id.'");'."\n";
			}
			else
			{
				$scripttext .= ' var '.$markergroupname.' = new YMaps.GeoObjectCollection();'."\n";
			}
			$scripttext .= $markergroupname.'.title = "'.htmlspecialchars(str_replace('\\', '/', $currentmarkergroup->title), ENT_QUOTES, 'UTF-8').'";'."\n";
			if ((int)$map->markergroupshowicon == 1)
			{
				$scripttext .= 'addMenuItem('.$markergroupname.', map, YMaps.jQuery("#zhym-menu'.$markergroupcssstyle.'"), "'.$imgimg.'", '.(int)$currentmarkergroup->activeincluster.');'."\n";
			}
			else
			{
				$scripttext .= 'addMenuItem('.$markergroupname.', map, YMaps.jQuery("#zhym-menu'.$markergroupcssstyle.'"), "", '.(int)$currentmarkergroup->activeincluster.');'."\n";
			}    
			if ((int)$currentmarkergroup->activeincluster == 1)
			{
			        $scripttext .= 'map.addOverlay('.$markergroupname.');' ."\n";
			}                                                                                    
		}
	   }
	}

	//UserMarker - begin
	if ($allowUserMarker == 1)
	{
		$db = JFactory::getDBO();
		
		$scripttext .= 'if (YMaps.location) {' ."\n";
		$scripttext .= '  var insertPlacemarkLocation = new YMaps.GeoPoint(YMaps.location.longitude, YMaps.location.latitude);' ."\n";
		$scripttext .= '}else {' ."\n";
		$scripttext .= '  var insertPlacemarkLocation = new YMaps.GeoPoint(30.3158, 59.9388);' ."\n";
		$scripttext .= '}' ."\n";

		$scripttext .= 'var insertPlacemark = new YMaps.Placemark(insertPlacemarkLocation, {draggable: true});' ."\n";
		$scripttext .= 'insertPlacemark.name = "'.JText::_( 'COM_ZHYANDEXMAP_MAP_USER_NEWMARKER' ).'";' ."\n";
		$scripttext .= 'insertPlacemark.description = "'.JText::_( 'COM_ZHYANDEXMAP_MAP_USER_NEWMARKER_DESC' ).'";' ."\n";
		$scripttext .= 'map.addOverlay(insertPlacemark);' ."\n";

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



		$scripttext .= 'YMaps.Events.observe(insertPlacemark, insertPlacemark.Events.Drag, function (obj) {' ."\n";
		$scripttext .= '    insertPlacemark.closeBalloon();' ."\n";
		$scripttext .= '    insertPlacemarkLocation = obj.getGeoPoint().copy();' ."\n";

		$scripttext .= '  contentInsertPlacemarkButtons = \'<div id="contentInsertPlacemarkButtons">\' +' ."\n";
		$scripttext .= '    \'<hr />\'+' ."\n";					
		$scripttext .= '    \'<input name="markerlat" type="hidden" value="\'+insertPlacemarkLocation.getLat() + \'" />\'+' ."\n";
		$scripttext .= '    \'<input name="markerlng" type="hidden" value="\'+insertPlacemarkLocation.getLng() + \'" />\'+' ."\n";
		$scripttext .= '    \'<input name="markerid" type="hidden" value="" />\'+' ."\n";
		$scripttext .= '    \'<input name="contactid" type="hidden" value="" />\'+' ."\n";
		$scripttext .= '    \'<input name="marker_action" type="hidden" value="insert" />\'+' ."\n";	
		$scripttext .= '    \'<input name="markersubmit" type="submit" value="'.JText::_( 'COM_ZHYANDEXMAP_MAP_USER_BUTTON_ADD' ).'" />\'+' ."\n";
		$scripttext .= '    \'</form>\'+' ."\n";		
		$scripttext .= '\'</div>\'+'."\n";
		$scripttext .= '\'</div>\';'."\n";
		
		$scripttext .= '    insertPlacemark.setBalloonContent(contentInsertPlacemarkPart1+';
		$scripttext .= 'contentInsertPlacemarkIcon+';
		$scripttext .= 'contentInsertPlacemarkPart2+';
		$scripttext .= 'contentInsertPlacemarkButtons);'."\n";

		$scripttext .= '});' ."\n";


		//$scripttext .= 'YMaps.Events.observe(insertPlacemark, insertPlacemark.Events.Click, function (obj) {' ."\n";
		//$scripttext .= '    insertPlacemark.closeBalloon();' ."\n";
		//$scripttext .= '    insertPlacemarkLocation = obj.getGeoPoint().copy();' ."\n";
		//$scripttext .= '    YMaps.Events.notify(insertPlacemark, insertPlacemark.Events.BalloonOpen);' ."\n";
		//$scripttext .= '});' ."\n";
		
		$scripttext .= 'YMaps.Events.observe(map, map.Events.Click, function (map, mEvent) {' ."\n";
		$scripttext .= '    insertPlacemark.closeBalloon();' ."\n";
		$scripttext .= '    insertPlacemarkLocation = mEvent.getGeoPoint().copy();' ."\n";
		$scripttext .= '    insertPlacemark.setGeoPoint(insertPlacemarkLocation);' ."\n";

		$scripttext .= '  contentInsertPlacemarkButtons = \'<div id="contentInsertPlacemarkButtons">\' +' ."\n";
		$scripttext .= '    \'<hr />\'+' ."\n";					
		$scripttext .= '    \'<input name="markerlat" type="hidden" value="\'+insertPlacemarkLocation.getLat() + \'" />\'+' ."\n";
		$scripttext .= '    \'<input name="markerlng" type="hidden" value="\'+insertPlacemarkLocation.getLng() + \'" />\'+' ."\n";
		$scripttext .= '    \'<input name="markerid" type="hidden" value="" />\'+' ."\n";
		$scripttext .= '    \'<input name="contactid" type="hidden" value="" />\'+' ."\n";
		$scripttext .= '    \'<input name="marker_action" type="hidden" value="insert" />\'+' ."\n";	
		$scripttext .= '    \'<input name="markersubmit" type="submit" value="'.JText::_( 'COM_ZHYANDEXMAP_MAP_USER_BUTTON_ADD' ).'" />\'+' ."\n";
		$scripttext .= '    \'</form>\'+' ."\n";		
		$scripttext .= '\'</div>\'+'."\n";
		$scripttext .= '\'</div>\';'."\n";
		
		$scripttext .= '    insertPlacemark.setBalloonContent(contentInsertPlacemarkPart1+';
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
				$scripttext .= 'map.openBalloon(new YMaps.GeoPoint('.$map->longitude.', ' .$map->latitude.'), "'.htmlspecialchars(str_replace('\\', '/', $map->title), ENT_QUOTES, 'UTF-8').'");' ."\n";
			break;
			case 2:
				$scripttext .= 'var placemark = new YMaps.Placemark(new YMaps.GeoPoint('.$map->longitude.', ' .$map->latitude.'));' ."\n";
				$scripttext .= 'placemark.name = "' .htmlspecialchars(str_replace('\\', '/', $map->title), ENT_QUOTES, 'UTF-8').'";' ."\n";
				$scripttext .= 'placemark.description = "' .htmlspecialchars(str_replace('\\', '/', $map->description), ENT_QUOTES, 'UTF-8').'";' ."\n";
				$scripttext .= 'map.addOverlay(placemark);' ."\n";
				$scripttext .= 'placemark.openBalloon();' ."\n";
			break;
			case 3:
				$scripttext .= 'var placemark = new YMaps.Placemark(new YMaps.GeoPoint('.$map->longitude.', ' .$map->latitude.'));' ."\n";
				$scripttext .= 'placemark.name = "' .htmlspecialchars(str_replace('\\', '/', $map->title), ENT_QUOTES, 'UTF-8').'";' ."\n";
				$scripttext .= 'placemark.description = "' .htmlspecialchars(str_replace('\\', '/', $map->description), ENT_QUOTES, 'UTF-8').'";' ."\n";
				$scripttext .= 'map.addOverlay(placemark);' ."\n";
				$scripttext .= 'placemark.setIconContent("' .htmlspecialchars(str_replace('\\', '/', $map->title), ENT_QUOTES, 'UTF-8').'");' ."\n";
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

					if (isset($map->markergroupcontrol) 
						&& ((int)$map->markergroupcontrol != 0)
						&& ((int)$currentmarker->markergroup != 0) 
						&& ((int)$currentmarker->overridemarkericon == 1) 
						&& ((int)$currentmarker->publishedgroup == 1))
					{
						$scripttext .= 'var latlng'.$currentmarker->id.'= new YMaps.GeoPoint('.$currentmarker->longitude.', ' .$currentmarker->latitude.');' ."\n";
						
						$scripttext .= 'var '.$markername.'= new YMaps.Placemark(latlng'.$currentmarker->id.', {';
						if ((int)$currentmarker->actionbyclick == 1)
						{
							$scripttext .= ' hasBalloon:true ';
						}
						else
						{
							$scripttext .= ' hasBalloon:false ';
						}
						$scripttext .= '});' ."\n";
					}
					else
					{

						if ((int)$currentmarker->overridemarkericon == 0
						|| ((int)$currentmarker->publishedgroup == 0))
						{
							$imgimg = $imgpathIcons.str_replace("#", "%23", $currentmarker->icontype).'.png';
							$imgimg4size = $imgpath4size.$currentmarker->icontype.'.png';

							list ($imgwidth, $imgheight) = getimagesize($imgimg4size);

							$scripttext .= 'var s'. $currentmarker->id.' = new YMaps.Style();' ."\n";
							$scripttext .= 's'. $currentmarker->id.'.iconStyle = new YMaps.IconStyle();' ."\n";
							$scripttext .= 's'. $currentmarker->id.'.iconStyle.href ="'.$imgimg.'";' ."\n";
							$scripttext .= 's'. $currentmarker->id.'.iconStyle.size = new YMaps.Point('.$imgwidth.','.$imgheight.');' ."\n";
							if (isset($currentmarker->iconofsetx) 
							 && isset($currentmarker->iconofsety) 
							 && ((int)$currentmarker->iconofsetx !=0
							  || (int)$currentmarker->iconofsety !=0)
							 )
							{
								$scripttext .= 's'. $currentmarker->id.'.iconStyle.offset = new YMaps.Point('.(int)$currentmarker->iconofsetx.','.(int)$currentmarker->iconofsety.');' ."\n";
							}
						}
						else
						{
							$imgimg = $imgpathIcons.str_replace("#", "%23", $currentmarker->groupicontype).'.png';
							$imgimg4size = $imgpath4size.$currentmarker->groupicontype.'.png';

							list ($imgwidth, $imgheight) = getimagesize($imgimg4size);

							$scripttext .= 'var s'. $currentmarker->id.' = new YMaps.Style();' ."\n";
							$scripttext .= 's'. $currentmarker->id.'.iconStyle = new YMaps.IconStyle();' ."\n";
							$scripttext .= 's'. $currentmarker->id.'.iconStyle.href ="'.$imgimg.'";' ."\n";
							$scripttext .= 's'. $currentmarker->id.'.iconStyle.size = new YMaps.Point('.$imgwidth.','.$imgheight.');' ."\n";
							if (isset($currentmarker->groupiconofsetx) 
							 && isset($currentmarker->groupiconofsety) 
							 && ((int)$currentmarker->groupiconofsetx !=0
							  || (int)$currentmarker->groupiconofsety !=0)
							 )
							{
								$scripttext .= 's'. $currentmarker->id.'.iconStyle.offset = new YMaps.Point('.(int)$currentmarker->groupiconofsetx.','.(int)$currentmarker->groupiconofsety.');' ."\n";
							}
						}

						$scripttext .= 'var latlng'.$currentmarker->id.'= new YMaps.GeoPoint('.$currentmarker->longitude.', ' .$currentmarker->latitude.');' ."\n";
						
						$scripttext .= 'var '.$markername.'= new YMaps.Placemark(latlng'.$currentmarker->id.', {';
						if ((int)$currentmarker->actionbyclick == 1)
						{
							$scripttext .= ' hasBalloon:true, ';
						}
						else
						{
							$scripttext .= ' hasBalloon:false, ';
						}
						
						$scripttext .= ' style: s'. $currentmarker->id;
						
						$scripttext .= ' });' ."\n";
					}

					
					if (($allowUserMarker == 0)
					 || (isset($currentmarker->userprotection) && (int)$currentmarker->userprotection == 1)
					 || ($currentUserID == 0)
					 || (isset($currentmarker->createdbyuser) 
						&& (((int)$currentmarker->createdbyuser != $currentUserID )
						   || ((int)$currentmarker->createdbyuser == 0)))
					 )
					{
					
						$scripttext .= 'var contentString'. $currentmarker->id.' = \'<div id="placemarkContent'. $currentmarker->id.'">\' +' ."\n";
						if (isset($currentmarker->markercontent) &&
							(((int)$currentmarker->markercontent == 0) ||
							 ((int)$currentmarker->markercontent == 1))
							)
						{
							$scripttext .= '\'<h1 id="headContent'. $currentmarker->id.'" class="placemarkHead">'.htmlspecialchars(str_replace('\\', '/', $currentmarker->title), ENT_QUOTES, 'UTF-8').'</h1>\'+' ."\n";
						}
						$scripttext .= '\'<div id="bodyContent'. $currentmarker->id.'"  class="placemarkBody">\'+'."\n";

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

								
								$scripttext .= '\'</div>\'+'."\n";
						$scripttext .= '\'</div>\';'."\n";

						
						// Action By Click - Begin							
						switch ((int)$currentmarker->actionbyclick)
						{
							// None
							case 0:
								if ((int)$currentmarker->zoombyclick != 100)
								{
									$scripttext .= 'YMaps.Events.observe('.$markername.', '.$markername.'.Events.Click, function (obj) {' ."\n";
									$scripttext .= '  map.setCenter(latlng'. $currentmarker->id.');' ."\n";
									$scripttext .= '  map.setZoom('.(int)$currentmarker->zoombyclick.');' ."\n";
									$scripttext .= '});' ."\n";
								}
							break;
							// Info
							case 1:
								$scripttext .= 'YMaps.Events.observe('.$markername.', '.$markername.'.Events.Click, function (obj) {' ."\n";
								if ((int)$currentmarker->zoombyclick != 100)
								{
									$scripttext .= '  map.setCenter(latlng'. $currentmarker->id.');' ."\n";
									$scripttext .= '  map.setZoom('.(int)$currentmarker->zoombyclick.');' ."\n";
								}
								$scripttext .= $markername.'.setBalloonContent(contentString'. $currentmarker->id.');' ."\n";

								$scripttext .= '    YMaps.Events.notify('.$markername.', '.$markername.'.Events.BalloonOpen);' ."\n";
								//$scripttext .= '    '.$markername.'.openBalloon();' ."\n";
								
								$scripttext .= '  });' ."\n";
							break;
							// Link
							case 2:
								if ($currentmarker->hrefsite != "")
								{
									$scripttext .= 'YMaps.Events.observe('.$markername.', '.$markername.'.Events.Click, function (obj) {' ."\n";
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
										$scripttext .= 'YMaps.Events.observe('.$markername.', '.$markername.'.Events.Click, function (obj) {' ."\n";
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
									$scripttext .= 'YMaps.Events.observe('.$markername.', '.$markername.'.Events.Click, function (obj) {' ."\n";
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
										$scripttext .= 'YMaps.Events.observe('.$markername.', '.$markername.'.Events.Click, function (obj) {' ."\n";
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
						$scripttext .= $markername.'.setOptions({draggable: true});' ."\n";
						
						//$scripttext .= 'contentString'.$currentmarker->id.' = contentString'.$currentmarker->id.'+' ."\n";
						// replace content
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
							/*
							if(isset($map->usermarkersicon) && (int)$map->usermarkersicon == 1) 
							{
								$iconTypeJS = " onchange=\"javascript: ";
								$iconTypeJS .= " if (document.forms.updatePlacemarkForm".$currentmarker->id.".markerimage.options[selectedIndex].value!=\'\') ";
								$iconTypeJS .= " {document.markericonimage".$currentmarker->id.".src=\'".$imgpathIcons."\' + document.forms.updatePlacemarkForm".$currentmarker->id.".markerimage.options[selectedIndex].value.replace(/#/g,\'%23\') + \'.png\'}";
								$iconTypeJS .= " else ";
								$iconTypeJS .= " {document.markericonimage".$currentmarker->id.".src=\'\'}\"";
								
								$scripttext .= '    \''.JText::_( 'COM_ZHYANDEXMAP_MAP_USER_ICON_TYPE' ).' \'+' ."\n";
								$scripttext .= ' \'';
								$scripttext .= '<img name="markericonimage'.$currentmarker->id.'" src="'.$imgpathIcons .str_replace("#", "%23", $currentmarker->icontype).'.png" alt="" />';
								$scripttext .= '\'+' ."\n";
								$scripttext .= '    \'<br />\'+' ."\n";
								$scripttext .= ' \'';
								$scripttext .= str_replace('.png<', '<', 
													str_replace('.png"', '"', 
														str_replace('JOPTION_SELECT_IMAGE', JText::_('COM_ZHYANDEXMAP_MAP_USER_IMAGESELECT'),
															str_replace(array("\r", "\r\n", "\n"),'', JHTML::_('list.images',  'markerimage', $active = $currentmarker->icontype.'.png', $iconTypeJS, $directoryIcons, $extensions =  "png")))));
								$scripttext .= '\'+' ."\n";
								$scripttext .= '    \'<br />\'+' ."\n";		
							}
							else
							{
								$scripttext .= '    \'<input name="markerimage" type="hidden" value="default#" />\'+' ."\n";	
							}
							*/
							
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
						
						
						
						$scripttext .= 'YMaps.Events.observe('.$markername.', '.$markername.'.Events.Click, function (obj) {' ."\n";

						$scripttext .= 'var contentStringButtons'.$currentmarker->id.' = "" +' ."\n";
						$scripttext .= '    \'<hr />\'+' ."\n";					
						$scripttext .= '    \'<input name="markerlat" type="hidden" value="\'+latlng'. $currentmarker->id.'.getLat() + \'" />\'+' ."\n";
						$scripttext .= '    \'<input name="markerlng" type="hidden" value="\'+latlng'. $currentmarker->id.'.getLng() + \'" />\'+' ."\n";
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

						$scripttext .= $markername.'.setBalloonContent(contentStringPart1'.$currentmarker->id.'+';
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
						
						$scripttext .= '    YMaps.Events.notify('.$markername.', '.$markername.'.Events.BalloonOpen);' ."\n";
						//$scripttext .= '    '.$markername.'.openBalloon();' ."\n";
						
						$scripttext .= '});' ."\n";
						
						
						$scripttext .= 'YMaps.Events.observe('.$markername.', '.$markername.'.Events.Drag, function (obj) {' ."\n";
						$scripttext .= '    latlng'. $currentmarker->id.' = obj.getGeoPoint().copy();' ."\n";
						$scripttext .= '});' ."\n";

						// Change UserMarker - end
					}
					
					// Placemark Content - End
					
					
					/*
					  Does it need, or I move all???
					$scripttext .= $markername.'.name = "' .htmlspecialchars(str_replace('\\', '/', $currentmarker->title), ENT_QUOTES, 'UTF-8').'";' ."\n";
					$scripttext .= $markername.'.description = "' .htmlspecialchars(str_replace('\\', '/', $currentmarker->description), ENT_QUOTES, 'UTF-8').'";' ."\n";
					*/

					
					
					if (isset($currentmarker->showiconcontent) && ((int)$currentmarker->showiconcontent == 1))
					{			
						$scripttext .= $markername.'.setIconContent("'.htmlspecialchars(str_replace('\\', '/', $currentmarker->title), ENT_QUOTES, 'UTF-8').'");' ."\n";
					}

					if (isset($map->markergroupcontrol) && ((int)$map->markergroupcontrol != 0)
						&& ($currentmarker->markergroup != 0))
					{
						$markergroupname ='';
						$markergroupname = 'markergroup'. $currentmarker->markergroup;
						$scripttext .= $markergroupname.'.add('.$markername.');'."\n";

					}
					else
					{
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
							$scripttext .= 'map.addOverlay('.$markername.');' ."\n";
						}
					}

					if ($currentmarker->openbaloon == '1')
					{
						// $scripttext .= 'YMaps.Events.notify('.$markername.', '.$markername.'.Events.Click);';
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
									$scripttext .= $markername.'.setBalloonContent(contentString'. $currentmarker->id.');' ."\n";

									//$scripttext .= '    YMaps.Events.notify('.$markername.', '.$markername.'.Events.BalloonOpen);' ."\n";
									$scripttext .= '    '.$markername.'.openBalloon();' ."\n";
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
						
						// Action By Click - For Placemark Open Balloon Property - End
						
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
														$scripttext .= $markername.'.setBalloonContent(contentString'. $currentmarker->id.');' ."\n";

														//$scripttext .= '    YMaps.Events.notify('.$markername.', '.$markername.'.Events.BalloonOpen);' ."\n";
														$scripttext .= '    '.$markername.'.openBalloon();' ."\n";
														
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
													$scripttext .= $markername.'.setBalloonContent(contentString'. $currentmarker->id.');' ."\n";

													//$scripttext .= '    YMaps.Events.notify('.$markername.', '.$markername.'.Events.BalloonOpen);' ."\n";
													$scripttext .= '    '.$markername.'.openBalloon();' ."\n";
													
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


	// Routers
	if (isset($this->routers) && !empty($this->routers)) 
	{
		$routepanelcount = 0;
		$routepaneltotalcount = 0;

		$routeHTMLdescription ='';
		
		//Begin for each Route
		foreach ($this->routers as $key => $currentrouter) 
		{
			$routername ='';
			$routername = 'route'. $currentrouter->id;
			if ($currentrouter->route != "")
			{
				$scripttext .= ' var '.$routername.' = new YMaps.Router(['.$currentrouter->route.'],[],'."\n";
					$scripttext .=       '{ ';
					if (isset($currentrouter->showtype) && (int)$currentrouter->showtype == 1)
					{
						$scripttext .=       ' viewAutoApply: false ';
					}
					else
					{
						$scripttext .=       ' viewAutoApply: true ';
					}
					if (isset($currentrouter->checktraffic) && (int)$currentrouter->checktraffic == 1)
					{
						$scripttext .=       ', avoidTrafficJams: true ';
					}
					else
					{
						$scripttext .=       ', avoidTrafficJams: false ';
					}
					$scripttext .=       '});'."\n";
				$scripttext .=       'map.addOverlay('.$routername.');'."\n";
				$scripttext .=       'YMaps.Events.observe('.$routername.', '.$routername.'.Events.GeocodeError, function (link, number) {'."\n";
				$scripttext .=       '   alert(\''.JText::_('COM_ZHYANDEXMAP_MAP_ERROR_GEOCODING').'\' + number);'."\n";
				$scripttext .=       '}); '."\n";
				$scripttext .=       'YMaps.Events.observe('.$routername.', '.$routername.'.Events.RouteError, function (number) {'."\n";
				$scripttext .=       '   alert(\''.JText::_('COM_ZHYANDEXMAP_MAP_ERROR_UNABLE_DRAW_ROUTE').'\' + number);'."\n";
				$scripttext .=       '}); '."\n";

				if (isset($currentrouter->showpanel) && (int)$currentrouter->showpanel == 1) 
				{
					$routepanelcount++;
					if (isset($currentrouter->showpaneltotal) && (int)$currentrouter->showpaneltotal == 1) 
					{
						$routepaneltotalcount++;
					}
				}

				if (isset($currentrouter->showpanel) && (int)$currentrouter->showpanel == 1) 
				{
					$scripttext .= 'YMaps.Events.observe('.$routername.', '.$routername.'.Events.Success, function () {'."\n";
					$scripttext .= ' var total_km = '.$routername.'.getDistance();'."\n";
					$scripttext .= ' var total_time = '.$routername.'.getDuration();'."\n";
					$scripttext .= ' total_km = total_km / 1000.;'."\n";
					$scripttext .= ' total_time = total_time / 60.;'."\n";
					$scripttext .= ' total_km = total_km.toFixed(1);'."\n";
					$scripttext .= ' total_time = total_time.toFixed(1);'."\n";

					$scripttext .= '  document.getElementById("YMapsRoutePanel_Total").innerHTML = "<p>';
					$scripttext .= JText::_('COM_ZHYANDEXMAP_MAPROUTER_DETAIL_SHOWPANEL_HDR_TOTAL_KM');
					$scripttext .= ' " + total_km + " ';
					$scripttext .= JText::_('COM_ZHYANDEXMAP_MAPROUTER_DETAIL_SHOWPANEL_HDR_KM');
					$scripttext .= ', ';
					$scripttext .= JText::_('COM_ZHYANDEXMAP_MAPROUTER_DETAIL_SHOWPANEL_HDR_TOTAL_TIME');
					if (isset($currentrouter->checktraffic) && (int)$currentrouter->checktraffic == 1)
					{
						$scripttext .= ' '.JText::_('COM_ZHYANDEXMAP_MAPROUTER_DETAIL_SHOWPANEL_HDR_JAM');
					}					
					$scripttext .= ' " + total_time + " ';
					$scripttext .= JText::_('COM_ZHYANDEXMAP_MAPROUTER_DETAIL_SHOWPANEL_HDR_TIME');
					$scripttext .= '</p>";' ."\n";
					$scripttext .= '}); '."\n";
				}
				
				
			}
			
			
			if ($currentrouter->routebymarker != "")
			{
				$router2name ='';
				$router2name = 'routeByMarker'. $currentrouter->id;
				
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
					
					$scripttext .= ' var '.$router2name.' = new YMaps.Router(['.$routeToDraw.'],[],'."\n";
					$scripttext .=       '{ ';
					if (isset($currentrouter->showtype) && (int)$currentrouter->showtype == 1)
					{
						$scripttext .=       ' viewAutoApply: false ';
					}
					else
					{
						$scripttext .=       ' viewAutoApply: true ';
					}
					if (isset($currentrouter->checktraffic) && (int)$currentrouter->checktraffic == 1)
					{
						$scripttext .=       ', avoidTrafficJams: true ';
					}
					else
					{
						$scripttext .=       ', avoidTrafficJams: false ';
					}
					$scripttext .=       '});'."\n";
					$scripttext .=       'map.addOverlay('.$router2name.');'."\n";
					$scripttext .=       'YMaps.Events.observe('.$router2name.', '.$router2name.'.Events.GeocodeError, function (link, number) {'."\n";
					$scripttext .=       '   alert(\''.JText::_('COM_ZHYANDEXMAP_MAP_ERROR_GEOCODING').'\' + number);'."\n";
					$scripttext .=       '}); '."\n";
					$scripttext .=       'YMaps.Events.observe('.$router2name.', '.$router2name.'.Events.RouteError, function (number) {'."\n";
					$scripttext .=       '   alert(\''.JText::_('COM_ZHYANDEXMAP_MAP_ERROR_UNABLE_DRAW_ROUTE').'\' + number);'."\n";
					$scripttext .=       '}); '."\n";

					if (isset($currentrouter->showpanel) && (int)$currentrouter->showpanel == 1) 
					{
						$routepanelcount++;
						if (isset($currentrouter->showpaneltotal) && (int)$currentrouter->showpaneltotal == 1) 
						{
							$routepaneltotalcount++;
						}
					}

					if (isset($currentrouter->showpanel) && (int)$currentrouter->showpanel == 1) 
					{
						$scripttext .= 'YMaps.Events.observe('.$router2name.', '.$router2name.'.Events.Success, function () {'."\n";
						$scripttext .= ' var total_km = '.$router2name.'.getDistance();'."\n";
						$scripttext .= ' var total_time = '.$router2name.'.getDuration();'."\n";
						$scripttext .= ' total_km = total_km / 1000.;'."\n";
						$scripttext .= ' total_time = total_time / 60.;'."\n";
						$scripttext .= ' total_km = total_km.toFixed(1);'."\n";
						$scripttext .= ' total_time = total_time.toFixed(1);'."\n";

						$scripttext .= '  document.getElementById("YMapsRoutePanel_Total").innerHTML = "<p>';
						$scripttext .= JText::_('COM_ZHYANDEXMAP_MAPROUTER_DETAIL_SHOWPANEL_HDR_TOTAL_KM');
						$scripttext .= ' " + total_km + " ';
						$scripttext .= JText::_('COM_ZHYANDEXMAP_MAPROUTER_DETAIL_SHOWPANEL_HDR_KM');
						$scripttext .= ', ';
						$scripttext .= JText::_('COM_ZHYANDEXMAP_MAPROUTER_DETAIL_SHOWPANEL_HDR_TOTAL_TIME');
						if (isset($currentrouter->checktraffic) && (int)$currentrouter->checktraffic == 1)
						{
							$scripttext .= ' '.JText::_('COM_ZHYANDEXMAP_MAPROUTER_DETAIL_SHOWPANEL_HDR_JAM');
						}					
						$scripttext .= ' " + total_time + " ';
						$scripttext .= JText::_('COM_ZHYANDEXMAP_MAPROUTER_DETAIL_SHOWPANEL_HDR_TIME');
						$scripttext .= '</p>";' ."\n";
						$scripttext .= '}); '."\n";
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
				$scripttext .= 'var '.$kml1.' = new YMaps.YMapsML("'.$currentrouter->kmllayerymapsml.'");' ."\n";
				$scripttext .= 'map.addOverlay('.$kml1.');' ."\n";

				$scripttext .= 'YMaps.Events.observe('.$kml1.', '.$kml1.'.Events.Fault, function ('.$kml1.', error) {' ."\n";
				$scripttext .= '    alert(\''.JText::_('COM_ZHYANDEXMAP_MAP_ERROR_YMAPSML').'\' + error);' ."\n";
				$scripttext .= '});' ."\n";
				
			}

			if ($currentrouter->kmllayerkml != "")
			{
				$kml2 = 'KML'.$routername;
				$scripttext .= 'var '.$kml2.' = new YMaps.KML("'.$currentrouter->kmllayerkml.'");' ."\n";
				$scripttext .= 'map.addOverlay('.$kml2.');' ."\n";
				
				$scripttext .= 'YMaps.Events.observe('.$kml2.', '.$kml2.'.Events.Fault, function ('.$kml2.', error) {' ."\n";
                $scripttext .= '	alert(\''.JText::_('COM_ZHYANDEXMAP_MAP_ERROR_KML').'\' + error);' ."\n";
				$scripttext .= '});' ."\n";
			}

			if ($currentrouter->kmllayergpx != "")
			{
				$kml3 = 'GPX'.$routername;
				$scripttext .= 'var '.$kml3.' = new YMaps.GPX("'.$currentrouter->kmllayergpx.'");' ."\n";
				$scripttext .= 'map.addOverlay('.$kml3.');' ."\n";

				$scripttext .= 'YMaps.Events.observe('.$kml3.', '.$kml3.'.Events.Fault, function ('.$kml3.', error) {' ."\n";
				$scripttext .= '    alert(\''.JText::_('COM_ZHYANDEXMAP_MAP_ERROR_GPX').'\' + error);' ."\n";
				$scripttext .= '});' ."\n";
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
			if ((int)$currentpath->objecttype == 0)
			{		
				$scripttext .= ' var spth'.$currentpath->id.' = new YMaps.Style();'."\n";
				$scripttext .= ' spth'.$currentpath->id.'.lineStyle = new YMaps.LineStyle();'."\n";
				$scripttext .= ' spth'.$currentpath->id.'.lineStyle.strokeColor = \''.$currentpath->color.'\';'."\n";
				$scripttext .= ' spth'.$currentpath->id.'.lineStyle.strokeWidth = \''.$currentpath->width.'\';'."\n";
				$scripttext .= ' YMaps.Styles.add("custom#PolyLine'.$currentpath->id.'", spth'.$currentpath->id.');'."\n";

				$scripttext .= ' var pl'.$currentpath->id.' = new YMaps.Polyline([ '."\n";
				$scripttext .=' new YMaps.GeoPoint('.str_replace(";","), new YMaps.GeoPoint(", $currentpath->path).') '."\n";
				$scripttext .= ' ]); '."\n";

				$scripttext .= 'pl'.$currentpath->id.'.setStyle("custom#PolyLine'.$currentpath->id.'");'."\n";
				$scripttext .= 'pl'.$currentpath->id.'.setBalloonContent("'.htmlspecialchars(str_replace('\\', '/', $currentpath->title), ENT_QUOTES, 'UTF-8').'");' ."\n";
				$scripttext .= 'map.addOverlay(pl'.$currentpath->id.');'."\n";
			}
		}
	}

	$context_suffix = 'map';

	if ($map->kmllayer != "")
	{
		$kml1 = 'YMapsML'.$context_suffix;
		$scripttext .= 'var '.$kml1.' = new YMaps.YMapsML("'.$map->kmllayer.'");' ."\n";
		$scripttext .= 'map.addOverlay('.$kml1.');' ."\n";

		$scripttext .= 'YMaps.Events.observe('.$kml1.', '.$kml1.'.Events.Fault, function ('.$kml1.', error) {' ."\n";
		$scripttext .= '    alert(\''.JText::_('COM_ZHYANDEXMAP_MAP_ERROR_YMAPSML').'\' + error);' ."\n";
		$scripttext .= '});' ."\n";
		
	}

	if ($map->kmllayerkml != "")
	{
		$kml2 = 'KML'.$context_suffix;
		$scripttext .= 'var '.$kml2.' = new YMaps.KML("'.$map->kmllayerkml.'");' ."\n";
		$scripttext .= 'map.addOverlay('.$kml2.');' ."\n";
		
		$scripttext .= 'YMaps.Events.observe('.$kml2.', '.$kml2.'.Events.Fault, function ('.$kml2.', error) {' ."\n";
		$scripttext .= '	alert(\''.JText::_('COM_ZHYANDEXMAP_MAP_ERROR_KML').'\' + error);' ."\n";
		$scripttext .= '});' ."\n";
	}

	if ($map->kmllayergpx != "")
	{
		$kml3 = 'GPX'.$context_suffix;
		$scripttext .= 'var '.$kml3.' = new YMaps.GPX("'.$map->kmllayergpx.'");' ."\n";
		$scripttext .= 'map.addOverlay('.$kml3.');' ."\n";

		$scripttext .= 'YMaps.Events.observe('.$kml3.', '.$kml3.'.Events.Fault, function ('.$kml3.', error) {' ."\n";
		$scripttext .= '    alert(\''.JText::_('COM_ZHYANDEXMAP_MAP_ERROR_GPX').'\' + error);' ."\n";
		$scripttext .= '});' ."\n";
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
						$scripttext .= '    YMaps.Events.notify(btnPlacemarkList, btnPlacemarkList.Events.Select);' ."\n";
						
					break;
					case 12:
						//$scripttext .= '	var toShowDiv = document.getElementById("YMapsMarkerList");' ."\n";
						//$scripttext .= '	toShowDiv.style.display = "block";' ."\n";
						$scripttext .= '    YMaps.Events.notify(btnPlacemarkList, btnPlacemarkList.Events.Select);' ."\n";
					break;
					default:
						$scripttext .= '';
					break;
				}
			}
								
		}	
	}
	// Open Placemark List Presets
	
$scripttext .= '});' ."\n";
// End initialize jquery function
	

	// Geo Position - Begin
	if ((isset($map->autoposition) && (int)$map->autoposition == 1)
	 || (isset($map->autopositioncontrol) && (int)$map->autopositioncontrol != 0))
	{
			$scripttext .= 'function findMyPosition(AutoPosition) {' ."\n";
			$scripttext .= '     if (AutoPosition == "Button")' ."\n";
			$scripttext .= '     {' ."\n";
        	$scripttext .= '        if (YMaps.location) ' ."\n";
			$scripttext .= '        {' ."\n";
	        $scripttext .= '        	p_center = new YMaps.GeoPoint(YMaps.location.longitude, YMaps.location.latitude);' ."\n";
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
        	$scripttext .= '        if (YMaps.location) ' ."\n";
			$scripttext .= '        {' ."\n";
	        $scripttext .= '        	p_center = new YMaps.GeoPoint(YMaps.location.longitude, YMaps.location.latitude);' ."\n";
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


// Find option Begin
	if (isset($map->findcontrol) && (int)$map->findcontrol == 1) 
	{
        $scripttext .= 'function showAddressByGeocoding (value) {' ."\n";
        // Delete Previous Result
        $scripttext .= '    map.removeOverlay(geoResult);' ."\n";

        // Geocoding
		$scripttext .= '   if ((map.getType() == YMaps.MapType.PMAP) || (map.getType() == YMaps.MapType.PHYBRID))';
		$scripttext .= '   {';
        $scripttext .= '     var geocoder = new YMaps.Geocoder(value, {results: 1, boundedBy: map.getBounds(), geocodeProvider:"yandex#pmap"});' ."\n";
		$scripttext .= '   }';
		$scripttext .= '   else';
		$scripttext .= '   {';
        $scripttext .= '     var geocoder = new YMaps.Geocoder(value, {results: 1, boundedBy: map.getBounds()});' ."\n";
		$scripttext .= '   }';
		
		  
        // Success geocoding
        $scripttext .= '   YMaps.Events.observe(geocoder, geocoder.Events.Load, function () {' ."\n";
        // if find then add to map
        // set center map
        $scripttext .= '        if (this.length()) ' ."\n";
		$scripttext .= '		{' ."\n";
        $scripttext .= '            geoResult = this.get(0);' ."\n";
        $scripttext .= '            map.addOverlay(geoResult);' ."\n";
        $scripttext .= '            map.setBounds(geoResult.getBounds());' ."\n";
		// add route
		if (isset($map->findroute) && (int)$map->findroute == 1) 
		{
			$scripttext .= '            getMyMapRoute(geoResult.getGeoPoint()); '."\n";
		}
		// end add route
        $scripttext .= '        }' ."\n";
		$scripttext .= '		else ' ."\n";
		$scripttext .= '		{' ."\n";
        $scripttext .= '            alert("'.JText::_('COM_ZHYANDEXMAP_MAP_ERROR_FIND_GEOCODING').'");' ."\n";
        $scripttext .= '        }' ."\n";
        $scripttext .= '    });' ."\n";

        // Failure geocoding
        $scripttext .= '    YMaps.Events.observe(geocoder, geocoder.Events.Fault, function (geocoder, error) {' ."\n";
        $scripttext .= '        alert("'.JText::_('COM_ZHYANDEXMAP_MAP_ERROR_FIND_GEOCODING_ERROR').'" + error);' ."\n";
        $scripttext .= '    })' ."\n";
        $scripttext .= '};' ."\n";
	}
// Find option End


// Add Map Route
		if (isset($map->findroute) && (int)$map->findroute == 1) 
		{
			$scripttext .= 'function getMyMapRoute(curposition) {'."\n";
		
			$scripttext .= '       map.removeOverlay(geoRoute);' ."\n";
			$scripttext .= '       geoRoute = new YMaps.Router([curposition, mapcenter],[],'."\n";
			$scripttext .= '       { viewAutoApply: true });'."\n";
			$scripttext .= '       map.addOverlay(geoRoute);'."\n";
			
			$scripttext .= '       YMaps.Events.observe(geoRoute, geoRoute.Events.Success, function () {'."\n";
			$scripttext .= '       		geoRoute.getWayPoint(0).setIconContent(\''.JText::_('COM_ZHYANDEXMAP_MAP_FIND_GEOCODING_START_POINT').'\');'."\n";
			
			$scripttext .= '       		geoRoute.getWayPoint(1).setIconContent(\''.JText::_('COM_ZHYANDEXMAP_MAP_FIND_GEOCODING_END_POINT').'\');'."\n";
			$scripttext .= '       		geoRoute.getWayPoint(1).setBalloonContent(\''.JText::_('COM_ZHYANDEXMAP_MAP_FIND_GEOCODING_END_POINT').'\');'."\n";
			$scripttext .= '       });'."\n";	
			
			$scripttext .= '       YMaps.Events.observe(geoRoute, geoRoute.Events.GeocodeError, function (link, number) {'."\n";
			$scripttext .= '         alert(\''.JText::_('COM_ZHYANDEXMAP_MAP_ERROR_GEOCODING').'\' + number);'."\n";
			$scripttext .= '       }); '."\n";
			
			$scripttext .= '       YMaps.Events.observe(geoRoute, geoRoute.Events.RouteError, function (link, number) {'."\n";
			$scripttext .= '          alert(\''.JText::_('COM_ZHYANDEXMAP_MAP_ERROR_UNABLE_DRAW_ROUTE').'\' + number);'."\n";
			$scripttext .= '       }); '."\n";

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


