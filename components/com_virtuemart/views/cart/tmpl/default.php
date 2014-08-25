<?php
/**
 *
 * Layout for the shopping cart
 *
 * @package    VirtueMart
 * @subpackage Cart
 * @author Max Milbers
 *
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: cart.php 2551 2010-09-30 18:52:40Z milbo $
 */

// Check to ensure this file is included in Joomla!
defined ('_JEXEC') or die('Restricted access');
if(VmConfig::get('usefancy',0)){
	vmJsApi::js( 'fancybox/jquery.fancybox-1.3.4.pack');
	vmJsApi::css('jquery.fancybox-1.3.4');
	$box = "
//<![CDATA[
	jQuery(document).ready(function($) {
		$('div#full-tos').hide();
		var con = $('div#full-tos').html();
		$('a#terms-of-service').click(function(event) {
			event.preventDefault();
			$.fancybox ({ div: '#full-tos', content: con });
		});
	});

//]]>
";
} else {
	vmJsApi::js ('facebox');
	vmJsApi::css ('facebox');
	$box = "
//<![CDATA[
	jQuery(document).ready(function($) {
		$('div#full-tos').hide();
		$('a#terms-of-service').click(function(event) {
			event.preventDefault();
			$.facebox( { div: '#full-tos' }, 'my-groovy-style');
		});
	});

//]]>
";
}

