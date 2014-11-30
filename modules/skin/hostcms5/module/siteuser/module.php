<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Site users.
 *
 * @package HostCMS 6\Skin
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2012 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Skin_Hostcms5_Module_Siteuser_Module extends Siteuser_Module{	public function adminPage($type = 0, $ajax = FALSE)
	{
		$oSiteusers = Core_Entity::factory('Siteuser');
		$oSiteusers->queryBuilder()
			->where('site_id', '=', CURRENT_SITE)
			->orderBy('id', 'DESC')
			->limit(5);

		$aSiteusers = $oSiteusers->findAll();

		if (is_array($aSiteusers) && count($aSiteusers) > 0)
		{
			?>
			<td valign="top" class="index_table_td">

			<div class="main_div"><span class="div_title"><?php echo Core::_('Siteuser.index_last_site_users')?></span>
			<div class="div_content">

			<table cellspacing="2" cellpadding="2" width="100%" class="admin_table">
				<tr class="admin_table_title">
					<td><b><?php echo Core::_('Siteuser.index_site_users_name')?></b></td>
					<td><b><?php echo Core::_('Siteuser.index_site_users_mail')?></b></td>
				</tr>
				<?php
				foreach($aSiteusers as $oSiteuser)
				{
					?>
					<tr>
						<td><?php echo htmlspecialchars($oSiteuser->login)?></td>
						<td><a href="mailto:<?php echo htmlspecialchars($oSiteuser->email)?>"><?php echo htmlspecialchars($oSiteuser->email)?></a></td>
					</tr>
					<?php
				}
				?>
			</table>
			<span style="margin: 5px 0px 5px 0px"><a href="/admin/siteuser/index.php" onclick="$.adminLoad({path: '/admin/siteuser/index.php'}); return false"><?php echo Core::_('Siteuser.index_site_users_link')?></a></span>

			</div>
			</div>
			</td>
			<?php
			return TRUE;
		}
	}
}