<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Online shop.
 *
 * @package HostCMS 6\Shop
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2013 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Shop_Item_Card extends Core_Servant_Properties
{
	/**
	 * Allowed object properties
	 * @var array
	 */
	protected $_allowedProperties = array(
		'fio',
		'date',
		'height',
		'width',
		'font'
	);
	
	/**
	 * Show CSS
	 */
	public function showcss()
	{
		?>
		<style>
			div.main {
				width: <?php echo $this->width?>mm;
				height: <?php echo $this->height?>mm;
				font-size: <?php echo $this->font?>pt;
				overflow: hidden; 
				font-family: Arial, sans-serif;
				border: 1px solid #000;
				margin: 0;
				display: inline-block;
				line-height: 150%
			}

			div.main > div {display: table; width: 100%; height: 100%}
			div.main > div > div {display: table-row}
			div.main > div > div > div {display: table-cell; vertical-align: middle; padding: 0 5px}

			div.main > div > div:first-child > div {height: 8%; background-color: #c0c0c0; border-bottom: 1px solid #000;}
			div.txt {height: 6%}

			.c {text-align: center}
			.b {font-weight: bold}
			.s {font-size: 0.7em}
			.r {float: right}

			div.name {font-size: 1.6em}
			div.price {font-size: 2.1em}

			div.footer > div {display: table; width: 100%}
			div.footer p {display: table-row; margin: 0; padding: 0}
			div.footer span {display: table-cell}
			div.footer span.d {white-space: nowrap; padding-left: 5px}
			div.footer > div > p:first-child span {border-bottom: 1px solid #000}
			div.footer > div > p:first-child span.d {width: 40%; border-bottom-width: 0}
		</style>
		<?php
	}
	
	/**
	 * Build DIV for item
	 * @param Shop_Item_Model $oShopItem item
	 * @return string
	 */
	public function build(Shop_Item_Model $oShopItem)
	{
		$sMeasureName = $oShopItem->Shop_Measure->name;
		$sMeasureTitle = '';
		$sMeasureName != '' && $sMeasureTitle = 'Ед.: ';
		$sMarkingName = $oShopItem->marking;
		$sMarkingTitle = '';
		$sMarkingName != '' && $sMarkingTitle = 'Артикул: ';
		
		?><div class="main"><div><div><div class="c b"><?php echo $oShopItem->Shop->Shop_Company->name?></div></div><div><div class="txt"><?php echo Core::_('Shop_Item.item_cards_desription')?>:</div></div><div><div class="name c"><?php echo $oShopItem->name?></div></div><div><div class="txt"><span class="s"><?php echo $sMeasureTitle?></span><span class="b"><?php echo $sMeasureName?></span><span class="r"><span class="s"><?php echo $sMarkingTitle?></span><span class="b"><?php echo $sMarkingName?></span></span></div></div><div><div class="txt"><span class="s"><?php echo Core::_('Shop_Item.item_cards_price')?>,</span> <span class="b"><?php echo $oShopItem->Shop_Currency->name?></span>:</div></div><div><div class="price c b"><?php echo $oShopItem->price?></div></div><div><div class="txt s"><?php echo Core::_('Shop_Item.item_cards_sign')?>:</div></div><div><div class="txt s footer"><div><p><span> </span><span class="d"><?php echo $this->fio?></span></p><p><span></span><span class="d"><?php echo $this->date?></span></p></div></div></div></div></div><?php
	}
}