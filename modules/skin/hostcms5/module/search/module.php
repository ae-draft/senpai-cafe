<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Search.
 *
 * @package HostCMS 6\Skin
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2012 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Skin_Hostcms5_Module_Search_Module extends Search_Module{	public function adminPage($type = 0, $ajax = FALSE)
	{
		$count = Search_Controller::getPageCount(CURRENT_SITE);

		?><td valign="top" class="index_table_td">
		<div class="main_div"><span class="div_title"><?php echo Core::_('Search.index_search')?></span>
		<div class="div_content">
		<p><?php echo Core::_('Search.index_indexed')?> <b><?php echo $count?>&nbsp;<?php echo Core::_('Search.index_ind_pages')?></b></p>

		<form id="SearchIndexation" enctype="multipart/form-data" action="/admin/search/index.php" method="post">
		<table border="0" cellpadding="3" cellspacing="0">
			<tr>
				<td valign="bottom"><?php echo Core::_('Search.index_blocks_size')?>
				<select name="step">
					<option value="10">10</option>
					<option value="20">20</option>
					<option value="30">30</option>
					<option value="40">40</option>
					<option value="50">50</option>
					<option value="60">60</option>
					<option value="70">70</option>
					<option value="80">80</option>
					<option value="90">90</option>
					<option value="100" selected="selected">100</option>
				</select></td>
				<td valign="bottom"><?php echo Core::_('Search.index_search_delay')?>
				<input type="text" size="3" name="timeout" value="0" />&nbsp;<?php echo Core::_('Search.index_search_delay_unit')?></td>
			</tr>
		</table>
		<br />
		<input type="submit" onclick="$.adminSendForm({buttonObject: this,action: 'process',operation: '',additionalParams: '',windowId: 'id_content'}); return false" name="process" value="<?php echo Core::_('Search.index_search_indexation')?>" />
		</form>
		</div>
		</div>

		</td>
		<?php
		return TRUE;
	}}