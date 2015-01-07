<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE xsl:stylesheet>
<xsl:stylesheet version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:hostcms="http://www.hostcms.ru/"
	exclude-result-prefixes="hostcms">
	<xsl:output xmlns="http://www.w3.org/TR/xhtml1/strict" doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN" encoding="utf-8" indent="yes" method="html" omit-xml-declaration="no" version="1.0" media-type="text/xml"/>
	
	<xsl:decimal-format name="my" decimal-separator="," grouping-separator=" "/>
	
	<xsl:template match="/">
		<xsl:apply-templates select="/shop"/>
	</xsl:template>
	
	<xsl:variable name="n" select="number(3)"/>
	
	<xsl:template match="/shop">
		
		<!-- Шлюхи блять пиздец! Получаем ID родительской группы и записываем в переменную $group -->
		<xsl:variable name="group" select="group"/>
		
		<xsl:choose>
			<xsl:when test="$group = 0">
				<!-- Описание выводится при отсутствии фильтрации по тэгам -->
				<xsl:if test="count(tag) = 0 and page = 0 and description != ''">
					<div hostcms:id="{@id}" hostcms:field="description" hostcms:entity="shop" hostcms:type="wysiwyg"><xsl:value-of disable-output-escaping="yes" select="description"/></div>
				</xsl:if>
			</xsl:when>
			<xsl:otherwise>
				<!--h1 hostcms:id="{$group}" hostcms:field="name" hostcms:entity="shop_group">
				<xsl:value-of disable-output-escaping="yes" select=".//shop_group[@id=$group]/name"/>
			</h1-->
			
			<!-- Описание выводим только на первой странице -->
			<xsl:if test="page = 0 and .//shop_group[@id=$group]/description != ''">
				<div hostcms:id="{$group}" hostcms:field="description" hostcms:entity="shop_group" hostcms:type="wysiwyg"><xsl:value-of disable-output-escaping="yes" select=".//shop_group[@id=$group]/description"/></div>
			</xsl:if>
			
			<!-- Путь к группе -->
			<!--p>
			<xsl:apply-templates select=".//shop_group[@id=$group]" mode="breadCrumbs"/>
		</p-->
	</xsl:otherwise>
</xsl:choose>

<!-- Обработка выбранных тэгов -->
<xsl:if test="count(tag)">
<p class="h2">Метка — <strong><xsl:value-of select="tag/name"/></strong>.</p>
	<xsl:if test="tag/description != ''">
		<p><xsl:value-of select="tag/description" disable-output-escaping="yes" /></p>
	</xsl:if>
</xsl:if>

<xsl:variable name="count">1</xsl:variable>

<!-- Отображение подгрупп данной группы, только если подгруппы есть и не идет фильтра по меткам -->
<!--xsl:if test="count(tag) = 0 and count(shop_producer) = 0 and count(//shop_group[parent_id=$group]) &gt; 0">
<div class="group_list">
	<xsl:apply-templates select=".//shop_group[parent_id=$group][position() mod $n = 1]"/>
</div>
</xsl:if-->

<xsl:if test="count(shop_item) &gt; 0 or /shop/filter = 1">
<!-- дополнение пути для action, если выбрана метка -->
<xsl:variable name="form_tag_url"><xsl:if test="count(tag) = 1">tag/<xsl:value-of select="tag/urlencode"/>/</xsl:if></xsl:variable>

<xsl:variable name="path"><xsl:choose>
		<xsl:when test="/shop//shop_group[@id=$group]/node()"><xsl:value-of select="/shop//shop_group[@id=$group]/url"/></xsl:when>
		<xsl:otherwise><xsl:value-of select="/shop/url"/></xsl:otherwise>
</xsl:choose></xsl:variable>

