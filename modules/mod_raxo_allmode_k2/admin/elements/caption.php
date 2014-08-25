<?php
/*
 * =============================================================
 * RAXO All-mode K2 J2.5 - Element
 * -------------------------------------------------------------
 * @package		RAXO All-mode K2
 * @copyright	Copyright (C) 2009-2012 RAXO Group
 * @license		RAXO Commercial Licence
 * 				This file is forbidden for redistribution
 * @link		http://raxo.org
 * =============================================================
 */

// no direct access
defined('_JEXEC') or die;

class JFormFieldCaption extends JFormField
{
	protected $type = 'Caption';

	protected function getInput()
	{
		return null;
	}

	protected function getLabel()
	{
		$text = $this->element['label'] ? (string) $this->element['label'] : '';
		return '<h3 class="caption"><span>'. JText::_($text) .'</span></h3>';
	}
}