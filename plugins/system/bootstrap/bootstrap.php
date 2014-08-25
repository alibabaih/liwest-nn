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
class plgSystemBootstrap extends JPlugin
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

        $onlyFrontside = $this->params->get("onlyFrontside", TRUE);

        //ignore admin
        if ($onlyFrontside && $app->isAdmin()) {
            return true;
        }
        $doc = JFactory::getDocument();

        $onlyHTML = $this->params->get("onlyHTML", TRUE);
        // ignore non html
        if ($onlyHTML && $doc->getType() != 'html') {
            return true;
        }
        // ignore modal pages or other incomplete pages
        $notModal = $this->params->get("notModal", TRUE);
        $nogo = array('component', 'raw');
        if ($notModal && in_array(JRequest::getString('tmpl'), $nogo)) {
            return true;
        }
        $loadCSS = $this->params->get("loadCSS", TRUE);
        if ($loadCSS) {
            $doc->addStyleSheet(JURI::root(true) . '/media/bootstrap/css/bootstrap.min.css');
            $doc->addStyleSheet(JURI::root(true) . '/media/bootstrap/css/bootstrap-responsive.min.css');
        }
        $loadJS = $this->params->get("loadJS", TRUE);
        if ($loadJS) {

            $loadJQuery = $this->params->get("loadJQuery", TRUE);
            if ($loadJQuery) {
                $jQueryFromLocal = $this->params->get("jQueryFromLocal", TRUE);
                if ($jQueryFromLocal) {
                    $doc->addScript(JURI::root(true) . '/media/bootstrap/js/jquery.min.js');
                }else{
                    $doc->addScript('https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js');
                }
            }
            $doc->addScript(JURI::root(true) . '/media/bootstrap/js/bootstrap.min.js');
        }

    }

}
