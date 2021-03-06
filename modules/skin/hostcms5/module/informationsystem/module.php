<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Information systems.
 *
 * @package HostCMS 6\Skin
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2013 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Skin_Hostcms5_Module_Informationsystem_Module extends Informationsystem_Module{
	/**
	 * Show admin widget
	 * @param int $type
	 * @param boolean $ajax
	 * @return self
	 */	public function adminPage($type = 0, $ajax = FALSE)
	{
		$oUser = Core_Entity::factory('User')->getCurrent();
		
		$oComments = Core_Entity::factory('Comment');
		
		$oComments->queryBuilder()
			->leftJoin('comment_informationsystem_items', 'comments.id', '=', 'comment_informationsystem_items.comment_id')
			->leftJoin('informationsystem_items', 'comment_informationsystem_items.informationsystem_item_id', '=', 'informationsystem_items.id')
			->leftJoin('informationsystems', 'informationsystem_items.informationsystem_id', '=', 'informationsystems.id')
			->where('informationsystem_items.deleted', '=', 0)
			->where('informationsystems.deleted', '=', 0)
			->where('site_id', '=', CURRENT_SITE)
			->orderBy('comments.datetime', 'DESC')
			->limit(5);

		// Права доступа пользователя к комментариям
		if ($oUser->superuser == 0 && $oUser->only_access_my_own == 1)
		{
			$oComments->queryBuilder()->where('comments.user_id', '=', $oUser->id);
		}

		$aComments = $oComments->findAll();

		if (count($aComments) > 0)
		{
			?><td valign="top" class="index_table_td">

			<div class="main_div"><span class="div_title"><?php echo Core::_('Informationsystem.widget_title')?></span>
			<div class="div_content">
			<table cellpadding="2" cellspacing="2" border="0" width="100%" class="admin_table">
				<tr class=admin_table_title>
					<td><b><?php echo Core::_('Informationsystem.date')?></b></td>
					<td><b><?php echo Core::_('Informationsystem.subject')?></b></td>
				</tr>
				<?php
				foreach($aComments as $oComment)
				{
					?><tr>
					<td nowrap width="120"><?php echo Core_Date::sql2date($oComment->datetime)?></td>
					<td><?php echo ($oComment->subject != '' ? htmlspecialchars(Core_Str::cut($oComment->subject, 30)) : Core::_('Informationsystem.subject_not_found'))?></td>
				</tr>
				<?php
				}
				?>
			</table>

			<span style="margin: 5px 0px 5px 0px"><a href="/admin/informationsystem/item/comment/index.php" onclick="$.adminLoad({path: '/admin/informationsystem/item/comment/index.php'}); return false"><?php echo Core::_('Informationsystem.widget_other_comments')?></a></span>
			</div>
			</div>

			</td><?php
			return TRUE;
		}
	}}