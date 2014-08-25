<?php
/**
 * @version SVN: $Id: builder.php 469 2011-07-29 19:03:30Z elkuku $
 * @package    Bootstrap
 * @subpackage Base
 * @author     OSTree Team {@link http://www.ostree.org}
 * @author     Created on 16-Jan-2012
 * @license    GNU/GPL
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

jimport('joomla.plugin.plugin');

/**
 * System Plugin.
 *
 * @package    Bootstrap
 * @subpackage Plugin
 */
class plgSystemHyphenator extends JPlugin
{
    /**
     * Constructor
     *
     * @param object $subject The object to observe
     * @param array $config  An array that holds the plugin configuration
     */
    public function __construct(& $subject, $config)
    {
        parent::__construct($subject, $config);
    }

    public function onBeforeRender()
    {
        $app = JFactory::getApplication();

        $doc = JFactory::getDocument();

        $loadJS = $this->params->get("loadJS", TRUE);
        if ($loadJS) {
            $doc->addScript(JURI::root(true) . '/media/hyphenator/hyphenator.js');
        }

    }

}
