<?php defined ('_JEXEC') or die('Restricted access');
/**
 *
 * Layout for the shopping cart
 *
 * @package    VirtueMart
 * @subpackage Cart
 * @author Max Milbers
 * @author Patrick Kohl
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 *
 */

?>
<div>
	<div class="floatleft">

		<span><span class="vmicon vm2-billto-icon"></span>
			<?php echo JText::_ ('Укажите информацию для связи с вами'); ?></span>
		<?php // Output Bill To Address ?>
		<div class="output-billto">
			<?php

			foreach ($this->cart->BTaddress['fields'] as $item) {
				if (!empty($item['value'])) {
					if ($item['name'] === 'agreed') {
						$item['value'] = ($item['value'] === 0) ? JText::_ ('COM_VIRTUEMART_USER_FORM_BILLTO_TOS_NO') : JText::_ ('COM_VIRTUEMART_USER_FORM_BILLTO_TOS_YES');
					}
					?><!-- span class="titles"><?php echo $item['title'] ?></span -->
					<span class="values vm2<?php echo '-' . $item['name'] ?>"><?php echo $this->escape ($item['value']) ?></span>
					<?php if ($item['name'] != 'title' and $item['name'] != 'first_name' and $item['name'] != 'middle_name' and $item['name'] != 'zip') { ?>
						<br class="clear"/>
						<?php
					}
				}
			} ?>
			<div class="clear"></div>
		</div>

		<a class="details" href="<?php echo JRoute::_ ('index.php?option=com_virtuemart&view=user&task=editaddresscart&addrtype=BT', $this->useXHTML, $this->useSSL) ?>">
			<?php echo JText::_ ('COM_VIRTUEMART_USER_FORM_EDIT_BILLTO_LBL'); ?>
		</a>

		<input type="hidden" name="billto" value="<?php echo $this->cart->lists['billTo']; ?>"/>
	</div>	
</div>

