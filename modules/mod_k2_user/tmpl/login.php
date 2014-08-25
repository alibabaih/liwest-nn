<?php
defined('_JEXEC') or die('Restricted access');
?>

<div id="k2ModuleBox<?php echo $module->id; ?>" class="k2LoginBlock<?php if($params->get('moduleclass_sfx')) echo ' '.$params->get('moduleclass_sfx'); ?>">
	<form action="<?php echo JRoute::_( 'index.php', true, $params->get('usesecure')); ?>" method="post" name="login" id="form-login" >
		<?php if($params->get('pretext')): ?>
		<p class="preText"><?php echo $params->get('pretext'); ?></p>
	  <?php endif; ?>
	  
	  <fieldset class="input">
		<legend>Авторизация</legend>
	    <p id="form-login-username">
	      <!-- <label for="modlgn_username"><?php echo JText::_('K2_USERNAME') ?></label> -->
	      <input id="modlgn_username" type="text" name="username" placeholder="Ваш логин" class="inputbox" size="18" />
	    </p>
	    <p id="form-login-password">
	      <!-- <label for="modlgn_passwd"><?php echo JText::_('K2_PASSWORD') ?></label> -->
	      <input id="modlgn_passwd" type="password" placeholder="Ваш пароль" name="<?php echo (K2_JVERSION=='16')?'password':'passwd'?>" class="inputbox" size="18" />
	    </p>
	    <?php if(JPluginHelper::isEnabled('system', 'remember')): ?>
	    <p id="form-login-remember">
	      <input id="modlgn_remember" type="checkbox" name="remember" class="inputbox" value="yes" />
	      <label for="modlgn_remember"><?php echo JText::_('K2_REMEMBER_ME') ?></label>
	    </p>
	    <?php endif; ?>
	    <button type="submit" name="Submit" class="btn" value="Войти">Войти</button>
	  </fieldset>
	  
	  <ul>
	    <li><a href="<?php echo JRoute::_((K2_JVERSION=='16')?'index.php?option=com_users&view=reset':'index.php?option=com_user&view=reset'); ?>"><?php echo JText::_('K2_FORGOT_YOUR_PASSWORD'); ?></a></li>
	    <li><a href="<?php echo JRoute::_((K2_JVERSION=='16')?'index.php?option=com_users&view=remind':'index.php?option=com_user&view=remind'); ?>"><?php echo JText::_('K2_FORGOT_YOUR_USERNAME'); ?></a></li>
	    <?php if ($usersConfig->get('allowUserRegistration')): ?>
	    <li><a href="<?php echo JRoute::_((K2_JVERSION=='16')?'index.php?option=com_users&view=registration':'index.php?option=com_user&view=register'); ?>"><?php echo JText::_('K2_CREATE_AN_ACCOUNT'); ?></a></li>
	    <?php endif; ?>
	  </ul>
	  
	  <?php if($params->get('posttext')): ?>
	  <p class="postText"><?php echo $params->get('posttext'); ?></p>
	  <?php endif; ?>
	  
	  <input type="hidden" name="option" value="<?php echo (K2_JVERSION=='16')?'com_users':'com_user'?>" />
	  <input type="hidden" name="task" value="<?php echo (K2_JVERSION=='16')?'user.login':'login'?>" />
	  <input type="hidden" name="return" value="<?php echo $return; ?>" />
	  <?php echo JHTML::_( 'form.token' ); ?>
	</form>
</div>