<form method="get" action="{$path}{$form_tag_url}">
	<xsl:if test="1=0">
		<div class="shop_filter">
			<div class="sorting">
				<select name="sorting" onchange="$(this).parents('form:first').submit()">
					<option>Сортировать</option>
					<option value="1">
					<xsl:if test="/shop/sorting = 1"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>
						По цене (сначала дешевые)
					</option>
					<option value="2">
					<xsl:if test="/shop/sorting = 2"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>
						По цене (сначала дорогие)
					</option>
					<option value="3">
					<xsl:if test="/shop/sorting = 3"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>
						По названию
					</option>
				</select>
			</div>
			
			<div class="priceFilter">
				<xsl:text>Цена от: </xsl:text>
				<input name="price_from" size="5" type="text">
					<xsl:if test="/shop/price_from != 0">
						<xsl:attribute name="value"><xsl:value-of select="/shop/price_from"/></xsl:attribute>
					</xsl:if>
				</input>
				
				<xsl:text>до: </xsl:text>
				<input name="price_to" size="5" type="text">
					<xsl:if test="/shop/price_to != 0">
						<xsl:attribute name="value"><xsl:value-of select="/shop/price_to"/></xsl:attribute>
					</xsl:if>
				</input>
			</div>
			
			<!-- Фильтр по дополнительным свойствам товара: -->
			<xsl:if test="count(shop_item_properties//property[filter != 0 and (type = 0 or type = 1 or type = 3 or type = 7 or type = 11)])">
				<span class="table_row"></span>
				<xsl:apply-templates select="shop_item_properties//property[filter != 0 and (type = 0 or type = 1 or type = 3 or type = 7 or type = 11)]" mode="propertyList"/>
			</xsl:if>
			
			<input name="filter" class="button" value="Применить" type="submit"/>
		</div>
	</xsl:if>
	<!-- Таблица с элементами для сравнения -->
	<xsl:if test="count(/shop/compare_items/compare_item) &gt; 0">
		<table cellpadding="5px" cellspacing="0" border="0">
			<tr>
				<td>
					<input type="checkbox" onclick="SelectAllItemsByPrefix(this.checked, 'del_compare_id_')" />
				</td>
				<td>
					<b>Сравниваемые элементы</b>
				</td>
			</tr>
			<xsl:apply-templates select="compare_items/compare_item"/>
		</table>
		
		<!-- <div class="nofloat" style="height: 47px; padding: 5px 0;">
			<div class="gray_button left">
				<div>
					<input name="apply_compare" value="Сравнить" type="button" onclick="javascript:location='{/shop/url}compare_items/';"/>
				</div>
			</div>
			<div class="gray_button right">
				<div>
					<input name="delete_compare" value="Удалить" type="submit"/>
				</div>
			</div>
		</div> -->
	</xsl:if>
	
	<div class="shop_block">
		<div class="shop_table">
			<!-- Выводим товары магазина -->
			<xsl:apply-templates select="shop_item" />
		</div>
	</div>
	
	<p class="button" id="compareButton">
		<xsl:if test="count(/shop/comparing/shop_item) = 0">
			<xsl:attribute name="style">display: none</xsl:attribute>
		</xsl:if>
		<a href="{/shop/url}compare_items/">Сравнить товары</a>
	</p>
	
	<xsl:if test="total &gt; 0 and limit &gt; 0">
		
		<xsl:variable name="count_pages" select="ceiling(total div limit)"/>
		
		<xsl:variable name="visible_pages" select="5"/>
		
		<xsl:variable name="real_visible_pages"><xsl:choose>
				<xsl:when test="$count_pages &lt; $visible_pages"><xsl:value-of select="$count_pages"/></xsl:when>
				<xsl:otherwise><xsl:value-of select="$visible_pages"/></xsl:otherwise>
		</xsl:choose></xsl:variable>
		
		<!-- Считаем количество выводимых ссылок перед текущим элементом -->
		<xsl:variable name="pre_count_page"><xsl:choose>
				<xsl:when test="page - (floor($real_visible_pages div 2)) &lt; 0">
					<xsl:value-of select="page"/>
				</xsl:when>
				<xsl:when test="($count_pages - page - 1) &lt; floor($real_visible_pages div 2)">
					<xsl:value-of select="$real_visible_pages - ($count_pages - page - 1) - 1"/>
				</xsl:when>
				<xsl:otherwise>
					<xsl:choose>
						<xsl:when test="round($real_visible_pages div 2) = $real_visible_pages div 2">
							<xsl:value-of select="floor($real_visible_pages div 2) - 1"/>
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="floor($real_visible_pages div 2)"/>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:otherwise>
		</xsl:choose></xsl:variable>
		
		<!-- Считаем количество выводимых ссылок после текущего элемента -->
		<xsl:variable name="post_count_page"><xsl:choose>
				<xsl:when test="0 &gt; page - (floor($real_visible_pages div 2) - 1)">
					<xsl:value-of select="$real_visible_pages - page - 1"/>
				</xsl:when>
				<xsl:when test="($count_pages - page - 1) &lt; floor($real_visible_pages div 2)">
					<xsl:value-of select="$real_visible_pages - $pre_count_page - 1"/>
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="$real_visible_pages - $pre_count_page - 1"/>
				</xsl:otherwise>
		</xsl:choose></xsl:variable>
		
		<xsl:variable name="i"><xsl:choose>
				<xsl:when test="page + 1 = $count_pages"><xsl:value-of select="page - $real_visible_pages + 1"/></xsl:when>
				<xsl:when test="page - $pre_count_page &gt; 0"><xsl:value-of select="page - $pre_count_page"/></xsl:when>
				<xsl:otherwise>0</xsl:otherwise>
		</xsl:choose></xsl:variable>
		
		<p>
			<xsl:call-template name="for">
				<xsl:with-param name="limit" select="limit"/>
				<xsl:with-param name="page" select="page"/>
				<xsl:with-param name="items_count" select="total"/>
				<xsl:with-param name="i" select="$i"/>
				<xsl:with-param name="post_count_page" select="$post_count_page"/>
				<xsl:with-param name="pre_count_page" select="$pre_count_page"/>
				<xsl:with-param name="visible_pages" select="$real_visible_pages"/>
			</xsl:call-template>
		</p>
	</xsl:if>
	
	<!--p style="float:right; margin: 0 40px 0 0">Показать по:
