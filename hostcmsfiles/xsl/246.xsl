<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE xsl:stylesheet>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:exsl="http://exslt.org/common" extension-element-prefixes="exsl">
	<xsl:output xmlns="http://www.w3.org/TR/xhtml1/strict" doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN" encoding="utf-8" indent="yes" method="html" omit-xml-declaration="no" version="1.0" media-type="text/xml"/>
	
	<xsl:decimal-format name="my" decimal-separator="," grouping-separator=" "/>
	
	<!-- Шаблон для корзины -->
	<xsl:template match="/shop">
		<xsl:choose>
			<xsl:when test="count(shop_cart) = 0">
				
				<h3>В корзине нет ни одного товара.</h3>
				<p>
					<xsl:choose>
						<!-- Пользователь авторизован или модуль пользователей сайта отсутствует -->
						<xsl:when test="siteuser_id > 0 or siteuser_id = 0">Для оформления заказа добавьте товар в корзину.</xsl:when>
						<xsl:otherwise>Вы не авторизированы. Если Вы зарегистрированный пользователь, данные Вашей корзины станут видны после авторизации.</xsl:otherwise>
					</xsl:choose>
				</p>
			</xsl:when>
			<xsl:otherwise>
				<form action="{/shop/url}cart/" method="post">
					<div class="cart-panel" style="background: url('/assets/img/template/back-kontakt.png') repeat left top;">
						<!-- Если есть товары -->
						<xsl:if test="count(shop_cart[postpone = 0]) > 0">
							<div class="container shop-cart-container" style="width:100%;">
								<div class="table-responsive">
									<table class="table table-condensed">
										<thead>
											<th>Товар</th>
											<th>Кол-во</th>
											<th>Цена</th>
											<th>Сумма</th>
											<th>Действие</th>
										</thead>
										<tbody>
											
											<xsl:apply-templates select="shop_cart[postpone = 0]"/>
											
											<!-- Скидки -->
											<xsl:if test="count(shop_purchase_discount)">
												<xsl:apply-templates select="shop_purchase_discount"/>
												<tr class="total">
													<td>Всего:</td>
													<td></td>
													<td></td>
													<td>
														<xsl:value-of select="format-number(total_amount, '### ##0,00', 'my')"/><xsl:text> </xsl:text><xsl:value-of disable-output-escaping="yes" select="/shop/shop_currency/name"/>
													</td>
													<td></td>
													<xsl:if test="count(/shop/shop_warehouse)">
														<td></td>
													</xsl:if>
													<td></td>
													<td></td>
												</tr>
											</xsl:if>
										</tbody>
									</table>
									
									<xsl:call-template name="tableFooter">
										<xsl:with-param name="nodes" select="shop_cart[postpone = 0]"/>
									</xsl:call-template>
								</div>
							</div>
						</xsl:if>
						
						<!-- Купон -->
						<!--<div class="shop_coupon">
							Купон: <input name="coupon_text" type="text" value="{coupon_text}"/>
						</div>-->
						
						<!-- Если есть отложенные товары -->
						<!--<xsl:if test="count(shop_cart[postpone = 1]) > 0">
							<div class="transparent">
								<h2>Отложенные товары</h2>
								<table class="shop_cart">
									<xsl:call-template name="tableHeader"/>
									<xsl:apply-templates select="shop_cart[postpone = 1]"/>
									<xsl:call-template name="tableFooter">
										<xsl:with-param name="nodes" select="shop_cart[postpone = 1]"/>
									</xsl:call-template>
								</table>
							</div>
						</xsl:if>-->
						
						<!-- Кнопки -->
						<div class="shop-cart-control">
							<input name="recount" value="Пересчитать" type="submit" class="btn btn-default shop-cart-recalculate btn-sm" />
							
							<!-- Пользователь авторизован или модуль пользователей сайта отсутствует -->
							<xsl:if test="count(shop_cart[postpone = 0]) and (siteuser_id > 0 or siteuser_exists = 0)">
								<input name="step" value="1" type="hidden" />
								<input value="Оформить заказ" type="submit" class="btn btn-default shop-cart-apply btn-sm"/>
							</xsl:if>
						</div>
					</div>
				</form>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	
	<!-- Заголовок таблицы -->
	<xsl:template name="tableHeader">
		<tr>
			<th>Товар</th>
			<th width="70">Кол-во</th>
			<th width="110">Цена</th>
			<th width="150">Сумма</th>
			<xsl:if test="count(/shop/shop_warehouse)">
				<th width="100">Склад</th>
			</xsl:if>
			<th>Отложить</th>
			<th>Действия</th>
		</tr>
	</xsl:template>
	
	<!-- Итоговая строка таблицы -->
	<xsl:template name="tableFooter">
		<xsl:param name="nodes"/>
		<div class="container-fluid">
			<div class="row shop-cart-footer">
				<div class="col-md-2 col-sm-2 col-xs-12">
					Итого: <xsl:value-of disable-output-escaping="yes" select="sum($nodes/quantity)"/> позиция(и/й)
				</div>
				<div class="col-md-10 col-sm-10 col-xs-12">
					На сумму:
					<xsl:variable name="subTotals">
						<xsl:for-each select="$nodes">
							<sum><xsl:value-of select="shop_item/price * quantity"/></sum>
						</xsl:for-each>
					</xsl:variable>
					
					<xsl:value-of select="format-number(sum(exsl:node-set($subTotals)/sum), '### ##0,00', 'my')"/><xsl:text> </xsl:text><xsl:value-of disable-output-escaping="yes" select="/shop/shop_currency/name"/>
				</div>
			</div>
		</div>
	</xsl:template>
	
	<!-- Шаблон для товара в корзине -->
	<xsl:template match="shop_cart">
		<tr>
			<td>
				<a href="{shop_item/url}" class="shop-cart-itemname">
					<xsl:value-of disable-output-escaping="yes" select="shop_item/name"/>
				</a>
			</td>
			<td>
				<input type="text" size="3" name="quantity_{shop_item/@id}" id="quantity_{shop_item/@id}" value="{quantity}" class="shop-cart-itemcount form-control input-sm"/>
			</td>
			<td>
				<!-- Цена -->
				<div class="shop-cart-itemprice">
					<xsl:value-of select="format-number(shop_item/price, '### ##0,00', 'my')"/><xsl:text> </xsl:text><xsl:value-of select="shop_item/currency" disable-output-escaping="yes"/>
				</div>
			</td>
			<td>
				<div class="shop-cart-itemtotalprice">
					<!-- Сумма -->
					<xsl:value-of disable-output-escaping="yes" select="format-number(shop_item/price * quantity, '### ##0,00', 'my')"/><xsl:text> </xsl:text><xsl:value-of disable-output-escaping="yes" select="shop_item/currency"/>
				</div>
			</td>
			<td>
				<a href="?delete={shop_item/@id}" onclick="return confirm('Вы уверены, что хотите удалить?')" title="Удалить товар из корзины" alt="Удалить товар из корзины" class="glyphicon glyphicon-trash shop-cart-itemdelete" style="display: inline;"></a>
			</td>
		</tr>
	</xsl:template>
	
	<!-- Шаблон для скидки от суммы заказа -->
	<xsl:template match="shop_purchase_discount">
		<tr>
			<td>
				<xsl:value-of disable-output-escaping="yes" select="name"/>
			</td>
			<td></td>
			<td></td>
			<td>
				<!-- Сумма -->
				<xsl:value-of select="format-number(discount_amount * -1, '### ##0,00', 'my')"/><xsl:text> </xsl:text><xsl:value-of select="/shop/shop_currency/name" disable-output-escaping="yes"/>
			</td>
			<xsl:if test="count(/shop/shop_warehouse)">
				<td></td>
			</xsl:if>
			<td></td>
			<td></td>
		</tr>
	</xsl:template>
	
	<!-- option для склада -->
	<xsl:template match="shop_warehouse_item">
		<xsl:if test="count != 0">
			<xsl:variable name="shop_warehouse_id" select="shop_warehouse_id" />
			<option value="{$shop_warehouse_id}">
				<xsl:if test="../../shop_warehouse_id = $shop_warehouse_id">
					<xsl:attribute name="selected">selected</xsl:attribute>
				</xsl:if>
				<xsl:value-of select="/shop/shop_warehouse[@id=$shop_warehouse_id]/name"/> (<xsl:value-of select="count"/>)
			</option>
		</xsl:if>
	</xsl:template>
	
</xsl:stylesheet>