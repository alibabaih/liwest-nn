<?php

/**
* Authorization plugin
* @Copyright (C) 2009-2011 Gavick.com
* @ All rights reserved
* @ Joomla! is Free Software
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version $Revision: GK4 1.0 $
**/

// No direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

class plgAuthenticationFacebook extends JPlugin
{
	function onUserAuthenticate($credentials, $options, &$response)
	{
		jimport('joomla.user.helper');
		jimport('joomla.user.helper');
		
		$response->type = 'Joomla';
		// Joomla does not like blank passwords
		if (empty($credentials['password'])) {
			$response->status = JAUTHENTICATE_STATUS_FAILURE;
			$response->error_message = JText::_('JGLOBAL_AUTH_EMPTY_PASS_NOT_ALLOWED');
			return false;
		}
		
		if($credentials['password'] == 'Facebook' && $credentials['username'] == 'Facebook') {
			$plugin = JPluginHelper::getPlugin('authentication', 'facebook'); 
			$params = json_decode($plugin->params);			
			
			require 'sdk/facebook.php';
			
			$facebook = new Facebook(array(
			  'appId'  => $params->facebook_api_id,
			  'secret' => $params->facebook_api_secret
			));
			
			// See if there is a user from a cookie
			$user = $facebook->getUser();
			$user_profile = null;
			
			if ($user) {
			  try {
			    // Proceed knowing you have a logged in user who's authenticated.
			    $user_profile = $facebook->api('/me');
			  } catch (FacebookApiException $e) {
			    echo '<pre>'.htmlspecialchars(print_r($e, true)).'</pre>';
			    $user = null;
			  }
			}
						
			if($user_profile) {
				// Initialise variables.
				$conditions = '';
				// Get a database object
				$db		= JFactory::getDbo();
				$query	= $db->getQuery(true);
				$query->select('id');
				$query->from('#__users');
				$query->where('email = ' . $db->Quote($user_profile['email']));
				$db->setQuery($query);
				$result = $db->loadObject();
		
				if ($result) {
					$user = JUser::getInstance($result->id); // Bring this in line with the rest of the system
					$response->email = $user->email;
					$response->username = $user->username;
					$response->fullname = $user->name;
					
					if (JFactory::getApplication()->isAdmin()) {
						$response->language = $user->getParam('admin_language');
					} else {
						$response->language = $user->getParam('language');
					}
					
					$response->status = JAUTHENTICATE_STATUS_SUCCESS;
					$response->error_message = JText::_('PLG_GK_FACEBOOK_SUCCESS') . '<strong>' . $user->username . '</strong>';
				} else {
					if($params->auto_register == 1) {
						$response->email = $user_profile['email'];
						$response->username = $user_profile['email'];
						$response->fullname = $user_profile['name'];
				
						$response->status = JAUTHENTICATE_STATUS_SUCCESS;
						$response->error_message = JText::_('PLG_GK_FACEBOOK_NEW_ACCOUNT') . '<strong>' . $user_profile['name'] . '</strong>';
					} else {
						$response->status = JAUTHENTICATE_STATUS_FAILURE;
						$response->error_message = JText::_('JGLOBAL_AUTH_NO_USER');
					}
				}
			} 
		} else {
			$response->status = JAUTHENTICATE_STATUS_FAILURE;
			$response->error_message = JText::_('JGLOBAL_AUTH_NO_USER');
		}
		
		return $response;		
	}
}