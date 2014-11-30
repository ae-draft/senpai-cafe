<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Admin forms.
 *
 * @package HostCMS 6\Admin
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2014 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Skin_Default_Admin_Form_Controller extends Admin_Form_Controller
{
	/**
	 * Show form content in administration center
	 * @return self
	 */
	protected function _showFormContent()
	{
		$aAdmin_Form_Fields = $this->_Admin_Form->Admin_Form_Fields->findAll();

		if (empty($aAdmin_Form_Fields))
		{
			throw new Core_Exception('Admin form does not have fields.');
		}

		$windowId = $this->getWindowId();

		$allow_filter = FALSE;

		?><table width="100%" cellpadding="2" cellspacing="2" class="admin_table"><?php
		?><tr class="admin_table_title"><?php

		// Ячейку над групповыми чекбоксами показываем только при наличии действий
		if ($this->_Admin_Form->show_operations && $this->_showOperations)
		{
			?><td width="25">&nbsp;</td><?php
		}

		foreach ($aAdmin_Form_Fields as $iAdmin_Form_Field_key => $oAdmin_Form_Field)
		{
			// Если был хотя бы один фильтр
			$oAdmin_Form_Field->allow_filter && $allow_filter = TRUE;

			$align = $oAdmin_Form_Field->align
				? ' align="' . htmlspecialchars($oAdmin_Form_Field->align) . '"'
				: '';

			$width = htmlspecialchars(trim($oAdmin_Form_Field->width));

			$Admin_Word_Value = $oAdmin_Form_Field->Admin_Word->getWordByLanguage($this->_Admin_Language->id);

			// Слово найдено
			$fieldName = $Admin_Word_Value && strlen($Admin_Word_Value->name) > 0
				? htmlspecialchars($Admin_Word_Value->name)
				: '&mdash;';

			// Определяем нужно ли отображать стрелки сортировки
			ob_start();

			// Не подсвечивать
			$highlight = FALSE;

			if ($oAdmin_Form_Field->allow_sorting)
			{
				$hrefDown = $this->getAdminLoadHref($this->getPath(), NULL, NULL, NULL, NULL, NULL, $oAdmin_Form_Field->id, 1);
				$onclickDown = $this->getAdminLoadAjax($this->getPath(), NULL, NULL, NULL, NULL, NULL, $oAdmin_Form_Field->id, 1);

				$hrefUp = $this->getAdminLoadHref($this->getPath(), NULL, NULL, NULL, NULL, NULL, $oAdmin_Form_Field->id, 0);
				$onclickUp = $this->getAdminLoadAjax($this->getPath(), NULL, NULL, NULL, NULL, NULL, $oAdmin_Form_Field->id, 0);

				if ($oAdmin_Form_Field->id == $this->_sortingFieldId)
				{
					// Подсвечивать
					$highlight = TRUE;

					if ($this->_sortingDirection == 0)
					{
						?><img src="/admin/images/arrow_up.gif" alt="&uarr" /> <?php
						?><a href="<?php echo $hrefDown?>" onclick="<?php echo $onclickDown?>"><img src="/admin/images/arrow_down_gray.gif" alt="&darr" /></a><?php
					}
					else
					{
						?><a href="<?php echo $hrefUp?>" onclick="<?php echo $onclickUp?>"><img src="/admin/images/arrow_up_gray.gif" alt="&uarr" /></a> <?php
						?><img src="/admin/images/arrow_down.gif" alt="&darr" /><?php
					}
				}
				else
				{
					?><a href="<?php echo $hrefUp?>" onclick="<?php echo $onclickUp?>"><img src="/admin/images/arrow_up_gray.gif" alt="&uarr" /></a> <?php
					?><a href="<?php echo $hrefDown?>" onclick="<?php echo $onclickDown?>"><img src="/admin/images/arrow_down_gray.gif" alt="&darr" /></a><?php
				}
			}

			$sort_arrows = ob_get_clean();

			?><td <?php if (!empty($width)) { echo 'width="' . $width . '"'; }?><?php echo $align?><?php echo $highlight ? ' class="hl"' : ''?>><?php
				?><nobr><?php echo $fieldName?> <?php echo $sort_arrows?></nobr><?php
			?></td><?php
		}

		// Текущий пользователь
		$oUser = Core_Entity::factory('User')->getCurrent();

		// Доступные действия для пользователя
		$aAllowed_Admin_Form_Actions = $this->_Admin_Form->Admin_Form_Actions->getAllowedActionsForUser($oUser);

		if ($this->_Admin_Form->show_operations && $this->_showOperations
		|| $allow_filter && $this->_showFilter)
		{
			/*if (isset($this->form_params['actions_width']))
			{
				$width = Core_Type_Conversion::toStr($this->form_params['actions_width']);
			}
			else
			{*/
				// min width action column
				$width = 10;

				foreach ($aAllowed_Admin_Form_Actions as $o_Admin_Form_Action)
				{
					// Отображаем действие, только если разрешено.
					if ($o_Admin_Form_Action->single)
					{
						$width += 16;
					}
				}
			//}

			?><td width="<?php echo $width?>">&nbsp;</td><?php
		}
		?></tr><?php
		?><tr class="admin_table_filter"><?php
		// Чекбокс "Выбрать все" показываем только при наличии действий
		if ($this->_Admin_Form->show_operations && $this->_showOperations)
		{
			?><td align="center" width="25"><input type="checkbox" name="admin_forms_all_check" id="id_admin_forms_all_check" onclick="$('#<?php echo $windowId?>').highlightAllRows(this.checked)" /></td><?php
		}

		// Фильтр.
		foreach ($aAdmin_Form_Fields as $iAdmin_Form_Field_key => $oAdmin_Form_Field)
		{
			// Перекрытие параметров для данного поля
			foreach ($this->_datasets as $datasetKey => $oAdmin_Form_Dataset)
			{
				$oAdmin_Form_Field_Changed = $this->_changeField($oAdmin_Form_Dataset, $oAdmin_Form_Field);
			}

			$width = htmlspecialchars(trim($oAdmin_Form_Field->width));

			// Подсвечивать
			$highlight = $oAdmin_Form_Field->allow_sorting
				? ($oAdmin_Form_Field->id == $this->_sortingAdmin_Form_Field->id)
				: FALSE;

			?><td <?php echo !empty($width) ? 'width="'.$width.'"' : ''?><?php echo $highlight ? ' class="hl"' : ''?>><?php

				if ($oAdmin_Form_Field->allow_filter)
				{
					$value = trim(Core_Array::get($this->request, "admin_form_filter_{$oAdmin_Form_Field->id}"));

					// Функция обратного вызова для фильтра
					if (isset($this->_filters[$oAdmin_Form_Field->name]))
					{
						switch ($oAdmin_Form_Field->type)
						{
							case 1: // Строка
							case 2: // Поле ввода
							case 4: // Ссылка
							case 10: // Функция обратного вызова
							case 3: // Checkbox.
							case 8: // Выпадающий список
								echo call_user_func($this->_filters[$oAdmin_Form_Field->name], $value, $oAdmin_Form_Field);
							break;

							case 5: // Дата-время.
							case 6: // Дата.
								$date_from = Core_Array::get($this->request, "admin_form_filter_from_{$oAdmin_Form_Field->id}", NULL);
								$date_to = Core_Array::get($this->request, "admin_form_filter_to_{$oAdmin_Form_Field->id}", NULL);

								echo call_user_func($this->_filters[$oAdmin_Form_Field->name], $date_from, $date_to, $oAdmin_Form_Field);
							break;
						}
					}
					else
					{
						$style = !empty($width)
							? "width: {$width};"
							: "width: 97%;";

						switch ($oAdmin_Form_Field->type)
						{
							case 1: // Строка
							case 2: // Поле ввода
							case 4: // Ссылка
							case 10: // Функция обратного вызова
								$value = htmlspecialchars($value);
								?><input type="text" name="admin_form_filter_<?php echo $oAdmin_Form_Field->id?>" id="id_admin_form_filter_<?php echo $oAdmin_Form_Field->id?>" value="<?php echo $value?>" style="<?php echo $style?>" /><?php
							break;

							case 3: // Checkbox.
								?><select name="admin_form_filter_<?php echo $oAdmin_Form_Field->id?>" id="id_admin_form_filter_<?php echo $oAdmin_Form_Field->id?>">
									<option value="0" <?php echo $value == 0 ? "selected" : ''?>><?php echo htmlspecialchars(Core::_('Admin_Form.filter_selected_all'))?></option>
									<option value="1" <?php echo $value == 1 ? "selected" : ''?>><?php echo htmlspecialchars(Core::_('Admin_Form.filter_selected'))?></option>
									<option value="2" <?php echo $value == 2 ? "selected" : ''?>><?php echo htmlspecialchars(Core::_('Admin_Form.filter_not_selected'))?></option>
								</select><?php
							break;

							case 5: // Дата-время.
								$date_from = Core_Array::get($this->request, "admin_form_filter_from_{$oAdmin_Form_Field->id}", NULL);
								$date_from = htmlspecialchars($date_from);

								$date_to = Core_Array::get($this->request, "admin_form_filter_to_{$oAdmin_Form_Field->id}", NULL);
								$date_to = htmlspecialchars($date_to);

								?><input type="text" name="admin_form_filter_from_<?php echo $oAdmin_Form_Field->id?>" id="id_admin_form_filter_from_<?php echo $oAdmin_Form_Field->id?>" value="<?php echo $date_from?>" size="17" class="calendar_field" />
								<div><input type="text" name="admin_form_filter_to_<?php echo $oAdmin_Form_Field->id?>" id="id_admin_form_filter_to_<?php echo $oAdmin_Form_Field->id?>" value="<?php echo $date_to?>" size="17" class="calendar_field" /></div>
								<script type="text/javascript">
								(function($) {
									$("#id_admin_form_filter_from_<?php echo $oAdmin_Form_Field->id?>").datetimepicker({showOtherMonths: true, selectOtherMonths: true, changeMonth: true, changeYear: true, timeFormat: 'hh:mm:ss'});
									$("#id_admin_form_filter_to_<?php echo $oAdmin_Form_Field->id?>").datetimepicker({showOtherMonths: true, selectOtherMonths: true, changeMonth: true, changeYear: true, timeFormat: 'hh:mm:ss'});
								})(jQuery);
								</script><?php
							break;

							case 6: // Дата.
								$date_from = Core_Array::get($this->request, "admin_form_filter_from_{$oAdmin_Form_Field->id}", NULL);
								$date_from = htmlspecialchars($date_from);

								$date_to = Core_Array::get($this->request, "admin_form_filter_to_{$oAdmin_Form_Field->id}", NULL);
								$date_to = htmlspecialchars($date_to);

								?><input type="text" name="admin_form_filter_from_<?php echo $oAdmin_Form_Field->id?>" id="id_admin_form_filter_from_<?php echo $oAdmin_Form_Field->id?>" value="<?php echo $date_from?>" size="8" class="calendar_field" />
								<div><input type="text" name="admin_form_filter_to_<?php echo $oAdmin_Form_Field->id?>" id="id_admin_form_filter_to_<?php echo $oAdmin_Form_Field->id?>" value="<?php echo $date_to?>" size="8" class="calendar_field" /></div>
								<script type="text/javascript">
								(function($) {
									$("#id_admin_form_filter_from_<?php echo $oAdmin_Form_Field->id?>").datepicker({showOtherMonths: true, selectOtherMonths: true, changeMonth: true, changeYear: true});
									$("#id_admin_form_filter_to_<?php echo $oAdmin_Form_Field->id?>").datepicker({showOtherMonths: true, selectOtherMonths: true, changeMonth: true, changeYear: true});
								})(jQuery);
								</script>
								<?php
							break;

							case 8: // Выпадающий список.

							?><select name="admin_form_filter_<?php echo $oAdmin_Form_Field->id?>" id="id_admin_form_filter_<?php echo $oAdmin_Form_Field->id?>" style="<?php echo $style?>">
							<option value="HOST_CMS_ALL" <?php echo $value == 'HOST_CMS_ALL' ? "selected" : ''?>><?php echo htmlspecialchars(Core::_('Admin_Form.filter_selected_all'))?></option>
							<?php
							$str_array = explode("\n", $oAdmin_Form_Field_Changed->list);
							$value_array = array();

							foreach ($str_array as $str_value)
							{
								// Каждую строку разделяем по равно
								$str_explode = explode('=', $str_value);

								if ($str_explode[0] != 0 && count($str_explode) > 1)
								{
									// сохраняем в массив варинаты значений и ссылки для них
									$value_array[intval(trim($str_explode[0]))] = trim($str_explode[1]);

									?><option value="<?php echo htmlspecialchars($str_explode[0])?>" <?php echo $value == $str_explode[0] ? "selected" : ''?>><?php echo htmlspecialchars(trim($str_explode[1]))?></option><?php
								}
							}
							?>
							</select>
							<?php
							break;

							default:
							?><div style="color: #CEC3A3; text-align: center">&mdash;</div><?php
							break;
						}
					}
				}
				else
				{
					// Фильтр не разрешен.
					?><div style="color: #CEC3A3; text-align: center">&mdash;</div><?php
				}
			?></td><?php
		}

		// Фильтр показываем если есть события или хотя бы у одного есть фильтр
		if ($this->_Admin_Form->show_operations && $this->_showOperations
		|| $allow_filter && $this->_showFilter)
		{
			$onclick = $this->getAdminLoadAjax($this->getPath());
			?><td><?php
				?><input title="<?php echo Core::_('Admin_Form.button_to_filter')?>" type="image" src="/admin/images/filter.gif" id="admin_forms_apply_button" type="button" value="<?php echo Core::_('Admin_Form.button_to_filter')?>" onclick="<?php echo $onclick?>" /> <input title="<?php echo Core::_('Admin_Form.button_to_clear')?>" type="image" src="/admin/images/clear.png" type="button" value="<?php echo Core::_('Admin_Form.button_to_clear')?>" onclick="$.clearFilter('<?php echo $windowId?>')" /><?php
			?></td><?php
		}
		?></tr><?php

		$aEntities = array();

		// Устанавливаем ограничения на источники
		$this->setDatasetConditions();

		foreach ($this->_datasets as $datasetKey => $oAdmin_Form_Dataset)
		{
			// Добавляем внешнюю замену по датасету
			$this->AddExternalReplace('{dataset_key}', $datasetKey);

			$aDataFromDataset = $oAdmin_Form_Dataset->load();

			if (!empty($aDataFromDataset))
			{
				foreach ($aDataFromDataset as $oEntity)
				{
					try
					{
						$key_field_name = $this->_Admin_Form->key_field;
						$key_field_value = $oEntity->$key_field_name;

						// Экранируем ' в имени индексного поля, т.к. дальше это значение пойдет в JS
						$key_field_value = str_replace("'", "\'", $key_field_value);
					}
					catch (Exception $e)
					{
						Core_Message::show('Caught exception: ' .  $e->getMessage() . "\n", 'error');
						$key_field_value = NULL;
					}

					?><tr id="row_<?php echo $datasetKey?>_<?php echo $key_field_value?>">
						<?php
						// Чекбокс "Для элемента" показываем только при наличии действий
						if ($this->_Admin_Form->show_operations && $this->_showOperations)
						{
							?><td align="center" width="25">
								<input type="checkbox" id="check_<?php echo $datasetKey?>_<?php echo $key_field_value?>" onclick="$('#<?php echo $windowId?>').setTopCheckbox(); $('#' + getWindowId('<?php echo $windowId?>') + ' #row_<?php echo $datasetKey?>_<?php echo $key_field_value?>').toggleHighlight()" /><?php
							?></td><?php
						}

						foreach ($aAdmin_Form_Fields AS $iAdmin_Form_Field_key => $oAdmin_Form_Field)
						{
							// Перекрытие параметров для данного поля
							$oAdmin_Form_Field_Changed = $this->_changeField($oAdmin_Form_Dataset, $oAdmin_Form_Field);

							/*
							// Проверяем, установлено ли пользователем перекрытие параметров
							// для данного поля.
							if (isset($this->form_params['field_params'][$datasetKey][$field_value['admin_forms_field_name']]))
							{
								// Пользователь перекрыл параметры для данного поля.
								$field_value = array_merge($field_value, $this->form_params['field_params'][$datasetKey][$field_value['admin_forms_field_name']]);
							}
							elseif (isset($this->form_params['field_params'][$datasetKey][$oAdmin_Form_Field_Changed->id]))
							{
								// Проверка перекрытых параметров по идентификатору.
								$field_value = array_merge($field_value, $this->form_params['field_params'][$datasetKey][$oAdmin_Form_Field_Changed->id]);
							}
							*/

							// Параметры поля.
							$width_value = htmlspecialchars(trim($oAdmin_Form_Field_Changed->width));

							$width = !empty($width_value)
								? 'width="'.$width_value.'"'
								: '';

							$style = htmlspecialchars(trim($oAdmin_Form_Field_Changed->style));
							$style = empty($style)
								? ''
								: 'style="'.$style.'"';

							$align = htmlspecialchars(trim($oAdmin_Form_Field_Changed->align));

							if (!empty($align))
							{
								$align = 'align="'.$align.'"';
							}

							$attrib = trim($oAdmin_Form_Field_Changed->attributes);

							// Не подсвечивать
							$highlight = false;

							if ($oAdmin_Form_Field_Changed->allow_sorting)
							{
								if ($oAdmin_Form_Field_Changed->id == $this->_sortingAdmin_Form_Field->id)
								{
									// Подсвечивать
									$highlight = TRUE;
								}
							}

							?><td <?php echo $width?> <?php echo $style?> <?php echo $align?> <?php echo $attrib?><?php echo $highlight ? ' class="hl"' : ''?>><?php

							$fieldName = $oAdmin_Form_Field_Changed->name;

							try
							{
								if ($oAdmin_Form_Field_Changed->type != 10)
								{
									if (isset($oEntity->$fieldName))
									{
										// Выведим значение свойства
										$value = htmlspecialchars($oEntity->$fieldName);
									}
									elseif (method_exists($oEntity, $fieldName))
									{
										// Выполним функцию обратного вызова
										$value = htmlspecialchars($oEntity->$fieldName());
									}
									else
									{
										$value = NULL;
									}
								}

								$element_name = "apply_check_{$datasetKey}_{$key_field_value}_fv_{$oAdmin_Form_Field_Changed->id}";

								// Функция, выполняемая перед отображением поля
								$methodName = 'prefix' . ucfirst($fieldName);
								if (method_exists($oEntity, $methodName))
								{
									// Выполним функцию обратного вызова
									echo $oEntity->$methodName();
								}
								
								// Отображения элементов полей, в зависимости от их типа.
								switch ($oAdmin_Form_Field_Changed->type)
								{
									case 1: // Текст.
										//trim($value) == '' && $value = '&nbsp;';

										$class = 'dl';

										$oAdmin_Form_Field_Changed->editable && $class .= ' editable';

										?><div id="<?php echo $element_name?>"><div <?php echo !empty($width_value) ? 'style="width: ' . $width_value . '"' : '' ?> class="<?php echo $class?>"><?php
										echo $this->applyFormat(nl2br($value), $oAdmin_Form_Field_Changed->format);
										?></div></div><?php
									break;
									case 2: // Поле ввода.
										?><input type="text" name="<?php echo $element_name?>" id="<?php echo $element_name?>" value="<?php echo $value?>" <?php echo $style?> <?php echo ''/*$size*/?> onchange="$.setCheckbox('<?php echo $windowId?>', 'check_<?php echo $datasetKey?>_<?php echo $key_field_value?>'); $('#' + getWindowId('<?php echo $windowId?>') + ' #row_<?php echo $datasetKey?>_<?php echo $key_field_value?>').toggleHighlight()" onkeydown="$.setCheckbox('<?php echo $windowId?>', 'check_<?php echo $datasetKey?>_<?php echo $key_field_value?>'); $('#' + getWindowId('<?php echo $windowId?>') + ' #row_<?php echo $datasetKey?>_<?php echo $key_field_value?>').toggleHighlight()" /><?php
									break;
									case 3: // Checkbox.
										?><input type="checkbox" name="<?php echo $element_name?>" id="<?php echo $element_name?>" <?php echo intval($value) ? 'checked="checked"' : ''?> onclick="$.setCheckbox('<?php echo $windowId?>', 'check_<?php echo $datasetKey?>_<?php echo $key_field_value?>'); $('#' + getWindowId('<?php echo $windowId?>') + ' #row_<?php echo $datasetKey?>_<?php echo $key_field_value?>').toggleHighlight();" value="1" /><?php
									break;
									case 4: // Ссылка.
										$link = $oAdmin_Form_Field_Changed->link;
										$onclick = $oAdmin_Form_Field_Changed->onclick;

										//$link_text = trim($value);
										$link_text = $this->applyFormat($value, $oAdmin_Form_Field_Changed->format);

										$link = $this->doReplaces($aAdmin_Form_Fields, $oEntity, $link);
										$onclick = $this->doReplaces($aAdmin_Form_Fields, $oEntity, $onclick);

										// Нельзя применять, т.к. 0 является пустотой if (empty($link_text))
										if (mb_strlen($link_text) != 0)
										{
											?><a href="<?php echo $link?>" <?php echo (!empty($onclick)) ? "onclick=\"{$onclick}\"" : ''?>><?php echo $link_text?></a><?php
										}
										else
										{
											?>&nbsp;<?php
										}
									break;
									case 5: // Дата-время.
										$value = $value == '0000-00-00 00:00:00' || $value == ''
											? ''
											: Core_Date::sql2datetime($value);
										echo $this->applyFormat($value, $oAdmin_Form_Field_Changed->format);

									break;
									case 6: // Дата.
										$value = $value == '0000-00-00 00:00:00' || $value == ''
											? ''
											: Core_Date::sql2date($value);
										echo $this->applyFormat($value, $oAdmin_Form_Field_Changed->format);

									break;
									case 7: // Картинка-ссылка.
										$link = $oAdmin_Form_Field_Changed->link;
										$onclick = $oAdmin_Form_Field_Changed->onclick;

										$link = $this->doReplaces($aAdmin_Form_Fields, $oEntity, $link);
										$onclick = $this->doReplaces($aAdmin_Form_Fields, $oEntity, $onclick);

										// ALT-ы к картинкам
										$alt_array = array();

										// TITLE-ы к картинкам
										$title_array = array();

										$value_trim = trim($value);

										/*
										Разделяем варианты значений на строки, т.к. они приходят к нам в виде:
										0 = /images/off.gif
										1 = /images/on.gif
										*/
										$str_array = explode("\n", $oAdmin_Form_Field_Changed->image);
										$value_array = array();

										foreach ($str_array as $str_value)
										{
											// Каждую строку разделяем по равно
											$str_explode = explode('=', $str_value/*, 2*/);

											if (count($str_explode) > 1)
											{
												// сохраняем в массив варинаты значений и ссылки для них
												$value_array[trim($str_explode[0])] = trim($str_explode[1]);

												// Если указано альтернативное значение для картинки - добавим его в alt и title
												if (isset($str_explode[2]) && $value_trim == trim($str_explode[0]))
												{
													$alt_array[$value_trim] = trim($str_explode[2]);
													$title_array[$value_trim] = trim($str_explode[2]);
												}
											}
										}

										// Получаем заголовок столбца на случай, если для IMG не было указано alt-а или title
										$Admin_Word_Value = $oAdmin_Form_Field->Admin_Word->getWordByLanguage(
											$this->_Admin_Language->id
										);

										$fieldName = $Admin_Word_Value
											? htmlspecialchars($Admin_Word_Value->name)
											: "&mdash;";

										// Warning: 01-06-11 Создать отдельное поле в таблице в БД и в нем хранить alt-ы
										if (isset($field_value['admin_forms_field_alt']))
										{
											$str_array_alt = explode("\n", trim($field_value['admin_forms_field_alt']));

											foreach ($str_array_alt as $str_value)
											{
												// Каждую строку разделяем по равно
												$str_explode_alt = explode('=', $str_value, 2);

												// сохраняем в массив варинаты значений и ссылки для них
												if (count($str_explode_alt) > 1)
												{
													$alt_array[trim($str_explode_alt[0])] = trim($str_explode_alt[1]);
												}
											}
										}
										elseif (!isset($alt_array[$value]))
										{
											$alt_array[$value] = $fieldName;
										}

										// ToDo: Создать отдельное поле в таблице в БД и в нем хранить title-ы
										if (isset($field_value['admin_forms_field_title']))
										{
											$str_array_title = explode("\n", $field_value['admin_forms_field_title']);

											foreach ($str_array_title as $str_value)
											{
												// Каждую строку разделяем по равно
												$str_explode_title = explode('=', $str_value, 2);

												if (count($str_explode_title) > 1)
												{
													// сохраняем в массив варинаты значений и ссылки для них
													$title_array[trim($str_explode_title[0])] = trim($str_explode_title[1]);
												}
											}
										}
										elseif (!isset($title_array[$value]))
										{
											$title_array[$value] = $fieldName;
										}

										if (isset($value_array[$value]))
										{
											$src = $value_array[$value];
										}
										elseif(isset($value_array['']))
										{
											$src = $value_array[''];
										}
										else
										{
											$src = NULL;
										}

										// Отображаем картинку ссылкой
										if (!empty($link) && !is_null($src))
										{
											?><a href="<?php echo $link?>" onclick="$('#' + getWindowId('<?php echo $windowId?>') + ' #row_<?php echo $datasetKey?>_<?php echo $key_field_value?>').toggleHighlight();<?php echo $onclick?>"><img src="<?php echo htmlspecialchars($src)?>" alt="<?php echo Core_Type_Conversion::toStr($alt_array[$value])?>" title="<?php echo Core_Type_Conversion::toStr($title_array[$value])?>"></a><?php
										}
										// Отображаем картинку без ссылки
										elseif (!is_null($src))
										{
											?><img src="<?php echo htmlspecialchars($src)?>" alt="<?php echo Core_Type_Conversion::toStr($alt_array[$value])?>" title="<?php echo Core_Type_Conversion::toStr($title_array[$value])?>"><?php
										}
										/*elseif (!empty($link) && !isset($value_array[$value]))
										{
											// Картинки для такого значения не найдено, но есть ссылка
											?><a href="<?php echo $link?>" onclick="$('#' + getWindowId('<?php echo $windowId?>') + ' #row_<?php echo $datasetKey?>_<?php echo $key_field_value?>').toggleHighlight();<?php echo $onclick?> ">&mdash;</a><?php
										}*/
										else
										{
											// Картинки для такого значения не найдено
											?>&mdash;<?php
										}
									break;
									case 8: // Выпадающий список
										/*
										Разделяем варианты значений на строки, т.к. они приходят к нам в виде:
										0 = /images/off.gif
										1 = /images/on.gif
										*/

										$str_array = explode("\n", $oAdmin_Form_Field_Changed->list);

										$value_array = array();

										?><select name="<?php echo $element_name?>" id="<?php echo $element_name?>" onchange="$.setCheckbox('<?php echo $windowId?>', 'check_<?php echo $datasetKey?>_<?php echo $key_field_value?>');" <?php echo $style?>><?php

										foreach ($str_array as $str_value)
										{
											// Каждую строку разделяем по равно
											$str_explode = explode('=', $str_value, 2);

											if (count($str_explode) > 1)
											{
												// сохраняем в массив варинаты значений и ссылки для них
												$value_array[intval(trim($str_explode[0]))] = trim($str_explode[1]);

												$selected = $value == $str_explode[0]
													? ' selected = "" '
													: '';

												?><option value="<?php echo htmlspecialchars($str_explode[0])?>" <?php echo $selected?>><?php echo htmlspecialchars(trim($str_explode[1]))?></option><?php
											}
										}
										?>
										</select>
										<?php

									break;
									case 9: // Текст "AS IS"
										if (mb_strlen($value) != 0)
										{
											echo html_entity_decode($value, ENT_COMPAT, 'UTF-8');
										}
										else
										{
											?>&nbsp;<?php
										}

									break;
									case 10: // Вычисляемое поле с помощью функции обратного вызова,
									// имя функции обратного вызова f($field_value, $value)
									// передается функции с именем, содержащимся в $field_value['callback_function']
										if (method_exists($oEntity, $fieldName)
											|| method_exists($oEntity, 'isCallable') && $oEntity->isCallable($fieldName)
										)
										{
											// Выполним функцию обратного вызова
											echo $oEntity->$fieldName($oAdmin_Form_Field, $this);
										}
										elseif (property_exists($oEntity, $fieldName))
										{
											// Выведим значение свойства
											echo $oEntity->$fieldName;
										}
									break;
									default: // Тип не определен.
										?>&nbsp;<?php
									break;
								}
								
								// Функция, выполняемая после отображением поля
								$methodName = 'suffix' . ucfirst($fieldName);
								if (method_exists($oEntity, $methodName))
								{
									// Выполним функцию обратного вызова
									echo $oEntity->$methodName();
								}
							}
							catch (Exception $e)
							{
								Core_Message::show('Caught exception: ' .  $e->getMessage() . "\n", 'error');
							}
							?></td><?php
						}

						// Действия для строки в правом столбце
						if ($this->_Admin_Form->show_operations
						&& $this->_showOperations
						|| $allow_filter && $this->_showFilter)
						{
							// Определяем ширину столбца для действий.
							$width = isset($this->form_params['actions_width'])
								? strval($this->form_params['actions_width'])
								: '10px'; // Минимальная ширина

							// <nobr> из-за IE
							?><td class="admin_forms_action_td" style="width: <?php echo $width?>"><nobr><?php

							foreach ($aAllowed_Admin_Form_Actions as $o_Admin_Form_Action)
							{
								// Отображаем действие, только если разрешено.
								if (!$o_Admin_Form_Action->single)
								{
									continue;
								}

								// Проверяем, привязано ли действие к определенному dataset'у.
								if ($o_Admin_Form_Action->dataset != -1
								&& $o_Admin_Form_Action->dataset != $datasetKey)
								{
									continue;
								}

								$Admin_Word_Value = $o_Admin_Form_Action->Admin_Word->getWordByLanguage($this->_Admin_Language->id);

								if ($Admin_Word_Value && strlen($Admin_Word_Value->name) > 0)
								{
									$name = $Admin_Word_Value->name;
								}
								else
								{
									$name = '';
								}

								$href = $this->getAdminActionLoadHref($this->getPath(), $o_Admin_Form_Action->name, NULL,
										$datasetKey, $key_field_value);

								$onclick = $this->getAdminActionLoadAjax($this->getPath(), $o_Admin_Form_Action->name, NULL, $datasetKey, $key_field_value);

								// Добавляем установку метки для чекбокса и строки + добавлем уведомление, если необходимо
								if ($o_Admin_Form_Action->confirm)
								{
									$onclick = "res = confirm('".Core::_('Admin_Form.confirm_dialog', htmlspecialchars($name))."'); if (!res) { $('#{$windowId} #row_{$datasetKey}_{$key_field_value}').toggleHighlight(); } else {{$onclick}} return res;";
								}

								?><a href="<?php echo $href?>" onclick="<?php echo $onclick?>"><img src="<?php echo htmlspecialchars($o_Admin_Form_Action->picture)?>" alt="<?php echo $name?>" title="<?php echo $name?>"></a> <?php
							}
							?></nobr></td><?php
						}

						?></tr><?php
				}
			}
		}

		?></table><?php

		return $this;
	}

	/**
	 * Show action panel in administration center
	 * @return self
	 */
	protected function _showBottomActions()
	{
		// Строка с действиями
		if ($this->_showBottomActions)
		{
			$windowId = $this->getWindowId();

			// Текущий пользователь
			$oUser = Core_Entity::factory('User')->getCurrent();

			// Доступные действия для пользователя
			$aAllowed_Admin_Form_Actions = $this->_Admin_Form->Admin_Form_Actions->getAllowedActionsForUser($oUser);
		?>
		<table cellpadding="5" cellspacing="0" border="1" width="100%" style="margin-top: 8px;" class="light_table">
		<tr>
		<?php
		// Чекбокс "Выбрать все" показываем только при наличии действий
		if ($this->_Admin_Form->show_operations && $this->_showOperations)
		{
			?><td align="center" width="25">
				<input type="checkbox" name="admin_forms_all_check2" id="id_admin_forms_all_check2" onclick="$('#<?php echo $windowId?>').highlightAllRows(this.checked)" />
			</td><?php
		}

		?><td>
			<div class="admin_form_action"><?php

				if ($this->_Admin_Form->show_group_operations)
				{
					// Групповые операции
					if (!empty($aAllowed_Admin_Form_Actions))
					{
						foreach ($aAllowed_Admin_Form_Actions as $o_Admin_Form_Action)
						{
							if ($o_Admin_Form_Action->group)
							{
								$Admin_Word_Value = $o_Admin_Form_Action->Admin_Word->getWordByLanguage($this->_Admin_Language->id);

								if ($Admin_Word_Value && strlen($Admin_Word_Value->name) > 0)
								{
									$text = $Admin_Word_Value->name;
								}
								else
								{
									$text = '';
								}

								$href = $this->getAdminLoadHref($this->getPath(), $o_Admin_Form_Action->name);
								$onclick = $this->getAdminLoadAjax($this->getPath(), $o_Admin_Form_Action->name);

								// Нужно подтверждение для действия
								if ($o_Admin_Form_Action->confirm)
								{
									$onclick = "res = confirm('".Core::_('Admin_Form.confirm_dialog', htmlspecialchars($text))."'); if (res) { $onclick } else {return false}";

									$link_class = 'admin_form_action_alert_link';
								}
								else
								{
									$link_class = 'admin_form_action_link';
								}

								// ниже по тексту alt-ы и title-ы не выводятся, т.к. они дублируются текстовыми
								// надписями и при отключении картинок текст дублируется
								/* alt="<?php echo htmlspecialchars($text)?>"*/
								?><nobr><a href="<?php echo $href?>" onclick="<?php echo $onclick?>"><img src="<?php echo htmlspecialchars($o_Admin_Form_Action->picture)?>" title="<?php echo htmlspecialchars($text)?>"></a> <a href="<?php echo $href?>" onclick="<?php echo $onclick?>" class="<?php echo $link_class?>"><?php echo htmlspecialchars($text)?></a>
								</nobr><?php
							}
						}
					}
				}
				?>
			</div>
			</td>
			<td width="110" align="center">
				<div class="admin_form_action">
				<?php
				// Дописываем параметры фильтра
				/*if (count($_REQUEST) > 0)
				{
					foreach ($_REQUEST as $rkey => $rvalue)
					{
						// Передаем параметры фильтра
						if (mb_strpos($rkey, 'admin_form_filter_') === 0)
						{
							$this->AAdditionalParams .= "&{$rkey}=".urlencode($rvalue);
						}
					}
				}
				$action_href = '';
				?>
				<nobr>
				<a href="<?php echo $action_href?>" target="_blank"><img src="/admin/images/export.gif" title="<?php echo Core::_('Admin_Form.export_csv')?>"></a>
				<a href="<?php echo $action_href?>" target="_blank"><?php echo Core::_('Admin_Form.export_csv')?></a>
				</nobr><?php */
				?></div>
			</td>
			<td width="60" align="center"><?php
				$this->_onPageSelector()
			?></td>
		</tr>
		</table>
		<?php
		}

		return $this;
	}
}