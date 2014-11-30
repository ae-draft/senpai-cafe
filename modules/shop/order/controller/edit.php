<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Online shop.
 *
 * @package HostCMS 6\Shop
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2014 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Shop_Order_Controller_Edit extends Admin_Form_Action_Controller_Type_Edit
{
	/**
	 * Set object
	 * @param object $object object
	 * @return self
	 */
	public function setObject($object)
	{
		$this
			->addSkipColumn('unloaded');

		if (is_null($object->id))
		{
			$object->shop_id = Core_Array::getGet('shop_id');
		}

		parent::setObject($object);

		$oMainTab = $this->getTab('main');
		$oAdditionalTab = $this->getTab('additional');
		$oSeparator = Admin_Form_Entity::factory('Separator');

		$this
			->addTabAfter
				(
					$oDescriptionTab = Admin_Form_Entity::factory('Tab')
						->caption(Core::_('Shop_Order.tab3'))
						->name('Description'), $oMainTab
				)
			->addTabAfter
				(
					$oContactsTab = Admin_Form_Entity::factory('Tab')
						->caption(Core::_('Shop_Order.tab2'))
						->name('Contacts'), $oMainTab
				)
			->addTabAfter
				(
					$oDocumentsTab = Admin_Form_Entity::factory('Tab')
						->caption(Core::_('Shop_Order.tab4'))
						->name('Documents'), $oContactsTab
				);

		$Shop_Delivery_Condition_Controller_Edit = new Shop_Delivery_Condition_Controller_Edit($this->_Admin_Form_Action);

		$Shop_Delivery_Condition_Controller_Edit->controller($this->_Admin_Form_Controller);

		// Удаляем страны
		$oAdditionalTab->delete(
			$this->getField('shop_country_id')
		);

		// Удаляем местоположения
		$oAdditionalTab->delete(
			$this->getField('shop_country_location_id')
		);

		// Удаляем города
		$oAdditionalTab->delete(
			$this->getField('shop_country_location_city_id')
		);

		// Удаляем районы
		$oAdditionalTab->delete(
			$this->getField('shop_country_location_city_area_id')
		);

		$lastField = $Shop_Delivery_Condition_Controller_Edit
			->generateCountryFields($this,
					$oContactsTab,
					NULL
				);

		$oMainTab
			->moveAfter($oPostcodeField = $this->getField('postcode'), $lastField, $oContactsTab)
			->moveAfter($oAddressField = $this->getField('address'), $oPostcodeField, $oContactsTab)
			->moveAfter($oSurnameField = $this->getField('surname'), $oAddressField, $oContactsTab)
			->moveAfter($oNameField = $this->getField('name'), $oSurnameField, $oContactsTab)
			->moveAfter($oPatronymicField = $this->getField('patronymic'), $oNameField, $oContactsTab)
			->moveAfter($oCompanyField = $this->getField('company'), $oPatronymicField, $oContactsTab)
			->moveAfter($oPhoneField = $this->getField('phone'), $oCompanyField, $oContactsTab)
			->moveAfter($oFaxField = $this->getField('fax'), $oPhoneField, $oContactsTab)
			->moveAfter($oEmailField = $this->getField('email'), $oFaxField, $oContactsTab);

		$oMainTab->move($this->getField('guid'), $oAdditionalTab);

		$oMainTab
			->moveBefore($oInvoiceField = $this->getField('invoice'), $oIpField = $this->getField('ip'), $oMainTab)
			->moveAfter($oDatetimeField = $this->getField('datetime'), $oInvoiceField, $oMainTab);

		$oAdditionalTab
			->moveAfter($oSiteuseridField = $this->getField('siteuser_id'), $oDatetimeField, $oMainTab);

		$oOrderSumTextBox = Admin_Form_Entity::factory('Input')
			->name("sum")
			->style("width: 150px");

		$oMainTab
			->addAfter($oOrderSumTextBox, $oSiteuseridField)
			->moveAfter($oPaidField = $this->getField('paid'), $oOrderSumTextBox, $oMainTab)
			->moveAfter($oCanceledField = $this->getField('canceled'), $oPaidField, $oMainTab)
			->moveAfter($oPaymentdatetimeField = $this->getField('payment_datetime'), $oCanceledField, $oMainTab)
			->moveAfter($oStatusdatetimeField = $this->getField('status_datetime'), $oPaymentdatetimeField, $oMainTab);

		$this->getField('status_datetime')->id('status_datetime');

		if ($this->_object->siteuser_id && Core::moduleIsActive('siteuser'))
		{
			$oSiteuser = $this->_object->Siteuser;

			$oSiteuserLink = Admin_Form_Entity::factory('Link');
			$oSiteuserLink
				->divAttr(array('style' => 'float: left'))
				->a
					->href($this->_Admin_Form_Controller->getAdminActionLoadHref('/admin/siteuser/siteuser/index.php', 'edit', NULL, 0, $oSiteuser->id))
					->onclick("$.openWindowAddTaskbar({path: '/admin/siteuser/siteuser/index.php', additionalParams: 'hostcms[checked][0][{$oSiteuser->id}]=1&hostcms[action]=edit', shortcutImg: '" . '/modules/skin/' . Core_Skin::instance()->getSkinName() . '/images/module/siteuser.png' . "', shortcutTitle: 'undefined', Minimize: true}); return false")
					->value($oSiteuser->login)
					->target('_blank');
			$oSiteuserLink
				->img
					->src('/admin/images/new_window.gif');

			$oMainTab->addAfter($oSiteuserLink, $oSiteuseridField);

			$oMainTab->addAfter($oSeparator, $oSiteuserLink);
		}
		else
		{
			$oMainTab->addAfter($oSeparator, $oSiteuseridField);
		}

		$oMainTab
			->move($oDescriptionField = $this->getField('description'), $oDescriptionTab)
			->moveAfter($oSysteminformationField = $this->getField('system_information'), $oDescriptionField, $oDescriptionTab)
			->moveAfter($oDeliveryinformationField = $this->getField('delivery_information'), $oSysteminformationField, $oDescriptionTab);

		$oInvoiceField
			->class("large")
			->divAttr(array('style' => 'float: left'))
			->style("width: 150px;");

		$oDatetimeField
			->class("calendar_field_large")
			->divAttr(array('style' => 'float: left'));

		$oSiteuseridField
			->class("large")
			->divAttr(array('style' => 'float: left'))
			->style("width: 150px;");

		$oOrderSumTextBox
			->divAttr(array('style' => 'float: left'))
			->class("large")
			->style("width: 150px; ")
			->value($this->_object->getAmount())
			->readonly('readonly')
			->caption(Core::_("Shop_Order.cond_of_delivery_add_form_price_order"));

		$oAdditionalTab->delete(
			$this->getField('shop_currency_id')
		);

		$oCurrencySelect = Admin_Form_Entity::factory('Select');
		$Shop_Controller_Edit = new Shop_Controller_Edit($this->_Admin_Form_Action);

		$oCurrencySelect
			->caption(Core::_('Shop_Order.order_currency'))
			->style("width: 150px")
			->class("large")
			->divAttr(array('style' => 'float: left'))
			->options(
					$Shop_Controller_Edit->fillCurrencies()
				)
			->name('shop_currency_id')
			->value($this->_object->shop_currency_id);

		$oMainTab->addAfter($oCurrencySelect, $oOrderSumTextBox);

		$shop_group_id = Core_Array::getGet('shop_group_id', 0);
		$shop_dir_id = Core_Array::getGet('shop_dir_id', 0);
		$shop_order_id = intval($this->_object->id);
		$shop_id = Core_Array::getGet('shop_id', 0);

		$sShopOrderItemsPath = '/admin/shop/order/item/index.php';
		$sAdditionalParams = "shop_id={$shop_id}&shop_group_id={$shop_group_id}&shop_dir_id={$shop_dir_id}&shop_order_id={$shop_order_id}";

		$oItemsLink = Admin_Form_Entity::factory('Link');
		$oItemsLink
			->divAttr(array('style' => 'float: left'))
			->a
				->href($this->_Admin_Form_Controller->getAdminLoadHref(
						$sShopOrderItemsPath, NULL, NULL, $sAdditionalParams
					)
				)
				->onclick($this->_Admin_Form_Controller->getAdminLoadAjax(
						$sShopOrderItemsPath, NULL, NULL, $sAdditionalParams
					))
				->value(Core::_('Shop_Order.order_items_link'));
		$oItemsLink
			->img
				->src('/admin/images/page.gif');

		$oMainTab->addAfter($oItemsLink, $oCurrencySelect);

		$oMainTab->addAfter($oSeparator, $oItemsLink);

		$oPaidField
			->divAttr(array('style' => 'float: left'));

		$oAdditionalTab->delete(
			$this->getField('shop_payment_system_id')
		);

		$oShopPaymentSystemsSelect = Admin_Form_Entity::factory('Select');

		$oShopPaymentSystemsSelect
			->caption(Core::_('Shop_Order.system_of_pay'))
			->style("width: 300px")
			->divAttr(array('style' => 'float: left'))
			->options(
				$this->_fillPaymentSystems(Core_Array::getGet('shop_id', 0))
			)
			->name('shop_payment_system_id')
			->value($this->_object->shop_payment_system_id);

		$oMainTab->addAfter(
				$oShopPaymentSystemsSelect, $oPaymentdatetimeField
			);

		$oPaymentdatetimeField
			->divAttr(array('style' => 'float: left'));

		$oPrintLink = Admin_Form_Entity::factory('Link');
		$oPrintLink->div->style("width: 100px");
		$oPrintLink
			->a
				->href($this->_Admin_Form_Controller->getAdminLoadHref(
						"/admin/shop/order/print/index.php",
						NULL, NULL, "shop_order_id=" . intval($this->_object->id
					)))
				->value(Core::_('Shop_Order.print'));
		$oPrintLink
			->img
				->src('/admin/images/printer.gif');
		$oDocumentsTab->add($oPrintLink);

		$oOrderCardLink = Admin_Form_Entity::factory('Link');
		$oOrderCardLink
			->a
				->href(
					$this->_Admin_Form_Controller->getAdminLoadHref(
						"/admin/shop/order/card/index.php",
						NULL, NULL, "shop_order_id=" . intval($this->_object->id
					)))
				->value(Core::_('Shop_Order.order_card'))
				->target('_blank');
		$oOrderCardLink
			->img
				->src('/admin/images/new_window.gif');
		$oOrderCardLink->divAttr(array('style' => 'width: 200px'));
		$oDocumentsTab->add($oOrderCardLink);

		$oActLink = Admin_Form_Entity::factory('Link');
		$oActLink->a->href($this->_Admin_Form_Controller->getAdminLoadHref("/admin/shop/order/acceptance/report/index.php", NULL, NULL, "shop_order_id=" . intval($this->_object->id)))->value(Core::_('Shop_Order.acceptance_report_form'))->target('_blank');
		$oActLink->img->src('/admin/images/new_window.gif');
		$oActLink->divAttr(array('style' => 'width: 80px; float: left'));
		$oDocumentsTab->add($oActLink);

		$oNaklLink = Admin_Form_Entity::factory('Link');
		$oNaklLink->a->href($this->_Admin_Form_Controller->getAdminLoadHref("/admin/shop/order/torg12/index.php", NULL, NULL, "shop_order_id=" . intval($this->_object->id)))->value(Core::_("Shop_Order.torg12_title"))->target('_blank');
		$oNaklLink->img->src('/admin/images/new_window.gif');
		$oNaklLink->divAttr(array('style' => 'width: 100px; float: left'));
		$oDocumentsTab->add($oNaklLink);

		$oMainTab->delete($this->getField('acceptance_report'));
		$oAdmin_Form_Entity_Input = Admin_Form_Entity::factory('Input');
		$oAdmin_Form_Entity_Input
			->name('acceptance_report')
			->caption('&nbsp;')
			->value($this->_object->acceptance_report)
			->style("width: 100px; margin-left: 0");
		$oDocumentsTab->add($oAdmin_Form_Entity_Input);

		$oFactLink = Admin_Form_Entity::factory('Link');
		$oFactLink->a->href($this->_Admin_Form_Controller->getAdminLoadHref("/admin/shop/order/vat/invoice/index.php", NULL, NULL, "shop_order_id=" . intval($this->_object->id)))->value(Core::_('Shop_Order.acceptance_report_invoice'))->target('_blank');
		$oFactLink->img->src('/admin/images/new_window.gif');
		$oFactLink->divAttr(array('style' => 'width: 180px; float: left'));
		$oDocumentsTab->add($oFactLink);

		$oMainTab->delete($this->getField('vat_invoice'));
		$oAdmin_Form_Entity_Input = Admin_Form_Entity::factory('Input');
		$oAdmin_Form_Entity_Input
			->name('vat_invoice')
			->caption('&nbsp;')
			->value($this->_object->vat_invoice)
			->style("width: 100px; margin-left: 0");
		$oDocumentsTab->add($oAdmin_Form_Entity_Input);

		$oAdditionalTab->delete(
			$this->getField('shop_order_status_id')
		);

		$oShopOrderStatusesSelect = Admin_Form_Entity::factory('Select');

		$objectId = intval($this->_object->id);
		$windowId = $this->_Admin_Form_Controller->getWindowId();

		$oShopOrderStatusesSelect
			->caption(Core::_('Shop_Order.show_order_status'))
			->style("width: 100px")
			->options(
				$Shop_Controller_Edit->fillOrderStatuses(Core_Array::getGet('shop_id', 0))
			)
			->divAttr(array('style' => 'float: left'))
			->name('shop_order_status_id')
			->value($this->_object->shop_order_status_id)
			->onchange("$.changeOrderStatus('{$windowId}')");

		$oMainTab->addAfter(
				$oShopOrderStatusesSelect, $oShopPaymentSystemsSelect
			);

		$oMainTab->addAfter($oSeparator, $oShopOrderStatusesSelect);

		$oStatusdatetimeField
			->divAttr(array('style' => 'float: left'));

		$oIpField
			->style("width: 100px");

		$Shop_Delivery_Controller_Edit = new Shop_Delivery_Controller_Edit($this->_Admin_Form_Action);

		$oShopDelivery = Core_Entity::factory('Shop_Delivery_Condition', $this->_object->shop_delivery_condition_id)->Shop_Delivery;

		$oAdditionalTab->delete(
			$this->getField('shop_delivery_id')
		);
		$oShopDeliveryTypeSelect = Admin_Form_Entity::factory('Select');

		$oShopDeliveryTypeSelect
			->caption(Core::_('Shop_Order.type_of_delivery'))
			->style("width: 300px")
			->options(
				$Shop_Delivery_Controller_Edit->fillDeliveries(Core_Array::getGet('shop_id', 0))
			)
			->divAttr(array('style' => 'float: left'))
			->name('shop_delivery_id')
			->value($this->_object->shop_delivery_id)
			->onchange("$.ajaxRequest({path: '/admin/shop/order/index.php',context: 'shop_delivery_condition_id', callBack: $.loadSelectOptionsCallback, objectId: {$objectId}, action: 'loadDeliveryConditionsList',additionalParams: 'delivery_id=' + this.value,windowId: '{$windowId}'}); return false");

		$oMainTab->addAfter(
				$oShopDeliveryTypeSelect, $oIpField
			);

		$oAdditionalTab->delete(
			$this->getField('shop_delivery_condition_id')
		);

		$oShopDeliveryConditionsTypeSelect = Admin_Form_Entity::factory('Select');

		$iShop_Delivery_Conditions = $oShopDelivery->Shop_Delivery_Conditions->getCount();

		$oShopDeliveryConditionsTypeSelect
			->caption(Core::_('Shop_Order.shop_delivery_condition_id'))
			->id('shop_delivery_condition_id')
			->style("width: 300px")
			->options(
				$iShop_Delivery_Conditions <= 250
					? $this->_fillDeliveryConditions($oShopDelivery->id)
					: array($this->_object->shop_delivery_condition_id => $this->_object->Shop_Delivery_Condition->name)
			)
			->name('shop_delivery_condition_id')
			->value($this->_object->shop_delivery_condition_id);

		$oMainTab->addAfter(
				$oShopDeliveryConditionsTypeSelect, $oShopDeliveryTypeSelect
			);

		$oPostcodeField
			->style("width: 100px")
			->divAttr(array('style' => 'float: left'));

		$oAddressField
			->style("width: 490px");

		$oSurnameField
			->style("width: 190px")
			->divAttr(array('style' => 'float: left'));

		$oNameField
			->class('')
			->style("width: 190px")
			->divAttr(array('style' => 'float: left'));

		$oPatronymicField
			->style("width: 190px")
			->divAttr(array('style' => 'float: left'));

		$oCompanyField
			->style("width: 190px");

		$oPhoneField
			->style("width: 190px")
			->divAttr(array('style' => 'float: left'));

		$oFaxField
			->style("width: 190px")
			->divAttr(array('style' => 'float: left'));

		$oEmailField
			->style("width: 190px");

		$iOrderId = intval($this->_object->id);
		$sOrderPath = '/admin/shop/order/index.php';

		$oRecalcDeliveryPriceLink = Admin_Form_Entity::factory('Link');
		$oRecalcDeliveryPriceLink
			->a
				->href($this->_Admin_Form_Controller->getAdminActionLoadHref
					(
						$sOrderPath, 'recalcDelivery', NULL, 0, $iOrderId
					)
				)
				->onclick($this->_Admin_Form_Controller->getAdminActionLoadAjax
					(
						$sOrderPath, 'recalcDelivery', NULL, 0, $iOrderId
					)
				)
				->value(Core::_('Shop_Order.recalc_order_delivery_sum'));
		$oRecalcDeliveryPriceLink
			->img
				->src('/admin/images/coins.gif');

		$oRecalcDeliveryPriceLink->div->style("width: 350px");

		$oMainTab->addAfter($oRecalcDeliveryPriceLink, $oShopDeliveryConditionsTypeSelect);

		$oTinField = $this->getField('tin');
		$oTinField
			->style("width: 190px")
			->divAttr(array('style' => 'float: left'));
		$oKppField = $this->getField('kpp');
		$oKppField->style("width: 190px");

		$oMainTab->moveAfter($oTinField, $oEmailField, $oContactsTab);
		$oMainTab->moveAfter($oKppField, $this->getField('tin'), $oContactsTab);

		$oPropertyTab = Admin_Form_Entity::factory('Tab')
			->caption(Core::_("Shop_Order.tab_properties"))
			->name('Property');

		$this->addTabBefore($oPropertyTab, $oAdditionalTab);

		// ---- Дополнительные свойства
		$oProperty_Controller_Tab = new Property_Controller_Tab($this->_Admin_Form_Controller);
		$oProperty_Controller_Tab
			->setObject($this->_object)
			->setDatasetId($this->getDatasetId())
			->linkedObject(Core_Entity::factory('Shop_Order_Property_List', $shop_id))
			->setTab($oPropertyTab)
			->template_id($this->_object->Shop->Structure->template_id
					? $this->_object->Shop->Structure->template_id
					: 0)
			->fillTab();

		$title = $this->_object->id
			? Core::_('Shop_Order.order_edit_form_title')
			: Core::_('Shop_Order.order_add_form_title');

		$this->title($title);

		return $this;
	}

	/**
	 * Processing of the form. Apply object fields.
	 * @return self
	 * @hostcms-event Shop_Order_Controller_Edit.onAfterRedeclaredApplyObjectProperty
	 */
	protected function _applyObjectProperty()
	{
		// Может измениться в parent::_applyObjectProperty()
		$bShop_payment_system_id = $this->_object->shop_payment_system_id;

		if ($bShop_payment_system_id)
		{
			$oShop_Payment_System_Handler = Shop_Payment_System_Handler::factory(
				Core_Entity::factory('Shop_Payment_System', $this->_object->shop_payment_system_id)
			);

			if ($oShop_Payment_System_Handler)
			{
				$oShop_Payment_System_Handler->shopOrder($this->_object)
					->shopOrderBeforeAction(clone $this->_object);
			}
			// HostCMS v. 5
			elseif (defined('USE_HOSTCMS_5') && USE_HOSTCMS_5)
			{
				$shop = new shop();
				$order_row = $shop->GetOrder($this->_object->id);
			}
		}

		if ($this->_object->id)
		{
			$this->_object->paid != Core_Array::get($this->_formValues, 'paid') && $this->_object->paid == 0
				? $this->_object->paid()
				: $this->_object->cancelPaid();
		}

		parent::_applyObjectProperty();

		// ---- Дополнительные свойства
		$oProperty_Controller_Tab = new Property_Controller_Tab($this->_Admin_Form_Controller);
		$oProperty_Controller_Tab
			->setObject($this->_object)
			->linkedObject(Core_Entity::factory('Shop_Order_Property_List', $this->_object->Shop->id))
			->applyObjectProperty()
			;
		// ----

		if ($this->_object->invoice == '')
		{
			$this->_object->invoice = $this->_object->id;
			$this->_object->save();
		}

		if ($bShop_payment_system_id)
		{
			if ($oShop_Payment_System_Handler)
			{
				$oShop_Payment_System_Handler->changedOrder('edit');
			}
			// HostCMS v. 5
			elseif (defined('USE_HOSTCMS_5') && USE_HOSTCMS_5)
			{
				// Вызываем обработчик платежной системы для события сменя статуса HostCMS v. 5
				$shop->ExecSystemsOfPayChangeStatus($order_row['shop_system_of_pay_id'], array(
					'shop_order_id' => $this->_object->id,
					'action' => 'edit',
					// Предыдущие даные о заказе до редактирования
					'prev_order_row' => $order_row
				));
			}
		}

		Core_Event::notify(get_class($this) . '.onAfterRedeclaredApplyObjectProperty', $this, array($this->_Admin_Form_Controller));

		return $this;
	}

	/**
	 * Fill delivery conditions list
	 * @param int $iDeliveryId delivery ID
	 * @return array
	 */
	protected function _fillDeliveryConditions($iDeliveryId)
	{
		$oObject = Core_Entity::factory('Shop_Delivery_Condition');

		$iDeliveryId = intval($iDeliveryId);

		$oObject->queryBuilder()
			->where("shop_delivery_id", "=", $iDeliveryId)
			->orderBy("id");

		$aObjects = $oObject->findAll();

		$aReturn = array(" … ");

		foreach ($aObjects as $oObject)
		{
			$aReturn[$oObject->id] = $oObject->name;
		}

		return $aReturn;
	}

	/**
	 * Fill payment systems list
	 * @param int $iShopId shop ID
	 * @return array
	 */
	protected function _fillPaymentSystems($iShopId)
	{
		$iShopId = intval($iShopId);

		$oObject = Core_Entity::factory('Shop_Payment_System');
		$oObject->queryBuilder()
				->where("shop_id", "=", $iShopId)
				->orderBy("sorting");

		$aReturn = array(" … ");
		$aObjects = $oObject->findAll();
		foreach ($aObjects as $oObject)
		{
			//$aReturn[$oObject->id] = $oObject->name;
			$aReturn[$oObject->id] = array('value' => $oObject->name);
			!$oObject->active && $aReturn[$oObject->id]['attr'] = array('style' => 'text-decoration: line-through');
		}

		return $aReturn;
	}
}