<a href="{$path}?on_page=20">20</a><xsl:text> </xsl:text>
<a href="{$path}?on_page=50">50</a><xsl:text> </xsl:text>
	<a href="{$path}?on_page=100">100</a>
</p-->
<div style="clear: both"></div>
</form>
</xsl:if>

<!-- Есть избранные товары -->
<xsl:if test="favorite/shop_item">
<p class="h1 red">Мои избранные товары</p>
<div class="shop_block">
<div class="shop_table">
	<!-- Выводим товары магазина -->
	<xsl:apply-templates select="favorite/shop_item[position() &lt; 4]" />
</div>
</div>
</xsl:if>

<!-- Есть просмотренные товары -->
<!--xsl:if test="viewed/shop_item">
<p class="h1 red">Просмотренные товары</p>
<div class="shop_block">
<div class="shop_table"-->
<!-- Выводим товары магазина -->
<!--xsl:apply-templates select="viewed/shop_item[position() &lt; 4]" />
</div>
</div>
</xsl:if-->

<!-- Есть производители-->
<xsl:if test="count(producers/shop_producer) > 0">
<div style="margin-top: 40px">
<h1>Производители</h1>
<xsl:apply-templates select="producers/shop_producer"/>
</div>
</xsl:if>
</xsl:template>

<!-- Шаблон для товара -->
<xsl:template match="shop_item">

<div class="shop_item">
<div class="shop_table_item">
<div class="container">
<div class="row">
<div class="col-md-6">
	<xsl:if test="property_value[tag_name='menuNumber']/value != ''">
		<div class="menuNumber">
			<xsl:value-of select="property_value[tag_name='menuNumber']/value"/>
		</div>
	</xsl:if>
