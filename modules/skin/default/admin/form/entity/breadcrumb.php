<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Admin forms.
 *
 * @package HostCMS 6\Admin
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2013 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Skin_Default_Admin_Form_Entity_Breadcrumb extends Admin_Form_Entity
{
	/**
	 * Allowed object properties
	 * @var array
	 */
	protected $_allowedProperties = array(
		'name',
		'href',
		'onclick'
	);

	/**
	 * Executes the business logic.
	 */
	public function execute()
	{
		$href = $this->href;
		$onclick = $this->onclick;
		
		?><a href="<?php echo $href?>" onclick="<?php echo $onclick ?>"><?php echo htmlspecialchars($this->name)?></a><?php
	}
}