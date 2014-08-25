<?php
/**
 * @package		Joomla.Site
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
if (!isset($this->error)) {
	$this->error = JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
	$this->debug = false;
}
//get language and direction
$doc = JFactory::getDocument();
$this->language = $doc->language;
$this->direction = $doc->direction;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	<title><?php echo $this->error->getCode(); ?> - <?php echo $this->title; ?></title>
	<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/bootstrap.css">
</head>
<body>
	<div class="row-fluid">
		<div class="offset1 span3">
			<h1>Ой!</h1>
			<h2>Такой страницы не существует.</h2>
			<p>Если Вы уверены, что здесь должно что-то находится, пожалуйста известите нас об этом по адресу webmaster@liwest-nn.ru</p>	
		</div>
		<div class="span8">
			<p>Мы предлагаем Вам перейти на <a href="/index.php" title="главная страница">главную страницу сайта</a>.</p>
			<p>Так же возможно Вас заинтересуют следующие разделы сайта:</p>
			<ul>
				<li><a href="#">О компании Ли Вест</a></li>
				<li><a href="#">Работа</a></li>
				<li><a href="#">Продукция компании Ли Вест</a></li>
				<li><a href="#">Где мы находимся</a></li>
			</ul>
		</div>


			<p><?php echo $this->error->getMessage(); ?></p>
			<p><?php if ($this->debug) : echo $this->renderBacktrace(); endif; ?></p>
	</div>		
</body>
</html>