</div>
<div class="col-md-6">
	<!-- Ссылку на добавление в корзины выводим, если:
	type = 0 - простой тип товара
	type = 1 - электронный товар, при этом остаток на складе больше 0 или -1,
	что означает неограниченное количество -->
	<xsl:if test="type = 0 or (type = 1 and (digitals > 0 or digitals = -1))">
		<div class="toCart" onclick="return $.addIntoCart('{/shop/url}cart/', {@id}, 1)">
			<!-- <div style="border: 1px solid #CCCCCC; border-radius: 4px 4px 4px 4px; width: 100px; padding: 3px 0 3px 0; margin-left: 80px">-->
				<span class="button2 white medium">
					В корзину
				</span>
				<!-- </div> -->
		</div>
	</xsl:if>
</div>
</div>
</div>

<div class="image_row">
<div class="image_cell">
<!--a href="{url}"-->
	<xsl:choose>
		<xsl:when test="image_small != ''">
			<img src="{dir}{image_small}" alt="{name}" title="{name}"/>
		</xsl:when>
		<xsl:otherwise>
			<img src="/images/no-image.png" alt="{name}" title="{name}"/>
		</xsl:otherwise>
	</xsl:choose>
<!--/a-->
</div>
</div>
<div class="description_row">
<div class="description_sell">
<div class="container">
	<div class="row shop-item-name-holder" data-toggle="tooltip" data-placement="bottom" title="{name}">
		<div class="col-md-12">
			<a href="{url}" title="{name}" hostcms:id="{@id}" hostcms:field="name" hostcms:entity="shop_item" class="shop-item-name">
				<xsl:value-of disable-output-escaping="yes" select="name"/>
			</a>
		</div>
	</div>
	<div class="row composition-row" data-toggle="tooltip" data-placement="bottom" title="{property_value[tag_name='composition']/value}">
		<div class="col-md-12 composition">
			<xsl:value-of select="property_value[tag_name='composition']/value"/>
		</div>
	</div>
	<div class="row last-row">
		<div class="col-md-7 weightAndCalories">
			<xsl:value-of select="property_value[tag_name='weightAndCalories']/value"/>
		</div>
		<div class="col-md-5">
			<div class="price">
			<xsl:value-of select="format-number(price, '### ##0,00', 'my')"/><xsl:text> </xsl:text><xsl:value-of select="currency"/><xsl:text> </xsl:text>
				
				<!-- Сравнение товаров -->
				<xsl:variable name="shop_item_id" select="@id" />
				<div class="compare" onclick="return $.addCompare('{/shop/url}', {@id}, this)">
					<xsl:if test="/shop/comparing/shop_item[@id = $shop_item_id]/node()">
						<xsl:attribute name="class">compare current</xsl:attribute>
					</xsl:if>
				</div>
				<!-- Избранное -->
				<div class="favorite" onclick="return $.addFavorite('{/shop/url}', {@id}, this)">
					<xsl:if test="/shop/favorite/shop_item[@id = $shop_item_id]/node()">
						<xsl:attribute name="class">favorite favorite_current</xsl:attribute>
					</xsl:if>
				</div>
			</div>
		</div>
	</div>
</div>


</div>
</div>
</div>
</div>


</xsl:template>

<!-- Шаблон для групп товара -->
<xsl:template match="shop_group">
<ul>
<xsl:for-each select=". | following-sibling::shop_group[position() &lt; $n]">
<li>
<!-- <xsl:if test="image_small!=''">
<a href="{url}" target="_blank">
	<img src="{dir}{image_small}" align="middle"/>
</a><xsl:text> </xsl:text></xsl:if> -->
<a href="{url}" hostcms:id="{@id}" hostcms:field="name" hostcms:entity="shop_group"><xsl:value-of disable-output-escaping="yes" select="name"/></a><xsl:text> </xsl:text><span class="shop_count"><xsl:value-of select="items_total_count"/></span>
</li>
</xsl:for-each>
</ul>
</xsl:template>

