<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE xsl:stylesheet>
<!--МагазинКаталогТоваров_cenpai_mode_small::248-->
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


<!-- Отображение подгрупп данной группы, только если подгруппы есть и не идет фильтра по меткам -->
<xsl:if test="count(tag) = 0 and count(shop_producer) = 0 and count(//shop_group[parent_id=$group]) &gt; 0">
<div class="group_list hidden-xs visible-sm-* visible-md-* visible-lg-*">
	<xsl:apply-templates select=".//shop_group[parent_id=$group][position() mod $n = 1]" mode="screen"/>
</div>

<div class="group_list hidden-sm hidden-md hidden-lg visible-xs-*">
	<xsl:apply-templates select=".//shop_group[parent_id=$group][position() mod $n = 1]" mode="small"/>
</div>
</xsl:if>

<xsl:if test="count(shop_item) &gt; 0 or /shop/filter = 1">
<!-- дополнение пути для action, если выбрана метка -->
<xsl:variable name="form_tag_url"><xsl:if test="count(tag) = 1">tag/<xsl:value-of select="tag/urlencode"/>/</xsl:if></xsl:variable>

<xsl:variable name="path"><xsl:choose>
		<xsl:when test="/shop//shop_group[@id=$group]/node()"><xsl:value-of select="/shop//shop_group[@id=$group]/url"/></xsl:when>
		<xsl:otherwise><xsl:value-of select="/shop/url"/></xsl:otherwise>
</xsl:choose></xsl:variable>

<form method="get" action="{$path}{$form_tag_url}" id="screen-shop-form" class="hidden-xs visible-sm-* visible-md-* visible-lg-*">
	<div class="shop_block">
		<div class="shop_table">
			<!-- Выводим товары магазина -->
			<xsl:apply-templates select="shop_item" mode="screen" />
		</div>
	</div>
	<div style="clear: both"></div>
</form>

<form method="get" action="{$path}{$form_tag_url}" id="small-device-shop-form" class="hidden-sm hidden-md hidden-lg visible-xs-*">
	<div class="shop_block">
		<div class="shop_table">
			<!-- Выводим товары магазина -->
			<xsl:apply-templates select="shop_item" mode="small-device" />
		</div>
	</div>
	<div style="clear: both"></div>
</form>
</xsl:if>
</xsl:template>

<!-- small:start -->
<xsl:template match="shop_item" mode="small-device">
<div class="small-shop_item">
<div class="shop_table_item container-fluid">
	
	<!-- Small device shop -->
	<div id="small-device-shop">
		<div class="row">
			<div class="col-xs-5">
				<div class="left-part-shop-item">
					<div class="image_cell">
						<xsl:choose>
							<xsl:when test="image_small != ''">
								<img src="{dir}{image_small}" alt="{name}" title="{name}"/>
							</xsl:when>
							<xsl:otherwise>
								<img src="/images/no-image.png" alt="{name}" title="{name}"/>
							</xsl:otherwise>
						</xsl:choose>
					</div>
					<div class="props_cell">
						<xsl:if test="property_value[tag_name='menuNumber']/value != ''">
							<div class="menuNumber">
								<xsl:value-of select="property_value[tag_name='menuNumber']/value"/>
							</div>
						</xsl:if>
						<xsl:if test="type = 0 or (type = 1 and (digitals > 0 or digitals = -1))">
							<div class="toCart glyphicon glyphicon-shopping-cart" onclick="return $.addIntoCart('{/shop/url}cart/', {@id}, 1, true)">
							</div>
						</xsl:if>
					</div>
				</div>
			</div>
			
			<div class="col-xs-7">
				<div class="small-item-name"><!-- Name item -->
					<a href="#" title="{name}" hostcms:id="{@id}" hostcms:field="name" hostcms:entity="shop_item" class="shop-item-name">
						<xsl:value-of disable-output-escaping="yes" select="name"/>
					</a>
				</div>
				<div class="small-item-composition">
					<xsl:value-of select="property_value[tag_name='composition']/value"/>
				</div>
				<div class="price small-item-price">
				<xsl:value-of select="format-number(price, '### ##0,00', 'my')"/><xsl:text> </xsl:text><xsl:value-of select="currency"/><xsl:text> </xsl:text>
				</div>
			</div>
		</div>
	</div>
</div>
</div>
</xsl:template>
<!-- small:end -->

