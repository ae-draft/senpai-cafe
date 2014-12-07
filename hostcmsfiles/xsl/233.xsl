<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE xsl:stylesheet>
<xsl:stylesheet version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:hostcms="http://www.hostcms.ru/"
	exclude-result-prefixes="hostcms">
	<xsl:output xmlns="http://www.w3.org/TR/xhtml1/strict" doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN" encoding="utf-8" indent="yes" method="html" omit-xml-declaration="no" version="1.0" media-type="text/xml"/>
	
	<!-- МагазинГруппыТоваровНаГлавной -->
	
	<xsl:template match="/">
		<xsl:apply-templates select="/shop"/>
	</xsl:template>
	
	<!-- Шаблон для магазина -->
	<xsl:template match="/shop">
		<div id="scroll_menu_title">МЕНЮ</div>
		<div class="shop_list">
            <div class="shop_list-container">
                <xsl:apply-templates select="shop_group"/>
            </div>
		</div>
	</xsl:template>
	
	<!-- Шаблон для групп товара -->
	<xsl:template match="shop_group">
		<xsl:choose>
			<xsl:when test="position() != last()">
				<div class="on_main_menu_elem">
					<a href="{url}" hostcms:id="{@id}" hostcms:field="name" hostcms:entity="shop_group">
						<div class="image">
							<img src="{dir}{image_small}" />
							<hr />
						</div>
						<div class="name">
							<xsl:value-of disable-output-escaping="yes" select="name"/>
						</div>
					</a>
					
					<!-- Если есть подгруппы -->
					<!-- <xsl:if test="shop_group">
						<ul class="left_menu gray_link gray" id="{@id}" style="display: none;">
							<xsl:apply-templates select="shop_group"/>
						</ul>
					</xsl:if> -->
				</div>
			</xsl:when>
			<xsl:otherwise>
				<div class="on_main_menu_elem on_main_menu_elem_last">
					<a href="{url}" hostcms:id="{@id}" hostcms:field="name" hostcms:entity="shop_group">
						<div class="image">
							<img src="{dir}{image_small}" />
							<hr />
						</div>
						<div class="name">
							<xsl:value-of disable-output-escaping="yes" select="name"/>
						</div>
						
					</a>
					
					<!-- Если есть подгруппы -->
					<!-- <xsl:if test="shop_group">
						<ul class="left_menu gray_link gray" id="{@id}" style="display: none;">
							<xsl:apply-templates select="shop_group"/>
						</ul>
					</xsl:if> -->
				</div>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
</xsl:stylesheet>