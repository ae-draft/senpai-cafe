<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE xsl:stylesheet>
<xsl:stylesheet version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:hostcms="http://www.hostcms.ru/"
	exclude-result-prefixes="hostcms">
	<xsl:output xmlns="http://www.w3.org/TR/xhtml1/strict" doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN" encoding="utf-8" indent="yes" method="html" omit-xml-declaration="no" version="1.0" media-type="text/xml"/>
	
	<xsl:decimal-format name="my" decimal-separator="," grouping-separator=" "/>
	
	<!-- Шаблон для типов доставки -->
	<xsl:template match="/shop">
		
		<ol class="breadcrumb">
			<li>Адрес доставки</li>
			<li class="shop_navigation_current">Способ доставки</li>
			<li>Форма оплаты</li>
			<li>Данные доставки</li>
		</ol>
		
		<div class="cart-panel container-fluid" style="background: url('/assets/img/template/back-kontakt.png') repeat left top;">
			<form method="post">
				<!-- Проверяем количество способов доставки -->
				<xsl:choose>
					<xsl:when test="count(shop_delivery) = 0">
						<div class="alert alert-warning" role="alert">
							<p>По выбранным Вами условиям доставка не возможна, заказ будет оформлен без доставки.</p>
							<p>Уточнить данные о доставке Вы можете, связавшись с представителем нашей компании.</p>
						</div>
						<input type="hidden" name="shop_delivery_condition_id" value="0"/>
					</xsl:when>
					<xsl:otherwise>
						<table class="table table-condensed">
							<thead>
								<tr>
									<th>Способ доставки</th>
									<th>Описание</th>
									<th>Цена доставки</th>
									<th>Стоимость товаров</th>
									<th>Итого</th>
								</tr>
							</thead>
							<tbody>
								<xsl:apply-templates select="shop_delivery"/>
							</tbody>
						</table>
					</xsl:otherwise>
				</xsl:choose>
				
				<input name="step" value="3" type="hidden" />
				<input value="Далее →" type="submit" class="btn btn-default btn-sm" />
			</form>
		</div>
	</xsl:template>
	
	<xsl:template match="shop_delivery">
		<tr>
			<td>
				<label>
					<input type="radio" value="{shop_delivery_condition/@id}" name="shop_delivery_condition_id">
						<xsl:if test="position() = 1">
							<xsl:attribute name="checked">checked</xsl:attribute>
						</xsl:if>
					</input>
					<xsl:text> </xsl:text>
					<span class="caption"><xsl:value-of select="name"/></span>
				</label>
			</td>
			<td>
				<!--<xsl:value-of disable-output-escaping="yes" select="description"/>-->
				<xsl:choose>
					<xsl:when test="normalize-space(shop_delivery_condition/description) !=''">
						<xsl:value-of disable-output-escaping="yes" select="concat(description,' (',shop_delivery_condition/description,')')"/>
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of disable-output-escaping="yes" select="description"/>
					</xsl:otherwise>
				</xsl:choose>
			</td>
			<td>
			<xsl:value-of select="format-number(shop_delivery_condition/price, '### ##0,00', 'my')"/><xsl:text> </xsl:text><xsl:value-of select="/shop/shop_currency/name"/></td>
			<td>
				<xsl:value-of select="format-number(/shop/total_amount, '### ##0,00', 'my')"/><xsl:text> </xsl:text><xsl:value-of select="/shop/shop_currency/name"/>
			</td>
			<td class="total">
				<xsl:value-of select="format-number(/shop/total_amount + shop_delivery_condition/price, '### ##0,00', 'my')"/><xsl:text> </xsl:text><xsl:value-of select="/shop/shop_currency/name"/>
			</td>
		</tr>
	</xsl:template>
</xsl:stylesheet>