JHtml::_ ('behavior.formvalidation');
$document = JFactory::getDocument ();
$document->addScriptDeclaration ($box);
$document->addScriptDeclaration ("

//<![CDATA[
	jQuery(document).ready(function($) {
	if ( $('#STsameAsBTjs').is(':checked') ) {
				$('#output-shipto-display').hide();
			} else {
				$('#output-shipto-display').show();
			}
		$('#STsameAsBTjs').click(function(event) {
			if($(this).is(':checked')){
				$('#STsameAsBT').val('1') ;
				$('#output-shipto-display').hide();
			} else {
				$('#STsameAsBT').val('0') ;
				$('#output-shipto-display').show();
			}
		});
	});

//]]>

");
$document->addStyleDeclaration ('#facebox .content {display: block !important; height: 480px !important; overflow: auto; width: 560px !important; }');

?>

<div class="cart-view">
		<div class="width50 floatleft">
			<h1><?php echo JText::_ ('COM_VIRTUEMART_CART_TITLE'); ?></h1>
		</div>
			<?php if (VmConfig::get ('oncheckout_show_steps', 1) && $this->checkout_task === 'confirm') {
				vmdebug ('checkout_task', $this->checkout_task);
				echo '<div class="checkoutStep" id="checkoutStep4">' . JText::_ ('COM_VIRTUEMART_USER_FORM_CART_STEP4') . '</div>';
				} 
			?>
		<div class="width50 floatleft right">
			<?php // Continue Shopping Button
			if ($this->continue_link_html != '') {
				echo $this->continue_link_html;
			} ?>
		</div>
		<div class="clear"></div>




	<?php echo shopFunctionsF::getLoginForm ($this->cart, FALSE);

	// This displays the pricelist MUST be done with tables, because it is also used for the emails
	echo $this->loadTemplate ('pricelist');
	if ($this->checkout_task) {
		$taskRoute = '&task=' . $this->checkout_task;
	}
	else {
		$taskRoute = '';
	}


	// added in 2.0.8
	?>
	<div id="checkout-advertise-box">
		<?php
		if (!empty($this->checkoutAdvertise)) {
			foreach ($this->checkoutAdvertise as $checkoutAdvertise) {
				?>
				<div class="checkout-advertise">
					<?php echo $checkoutAdvertise; ?>
				</div>
				<?php
			}
		}
		?>
	</div>

	<form method="post" id="checkoutForm" name="checkoutForm" action="<?php echo JRoute::_ ('index.php?option=com_virtuemart&view=cart' . $taskRoute, $this->useXHTML, $this->useSSL); ?>">

		<?php // Leave A Comment Field ?>
		<div class="customer-comment marginbottom15">
			<span class="comment"><?php echo JText::_ ('COM_VIRTUEMART_COMMENT_CART'); ?></span><br/>
			<textarea class="customer-comment" name="customer_comment" cols="60" rows="1"><?php echo $this->cart->customer_comment; ?></textarea>
		</div>
		<?php // Leave A Comment Field END ?>



		<?php // Continue and Checkout Button ?>
		<div class="checkout-button-top">

			
						<div class="terms-of-service">
							<?php // Terms Of Service Checkbox
										if (!class_exists ('VirtueMartModelUserfields')) {
											require(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'userfields.php');
										}
										$userFieldsModel = VmModel::getModel ('userfields');
										if ($userFieldsModel->getIfRequired ('agreed')) {
												if (!class_exists ('VmHtml')) {
													require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'html.php');
												}
												echo VmHtml::checkbox ('tosAccepted', $this->cart->tosAccepted, 1, 0, 'class="terms-of-service"');

												if (VmConfig::get ('oncheckout_show_legal_info', 1)) {
													?>

							
								<a href="#purchaseTerms" role="button" class="terms-of-service" id="terms-of-service" data-toggle="modal">Я соглашаюсь с условиями покупки</a>
								<!-- Modal -->
								<div id="purchaseTerms" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
								  <div class="modal-header">
								    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
								    <h3 id="myModalLabel">Условия покупки</h3>
								  </div>
									<div class="modal-body">
									    <p>Товары доставляются способом, выбранным при оформлении заказа. Стоимость доставки включается в счет автоматически и зависит от выбранного способа доставки (если для региона возможен выбор), от общей массы товаров в заказе, стоимости заказа и расстояния до пункта назначения.</p>
										<p>Общий счет по заказу за пределы РФ не включает таможенную пошлину, размер и порядок оплаты которой установлен в стране получения заказа.</p>
										<p>При заказе на сумму свыше 4000 рублей доставка по Нижнему Новгороду бесплатная.</p>
										<p>Курьерская доставка по Нижнему Новгороду осуществляется в пределах города, а также в район Верхних Печёр.</p>
										<p>При заказе товаров, имеющихся на складе, отправка товаров осуществляется после верификации полученного платежа в течение шести рабочих дней с даты верификации платежа.</p>
										<p>Сроки доставки зависят от выбранного типа доставки и сообщаются покупателю при оформлении заказа.</p>
										<p>При выборе способа оплаты наличными курьеру доставка осуществляется после согласования времени доставки между покупателем и администратором ИСЦ Ли Вест. Первый контакт с покупателем администратор ИСЦ Ли Вест осуществляет посредством телефонного звонка либо письма по электронной почте в период не позднее, чем через один рабочий день после оформления заказа.</p>
										<p>При предзаказе товаров отправка осуществляется в срок до двух рабочих дней после поступления товара на склад при условии наличия верифицированного платежа от покупателя. Срок поступления выбранного товара на склад сообщается покупателю до оформления заказа.</p>
										<p>После оформления заказа до периода отправки покупатель имеет право отказаться от заказа без штрафных санкций.</p>
										<p>После отправки заказа покупатель имеет право на возврат и обмен товаров надлежащего качества в соответствии со ст. 25 закона о Защите прав потребителей:</p>
										<ul>
										<li>Если непродовольственный товар надлежащего качества, но не подходит покупателю по форме, габаритам, фасону и т. п., покупатель может вернуть его продавцу в течение четырнадцати дней, не считая дня покупки, при условии, что товар не был в употреблении, сохранены товарный вид, потребительские свойства, пломбы, фабричные ярлыки, а также товарный или кассовый чек.</li>
										<li>Перечень непродовольственных товаров надлежащего качества, не подлежащих возврату или обмену на аналогичный товар другого размера, формы, габарита, фасона, расцветки или комплектации.</li>
										<ol>
										<li>Товары для профилактики и лечения заболеваний в домашних условиях (предметы санитарии и гигиены из металла, резины, текстиля и других материалов, инструменты, приборы и аппаратура медицинские, средства гигиены полости рта, линзы очковые, предметы по уходу за детьми), лекарственные препараты.</li>
										<li>Текстильные товары (хлопчатобумажные, льняные, шелковые, шерстяные и синтетические ткани, товары из нетканых материалов типа тканей — ленты, тесьма, кружево и другие);</li>
										<li>Швейные и трикотажные изделия (изделия швейные и трикотажные бельевые, изделия чулочно-носочные).</li>
										<li>Непериодические издания (книги, брошюры, альбомы, картографические и нотные издания, листовые изоиздания, календари, буклеты, издания, воспроизведенные на технических носителях информации).</li>
										</ol>
										</ul>
										<p>При отказе от сделанного заказа после его отправки по причинам, не связанным с качеством товаров, доставка оплачивается покупателем.</p>
										<p>Ответственность за сохранность заказа во время пересылки несет почтовая служба. Пожалуйста, вскрывайте заказ сразу после его получения. В случае повреждения товара предъявляйте претензии почтовой службе.</p>
										<p>Если вы получили не полностью укомплектованный заказ, бракованный товар или товар, который не заказывали, обратитесь в магазин по адресу liwest@liwest-nn.ru, указав номер заказа.</p>
										<p>Оплатить заказ можно одним из перечисленных ниже способов.</p>
										<ul>
										<li>Наличными: оплата производится курьеру в момент получения товаров или при менеджену ИСЦ Ли Вест при выборе варианта доставки: "Самовывоз".</li>
										</ul>
										<hr />
										<p>© 2013 Информационно-сервисный центр Ли Вест Нижний Новгород.</p>
									</div>
								  <div class="modal-footer">
								    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
								  </div>
								</div>

							

							<div id="full-tos">
								<h2><?php echo JText::_ ('COM_VIRTUEMART_CART_TOS'); ?></h2>
								<?php echo $this->cart->vendor->vendor_terms_of_service; ?>
							</div>

						</div>
						<?php
					} // VmConfig::get('oncheckout_show_legal_info',1)
					//echo '<span class="tos">'. JText::_('COM_VIRTUEMART_CART_TOS_READ_AND_ACCEPTED').'</span>';
			}
			echo $this->checkout_link_html;
			?>
		</div>
		<?php // Continue and Checkout Button END ?>
		<input type='hidden' name='order_language' value='<?php echo $this->order_language; ?>'/>
		<input type='hidden' id='STsameAsBT' name='STsameAsBT' value='<?php echo $this->cart->STsameAsBT; ?>'/>
		<input type='hidden' name='task' value='<?php echo $this->checkout_task; ?>'/>
		<input type='hidden' name='option' value='com_virtuemart'/>
		<input type='hidden' name='view' value='cart'/>
	</form>
</div>
