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

class JFormFieldInterface extends JFormField
{
	protected $type = 'Interface';

	protected function getInput()
	{
		return null;
	}

	protected function getLabel()
	{
		JHtml::stylesheet($this->element['path'].'interface.css');
		JHtml::script($this->element['path'].'interface.js');

		return null;
	}
}