<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE xsl:stylesheet>
<xsl:stylesheet version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:hostcms="http://www.hostcms.ru/"
	exclude-result-prefixes="hostcms">
	<xsl:output xmlns="http://www.w3.org/TR/xhtml1/strict" doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN" encoding="utf-8" indent="yes" method="html" omit-xml-declaration="no" version="1.0" media-type="text/xml" />
	
	<!-- МагазинАдресДоставки -->
	
	<xsl:template match="/shop">
		<ul class="shop_navigation">
		<li class="shop_navigation_current"><span>Адрес доставки</span>→</li>
		<li><span>Способ доставки</span>→</li>
		<li><span>Форма оплаты</span>→</li>
		<li><span>Данные доставки</span></li>
		</ul>

        <div class="panel panel-default cart-panel" style="background: url('/assets/img/template/back-kontakt.png') repeat left top;">
            <div class="panel-body">
                <div class="pull-left">
                    <form method="POST" class="delivery-address">
                <div class="comment shop_address">
                    <div class="row hide">
                        <div class="caption">Страна:</div>
                        <div class="field">
                            <select id="shop_country_id" name="shop_country_id" onchange="$.loadLocations('{/shop/url}cart/', $(this).val())">
                                <option value="175">Россия</option>
                                <xsl:apply-templates select="shop_country" />
                            </select>
                            <span class="redSup"> *</span>
                        </div>
                    </div>

                    <div class="row hide">
                        <div class="caption">Область:</div>
                        <div class="field">
                            <select name="shop_country_location_id" id="shop_country_location_id" onchange="$.loadCities('{/shop/url}cart/', $(this).val())">
                                <option value="52">…</option>
                                <xsl:apply-templates select="shop_country_location" />
                            </select>
                            <span class="redSup"> *</span>
                        </div>
                    </div>
                    <div class="row hide">
                        <div class="caption">Город:</div>
                        <div class="field">
                            <select name="shop_country_location_city_id" id="shop_country_location_city_id" onchange="$.loadCityAreas('{/shop/url}cart/', $(this).val())">
                                <option value="1789">Саратов</option>
                                <xsl:apply-templates select="shop_country_location_city" />
                            </select>
                        </div>
                    </div>
                    <div class="row hide">
                        <div class="caption">Район города:</div>
                        <div class="field">
                            <select name="shop_country_location_city_area_id" id="shop_country_location_city_area_id">
                                <option value="0">…</option>
                            </select>
                        </div>
                    </div>
                    <div class="row hide">
                        <div class="caption">Индекс:</div>
                        <div class="field">
                            <input type="text" size="15" class="width1" name="postcode" value="{/shop/siteuser/postcode}" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label for="address">Улица, дом, квартира:</label>
                            <input type="text" size="30" name="address" value="{/shop/siteuser/address}" class="width2 form-control" id="address" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-4" style="padding-left: 0;">
                            <label for="surname">Фамилия</label>
                            <input type="text" size="15" class="width1 form-control" id="surname" name="surname" value="{/shop/siteuser/surname}" />
                        </div>
                        <div class="col-xs-4">
                            <label for="name">Имя</label>
                            <input type="text" size="15" class="width1 form-control" name="name" id="name" value="{/shop/siteuser/name}" />
                        </div>
                        <div class="col-xs-4">
                            <label for="patronymic">Отчество</label>
                            <input type="text" size="15" class="width1 form-control" name="patronymic" id="patronymic" value="{/shop/siteuser/patronymic}" />
                        </div>
                    </div>
                    <div class="row hide">
                        <div class="caption">Компания:</div>
                        <div class="field">
                            <input type="text" size="30" name="company" value="{/shop/siteuser/company}" class="width2 form-control" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label for="phone">Телефон:</label>
                            <input type="text" size="30" name="phone" value="{/shop/siteuser/phone}" class="width2 form-control" id="phone" />
                        </div>
                    </div>
                    <div class="row hide">
                        <div class="caption">Факс:</div>
                        <div class="field">
                            <input type="text" size="30" name="fax" value="{/shop/siteuser/fax}" class="width2 form-control" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label for="email">E-mail:</label>
                            <input type="text" size="30" name="email" value="{/shop/siteuser/email}" class="width2 form-control" id="email" />
                        </div>
                    </div>

                    <!-- Дополнительные свойства заказа -->
                    <xsl:if test="count(shop_order_properties//property[display != 0 and (type != 2 )])">
                        <xsl:apply-templates select="shop_order_properties//property[display != 0 and (type != 2 )]" mode="propertyList"/>
                    </xsl:if>

                    <div class="row">
                        <div class="form-group">
                            <label for="description">Комментарий:</label>
                            <textarea rows="3" name="description" class="width2 form-control" id="description"></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="caption"></div>
                        <div class="field">
                            <input name="step" value="2" type="hidden" />
                            <input value="Далее →" type="submit" class="btn btn-default" />
                        </div>
                    </div>
                </div>
            </form>
                </div>
                <div class="pull-right">
                    <div class="panel panel-default" style="margin-top:25px;">
                        <div class="panel-heading">
                            <h3 class="panel-title">Обратите внимание!</h3>
                        </div>
                        <div class="panel-body" style="color:black;">
                            <p>
                                - Для оформления заказа необходимо указать номер контактного телефона.
                            </p>
                            <p>
                                - Доставка бесплатна при сумме заказа от 500р.
                            </p>
                            <p>
                                - Доставка осуществляется только по г.Саратов.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
		

	</xsl:template>
	
	<!-- Шаблон для фильтра дополнительных свойств заказа -->
	<xsl:template match="property" mode="propertyList">
		<xsl:variable name="nodename">property_<xsl:value-of select="@id"/></xsl:variable>
		
		<div class="row">
			<div class="caption">
				<xsl:if test="display != 5">
					<xsl:value-of disable-output-escaping="yes" select="name"/>:
				</xsl:if>
			</div>
			<div class="field">
				<xsl:choose>
					<!-- Отображаем поле ввода -->
					<xsl:when test="display = 1">
						<input type="text" size="30" name="property_{@id}" class="width2">
							<xsl:choose>
								<xsl:when test="/shop/*[name()=$nodename] != ''">
									<xsl:attribute name="value"><xsl:value-of select="/shop/*[name()=$nodename]"/></xsl:attribute>
								</xsl:when>
							<xsl:otherwise><xsl:attribute name="value"><xsl:value-of select="default_value"/></xsl:attribute></xsl:otherwise>
							</xsl:choose>
						</input>
					</xsl:when>
					<!-- Отображаем список -->
					<xsl:when test="display = 2">
						<select name="property_{@id}">
							<option value="0">...</option>
							<xsl:apply-templates select="list/list_item"/>
						</select>
					</xsl:when>
					<!-- Отображаем переключатели -->
					<xsl:when test="display = 3">
						<div class="propertyInput">
							<input type="radio" name="property_{@id}" value="0" id="id_prop_radio_{@id}_0"></input>
							<label for="id_prop_radio_{@id}_0">Любой вариант</label>
							<xsl:apply-templates select="list/list_item"/>
						</div>
					</xsl:when>
					<!-- Отображаем флажки -->
					<xsl:when test="display = 4">
						<div class="propertyInput">
							<xsl:apply-templates select="list/list_item"/>
						</div>
					</xsl:when>
					<!-- Отображаем флажок -->
					<xsl:when test="display = 5">
						<input type="checkbox" name="property_{@id}" id="property_{@id}" style="padding-top:4px">
							<xsl:if test="/shop/*[name()=$nodename] != ''">
								<xsl:attribute name="checked"><xsl:value-of select="/shop/*[name()=$nodename]"/></xsl:attribute>
							</xsl:if>
						</input>
						<label for="property_{@id}">
							<xsl:value-of disable-output-escaping="yes" select="name"/><xsl:text> </xsl:text>
						</label>
					</xsl:when>
					<!-- Отображаем список с множественным выбором-->
					<xsl:when test="display = 7">
						<select name="property_{@id}[]" multiple="multiple">
							<xsl:apply-templates select="list/list_item"/>
						</select>
					</xsl:when>
					<!-- Отображаем большое текстовое поле -->
					<xsl:when test="display = 8">
						<textarea type="text" size="30" rows="5" name="property_{@id}" class="width2">
							<xsl:choose>
								<xsl:when test="/shop/*[name()=$nodename] != ''">
									<xsl:value-of select="/shop/*[name()=$nodename]"/>
								</xsl:when>
								<xsl:otherwise><xsl:value-of select="default_value"/></xsl:otherwise>
							</xsl:choose>
						</textarea>
					</xsl:when>
				</xsl:choose>
			</div>
		</div>
	</xsl:template>
	
	<xsl:template match="list/list_item">
		<xsl:if test="../../display = 2">
			<!-- Отображаем список -->
			<xsl:variable name="nodename">property_<xsl:value-of select="../../@id"/></xsl:variable>
			<option value="{@id}">
			<xsl:if test="/shop/*[name()=$nodename] = @id"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>
				<xsl:value-of disable-output-escaping="yes" select="value"/>
			</option>
		</xsl:if>
		<xsl:if test="../../display = 3">
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
		<xsl:if test="../../display = 4">
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
		<xsl:if test="../../display = 7">
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
	
	<xsl:template match="shop_country">
		<option value="{@id}">
			<xsl:if test="/shop/current_shop_country_id = @id or not(/shop/current_shop_country_id/node()) and /shop/shop_country_id = @id">
				<xsl:attribute name="selected">selected</xsl:attribute>
			</xsl:if>
			<xsl:value-of disable-output-escaping="yes" select="name" />
		</option>
	</xsl:template>
	
	<xsl:template match="shop_country_location">
		<option value="{@id}">
			<xsl:if test="/shop/current_shop_country_location_id = @id">
				<xsl:attribute name="selected">selected</xsl:attribute>
			</xsl:if>
			<xsl:value-of disable-output-escaping="yes" select="name" />
		</option>
	</xsl:template>
	
	<xsl:template match="shop_country_location_city">
		<option value="{@id}">
			<xsl:if test="/shop/current_shop_country_location_city_id = @id">
				<xsl:attribute name="selected">selected</xsl:attribute>
			</xsl:if>
			<xsl:value-of disable-output-escaping="yes" select="name" />
		</option>
	</xsl:template>
</xsl:stylesheet>