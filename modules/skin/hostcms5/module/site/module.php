<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Sites.
 *
 * @package HostCMS 6\Skin
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2013 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Skin_Hostcms5_Module_Site_Module extends Site_Module{
	/**
	 * Show admin widget
	 * @param int $type
	 * @param boolean $ajax
	 * @return self
	 */	public function adminPage($type = 0, $ajax = FALSE)
	{
		$oSite = Core_Entity::factory('Site', CURRENT_SITE);
		
		?><td valign="top" class="index_table_td"><?php
		?><div class="main_div"><span class="div_title"><?php echo Core::_('site.notes')?></span>
		<div class="div_content">
		<form enctype="multipart/form-data" action="/admin/index.php" method="post">
		<table border="0" cellpadding="3" cellspacing="0" width="100%">
			<tr>
				<td width="100%"><textarea name="notes" style="width: 100%" rows="5"><?php echo htmlspecialchars($oSite->notes)?></textarea></td>
			</tr>
			<tr>
			<td><input type="submit" name="save_notes" value="<?php echo Core::_('site.save_notes'); ?>" onclick="$.adminSendForm({buttonObject: this, additionalParams: 'save_notes=111', windowId: 'id_content'}); return false" /></td>
			</tr>
		</table>
		</form>
		</div>
		</div>

		</td>
		<?php
		
		return TRUE;
	}}