<!-- screen:start -->
<!-- Шаблон для товара -->
<xsl:template match="shop_item" mode="screen">
<div class="shop_item">
<div class="shop_table_item">
	<div class = "container">
		<div class="row">
			<div class="col-md-6 col-sm-6">
				<xsl:if test="property_value[tag_name='menuNumber']/value != ''">
					<div class="menuNumber">
						<xsl:value-of select="property_value[tag_name='menuNumber']/value"/>
					</div>
				</xsl:if>
			</div>
			<div class="col-md-6 col-sm-6">
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
						<!--a href="{url}" title="{name}" hostcms:id="{@id}" hostcms:field="name" hostcms:entity="shop_item" class="shop-item-name">
						<xsl:value-of disable-output-escaping="yes" select="name"/>
					</a-->
					<span class="shop-item-name"><xsl:value-of disable-output-escaping="yes" select="name"/></span>
				</div>
			</div>
			<div class="row composition-row" data-toggle="tooltip" data-placement="bottom" title="{property_value[tag_name='composition']/value}">
				<div class="col-md-12 composition">
					<xsl:value-of select="property_value[tag_name='composition']/value"/>
				</div>
			</div>
			<div class="row last-row">
				<div class="col-md-6 col-sm-6 weightAndCalories">
					<xsl:value-of select="property_value[tag_name='weightAndCalories']/value"/>
				</div>
				<div class="col-md-6 col-sm-6 price">
					<xsl:value-of select="format-number(price, '### ##0,00', 'my')"/>
					<!-- Если цена со скидкой - выводим ее -->
					<xsl:if test="discount != 0">
						<span class="oldPrice">
							<xsl:value-of select="format-number(price + discount, '### ##0,00', 'my')"/>
						</span>
					</xsl:if>
					<span class="currency">
						<xsl:text> </xsl:text><xsl:value-of select="currency" />
					</span>
				</div>
			</div>
		</div>
	</div>
</div>
</div>
</div>
</xsl:template>
<!-- screen:end -->

<!-- Шаблон для групп товара screen -->
<xsl:template match="shop_group" mode="screen">
<div class="nav-justified">
<xsl:for-each select=". | following-sibling::shop_group[position() &lt; $n]">
<div class="nav-elem-justified">
	<!--a href="{url}" hostcms:id="{@id}" hostcms:field="name" hostcms:entity="shop_group">
		<xsl:value-of disable-output-escaping="yes" select="name"/>
	</a-->
	<div style="background: url({dir}{image_small}) no-repeat top center; width: 299px; height: 315px; margin: 0 auto;" class="subgroup_holder">
		<div class="subgroup_title">
			<xsl:value-of disable-output-escaping="yes" select="name"/>
		</div>
		<a href="{url}" hostcms:id="{@id}" hostcms:field="name" hostcms:entity="shop_group" style="text-decoration:none;">
			<div class="subgroup_btn">
				Смотреть все
			</div>
		</a>
	</div>
	<xsl:text> </xsl:text>
	<span class="shop_count"><xsl:value-of select="items_total_count"/></span>
</div>
</xsl:for-each>
</div>
</xsl:template>

<!-- Шаблон для групп товара small -->
<xsl:template match="shop_group" mode="small">
<div class="subgroup-container">
<xsl:for-each select=". | following-sibling::shop_group[position() &lt; $n]">
	<div class="row">
		<div class="col-xs-2">
			<div class="left-part-shop-item subgroup-img-container">
				<div class="image_cell">
					<xsl:choose>
						<xsl:when test="image_small != ''">
							<img src="{dir}{image_small}" alt="{name}" title="{name}"/>
						</xsl:when>
						<xsl:otherwise>
							<img src="/images/no-image.png" alt="{name}" title="{name}"/>
						</xsl:otherwise>
					</xsl:choose>
				</div>
			</div>
		</div>
		
		<div class="col-xs-10">
			<div class="small-item-name"><!-- Name item -->
				<a href="{url}" title="{name}" hostcms:id="{@id}" hostcms:field="name" hostcms:entity="shop_item" class="shop-item-name">
					<xsl:value-of disable-output-escaping="yes" select="name"/>
				</a>
			</div>
		</div>
	</div>
</xsl:for-each>
</div>
</xsl:template>

<!-- Шаблон для групп товара menu-->
<xsl:template match="shop_group" mode="menu">
<ul>
<xsl:for-each select=". | following-sibling::shop_group[position() &lt; $n]">
<li>
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
<span>
<xsl:choose>
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
</xsl:choose>
</span>
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
</xsl:stylesheet>