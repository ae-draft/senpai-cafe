<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE xsl:stylesheet>
<xsl:stylesheet version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:hostcms="http://www.hostcms.ru/"
	exclude-result-prefixes="hostcms">
	<xsl:output xmlns="http://www.w3.org/TR/xhtml1/strict" doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN" encoding="utf-8" indent="yes" method="html" omit-xml-declaration="no" version="1.0" media-type="text/xml"/>
	
	<!-- МагазинКорзинаКраткая -->
	
	<xsl:decimal-format name="my" decimal-separator="," grouping-separator=" "/>
	
	<xsl:template match="/shop">
		
		<div id="little_cart">
			<xsl:choose>
				<!-- В корзине нет ни одного элемента -->
				<xsl:when test="count(shop_cart) = 0">
				<div class="mainpage-cart-title"><a href="{/shop/url}cart/">Корзина (пока пуста)</a></div>
				</xsl:when>
				<xsl:otherwise>
					<xsl:variable name="totalQuantity" select="sum(shop_cart[postpone = 0]/quantity)" />
					<div class="h1 cartTitle">
						<a href="{/shop/url}cart/">
							Корзина (
							<b><xsl:value-of select="$totalQuantity"/></b>
							<xsl:text> </xsl:text>
							<xsl:call-template name="declension">
								<xsl:with-param name="number" select="$totalQuantity"/>
							</xsl:call-template>
							на сумму
							<b>
								<xsl:value-of select="format-number(total_amount, '### ##0,00', 'my')"/>
								<xsl:text> </xsl:text>
								<xsl:value-of disable-output-escaping="yes" select="shop_currency/name"/>
							</b>
							)
						</a>
					</div>
				</xsl:otherwise>
			</xsl:choose>
		</div>
	</xsl:template>
	
	<!-- Склонение после числительных -->
	<xsl:template name="declension">
		
		<xsl:param name="number" select="number"/>
		
		<!-- Именительный падеж -->
		<xsl:variable name="nominative">
			<xsl:text>товар</xsl:text>
		</xsl:variable>
		
		<!-- Родительный падеж, единственное число -->
		<xsl:variable name="genitive_singular">
			<xsl:text>товара</xsl:text>
		</xsl:variable>
		
		
		<xsl:variable name="genitive_plural">
			<xsl:text>товаров</xsl:text>
		</xsl:variable>
		
		<xsl:variable name="last_digit">
			<xsl:value-of select="$number mod 10"/>
		</xsl:variable>
		
		<xsl:variable name="last_two_digits">
			<xsl:value-of select="$number mod 100"/>
		</xsl:variable>
		
		<xsl:choose>
			<xsl:when test="$last_digit = 1 and $last_two_digits != 11">
				<xsl:value-of select="$nominative"/>
			</xsl:when>
			<xsl:when test="$last_digit = 2 and $last_two_digits != 12     or     $last_digit = 3 and $last_two_digits != 13     or     $last_digit = 4 and $last_two_digits != 14">
				<xsl:value-of select="$genitive_singular"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="$genitive_plural"/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
</xsl:stylesheet>