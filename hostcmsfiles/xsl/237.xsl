<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE xsl:stylesheet>
<xsl:stylesheet version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:hostcms="http://www.hostcms.ru/"
	exclude-result-prefixes="hostcms">
	<xsl:output xmlns="http://www.w3.org/TR/xhtml1/strict" doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN" encoding="utf-8" indent="yes" method="html" omit-xml-declaration="no" version="1.0" media-type="text/xml"/>
	
	<xsl:template match="/site">
		<div class="nav-justified">
			<!-- Выбираем узлы структуры первого уровня -->
			<xsl:apply-templates select="structure[show=1]" />
		</div>
	</xsl:template>
	
	<!-- Запишем в константу ID структуры, данные для которой будут выводиться пользователю -->
	<xsl:variable name="current_structure_id" select="/site/current_structure_id"/>
	
	<xsl:template match="structure">
			<!-- Определяем адрес ссылки -->
			<xsl:variable name="link">
				<xsl:choose>
					<!-- Если внешняя ссылка -->
					<xsl:when test="url != ''">
						<xsl:value-of disable-output-escaping="yes" select="url"/>
					</xsl:when>
					<!-- Иначе если внутренняя ссылка -->
					<xsl:otherwise>
						<xsl:value-of disable-output-escaping="yes" select="link"/>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:variable>
			
			<!-- Ссылка на пункт меню -->
			<xsl:choose>
				<!-- Если внешняя ссылка -->
				<xsl:when test="name != 'Меню'">
					<a href="{$link}" title="{name}" hostcms:id="{@id}" hostcms:field="name" hostcms:entity="structure">
						<xsl:if test="$current_structure_id = @id or count(.//structure[@id=$current_structure_id]) = 1">
							<xsl:attribute name="class">current</xsl:attribute>
						</xsl:if>
						<xsl:value-of disable-output-escaping="yes" select="name"/>
					</a>
				</xsl:when>
				<!-- Иначе если внутренняя ссылка -->
				<xsl:otherwise>
					<a href="{$link}602" title="{name}" hostcms:id="{@id}" hostcms:field="name" hostcms:entity="structure">
						<xsl:if test="$current_structure_id = @id or count(.//structure[@id=$current_structure_id]) = 1">
							<xsl:attribute name="class">current</xsl:attribute>
						</xsl:if>
						<xsl:value-of disable-output-escaping="yes" select="name"/>
					</a>
				</xsl:otherwise>
			</xsl:choose>
	</xsl:template>
</xsl:stylesheet>