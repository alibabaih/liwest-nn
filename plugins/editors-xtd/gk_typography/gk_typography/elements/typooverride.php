<?php

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

class JFormFieldTypoOverride extends JFormField
{
	public $type = 'TypoOverride';
	public $html = '';
	protected function getInput() {
		
		$templates = JFolder::folders(JPATH_SITE.DS.'templates', $filter= '.', $recurse=false, $fullpath=false, $exclude=array('.svn', 'CVS')); // exclude repository files and recursive
		$counter = 0;
		foreach($templates as $template){
			$counter++;
            // check if the template folder contains typography subdirectory (true ? find_files : skip)
            if(JFolder::exists(JPATH_SITE . DS . 'templates' . DS . $template . DS.  'typography', $filter= 'xml', $recurse=false, $fullpath=false, $exclude=array('.svn', 'CVS'))){
            $files = JFolder::files(JPATH_SITE . DS . 'templates' . DS . $template . DS.  'typography', $filter= 'xml', $recurse=false, $fullpath=false, $exclude=array('.svn', 'CVS'));
            // check count of files 0 - skip
            if(count($files) !=0 ){					
			$option[$template]=array();
			$option[$template]['id']=$counter;
			$option[$template]['text']=JText::sprintf($template, $template);
			$option[$template]['items']=array();
				// start listing xml files 
				 foreach($files as $file){
					$text = $file;
					$option[$template]['items'][]	= JHTML::_('select.option', $template.':'.$file, $text);
				 }
            }
            } 
		}
		
		if(!isset($option))
		{
			$option[0]=array();
			$option[0]['id']=0;
			$option[0]['text']=JText::sprintf("no files found", "no files found");
			$option[0]['items']=array();
			$option[0]['items'][]	= JHTML::_('select.option', "error", "no files found");
		} 
		
		return JHtml::_('select.groupedlist', $option, $this->name, array('id'=>$this->id, 'group.id'=>'id', 'list.select' => $this->value));
	}
}
