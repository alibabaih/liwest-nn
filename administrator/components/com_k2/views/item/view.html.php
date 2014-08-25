<?php
/**
 * @version		$Id: view.html.php 1336 2011-11-25 14:45:04Z lefteris.kavadas $
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.gr
 * @copyright	Copyright (c) 2006 - 2011 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class K2ViewItem extends JView
{

	function display($tpl = null) {

		$mainframe = &JFactory::getApplication();
		$db = & JFactory::getDBO();
		jimport('joomla.filesystem.file');
		jimport('joomla.html.pane');
		JHTML::_('behavior.keepalive');
		JRequest::setVar('hidemainmenu', 1);
		$document = &JFactory::getDocument();
		$document->addScript(JURI::root(true).'/media/k2/assets/js/nicEdit.js');
		$js ="
		var K2SitePath = '".JURI::root(true)."/';
		var K2BasePath = '".JURI::base(true)."/';
		var K2Language = [
		'".JText::_('K2_REMOVE', true)."',
		'".JText::_('K2_LINK_TITLE_OPTIONAL',true)."',
		'".JText::_('K2_LINK_TITLE_ATTRIBUTE_OPTIONAL',true)."',
		'".JText::_('K2_ARE_YOU_SURE', true)."',
		'".JText::_('K2_YOU_ARE_NOT_ALLOWED_TO_POST_TO_THIS_CATEGORY', true)."',
		'".JText::_('K2_OR_SELECT_A_FILE_ON_THE_SERVER', true)."',
		]
		";
		$document->addScriptDeclaration($js);
		JModel::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'models');
		$model = & JModel::getInstance('Item', 'K2Model');
		$item = $model->getData();
		JFilterOutput::objectHTMLSafe( $item, ENT_QUOTES, array('video', 'params', 'plugins') );
		$user = & JFactory::getUser();

		// Permissions check on frontend
		if($mainframe->isSite()){
			JLoader::register('K2HelperPermissions', JPATH_COMPONENT.DS.'helpers'.DS.'permissions.php');
			$task = JRequest::getCmd('task');
			if($task=='edit' && !K2HelperPermissions::canEditItem($item->created_by, $item->catid)){
				JError::raiseError(403, JText::_('K2_ALERTNOTAUTH'));
			}
			if($task=='add' && !K2HelperPermissions::canAddItem()){
				JError::raiseError(403, JText::_('K2_ALERTNOTAUTH'));
			}
			// Get permissions
			$K2Permissions = &K2Permissions::getInstance();
			$this->assignRef('permissions', $K2Permissions->permissions);
		}

		if ( JTable::isCheckedOut($user->get ('id'), $item->checked_out )) {
			$message = JText::_('K2_THE_ITEM').': '.$item->title.' '.JText::_('K2_IS_CURRENTLY_BEING_EDITED_BY_ANOTHER_ADMINISTRATOR');
			$url = ($mainframe->isSite())?'index.php?option=com_k2&view=item&id='.$item->id.'&tmpl=component':'index.php?option=com_k2';
			$mainframe->redirect($url, $message);
		}

		if ($item->id){
			$item->checkout($user->get('id'));
		}
		else {
			$item->published = 1;
			$item->publish_down = $db->getNullDate();
			$item->modified = $db->getNullDate();
			$date =& JFactory::getDate();
			$now = $date->toMySQL();
			$item->created = $now;
			$item->publish_up = $item->created;
		}

		$lists = array ();
		if(version_compare( JVERSION, '1.6.0', 'ge' )) {
			$dateFormat = JText::_('K2_J16_DATE_FORMAT_CALENDAR');
		}
		else {
			$dateFormat = JText::_('K2_DATE_FORMAT_CALENDAR');
		}
		$item->publish_up = JHTML::_('date', $item->publish_up, $dateFormat);
		if($item->publish_down == $db->getNullDate()) {
			$item->publish_down = '';
		}
		else {
			$item->publish_down = JHTML::_('date', $item->publish_down, $dateFormat);
		}

		// Set up calendars
		$created = JHTML::_('date', $item->created, $dateFormat);
		$lists['createdCalendar'] = JHTML::_( 'calendar', $created, 'created', 'created');
		$lists['publish_up'] = JHTML::_( 'calendar', $item->publish_up, 'publish_up', 'publish_up');
		$lists['publish_down'] = JHTML::_( 'calendar', $item->publish_down, 'publish_down', 'publish_down');

		if($item->id){
		    $lists['created'] = JHTML::_('date', $item->created, JText::_('DATE_FORMAT_LC2'));
		}
		else {
		    $lists['created'] = JText::_('K2_NEW_DOCUMENT');
		}

		if($item->modified==$db->getNullDate() || !$item->id){
		    $lists['modified'] = JText::_('K2_NEVER');
		}
		else {
		    $lists['modified'] = JHTML::_('date', $item->modified, JText::_('DATE_FORMAT_LC2'));
		}

		$params = & JComponentHelper::getParams('com_k2');
		$wysiwyg = & JFactory::getEditor();

		if ($params->get("mergeEditors")){

			if (JString::strlen($item->fulltext) > 1) {
				$textValue = $item->introtext."<hr id=\"system-readmore\" />".$item->fulltext;
			}
			else {
				$textValue = $item->introtext;
			}
			$text = $wysiwyg->display('text', $textValue, '100%', '400px', '', '');
			$this->assignRef('text', $text);
		}

		else {
			$introtext = $wysiwyg->display('introtext', $item->introtext, '100%', '400px', '', '', array('readmore'));
			$this->assignRef('introtext', $introtext);
			$fulltext = $wysiwyg->display('fulltext', $item->fulltext, '100%', '400px', '', '', array('readmore'));
			$this->assignRef('fulltext', $fulltext);
		}


		$lists['published'] = JHTML::_('select.booleanlist', 'published', 'class="inputbox"', $item->published);
		$lists['featured'] = JHTML::_('select.booleanlist', 'featured', 'class="inputbox"', $item->featured);
		$lists['access'] = JHTML::_('list.accesslevel', $item);

		$query = "SELECT ordering AS value, title AS text FROM #__k2_items WHERE catid={$item->catid}";
		$lists['ordering'] = JHTML::_('list.specificordering', $item, $item->id, $query);

		if(!$item->id)
		$item->catid = $mainframe->getUserStateFromRequest('com_k2itemsfilter_category', 'catid',0, 'int');

		$categoriesModel = &JModel::getInstance('Categories', 'K2Model');
		$categories = $categoriesModel->categoriesTree();
		$lists['catid'] = JHTML::_('select.genericlist', $categories, 'catid', 'class="inputbox"', 'value', 'text', $item->catid);

		if(version_compare( JVERSION, '1.6.0', 'ge' )) {
			$languages = JHTML::_('contentlanguage.existing', true, true);
			$lists['language'] = JHTML::_('select.genericlist', $languages, 'language', '', 'value', 'text', $item->language);
		}

		$lists['checkSIG']=$model->checkSIG();
		$lists['checkAllVideos']=$model->checkAllVideos();

		$remoteVideo = false;
		$providerVideo = false;
		$embedVideo = false;

		if (stristr($item->video,'remote}') !== false) {
			$remoteVideo = true;
			$options['startOffset']= 1;
		}

		$providers = $model->getVideoProviders();

		if (count($providers)){

			foreach ($providers as $provider){
				$providersOptions[] = JHTML::_('select.option', $provider, ucfirst($provider));
				if (stristr($item->video,"{{$provider}}") !== false) {
					$providerVideo = true;
					$options['startOffset']= 2;
				}
			}

		}

		if (JString::substr($item->video, 0, 1) !== '{') {
			$embedVideo = true;
			$options['startOffset']= 3;
		}

		$lists['uploadedVideo'] = (!$remoteVideo && !$providerVideo && !$embedVideo) ? true : false;

		if ($lists['uploadedVideo'] || $item->video==''){
			$options['startOffset']= 0;
		}
		
		$document->addScriptDeclaration("var K2ActiveVideoTab = ".$options['startOffset']);

		$lists['remoteVideo'] = ($remoteVideo)?preg_replace('%\{[a-z0-9-_]*\}(.*)\{/[a-z0-9-_]*\}%i', '\1', $item->video):'';
		$lists['remoteVideoType'] = ($remoteVideo)?preg_replace('%\{([a-z0-9-_]*)\}.*\{/[a-z0-9-_]*\}%i', '\1', $item->video):'';
		$lists['providerVideo'] = ($providerVideo)?preg_replace('%\{[a-z0-9-_]*\}(.*)\{/[a-z0-9-_]*\}%i', '\1', $item->video):'';
		$lists['providerVideoType'] = ($providerVideo)?preg_replace('%\{([a-z0-9-_]*)\}.*\{/[a-z0-9-_]*\}%i', '\1', $item->video):'';
		$lists['embedVideo'] = ($embedVideo)?$item->video:'';

		if (isset($providersOptions)){
			$lists['providers'] = JHTML::_('select.genericlist', $providersOptions, 'videoProvider', '', 'value', 'text', $lists['providerVideoType']);
		}

		JPluginHelper::importPlugin ('content', 'jw_sigpro');
		JPluginHelper::importPlugin ('content', 'jw_allvideos');

		$dispatcher = &JDispatcher::getInstance ();

		// Detect gallery type
		if(JString::strpos($item->gallery, 'http://')) {
			$item->galleryType = 'flickr';
			$item->galleryValue = JString::substr($item->gallery, 9);
			$item->galleryValue = JString::substr($item->galleryValue, 0, -10);
		}
		else {
			$item->galleryType = 'server';
			$item->galleryValue = '';
		}

		$params->set('galleries_rootfolder', 'media/k2/galleries');
		$params->set('thb_width', '150');
		$params->set('thb_height', '120');
		$params->set('enabledownload', '0');
		$item->text=$item->gallery;
		$dispatcher->trigger ( 'onPrepareContent', array (&$item, &$params, null ) );
		$item->gallery=$item->text;

		if(!$embedVideo){
			$params->set('vfolder', 'media/k2/videos');
			$params->set('afolder', 'media/k2/audio');
			if(JString::strpos($item->video, 'remote}')){
				preg_match("#}(.*?){/#s",$item->video, $matches);
				if(JString::substr($matches[1], 0, 7)!='http://')
				$item->video = str_replace($matches[1], JURI::root().$matches[1], $item->video);
			}
			$item->text=$item->video;
			$dispatcher->trigger ( 'onPrepareContent', array (&$item, &$params, null ) );
			$item->video=$item->text;
		} else {
			// no nothing
		}

		if (isset($item->created_by)) {
			$author= & JUser::getInstance($item->created_by);
			$item->author=$author->name;
		}
		else {
			$item->author=$user->name;
		}
		if (isset($item->modified_by)) {
			$moderator = & JUser::getInstance($item->modified_by);
			$item->moderator=$moderator->name;
		}

		if($item->id){
			$active = $item->created_by;
		}
		else {
			$active = $user->id;
		}
		$lists['authors'] = JHTML::_('list.users', 'created_by', $active, false);

		$categories_option[]=JHTML::_('select.option', 0, JText::_('K2_SELECT_CATEGORY'));
		$categories = $categoriesModel->categoriesTree(NUll, true, false);
		if($mainframe->isSite()){
			JLoader::register('K2HelperPermissions', JPATH_SITE.DS.'components'.DS.'com_k2'.DS.'helpers'.DS.'permissions.php');
			if (($task == 'add' || $task =='edit') && !K2HelperPermissions::canAddToAll()) {
				for ($i = 0; $i < sizeof($categories); $i++) {
					if (!K2HelperPermissions::canAddItem($categories[$i]->value)){
						$categories[$i]->disable = true;
					}
				}
			}
		}
		$categories_options=@array_merge($categories_option, $categories);
		$lists['categories'] = JHTML::_('select.genericlist', $categories_options, 'catid', '', 'value', 'text', $item->catid);

		JTable::addIncludePath(JPATH_COMPONENT.DS.'tables');
		$category = & JTable::getInstance('K2Category', 'Table');
		$category->load($item->catid);

		$extraFieldModel= &JModel::getInstance('ExtraField', 'K2Model');
		if($category->id){
			$extraFields = $extraFieldModel->getExtraFieldsByGroup($category->extraFieldsGroup);
		}
		else {
			$extraFields = NULL;
		}


		for($i=0; $i<sizeof($extraFields); $i++){
			$extraFields[$i]->element=$extraFieldModel->renderExtraField($extraFields[$i],$item->id);
		}

		if($item->id){
			$item->attachments=$model->getAttachments($item->id);
			$rating = $model->getRating();
			if(is_null($rating)){
				$item->ratingSum = 0;
				$item->ratingCount = 0;
			}
			else{
				$item->ratingSum = (int)$rating->rating_sum;
				$item->ratingCount = (int)$rating->rating_count;
			}
		}
		else {
			$item->attachments = NULL;
			$item->ratingSum = 0;
			$item->ratingCount = 0;
		}


		if($user->gid<24 && $params->get('lockTags')){
			$params->set('taggingSystem',0);
		}

		$tags=$model->getAvailableTags($item->id);
		$lists['tags'] = JHTML::_ ( 'select.genericlist', $tags, 'tags', 'multiple="multiple" size="10" ', 'id', 'name' );

		if (isset($item->id)){
			$item->tags=$model->getCurrentTags($item->id);
			$lists['selectedTags'] = JHTML::_ ( 'select.genericlist', $item->tags, 'selectedTags[]', 'multiple="multiple" size="10" ', 'id', 'name' );
		}
		else {
			$lists['selectedTags']='<select size="10" multiple="multiple" id="selectedTags" name="selectedTags[]"></select>';
		}

		$lists['metadata']=new JParameter($item->metadata);

		$date =& JFactory::getDate($item->modified);
		$timestamp = '?t='.$date->toUnix();
		
		if (JFile::exists(JPATH_SITE.DS.'media'.DS.'k2'.DS.'items'.DS.'cache'.DS.md5("Image".$item->id).'_L.jpg')){
			$item->image = JURI::root().'media/k2/items/cache/'.md5("Image".$item->id).'_L.jpg'.$timestamp;
		}

		if (JFile::exists(JPATH_SITE.DS.'media'.DS.'k2'.DS.'items'.DS.'cache'.DS.md5("Image".$item->id).'_S.jpg')){
			$item->thumb = JURI::root().'media/k2/items/cache/'.md5("Image".$item->id).'_S.jpg'.$timestamp;
		}

		JPluginHelper::importPlugin ( 'k2' );
		$dispatcher = &JDispatcher::getInstance ();

		$K2PluginsItemContent=$dispatcher->trigger('onRenderAdminForm', array (&$item, 'item', 'content' ) );
		$this->assignRef('K2PluginsItemContent', $K2PluginsItemContent);

		$K2PluginsItemImage=$dispatcher->trigger('onRenderAdminForm', array (&$item, 'item', 'image' ) );
		$this->assignRef('K2PluginsItemImage', $K2PluginsItemImage);

		$K2PluginsItemGallery=$dispatcher->trigger('onRenderAdminForm', array (&$item, 'item', 'gallery' ) );
		$this->assignRef('K2PluginsItemGallery', $K2PluginsItemGallery);

		$K2PluginsItemVideo=$dispatcher->trigger('onRenderAdminForm', array (&$item, 'item', 'video' ) );
		$this->assignRef('K2PluginsItemVideo', $K2PluginsItemVideo);

		$K2PluginsItemExtraFields=$dispatcher->trigger('onRenderAdminForm', array (&$item, 'item', 'extra-fields' ) );
		$this->assignRef('K2PluginsItemExtraFields', $K2PluginsItemExtraFields);

		$K2PluginsItemAttachments=$dispatcher->trigger('onRenderAdminForm', array (&$item, 'item', 'attachments' ) );
		$this->assignRef('K2PluginsItemAttachments', $K2PluginsItemAttachments);

		$K2PluginsItemOther=$dispatcher->trigger('onRenderAdminForm', array (&$item, 'item', 'other' ) );
		$this->assignRef('K2PluginsItemOther', $K2PluginsItemOther);

		if(version_compare( JVERSION, '1.6.0', 'ge' )){
			jimport('joomla.form.form');
			$form = JForm::getInstance('itemForm', JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'item.xml');
			$values = array('params'=>json_decode($item->params));
			$form->bind($values);
		}
		else {
			$form = new JParameter('', JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'item.xml');
			$form->loadINI($item->params);
		}
		$this->assignRef('form', $form);

		$nullDate = $db->getNullDate();
		$this->assignRef('nullDate', $nullDate);

		$this->assignRef('extraFields', $extraFields);
		$this->assignRef('options', $options);
		$this->assignRef('row', $item);
		$this->assignRef('lists', $lists);
		$this->assignRef('params', $params);
		$this->assignRef('user', $user);
		(JRequest::getInt('cid'))? $title = JText::_('K2_EDIT_ITEM') : $title = JText::_('K2_ADD_ITEM');
		$this->assignRef('title', $title);
		$this->assignRef('mainframe', $mainframe);
		if($mainframe->isAdmin()){
			$this->params->set('showImageTab', true);
			$this->params->set('showImageGalleryTab', true);
			$this->params->set('showVideoTab', true);
			$this->params->set('showExtraFieldsTab', true);
			$this->params->set('showAttachmentsTab', true);
			$this->params->set('showK2Plugins', true);
			JToolBarHelper::title($title, 'k2.png');
			JToolBarHelper::save();
			JToolBarHelper::custom('saveAndNew','save.png','save_f2.png','K2_SAVE_AND_NEW', false);
			JToolBarHelper::apply();
			JToolBarHelper::cancel();
		}
		// ACE ACL integration
		$definedConstants = get_defined_constants();
		if (!empty($definedConstants['ACEACL']) && AceaclApi::authorize('permissions', 'com_aceacl')) {
			$aceAclFlag = true;
		}
		else {
			$aceAclFlag = false;
		}
		$this->assignRef('aceAclFlag', $aceAclFlag);
		
		parent::display($tpl);
	}

}