<!-- Шаблон для групп товара menu-->
<xsl:template match="shop_group" mode="menu">
<ul>
<xsl:for-each select=". | following-sibling::shop_group[position() &lt; $n]">
<li>
<!-- <xsl:if test="image_small!=''">
<a href="{url}" target="_blank">
	<img src="{dir}{image_small}" align="middle"/>
</a><xsl:text> </xsl:text></xsl:if> -->
<a href="{url}" hostcms:id="{@id}" hostcms:field="name" hostcms:entity="shop_group"><xsl:value-of disable-output-escaping="yes" select="name"/></a><xsl:text> </xsl:text><span class="shop_count"><xsl:value-of select="items_total_count"/></span>
</li>
</xsl:for-each>
</ul>
</xsl:template>

<!-- Вывод строки со значением свойства -->
<xsl:template match="property_value">
<xsl:if test="value/node() and value != '' or file/node() and file != ''">
<div class="shop_property">
<xsl:variable name="property_id" select="property_id" />
<xsl:variable name="property" select="/shop/shop_item_properties//property[@id=$property_id]" />

<!--xsl:value-of disable-output-escaping="yes" select="$property/name"/><xsl:text>: </xsl:text-->
<span><xsl:choose>
<xsl:when test="$property/type = 2">
<a href="{../dir}{file}" target="_blank"><xsl:value-of disable-output-escaping="yes" select="file_name"/></a>
</xsl:when>
<xsl:when test="$property/type = 7">
<input type="checkbox" disabled="disabled">
	<xsl:if test="value = 1">
		<xsl:attribute name="checked">checked</xsl:attribute>
	</xsl:if>
</input>
</xsl:when>
<xsl:otherwise>
<xsl:value-of disable-output-escaping="yes" select="value"/>
<!-- Единица измерения свойства -->
<xsl:if test="$property/shop_measure/node()">
	<xsl:text> </xsl:text><xsl:value-of select="$property/shop_measure/name"/>
</xsl:if>
</xsl:otherwise>
</xsl:choose></span>
</div>
</xsl:if>
</xsl:template>

<!-- Шаблон выводит рекурсивно ссылки на группы магазина -->
<xsl:template match="shop_group" mode="breadCrumbs">
<xsl:param name="parent_id" select="parent_id"/>

<!-- Получаем ID родительской группы и записываем в переменную $group -->
<xsl:param name="group" select="/shop/shop_group"/>

<xsl:apply-templates select="//shop_group[@id=$parent_id]" mode="breadCrumbs"/>

<xsl:if test="parent_id=0">
<a href="{/shop/url}" hostcms:id="{/shop/@id}" hostcms:field="name" hostcms:entity="shop">
<xsl:value-of select="/shop/name"/>
</a>
</xsl:if>

<span><xsl:text> → </xsl:text></span>

<a href="{url}" hostcms:id="{@id}" hostcms:field="name" hostcms:entity="shop_group">
<xsl:value-of disable-output-escaping="yes" select="name"/>
</a>
</xsl:template>

<!-- Шаблон для списка товаров для сравнения -->
<xsl:template match="compare_items/compare_item">
<xsl:variable name="var_compare_id" select="."/>
<tr>
<td>
<input type="checkbox" name="del_compare_id_{compare_item_id}" id="id_del_compare_id_{compare_item_id}"/>
</td>
<td>
<a href="{/shop/url}{compare_item_url}{compare_url}/">
<xsl:value-of disable-output-escaping="yes" select="compare_name"/>
</a>
</td>
</tr>
</xsl:template>

<!-- Шаблон для фильтра по дополнительным свойствам -->
<xsl:template match="property" mode="propertyList">
<xsl:variable name="nodename">property_<xsl:value-of select="@id"/></xsl:variable>
<xsl:variable name="nodename_from">property_<xsl:value-of select="@id"/>_from</xsl:variable>
<xsl:variable name="nodename_to">property_<xsl:value-of select="@id"/>_to</xsl:variable>

<div class="filterField">

