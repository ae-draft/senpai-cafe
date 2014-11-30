<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Sites.
 *
 * @package HostCMS 6\Site
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2014 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Site_Controller_addSiteWithTemplate extends Admin_Form_Action_Controller_Type_Edit{	/**
	 * Operation name
	 * @val string
	 */
	protected $_formOperation = NULL;

	/**
	 * Executes the business logic.
	 * @param mixed $operation Operation for action
	 * @return boolean
	 * @hostcms-event Site_Controller_addSiteWithTemplate.onBeforeExecute
	 * @hostcms-event Site_Controller_addSiteWithTemplate.onAfterExecute
	 */
	public function execute($operation = NULL)
	{
		Core_Event::notify(get_class($this) . '.onBeforeExecute', $this, array($operation));

		try
		{
			$return = NULL;

			$Site_Controller_Template = Site_Controller_Template::instance()
				->templatePath(CMS_FOLDER . TMP_DIR)
				->chmodFile(CHMOD_FILE)
				//->server('http://hostcms')
				;

			switch ($operation)
			{
				case NULL: // Показ формы
					// Операция для кнопки
					$this->_formOperation = 'chooseTemplate';

					$oMainTab = Admin_Form_Entity::factory('Tab')
						->caption('Main')
						->name('main');

					$this->addTab($oMainTab);

					$this->title(Core::_('Site.choose_site_template'));

					$this->_Admin_Form_Controller->title(
						$this->title
					);

					ob_start();

					$oXml = $Site_Controller_Template->loadTemplatesXml();

					?><div id="gallery"><?php
					$aGroups = $oXml->children();

					$iKeyGroup = 0;
					// цикл по дереву group
					foreach ($aGroups as $oGroup)
					{
						// если в группе есть шаблоны
						if ($oGroup->templates)
						{
							$iKeyTemplate = 0;
							// цикл по дереву template
							foreach ($oGroup->templates->template as $oTemplate)
							{
								if (strlen(trim((string)$oTemplate->template_file6)) && (int)$oTemplate['id'] != 2782)
								{
									$small_width = (string)$oTemplate->template_preview['width'];
									$small_height = 200 + 40;
									// вывод информации о шаблоне
									?>
									<div class="site_template_block">
									<div class="bord_img" style="width: <?php echo $small_width?>px; height:<?php echo $small_height?>px;">

										<a href="<?php echo $Site_Controller_Template->server . trim((string)$oTemplate->template_full_preview)?>" target="_blank">
										<img src="<?php echo $Site_Controller_Template->server . trim((string)$oTemplate->template_preview)?>" width="<?php echo $small_width?>" />
										</a>

										<div class="bottom">
											<input style="float: left" type="radio" <?php if ($iKeyTemplate == 0 && $iKeyGroup == 0) {echo 'checked="checked"';} ?> name="template_id" id="template_<?php echo (string)$oTemplate['id']?>" value="<?php echo (string)$oTemplate['id']?>" />

											<label for="template_<?php echo (string)$oTemplate['id']?>" style="margin-left: 5px; float: left">
												<b><?php echo (string)$oTemplate->template_name?></b>
												<br />
												<span><?php echo (string)$oGroup->group_name?></span>
											</label>
										</div>
									</div>
									</div>
									<?php
								}
								$iKeyTemplate++;
							}
						}

						$iKeyGroup++;

						// Для HostCMS-6 доступен только основной макет
						//break;
					}
					?>
					</div>
					<div style="clear: both"></div>
					<?php

					$oMainTab->add(
						Admin_Form_Entity::factory('Code')->html(
							ob_get_clean()
						)
					);

					$return = $this->_showEditForm();
				break;
				case 'chooseTemplate':
					// Операция для кнопки
					$this->_formOperation = 'chooseTemplateType';

					$oMainTab = Admin_Form_Entity::factory('Tab')
						->caption('Main')
						->name('main');

					$this->addTab($oMainTab);

					$this->title(Core::_('Site.choose_color_scheme'));

					$this->_Admin_Form_Controller->title(
						$this->title
					);

					ob_start();

					$oXml = $Site_Controller_Template->loadTemplatesXml();

					$iTemplateId = intval(Core_Array::getPost('template_id'));
					$Templates = $oXml->xpath("//group/templates/template[@id='{$iTemplateId}']");

					if (isset($Templates[0]))
					{
						// у выбранного шаблона есть цветовые схемы
						if (count($Templates[0]->template_types->template_type) > 0)
						{
							?>
							<div id="gallery">
							<?php
							// цикл по цветовым схемам
							foreach ($Templates[0]->template_types as $iKeyTemplateType => $oTemplateType)
							{
								$small_width = (string)$oTemplateType->template_type_full_preview['width'];
								$small_height = 200 + 40;

								?><div class="bord_img" style="width: <?php echo $small_width?>px; height:<?php echo $small_height?>px;">

								<a href="<?php echo $Site_Controller_Template->server . trim((string)$oTemplateType->template_type_full_preview)?>">
								<img src="<?php echo $Site_Controller_Template->server . trim((string)$oTemplateType->template_type_preview)?>"
								width="<?php echo $small_width?>" />
								</a>

								<div style="position: absolute; width: 100%; bottom: 5px;">
									<input style="float: left;" type="radio" <?php echo ($iKeyTemplateType == 0) ? 'checked="checked"' : ''?> name="template_id" id="template_<?php echo (string)$oTemplateType['id']?>" value="<?php echo (string)$oTemplateType['id']?>" />

									<label for="template_<?php echo (string)$oTemplateType['id']?>" style="margin-left: 5px; float: left">
										<b><?php echo (string)$oTemplateType->template_type_name?></b>
									</label>
									</div>
								</div>
								<?php
							}
							?>
							</div>
							<?php

							$oMainTab->add(
								Admin_Form_Entity::factory('Code')->html(
									ob_get_clean()
								)
							);

							$return = $this->_showEditForm();
							break;
						}
					}
				//break; // Если цветовой схемы нет, переходим к следующему шагу
				case 'chooseTemplateType':

					// Операция для кнопки
					$this->_formOperation = 'createSite';

					$oMainTab = Admin_Form_Entity::factory('Tab')
						->caption('Main')
						->name('main');

					$this->addTab($oMainTab);

					$this->title(Core::_('Site.template_settings'));

					$this->_Admin_Form_Controller->title(
						$this->title
					);

					ob_start();

					$oXml = $Site_Controller_Template->loadTemplatesXml();

					$iTemplateId = intval(Core_Array::getPost('template_id'));
					$iTemplateTypeId = intval(Core_Array::getPost('template_type_id'));

					$Templates = $oXml->xpath("//group/templates/template[@id='{$iTemplateId}']");

					if (isset($Templates[0]))
					{
						$Template = $Templates[0];

						// ссылка на макет файла
						$sTemplateFile = (string)$Template->template_file6;

						try
						{
							$Core_Http = Core_Http::instance()
								->url($Site_Controller_Template->server . $sTemplateFile)
								->port(80)
								->timeout(30)
								->execute();

							$sFileContents = $Core_Http->getBody();
						}
						catch (Exception $e)
						{
							Core_Message::show($e->getMessage(), 'error');
							$sFileContents = NULL;
						}

						// название архива макета
						$aTemplateFileUrl = explode('/', $Site_Controller_Template->server . $sTemplateFile);
						$sTemplateFileName = end($aTemplateFileUrl);

						// путь к локальному архиву макета
						$sTemplateFileUrl = $Site_Controller_Template->templatePath . $sTemplateFileName;

						try
						{
							// запись в файл данных макета
							if (Core_File::write($sTemplateFileUrl, $sFileContents))
							{
								// распаковка архива макета
								$Core_Tar = new Core_Tar($sTemplateFileUrl);
								$Core_Tar->extractModify($Site_Controller_Template->templatePath, $Site_Controller_Template->templatePath);
							}
							else
							{
								Core_Message::show(Core::_('install.write_error', $sTemplateFileUrl), 'error');
							}
						}
						catch (Exception $e)
						{
							$flag = FALSE;
							Core_Message::show($e->getMessage(), 'error');
						}

						// Массив из файла template.xml
						$oTemplateContentXml = $Site_Controller_Template->loadSelectedTemplateXml();

						if ($oTemplateContentXml)
						{
							$aXmlFields = $oTemplateContentXml->xpath("fields/field");

							if (count($aXmlFields))
							{
								?><p><?php echo Core::_('install.template_data_information')?></p>
								<table border="0" cellspacing="2" cellpadding="2" class="admin_table">
								<tr class="admin_table_title">
									<td><?php echo Core::_('install.table_field_param')?></td>
									<td><?php echo Core::_('install.table_field_value')?></td>
								</tr>
								<?php

								$aFields = $Site_Controller_Template->getFields($aXmlFields);

								foreach ($aFields as $aFieldsValue)
								{
									$sFieldName = htmlspecialchars($aFieldsValue['Name']);
									$sFieldValue = htmlspecialchars($aFieldsValue['Value']);
									$sFieldMacros = htmlspecialchars($aFieldsValue['Macros']);
									$sFieldExtension = htmlspecialchars($aFieldsValue['Extension']);
									$sFieldMaxWidth = intval($aFieldsValue['MaxWidth']);
									$sFieldMaxHeight = intval($aFieldsValue['MaxHeight']);
									$sFieldListValue = $aFieldsValue['ListValue'];

									$iFieldType = intval($aFieldsValue['Type']);

									// Название поля
									?><tr class="row">
										<td><?php echo $sFieldName?></td>
										<td><?php

									switch ($iFieldType)
									{
										case 0: // текст
											?>
											<input type="text" size="50" name="<?php echo $sFieldMacros?>" value="<?php echo $sFieldValue?>" />
											<?php
											break;
										case 1: // список
											?>
											<select name="<?php echo $sFieldMacros?>">
											<?php
											if (count($sFieldListValue) > 0)
											{
												foreach ($sFieldListValue as $option_key => $option_value)
												{
												?>
												<option value="<?php echo $option_key?>"><?php echo $option_value?></option>
												<?php
												}
											}
											?>
											</select>
											<?php
											break;
										case 2: // файл
											?>
											<input type="file" size="50" name="<?php echo $sFieldMacros?>" value="<?php echo $sFieldValue?>" />
											<?php
											if (trim($sFieldExtension) != '')
											{
												// Разрешенные расширения файла
												?><br /><?php echo Core::_('install.allowed_extension', $sFieldExtension)?><?php
											}

											if ($sFieldMaxWidth > 0 && $sFieldMaxHeight > 0)
											{
												// Максимальный размер файла
												?><br /><?php echo Core::_('install.max_file_size', $sFieldMaxWidth, $sFieldMaxHeight)?><?php
											}

											break;
										case 3: // большое текстовое поле
											?>
											<textarea name="<?php echo $sFieldMacros?>" rows="5" cols="47"><?php echo $sFieldValue?></textarea>
											<?php
											break;
									}
									?></td>
									</tr>
									<?php
								}
								?></table><?php
							}
							else
							{
								?><p><?php echo Core::_('install.empty_settings')?></p><?php
							}
						}
						else
						{
							Core_Message::show(Core::_('install.file_not_found', 'template.xml'), 'error');
						}
					}

					$oMainTab->add(
						Admin_Form_Entity::factory('Code')->html(
							ob_get_clean()
						)
					);

					$return = $this->_showEditForm();
				break;
				case 'createSite':
					$iTemplateId = Core_Array::getPost('template_id');
					$iTemplateTypeId = Core_Array::getPost('template_type_id');

					// Копируем файлы из ./template/ в папку системы
					if (is_dir($Site_Controller_Template->templatePath . 'template'))
					{
						try
						{
							Core_File::copyDir($Site_Controller_Template->templatePath . 'template', CMS_FOLDER);
							Core_Message::show(Core::_('install.template_files_copy_success'));
						}
						catch (Exception $e)
						{
							Core_Message::show($e->getMessage(), 'error');
							Core_Message::show(Core::_('install.template_files_copy_error'), 'error');
						}
					}

					if (is_file($Site_Controller_Template->templatePath . 'template.php'))
					{
						// Замены в макете
						$aReplace = array();

						// Массив из файла template.xml
						$oTemplateContentXml = $Site_Controller_Template->loadSelectedTemplateXml();

						if ($oTemplateContentXml)
						{
							$aXmlFields = $oTemplateContentXml->xpath("fields/field");

							if (count($aXmlFields))
							{
								$aFields = $Site_Controller_Template->getFields($aXmlFields);

								// цикл по дереву 'fields'
								foreach ($aFields as $aFieldsValue)
								{
									$sFieldName = $aFieldsValue['Name'];
									$sFieldMacros = $aFieldsValue['Macros'];
									$iFieldType = $aFieldsValue['Type'];

									// Файл
									if ($iFieldType == 2)
									{
										if (isset($_FILES[$sFieldMacros]['tmp_name'])
										&& is_file($_FILES[$sFieldMacros]['tmp_name'])
										&& $_FILES[$sFieldMacros]['size'] > 0)
										{
											$sFieldPath = $aFieldsValue['Path'];
											$sFieldExtension = $aFieldsValue['Extension'];

											$sExt = Core_File::getExtension($_FILES[$sFieldMacros]['name']);
											$aAllowedExt = explode(',', $sFieldExtension);

											if (strlen(trim($sFieldExtension)) == 0 || in_array($sExt, $aAllowedExt))
											{
												if (!move_uploaded_file($_FILES[$sFieldMacros]['tmp_name'], CMS_FOLDER . $sFieldPath))
												{
													Core_Message::show(Core::_('install.file_copy_error', $sFieldPath), 'error');
												}
											}
											else
											{
												Core_Message::show(Core::_('install.file_disabled_extension', $sFieldName), 'error');
											}
										}
									}
									// Остальные типы полей
									else
									{
										$aReplace["%{$sFieldMacros}%"] = $_REQUEST[$sFieldMacros];
									}
								}
							}
						}

						include($Site_Controller_Template->templatePath . 'template.php');

						Core_Message::show(Core::_('install.template_install_success'));
					}
					else
					{
						Core_Message::show(Core::_('install.file_not_found', 'template.php'), 'error');
					}

					try
					{
						is_dir($Site_Controller_Template->templatePath . 'template') && Core_File::deleteDir($Site_Controller_Template->templatePath . 'template');
						is_dir($Site_Controller_Template->templatePath . 'tmp') && Core_File::deleteDir($Site_Controller_Template->templatePath . 'tmp');
					}
					catch (Exception $e){}

					$return = NULL;
				break;
				default:
					$this->_applyObjectProperty();
					$return = FALSE; // Показываем форму
				break;
			}
		}
		catch (Exception $e)
		{
			Core_Message::show($e->getMessage(), 'error');
		}

		Core_Event::notify(get_class($this) . '.onAfterExecute', $this, array($operation));

		return $return;
	}

	/**
	 * Add form buttons
	 * @return Admin_Form_Entity_Buttons
	 */
	protected function _addButtons()
	{
		// Кнопки
		$oAdmin_Form_Entity_Buttons = Admin_Form_Entity::factory('Buttons');

		$oAdmin_Form_Entity_Button_Save = Admin_Form_Entity::factory('Button')
			->name($this->_formOperation)
			->class('applyButton')
			->value(Core::_('Install.next'))
			->onclick(
				$this->_Admin_Form_Controller->getAdminSendForm(NULL, $this->_formOperation)
			);

		$oAdmin_Form_Entity_Buttons
			->add($oAdmin_Form_Entity_Button_Save);

		return $oAdmin_Form_Entity_Buttons;
	}

	/**
	 * Processing of the form. Apply object fields.
	 * @hostcms-event Site_Controller_addSiteWithTemplate.onAfterRedeclaredApplyObjectProperty
	 */	protected function _applyObjectProperty()	{		//parent::_applyObjectProperty();
		$oConstantLogin = Core_Entity::factory('Constant')->getByName('HOSTCMS_USER_LOGIN');
		$oConstantNumber = Core_Entity::factory('Constant')->getByName('HOSTCMS_CONTRACT_NUMBER');
		$oConstantPin = Core_Entity::factory('Constant')->getByName('HOSTCMS_PIN_CODE');

		if (is_null($oConstantLogin))
		{
			$oConstantLogin = Core_Entity::factory('Constant');
			$oConstantLogin->name = 'HOSTCMS_USER_LOGIN';
			$oConstantLogin->active = 1;
		}

		if (is_null($oConstantNumber))
		{
			$oConstantNumber = Core_Entity::factory('Constant');
			$oConstantNumber->name = 'HOSTCMS_CONTRACT_NUMBER';
			$oConstantNumber->active = 1;
		}

		if (is_null($oConstantPin))
		{
			$oConstantPin = Core_Entity::factory('Constant');
			$oConstantPin->name = 'HOSTCMS_PIN_CODE';
			$oConstantPin->active = 1;
		}

		$oConstantLogin->value = Core_Array::getPost('HOSTCMS_USER_LOGIN');
		$oConstantLogin->save();

		$oConstantNumber->value = Core_Array::getPost('HOSTCMS_CONTRACT_NUMBER');
		$oConstantNumber->save();

		$oConstantPin->value = Core_Array::getPost('HOSTCMS_PIN_CODE');
		$oConstantPin->save();

		Core_Event::notify(get_class($this) . '.onAfterRedeclaredApplyObjectProperty', $this, array($this->_Admin_Form_Controller));	}}