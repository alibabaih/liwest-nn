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

class JFormFieldInputboxes extends JFormField
{
	protected $type = 'Inputboxes';
	protected $forceMultiple = true;

	protected function getInput()
	{
		$html = array();
		$class = $this->element['class'] ? ' class="inputboxes '.(string) $this->element['class'].'"' : ' class="inputboxes"';
		$options = $this->getOptions();

		$html[] = '<fieldset id="'.$this->id.'"'.$class.'>';
		$html[] = '<ul>';

		foreach ($options as $i => $option) {
			$value		= isset($this->value[$i]) ? htmlspecialchars($this->value[$i], ENT_COMPAT, 'UTF-8') : $option->value;
			$class		= !empty($option->class) ? ' class="'.$option->class.'"' : '';

			$html[] = '<li>';
			$html[] = '<label for="'.$this->id.$i.'"'.$class.'>'.JText::_($option->text).'</label>';
			$html[] = '<input type="text" id="'.$this->id.$i.'" name="'.$this->name.'" value="'.$value.'"'.$class.'/>';
			$html[] = !empty($option->dimension) ? '<span class="field_description">'.$option->dimension.'</span>' : '';
			$html[] = '</li>';
		}

		$html[] = '</ul>';
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
			$tmp->dimension = (string) $option['dimension'];

			$options[] = $tmp;
		}

		reset($options);
		return $options;
	}
}