<xsl:if test="filter != 5">
<legend><xsl:value-of disable-output-escaping="yes" select="name"/><xsl:text> </xsl:text></legend>
</xsl:if>

<xsl:choose>
<!-- Отображаем поле ввода -->
<xsl:when test="filter = 1">
<br/>
<input type="text" name="property_{@id}">
<xsl:if test="/shop/*[name()=$nodename] != ''">
<xsl:attribute name="value"><xsl:value-of select="/shop/*[name()=$nodename]"/></xsl:attribute>
</xsl:if>
</input>
</xsl:when>
<!-- Отображаем список -->
<xsl:when test="filter = 2">
<br/>
<select name="property_{@id}">
<option value="0">...</option>
<xsl:apply-templates select="list/list_item"/>
</select>
</xsl:when>
<!-- Отображаем переключатели -->
<xsl:when test="filter = 3">
<br/>
<div class="propertyInput">
<input type="radio" name="property_{@id}" value="0" id="id_prop_radio_{@id}_0"></input>
<label for="id_prop_radio_{@id}_0">Любой вариант</label>
<xsl:apply-templates select="list/list_item"/>
</div>
</xsl:when>
<!-- Отображаем флажки -->
<xsl:when test="filter = 4">
<div class="propertyInput">
<xsl:apply-templates select="list/list_item"/>
</div>
</xsl:when>
<!-- Отображаем флажок -->
<xsl:when test="filter = 5">
<input type="checkbox" name="property_{@id}" id="property_{@id}" style="padding-top:4px">
<xsl:if test="/shop/*[name()=$nodename] != ''">
<xsl:attribute name="checked"><xsl:value-of select="/shop/*[name()=$nodename]"/></xsl:attribute>
</xsl:if>
</input>
<label for="property_{@id}">
<xsl:value-of disable-output-escaping="yes" select="name"/><xsl:text> </xsl:text>
</label>
</xsl:when>
<!-- Отображение полей "от и до" -->
<xsl:when test="filter = 6">
<br/>
от: <input type="text" name="property_{@id}_from" size="2" value="{/shop/*[name()=$nodename_from]}"/> до: <input type="text" name="property_{@id}_to" size="2" value="{/shop/*[name()=$nodename_to]}"/>
</xsl:when>
<!-- Отображаем список с множественным выбором-->
<xsl:when test="filter = 7">
<br/>
<select name="property_{@id}[]" multiple="multiple">
<xsl:apply-templates select="list/list_item"/>
</select>
</xsl:when>
</xsl:choose>
</div>
</xsl:template>

<xsl:template match="list/list_item">
<xsl:if test="../../filter = 2">
<!-- Отображаем список -->
<xsl:variable name="nodename">property_<xsl:value-of select="../../@id"/></xsl:variable>
<option value="{@id}">
<xsl:if test="/shop/*[name()=$nodename] = @id"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>
<xsl:value-of disable-output-escaping="yes" select="value"/>
</option>
</xsl:if>
<xsl:if test="../../filter = 3">
<!-- Отображаем переключатели -->
<xsl:variable name="nodename">property_<xsl:value-of select="../../@id"/></xsl:variable>
<br/>
<input type="radio" name="property_{../../@id}" value="{@id}" id="id_property_{../../@id}_{@id}">
<xsl:if test="/shop/*[name()=$nodename] = @id">
<xsl:attribute name="checked">checked</xsl:attribute>
</xsl:if>
<label for="id_property_{../../@id}_{@id}">
<xsl:value-of disable-output-escaping="yes" select="value"/>
</label>
</input>
</xsl:if>
<xsl:if test="../../filter = 4">
<!-- Отображаем флажки -->
<xsl:variable name="nodename">property_<xsl:value-of select="../../@id"/></xsl:variable>
<br/>
<input type="checkbox" value="{@id}" name="property_{../../@id}[]" id="property_{../../@id}_{@id}">
<xsl:if test="/shop/*[name()=$nodename] = @id">
<xsl:attribute name="checked">checked</xsl:attribute>
</xsl:if>
<label for="property_{../../@id}_{@id}">
<xsl:value-of disable-output-escaping="yes" select="value"/>
</label>
</input>
</xsl:if>
<xsl:if test="../../filter = 7">
<!-- Отображаем список -->
<xsl:variable name="nodename">property_<xsl:value-of select="../../@id"/></xsl:variable>
<option value="{@id}">
<xsl:if test="/shop/*[name()=$nodename] = @id">
<xsl:attribute name="selected">
</xsl:attribute>
</xsl:if>
<xsl:value-of disable-output-escaping="yes" select="value"/>
</option>
</xsl:if>
</xsl:template>

