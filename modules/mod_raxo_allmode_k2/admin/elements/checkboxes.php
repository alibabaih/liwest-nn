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

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldCheckboxes extends JFormField
{
	protected $type = 'Checkboxes';
	protected $forceMultiple = true;

	protected function getInput()
	{
		$html = array();
		$class = $this->element['class'] ? ' class="checkboxes '.(string) $this->element['class'].'"' : ' class="checkboxes"';
		$options = $this->getOptions();

		$this->value = is_string($this->value) && !empty($this->value) ? explode(',', $this->value) : $this->value;

		$html[] = '<fieldset id="'.$this->id.'"'.$class.'>';
		$html[] = '<ul>';

		foreach ($options as $i => $option) {
			$checked	= (in_array((string)$option->value,(array)$this->value) ? ' checked="checked"' : '');
			$class		= !empty($option->class) ? ' class="'.$option->class.'"' : '';
			$disabled	= !empty($option->disable) ? ' disabled="disabled"' : '';
			$onclick	= !empty($option->onclick) ? ' onclick="'.$option->onclick.'"' : '';

			$html[] = '<li>';
			$html[] = '<input type="checkbox" id="'.$this->id.$i.'" name="'.$this->name.'" value="'.htmlspecialchars($option->value, ENT_COMPAT, 'UTF-8').'"'.$checked.$class.$onclick.$disabled.'/>';
			$html[] = '<label for="'.$this->id.$i.'"'.$class.'>'.JText::_($option->text).'</label>';
			$html[] = '</li>';
		}

		$i = $i+1;
		$html[] = '</ul>';
		$html[] = '<input type="hidden" id="'.$this->id.$i.'" name="'.$this->name.'" value="fix" />';
		$html[] = '</fieldset>';

		return implode($html);
	}

	protected function getOptions()
	{
		$options = array();

		foreach ($this->element->children() as $option) {
			if ($option->getName() != 'option') {
				continue;
			}

			$tmp = JHtml::_('select.option', (string) $option['value'], trim((string) $option), 'value', 'text', ((string) $option['disabled']=='true'));
			$tmp->class = (string) $option['class'];
			$tmp->onclick = (string) $option['onclick'];

			$options[] = $tmp;
		}

		reset($options);
		return $options;
	}
}