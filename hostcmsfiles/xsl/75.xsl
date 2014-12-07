<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE xsl:stylesheet>
<xsl:stylesheet version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:hostcms="http://www.hostcms.ru/"
	exclude-result-prefixes="hostcms">
	<xsl:output xmlns="http://www.w3.org/TR/xhtml1/strict" doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN" encoding="utf-8" indent="yes" method="html" omit-xml-declaration="no" version="1.0" media-type="text/xml"/>
	
	<xsl:decimal-format name="my" decimal-separator="." grouping-separator=" "/>
	
	<xsl:template match="/shop">
		<!-- Строка шага заказа -->
		<ul class="shop_navigation">
		<li><span>Адрес доставки</span>→</li>
		<li><span>Способ доставки</span>→</li>
		<li><span>Форма оплаты</span>→</li>
		<li class="shop_navigation_current"><span>Данные доставки</span></li>
		</ul>
		
		<h3>Ваш заказ оформлен</h3>
		
		<!--<p>Через некоторое время с Вами свяжется наш менеджер, чтобы согласовать заказанный товар и время доставки.</p>-->
		
		<xsl:apply-templates select="shop_order"/>
		
		<xsl:choose>
			<xsl:when test="count(shop_order/shop_order_item)">
				<h4>Заказанные товары</h4>

                <div class="container shop-cart-success panel panel-warning">
                    <div class="row">
                        <div class="col-md-3">
                            Наименование:
                        </div>
                        <div class="col-md-2">
                            Количество
                        </div>
                        <div class="col-md-3">
                            Цена
                        </div>
                        <div class="col-md-2">
                            Сумма
                        </div>
                    </div>
                    <xsl:apply-templates select="shop_order/shop_order_item"/>
                    <div class="row">
                        <div class="col-md-1"></div>
                        <div class="col-md-3">Итого:</div>
                        <div class="col-md-8"><xsl:value-of select="format-number(shop_order/total_amount,'### ##0.00', 'my')"/><xsl:text> </xsl:text><xsl:value-of select="/shop/shop_order/shop_currency/name"/></div>
                    </div>
                </div>
			</xsl:when>
			<xsl:otherwise>
			<p><b>Заказанных товаров нет.</b></p>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	
	<!-- Шаблон вывода данных о заказе -->
	<xsl:template match="shop_order">

        <div class="container shop-cart-success-delivery panel panel-warning">
            <div class="row">
                <div class="col-md-12">
                    <h4>Данные доставки</h4>
                </div>
            </div>
            <div class="row">
                <div class="col-md-2">
                    ФИО:
                </div>
                <div class="col-md-2">
                    <xsl:value-of select="surname"/><xsl:text> </xsl:text><xsl:value-of select="name"/><xsl:text> </xsl:text><xsl:value-of select="patronymic"/>
                </div>
            </div>
            <div class="row">
                <div class="col-md-2">
                    Email:
                </div>
                <div class="col-md-2">
                    <xsl:value-of select="email"/>
                </div>
            </div>
            <xsl:if test="phone != ''">
            <div class="row">
                <div class="col-md-2">
                    Телефон:
                </div>
                <div class="col-md-2">
                    <xsl:value-of select="phone"/>
                </div>
            </div>
            </xsl:if>
            <div class="row">
                <div class="col-md-2">
                    Адрес доставки:
                </div>
                <div class="col-md-2">
                    <xsl:variable name="location">, <xsl:value-of select="shop_country/shop_country_location/name"/></xsl:variable>
                    <xsl:variable name="city">, <xsl:value-of select="shop_country/shop_country_location/shop_country_location_city/name"/></xsl:variable>
                    <xsl:variable name="city_area">, <xsl:value-of select="shop_country/shop_country_location/shop_country_location_city/shop_country_location_city_area/name"/></xsl:variable>
                    <xsl:variable name="adres">, <xsl:value-of select="address"/></xsl:variable>

                    <xsl:if test="postcode != ''"><xsl:value-of select="postcode"/>, </xsl:if>
                    <xsl:if test="shop_country/name != ''">
                        <xsl:value-of select="shop_country/name"/>
                    </xsl:if>
                    <xsl:if test="$location != ', '">
                        <xsl:value-of select="$location"/>
                    </xsl:if>
                    <xsl:if test="$city != ', '">
                        <xsl:value-of select="$city"/>
                    </xsl:if>
                    <xsl:if test="$city_area != ', '">
                        <xsl:value-of select="$city_area"/>&#xA0;район</xsl:if>
                    <xsl:if test="$adres != ', '">
                        <xsl:value-of select="$adres"/>
                    </xsl:if>
                </div>
            </div>
            <xsl:if test="shop_delivery/name != ''">
            <div class="row">
                <div class="col-md-2">
                    Тип доставки:
                </div>
                <div class="col-md-2">
                    <xsl:value-of select="shop_delivery/name"/>
                </div>
            </div>
            </xsl:if>
            <xsl:if test="shop_payment_system/name != ''">
            <div class="row">
                <div class="col-md-2">
                    Способ оплаты:
                </div>
                <div class="col-md-2">
                    <xsl:value-of select="shop_payment_system/name"/>
                </div>
            </div>
            </xsl:if>
        </div>
	</xsl:template>
	
	<!-- Данные о товарах -->
	<xsl:template match="shop_order/shop_order_item">
        <div class="row">
            <div class="col-md-3">
                <xsl:choose>
                    <xsl:when test="shop_item/url != ''">
                        <a href="http://{/shop/site/site_alias/name}{shop_item/url}">
                            <xsl:value-of disable-output-escaping="yes" select="name"/>
                        </a>
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:value-of disable-output-escaping="yes" select="name"/>
                    </xsl:otherwise>
                </xsl:choose>
            </div>
            <div class="col-md-2">
                <xsl:value-of select="quantity"/><xsl:text> </xsl:text><xsl:value-of select="shop_item/shop_measure/name"/>
            </div>
            <div class="col-md-3">
                <xsl:value-of select="format-number(price,'### ##0.00', 'my')"/><xsl:text> </xsl:text><xsl:value-of select="/shop/shop_order/shop_currency/name" disable-output-escaping="yes" />
            </div>
            <div class="col-md-4">
                <xsl:value-of select="format-number(quantity * price,'### ##0.00', 'my')"/><xsl:text> </xsl:text><xsl:value-of select="/shop/shop_order/shop_currency/name" disable-output-escaping="yes" />
            </div>
        </div>
	</xsl:template>
</xsl:stylesheet>