<!-- Метки для товаров -->
<xsl:template match="tag">
<a href="{/shop/url}tag/{urlencode}/" class="tag">
<xsl:value-of select="tag_name"/>
</a>
<xsl:if test="position() != last()"><xsl:text>, </xsl:text></xsl:if>
</xsl:template>

<!-- Цикл для вывода строк ссылок -->
<xsl:template name="for">

<xsl:param name="limit"/>
<xsl:param name="page"/>
<xsl:param name="pre_count_page"/>
<xsl:param name="post_count_page"/>
<xsl:param name="i" select="0"/>
<xsl:param name="items_count"/>
<xsl:param name="visible_pages"/>

<xsl:variable name="n" select="ceiling($items_count div $limit)"/>

<xsl:variable name="start_page"><xsl:choose>
<xsl:when test="$page + 1 = $n"><xsl:value-of select="$page - $visible_pages + 1"/></xsl:when>
<xsl:when test="$page - $pre_count_page &gt; 0"><xsl:value-of select="$page - $pre_count_page"/></xsl:when>
<xsl:otherwise>0</xsl:otherwise>
</xsl:choose></xsl:variable>

<xsl:if test="$i = $start_page and $page != 0">
<span class="ctrl">
← Ctrl
</span>
</xsl:if>

<xsl:if test="$i = ($page + $post_count_page + 1) and $n != ($page+1)">
<span class="ctrl">
Ctrl →
</span>
</xsl:if>

<!-- Передаем фильтр -->
<xsl:variable name="filter"><xsl:if test="/shop/filter/node()">?filter=1&amp;sorting=<xsl:value-of select="/shop/sorting"/>&amp;price_from=<xsl:value-of select="/shop/price_from"/>&amp;price_to=<xsl:value-of select="/shop/price_to"/><xsl:for-each select="/shop/*"><xsl:if test="starts-with(name(), 'property_')">&amp;<xsl:value-of select="name()"/>[]=<xsl:value-of select="."/></xsl:if></xsl:for-each></xsl:if></xsl:variable>

<xsl:variable name="on_page"><xsl:if test="/shop/on_page/node() and /shop/on_page > 0"><xsl:choose><xsl:when test="/shop/filter/node()">&amp;</xsl:when><xsl:otherwise>?</xsl:otherwise></xsl:choose>on_page=<xsl:value-of select="/shop/on_page"/></xsl:if></xsl:variable>

<xsl:if test="$items_count &gt; $limit and ($page + $post_count_page + 1) &gt; $i">
<!-- Заносим в переменную $group идентификатор текущей группы -->
<xsl:variable name="group" select="/shop/group"/>

<!-- Путь для тэга -->
<xsl:variable name="tag_path"><xsl:if test="count(/shop/tag) != 0">tag/<xsl:value-of select="/shop/tag/urlencode"/>/</xsl:if></xsl:variable>

<!-- Путь для сравнения товара -->
<xsl:variable name="shop_producer_path"><xsl:if test="count(/shop/shop_producer)">producer-<xsl:value-of select="/shop/shop_producer/@id"/>/</xsl:if></xsl:variable>

<!-- Определяем группу для формирования адреса ссылки -->
<xsl:variable name="group_link"><xsl:choose><xsl:when test="$group != 0"><xsl:value-of select="/shop//shop_group[@id=$group]/url"/></xsl:when><xsl:otherwise><xsl:value-of select="/shop/url"/></xsl:otherwise></xsl:choose></xsl:variable>

