<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * XSLT driver
 *
 * http://www.php.net/manual/en/class.domdocument.php
 *
 * @package HostCMS 6\Xsl
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2013 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Xsl_Processor_Xslt extends Xsl_Processor
{
	/**
	 * Execute processor
	 * @return mixed
	 * @hostcms-event Xsl_Processor.onBeforeProcess
	 * @hostcms-event Xsl_Processor.onAfterProcess
	 */
	public function process()
	{
		Core_Event::notify('Xsl_Processor.onBeforeProcess', $this);

		$return = NULL;

		// Load the xml file and stylesheet as domdocuments
		$xsl = new DomDocument();

		$sXsl = $this->_xsl->loadXslFile();

		if (!Core::checkPanel())
		{
			$sXsl = $this->_clearXmlns($sXsl);
		}

		if ($xsl->loadXML($sXsl))
		{
			$inputdom = new DomDocument();

			if ($inputdom->loadXML($this->_xml))
			{
				// Create the processor and import the stylesheet
				$XsltProcessor = new XsltProcessor();

				$XsltProcessor->registerPHPFunctions();
				$XsltProcessor->importStylesheet($xsl);
				$XsltProcessor->setParameter(NULL, "titles", "Titles");

				// Transform and output the xml document
				$newdom = $XsltProcessor->transformToDoc($inputdom);

				if ($newdom)
				{
					$newdom->formatOutput = Core_Array::get(
						Core::$config->get('xsl_config'), 'formatOutput', TRUE
					);
					$content = $newdom->saveXML();

					$return = $this->_deleteXmlHeader($content);
				}
			}
		}

		Core_Event::notify('Xsl_Processor.onAfterProcess', $this);

		return $return;
	}
}
