<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE xsl:stylesheet>
<xsl:stylesheet version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:hostcms="http://www.hostcms.ru/"
	exclude-result-prefixes="hostcms">
	<xsl:output xmlns="http://www.w3.org/TR/xhtml1/strict" doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN" encoding="utf-8" indent="yes" method="html" omit-xml-declaration="no" version="1.0" media-type="text/xml" />
	
	<xsl:decimal-format name="my" decimal-separator="," grouping-separator=" " />
	
	<!-- СмсАдминистратору -->
	
	<xsl:template match="/shop">
		<xsl:apply-templates select="shop_order" />
		<xsl:choose>
			<xsl:when test="count(shop_order/shop_order_item)">
				<xsl:apply-templates select="shop_order/shop_order_item" />Итого:<xsl:value-of select="format-number(shop_order/total_amount, '### ##0,00', 'my')" />р
			</xsl:when>
			<xsl:otherwise>
				Заказанных товаров нет
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	
	<!-- Шаблон вывода данных о заказе -->
	<xsl:template match="shop_order">
<xsl:value-of select="surname"/>&#160;<xsl:value-of select="name"/>&#160;<xsl:value-of select="patronymic"/>,<xsl:value-of select="email" />,<xsl:if test="phone != ''"><xsl:value-of select="phone" />,</xsl:if><xsl:if test="postcode != ''"><xsl:value-of select="postcode" />,</xsl:if><xsl:if test="shop_country/shop_country_location/shop_country_location_city/name != ''"><xsl:value-of select="shop_country/shop_country_location/shop_country_location_city/name" />,</xsl:if><xsl:if test="shop_country/shop_country_location/shop_country_location_city/shop_country_location_city_area/name != ''"><xsl:value-of select="shop_country/shop_country_location/shop_country_location_city/shop_country_location_city_area/name" />,</xsl:if><xsl:if test="address != ''"><xsl:value-of select="address" />,</xsl:if><xsl:if test="description != ''"><xsl:value-of select="description" disable-output-escaping="yes" />,</xsl:if>
	</xsl:template>
	
	<!-- Данные о товарах -->
	<xsl:template match="shop_order/shop_order_item"><xsl:value-of select="name" />(<xsl:value-of select="quantity" />&#160;<xsl:value-of select="shop_item/shop_measure/name" />&#160;шт),</xsl:template>
</xsl:stylesheet>