<!-- Определяем адрес ссылки -->
<xsl:variable name="number_link"><xsl:if test="$i != 0">page-<xsl:value-of select="$i + 1"/>/</xsl:if></xsl:variable>

<!-- Выводим ссылку на первую страницу -->
<xsl:if test="$page - $pre_count_page &gt; 0 and $i = $start_page">
<a href="{$group_link}{$tag_path}{$shop_producer_path}{$filter}{$on_page}" class="page_link" style="text-decoration: none;">←</a>
</xsl:if>

<!-- Ставим ссылку на страницу-->
<xsl:if test="$i != $page">
<xsl:if test="($page - $pre_count_page) &lt;= $i and $i &lt; $n">
<!-- Выводим ссылки на видимые страницы -->
<a href="{$group_link}{$number_link}{$tag_path}{$shop_producer_path}{$filter}{$on_page}" class="page_link">
<xsl:value-of select="$i + 1"/>
</a>
</xsl:if>

<!-- Выводим ссылку на последнюю страницу -->
<xsl:if test="$i+1 &gt;= ($page + $post_count_page + 1) and $n &gt; ($page + 1 + $post_count_page)">
<!-- Выводим ссылку на последнюю страницу -->
<a href="{$group_link}page-{$n}/{$tag_path}{$shop_producer_path}{$filter}{$on_page}" class="page_link" style="text-decoration: none;">→</a>
</xsl:if>
</xsl:if>

<!-- Ссылка на предыдущую страницу для Ctrl + влево -->
<xsl:if test="$page != 0 and $i = $page"><xsl:variable name="prev_number_link"><xsl:if test="($page) != 0">page-<xsl:value-of select="$i"/>/</xsl:if></xsl:variable><a href="{$group_link}{$prev_number_link}{$tag_path}{$shop_producer_path}{$filter}{$on_page}" id="id_prev"></a></xsl:if>

<!-- Ссылка на следующую страницу для Ctrl + вправо -->
<xsl:if test="($n - 1) > $page and $i = $page">
<a href="{$group_link}page-{$page+2}/{$tag_path}{$shop_producer_path}{$filter}{$on_page}" id="id_next"></a>
</xsl:if>

<!-- Не ставим ссылку на страницу-->
<xsl:if test="$i = $page">
<span class="current">
<xsl:value-of select="$i+1"/>
</span>
</xsl:if>

<!-- Рекурсивный вызов шаблона. НЕОБХОДИМО ПЕРЕДАВАТЬ ВСЕ НЕОБХОДИМЫЕ ПАРАМЕТРЫ! -->
<xsl:call-template name="for">
<xsl:with-param name="i" select="$i + 1"/>
<xsl:with-param name="limit" select="$limit"/>
<xsl:with-param name="page" select="$page"/>
<xsl:with-param name="items_count" select="$items_count"/>
<xsl:with-param name="pre_count_page" select="$pre_count_page"/>
<xsl:with-param name="post_count_page" select="$post_count_page"/>
<xsl:with-param name="visible_pages" select="$visible_pages"/>
</xsl:call-template>
</xsl:if>
</xsl:template>

<!-- Шаблон для фильтра производителей -->
<xsl:template match="producers/shop_producer">
<!-- Заносим в переменную $group идентификатор текущей группы -->
<xsl:variable name="group" select="/shop/group"/>

<!-- Определяем группу для формирования адреса ссылки -->
<xsl:variable name="group_link"><xsl:choose><xsl:when test="$group != 0"><xsl:value-of select="/shop//shop_group[@id=$group]/url"/></xsl:when><xsl:otherwise><xsl:value-of select="/shop/url"/></xsl:otherwise></xsl:choose></xsl:variable>

<a href="{$group_link}?producer_id={@id}"><xsl:value-of disable-output-escaping="yes" select="name"/></a>
</xsl:template>
</xsl:stylesheet>