<!-- Таблица с товарами -->
<div class="row-fluid">
<div class="span12">
<fieldset>
	<table class="table table-bordered">
		<thead>
			<tr>
				<th align="center">Название</th>
				<th align="center">Артикул</th>
				<th align="center">Цена</th>
				<th align="center">Количество</th>
				<th align="center"><?php echo "<span  class='priceColor2'>" . JText::_ ('COM_VIRTUEMART_CART_SUBTOTAL_DISCOUNT_AMOUNT') . '</span>' ?></th>
				<th align="center">Сумма</th>
			</tr>
		</thead>

	<?php
	$i = 1;
	// 		vmdebug('$this->cart->products',$this->cart->products);
	foreach ($this->cart->products as $pkey => $prow) {
		?>
	<tr valign="top" class="sectiontableentry<?php echo $i ?>">
		<td align="center">
			<?php if ($prow->virtuemart_media_id) { ?>
			<span class="cart-images">
				<?php if (!empty($prow->image)) { echo $prow->image->displayMediaThumb ('', FALSE); } ?>		
			</span>
			<?php } ?>
			<?php echo JHTML::link ($prow->url, $prow->product_name) . $prow->customfields; ?>
		</td>
		<td align="center"><?php  echo $prow->product_sku ?></td>
		<td align="center">
			<?php
			if (VmConfig::get ('checkout_show_origprice', 1) && $this->cart->pricesUnformatted[$pkey]['discountedPriceWithoutTax'] != $this->cart->pricesUnformatted[$pkey]['priceWithoutTax']) {
				echo '<span class="line-through">' . $this->currencyDisplay->createPriceDiv ('basePriceVariant', '', $this->cart->pricesUnformatted[$pkey], TRUE, FALSE) . '</span><br />';
			}
			if ($this->cart->pricesUnformatted[$pkey]['discountedPriceWithoutTax']) {
				echo $this->currencyDisplay->createPriceDiv ('discountedPriceWithoutTax', '', $this->cart->pricesUnformatted[$pkey], FALSE, FALSE);
			} else {
				echo $this->currencyDisplay->createPriceDiv ('basePriceVariant', '', $this->cart->pricesUnformatted[$pkey], FALSE, FALSE);
			}
			// 					echo $prow->salesPrice ;
			?>
		</td>
		<td align="center"><?php
		//				$step=$prow->min_order_level;
					if ($prow->step_order_level)
						$step=$prow->step_order_level;
					else
						$step=1;
					if($step==0)
						$step=1;
					$alert=JText::sprintf ('COM_VIRTUEMART_WRONG_AMOUNT_ADDED', $step);
					?>
	                <script type="text/javascript">
					function check<?php echo $step?>(obj) {
	 				// use the modulus operator '%' to see if there is a remainder
					remainder=obj.value % <?php echo $step?>;
					quantity=obj.value;
	 				if (remainder  != 0) {
	 					alert('<?php echo $alert?>!');
	 					obj.value = quantity-remainder;
	 					return false;
	 				}
	 				return true;
	 				}
					</script>
			<form action="<?php echo JRoute::_ ('index.php'); ?>" method="post" class="inline">
				<input type="hidden" name="option" value="com_virtuemart"/>
					<!--<input type="text" title="<?php echo  JText::_('COM_VIRTUEMART_CART_UPDATE') ?>" class="inputbox" size="3" maxlength="4" name="quantity" value="<?php echo $prow->quantity ?>" /> -->
	            <input type="text"
					   onblur="check<?php echo $step?>(this);"
					   onclick="check<?php echo $step?>(this);"
					   onchange="check<?php echo $step?>(this);"
					   onsubmit="check<?php echo $step?>(this);"
					   title="<?php echo  JText::_('COM_VIRTUEMART_CART_UPDATE') ?>" class="quantity-input js-recalculate" size="3" maxlength="4" name="quantity" value="<?php echo $prow->quantity ?>" />
				<input type="hidden" name="view" value="cart"/>
				<input type="hidden" name="task" value="update"/>
				<input type="hidden" name="cart_virtuemart_product_id" value="<?php echo $prow->cart_item_id  ?>"/>
				<input type="submit" class="vmicon vm2-add_quantity_cart" name="update" title="<?php echo  JText::_ ('COM_VIRTUEMART_CART_UPDATE') ?>" align="middle" value=" "/>
			</form>
			<a class="vmicon vm2-remove_from_cart" title="<?php echo JText::_ ('COM_VIRTUEMART_CART_DELETE') ?>" align="middle" href="<?php echo JRoute::_ ('index.php?option=com_virtuemart&view=cart&task=delete&cart_virtuemart_product_id=' . $prow->cart_item_id) ?>"> </a>
		</td>	

		<!-- Скидка -->
		<td align="center"><?php echo "<span class='priceColor2'>" . $this->currencyDisplay->createPriceDiv ('discountAmount', '', $this->cart->pricesUnformatted[$pkey], FALSE, FALSE, $prow->quantity) . "</span>" ?></td>
		<td align="center">
			<?php
			if (VmConfig::get ('checkout_show_origprice', 1) && !empty($this->cart->pricesUnformatted[$pkey]['basePriceWithTax']) && $this->cart->pricesUnformatted[$pkey]['basePriceWithTax'] != $this->cart->pricesUnformatted[$pkey]['salesPrice']) {
				echo '<span class="line-through">' . $this->currencyDisplay->createPriceDiv ('basePriceWithTax', '', $this->cart->pricesUnformatted[$pkey], TRUE, FALSE, $prow->quantity) . '</span><br />';
			}
			elseif (VmConfig::get ('checkout_show_origprice', 1) && empty($this->cart->pricesUnformatted[$pkey]['basePriceWithTax']) && $this->cart->pricesUnformatted[$pkey]['basePriceVariant'] != $this->cart->pricesUnformatted[$pkey]['salesPrice']) {
				echo '<span class="line-through">' . $this->currencyDisplay->createPriceDiv ('basePriceVariant', '', $this->cart->pricesUnformatted[$pkey], TRUE, FALSE, $prow->quantity) . '</span><br />';
			}
			echo $this->currencyDisplay->createPriceDiv ('salesPrice', '', $this->cart->pricesUnformatted[$pkey], FALSE, FALSE, $prow->quantity) ?></td>
	</tr>
		<?php
		$i = ($i==1) ? 2 : 1;
	} ?>

	<tr class="sectiontableentry1">
		<td colspan="4" align="right"><?php echo JText::_ ('Обще количество товаров на сумму:'); ?></td>
		<td align="center"><?php echo "<span  class='priceColor2'>" . $this->currencyDisplay->createPriceDiv ('discountAmount', '', $this->cart->pricesUnformatted, FALSE) . "</span>" ?></td>
		<td align="center"><?php echo $this->currencyDisplay->createPriceDiv ('salesPrice', '', $this->cart->pricesUnformatted, FALSE) ?></td>
	</tr>

	<?php
	if (VmConfig::get ('coupons_enable')) {
		?>
	<tr class="sectiontableentry2">
		<td colspan="5" align="left">
			<?php if (!empty($this->layoutName) && $this->layoutName == 'default') {
			// echo JHTML::_('link', JRoute::_('index.php?view=cart&task=edit_coupon',$this->useXHTML,$this->useSSL), JText::_('COM_VIRTUEMART_CART_EDIT_COUPON'));
			echo $this->loadTemplate ('coupon');
			}
			?>

			<?php if (!empty($this->cart->cartData['couponCode'])) { ?>
			<?php
			echo $this->cart->cartData['couponCode'];
			echo $this->cart->cartData['couponDescr'] ? (' (' . $this->cart->cartData['couponDescr'] . ')') : '';
			?>
		</td>
		<td align="center">
			<?php echo $this->currencyDisplay->createPriceDiv ('salesPriceCoupon', '', $this->cart->pricesUnformatted['salesPriceCoupon'], FALSE); ?> 
		</td>
			<?php } else { ?>
			<!-- Если купоня не исползуются, выводим поле -->
			<td colspan="6" align="left">&nbsp;</td>
			<?php
		}

			?>
	</tr>
		<?php } ?>


	<?php
	foreach ($this->cart->cartData['DBTaxRulesBill'] as $rule) {
		?>
	<!-- Доставка -->
	<tr class="sectiontableentry<?php echo $i ?>">
		<td colspan="4" align="right"><?php echo $rule['calc_name'] ?> </td>

		<?php if (VmConfig::get ('show_tax')) { ?>
		<td align="right"></td>
		<?php } ?>
		<td align="right"><?php echo $this->currencyDisplay->createPriceDiv ($rule['virtuemart_calc_id'] . 'Diff', '', $this->cart->pricesUnformatted[$rule['virtuemart_calc_id'] . 'Diff'], FALSE); ?></td>
		<td align="right"><?php echo $this->currencyDisplay->createPriceDiv ($rule['virtuemart_calc_id'] . 'Diff', '', $this->cart->pricesUnformatted[$rule['virtuemart_calc_id'] . 'Diff'], FALSE); ?> </td>
	</tr>
		<?php
		if ($i) {
			$i = 1;
		} else {
			$i = 0;
		}
	} ?>

	<?php

	foreach ($this->cart->cartData['taxRulesBill'] as $rule) {
		?>
	<!-- Налог -->
	<tr class="sectiontableentry<?php echo $i ?>">
		<td colspan="4" align="right"><?php echo $rule['calc_name'] ?> </td>
		<?php if (VmConfig::get ('show_tax')) { ?>
		<td align="right"><?php echo $this->currencyDisplay->createPriceDiv ($rule['virtuemart_calc_id'] . 'Diff', '', $this->cart->pricesUnformatted[$rule['virtuemart_calc_id'] . 'Diff'], FALSE); ?> </td>
		<?php } ?>
		<td align="right"><?php ?> </td>
		<td align="right"><?php echo $this->currencyDisplay->createPriceDiv ($rule['virtuemart_calc_id'] . 'Diff', '', $this->cart->pricesUnformatted[$rule['virtuemart_calc_id'] . 'Diff'], FALSE); ?> </td>
	</tr>
		<?php
		if ($i) {
			$i = 1;
		} else {
			$i = 0;
		}
	}

	foreach ($this->cart->cartData['DATaxRulesBill'] as $rule) {
		?>
	<!-- Налог -->	
	<tr class="sectiontableentry<?php echo $i ?>">
		<td colspan="4" align="right"><?php echo   $rule['calc_name'] ?> </td>

		<?php if (VmConfig::get ('show_tax')) { ?>
		<td align="right"></td>

		<?php } ?>
		<td align="right"><?php echo $this->currencyDisplay->createPriceDiv ($rule['virtuemart_calc_id'] . 'Diff', '', $this->cart->pricesUnformatted[$rule['virtuemart_calc_id'] . 'Diff'], FALSE); ?>  </td>
		<td align="right"><?php echo $this->currencyDisplay->createPriceDiv ($rule['virtuemart_calc_id'] . 'Diff', '', $this->cart->pricesUnformatted[$rule['virtuemart_calc_id'] . 'Diff'], FALSE); ?> </td>
	</tr>
		<?php
		if ($i) {
			$i = 1;
		} else {
			$i = 0;
		}
	} ?>

	<!-- Доставка!!! -->
	<tr class="sectiontableentry1" valign="top">
		<?php if (!$this->cart->automaticSelectedShipment) { ?>

		<?php /*	<td colspan="2" align="right"><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_SHIPPING'); ?> </td> */ ?>
					<td colspan="4" align="left">
					<?php echo $this->cart->cartData['shipmentName']; ?>
		<br/>
		<?php
		if (!empty($this->layoutName) && $this->layoutName == 'default' && !$this->cart->automaticSelectedShipment) {
			if (VmConfig::get('oncheckout_opc', 0)) {
				$previouslayout = $this->setLayout('select');
				echo $this->loadTemplate('shipment');
				$this->setLayout($previouslayout);
			} else {
				echo JHTML::_('link', JRoute::_('index.php?view=cart&task=edit_shipment', $this->useXHTML, $this->useSSL), $this->select_shipment_text, 'class=""');
			}
		} else {
			echo JText::_ ('COM_VIRTUEMART_CART_SHIPPING');
		}
		} else {
		?>
		<td colspan="4" align="left">
			<?php echo $this->cart->cartData['shipmentName']; ?>
		</td>
		<?php } ?>

		<?php if (VmConfig::get ('show_tax')) { ?>
		<td align="right"><?php echo "<span  class='priceColor2'>" . $this->currencyDisplay->createPriceDiv ('shipmentTax', '', $this->cart->pricesUnformatted['shipmentTax'], FALSE) . "</span>"; ?> </td>
		<?php } ?>
		<td align="right"><?php if($this->cart->pricesUnformatted['salesPriceShipment'] < 0) echo $this->currencyDisplay->createPriceDiv ('salesPriceShipment', '', $this->cart->pricesUnformatted['salesPriceShipment'], FALSE); ?></td>
		<td align="right"><?php echo $this->currencyDisplay->createPriceDiv ('salesPriceShipment', '', $this->cart->pricesUnformatted['salesPriceShipment'], FALSE); ?> </td>
	</tr>
	<?php if ($this->cart->pricesUnformatted['salesPrice']>0.0 ) { ?>
	<!-- Оплата -->
	<tr class="sectiontableentry1" valign="top">
		<?php if (!$this->cart->automaticSelectedPayment) { ?>

		<td colspan="4" align="left">
			<?php echo $this->cart->cartData['paymentName']; ?>
			<br/>
			<?php if (!empty($this->layoutName) && $this->layoutName == 'default') {
				if (VmConfig::get('oncheckout_opc', 0)) {
					$previouslayout = $this->setLayout('select');
					echo $this->loadTemplate('payment');
					$this->setLayout($previouslayout);
				} else {
					echo JHTML::_('link', JRoute::_('index.php?view=cart&task=editpayment', $this->useXHTML, $this->useSSL), $this->select_payment_text, 'class=""');
				}
			} else {
			echo JText::_ ('COM_VIRTUEMART_CART_PAYMENT');
			} ?> 
		</td>

		<?php } else { ?>
		<td colspan="4" align="left">
			<?php echo $this->cart->cartData['paymentName']; ?> 
		</td>
		<?php } ?>

		<td align="center">
			<?php if($this->cart->pricesUnformatted['salesPricePayment'] < 0) echo $this->currencyDisplay->createPriceDiv ('salesPricePayment', '', $this->cart->pricesUnformatted['salesPricePayment'], FALSE); ?>
		</td>
		<td align="center">
			<?php  echo $this->currencyDisplay->createPriceDiv ('salesPricePayment', '', $this->cart->pricesUnformatted['salesPricePayment'], FALSE); ?> 
		</td>
	</tr>
	<?php } ?>
	<!-- Сумма итого -->
	<tr class="sectiontableentry2">
		<td colspan="4" align="right"><?php echo JText::_ ('COM_VIRTUEMART_CART_TOTAL') ?>:</td>

		<?php if (VmConfig::get ('show_tax')) { ?>
		<td align="right"> <?php echo "<span  class='priceColor2'>" . $this->currencyDisplay->createPriceDiv ('billTaxAmount', '', $this->cart->pricesUnformatted['billTaxAmount'], FALSE) . "</span>" ?> </td>
		<?php } ?>
		<td align="right"> <?php echo "<span  class='priceColor2'>" . $this->currencyDisplay->createPriceDiv ('billDiscountAmount', '', $this->cart->pricesUnformatted['billDiscountAmount'], FALSE) . "</span>" ?> </td>
		<td align="right"><strong><?php echo $this->currencyDisplay->createPriceDiv ('billTotal', '', $this->cart->pricesUnformatted['billTotal'], FALSE); ?></strong></td>
	</tr>
	

	</table>
</fieldset>
</div>
</div>