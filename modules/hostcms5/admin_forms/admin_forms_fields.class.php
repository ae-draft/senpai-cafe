<?php
/**
 * Система управления сайтом HostCMS v. 5.xx
 *
 * Copyright © 2005-2011 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 *
 * Класс, реализующий UI для редактирования различных параметров центра управления.
 *
 * Файл: /modules/admin_forms/admin_forms.class.php
 *
 * @package HostCMS 5
 * @author Hostmake LLC
 * @version 5.x
 */
class admin_forms_fields extends admin_forms
{
	var $tabs = array();
	var $fields = array();
	var $buttons = array();
	var $menus = array();

	var $SelectFilterCount = 0;

	/**
	* Свойство, содержащее html-код, дописываемый после отображения формы
	*
	* @var string
	*/
	var $external_html = '';

	/**
	* Свойство с параметрами формы
	*
	* @var array
	* $form_params['path_array'] - массив хлебных крошек, передается по форме
	* <br />[x]['name'] - название ссылки
	* <br />[x]['link'] - адрес ссылки
	* - $form_params['path_separator'] - разделитель для хлебных крошек
	*/
	var $form_params = array();

	/**
	* Конструктор класса
	*
	* @param array $form_params массив с параметрами
	* - string $form_params['form_attribs'] атрибуты формы
	*/
	function admin_forms_fields($form_params = array())
	{
		$this->form_params = Core_Type_Conversion::toArray($form_params);

		// Вызовем конструктор родителя
		$this->admin_forms();

		// Если текущая страница не определена - определим ее
		if (empty($this->form_params['current_page']))
		{
			if (isset($_REQUEST['limit']))
			{
				$this->form_params['current_page'] = Core_Type_Conversion::toInt($_REQUEST['limit']);
			}
			else
			{
				$this->form_params['current_page'] = 0;
			}
		}
	}

	/**
	 * Получить установленный внешний HTML для формы
	 *
	 * @return str
	 */
	function GetExternalHtml()
	{
		return $this->external_html;
	}

	/**
	 * Установить внешний HTML для формы
	 *
	 * @param str $external_html код
	 */
	function SetExternalHtml($external_html)
	{
		$this->external_html = Core_Type_Conversion::toStr($external_html);
	}

	/**
	 * Добавить внешний HTML для формы
	 *
	 * @param str $external_html код
	 */
	function AddExternalHtml($external_html)
	{
		$this->external_html .= Core_Type_Conversion::toStr($external_html);
	}

	/**
	* Вставка новой закладки
	*
	* @param string $tab_caption название закладки
	* - string $param['name'] название закладки
	* @return return int индекс закладки
	*/
	function AddTab($tab_caption, $tab_id = false)
	{
		$tab = array();
		$tab['name'] = Core_Type_Conversion::toStr($tab_caption);

		if ($tab_id === false)
		{
			$this->tabs[] = $tab;
			return count($this->tabs) - 1;
		}
		else
		{
			$this->tabs[$tab_id] = $tab;
			return $tab_id;
		}
	}

	/**
	* Добавление элемента на форму.
	*
	* @param array $param массив параметров
	* - int $param['tab_id'] идентификатор закладки
	* - int $param['type'] тип поля:
	* - 0 - Поле ввода (input).
	* - 1 - Флажок (checkbox).
	* - 2 - Выпадающий список (select).
	* - 3 - Зарезервировано для разделителя.
	* - 4 - Скрытое поле.
	* - 5 - Большое текстовое поле (textarea).
	* - 6 - Пароль.
	* - 7 - Визуальный редактор.
	* - 8 - Текст.
	* - 9 - Поле загрузки файла.
	* - 10 - Дата.
	* - 11 - Дата-время.
	* - 12 - Оценка-радиогруппа со звездочками.
	* - 13 - Радиогруппа.
	* - 14 - Компонент импорта из CSV-файла.
	* - 15 - Как есть. Используется при необходимости добавить в форму HTML-код.
	* - string $param['caption'] тестовое название поля
	* - string $param['name'] name-элемента
	* - string $param['lable'] пояснение для checkbox
	* - string $param['html_id'] id-элемента
	* - array $param['items'] массив элементов по форме:
	*   <br />$param['items'][1] = 'Значение 1';
	*   <br />$param['items'][2] = 'Значение 2';
	* - bool $param['apply_filter'] выводить для поля типа "Выпадающий список" поля фильтра, по умолчанию false
	* - array $param['attributes'] дополнительные атрибуты поля, например $param['attributes']['class'] = 'large';
	* - string $params['separator'] разделитель для радиогрупп, по умолчанию используется тег br
	*
	* - array $param['options'] - дополнительные параметры формы:
	* - - $param['options']['make_small_image_from_big_show'] - отображать ли checkbox с подписью "Создать малое изображение из большого" (1 -  отображать (по умолчанию), 0 - не отображать);
	* - - $param['options']['make_small_image_from_big_checked'] - вид ображения checkbox'а с подписью "Создать малое изображение из большого" выбранным (1 -  отображать выбранным (по умолчанию), 0 - невыбранным);
	* - - $param['options']['load_small_image_show'] - отображать ли поле загрузки малого изображения (1 -  отображать (по умолчанию), 0 - не отображать);
	* - - $param['options']['image_big_max_width'] - значение максимальной ширины большого изображения;
	* - - $param['options']['image_big_max_height'] - значение максимальной высоты большого изображения;
	* - - $param['options']['image_small_max_width'] - значение максимальной ширины малого изображения;
	* - - $param['options']['image_small_max_height'] - значение максимальной высоты малого изображения;
	* - - $param['options']['used_watermark_big_image_show'] - отображать ли checkbox с подписью "Наложить водяной знак на большое изображение" (1 -  отображать (по умолчанию), 0 - не отображать);
	* - - $param['options']['used_watermark_big_image_checked'] - вид ображения checkbox'а с подписью "Наложить водяной знак на большое изображение" (1 -  отображать выбранным (по умолчанию), 0 - невыбранным);
	* - - $param['options']['watermark_position_x'] - значение поля ввода с подписью "По оси X"
	* - - $param['options']['watermark_position_y'] - значение поля ввода с подписью "По оси Y"
	* - - $param['options']['used_big_image_preserve_aspect_ratio'] - отображать ли checkbox с подписью "Сохранять пропорции изображения" (1 -  отображать (по умолчанию), 0 - не отображать);
	* - - $param['options']['used_big_image_preserve_aspect_ratio_checked'] - вид ображения checkbox'а с подписью "Сохранять пропорции изображения" (1 -  отображать выбранным (по умолчанию), 0 - невыбранным);
	* - - $param['options']['used_watermark_small_image_show'] - отображать ли checkbox с подписью "Наложить водяной знак на малое изображение" (1 -  отображать (по умолчанию), 0 - не отображать);
	* - - $param['options']['used_watermark_small_image_checked'] - вид ображения checkbox'а с подписью "Наложить водяной знак на малое изображение" (1 -  отображать выбранным (по умолчанию), 0 - невыбранным);
	* - - $param['options']['big_image_path'] - адрес большого загруженного изображения
	* - - $param['options']['small_image_path'] - адрес малого загруженного изображения
	* - - $param['options']['show_big_image_params'] - параметр, определяющий отображать ли настройки большого изображения
	* - - $param['options']['show_small_image_params'] - параметр, определяющий отображать ли настройки малого изображения
	* - - $param['options']['used_small_image_preserve_aspect_ratio'] - отображать ли checkbox с подписью "Сохранять пропорции малого изображения" (1 -  отображать (по умолчанию), 0 - не отображать);
	* - - $param['options']['used_small_image_preserve_aspect_ratio_checked'] - вид ображения checkbox'а с подписью "Сохранять пропорции малого изображения" (1 -  отображать выбранным (по умолчанию), 0 - невыбранным);
	* - - $param['options']['onclick_delete_big_image'] - значение onclick для удаления большой картинки
	* - - $param['options']['href_delete_big_image'] - значение href для удаления большой картинки
	* - - $param['options']['onclick_delete_small_image'] - значение onclick для удаления малой картинки
	* - - $param['options']['href_delete_small_image'] - значение href для удаления малой картинки
	* - - $field['format']['minlen']['value'] минимальная длина содержимого поля
	* - - $field['format']['minlen']['message'] сообщение о несоответствии длины. Необязательное поле.
	* - - $field['format']['maxlen']['value'] максимальная длина содержимого поля
	* - - $field['format']['maxlen']['message'] сообщение о несоответствии длины. Необязательное поле.
	* - - $field['format']['reg']['value'] регулярное выражение для контроля содержимого поля
	* - - $field['format']['reg']['message'] сообщение о несоответствии длины.
	* - - $field['format']['fieldEquality']['value'] идентификатор поля в DOM-модели, которому должно соответствовать это поле.
	* - - $param['format']['fieldEquality']['message'] сообщение о несоответствии поля.
	* - - $field['format']['lib']['value'] название типового шаблона контроля содержимого поля
	* - int $param['template'] идентификатор макета (используется в визуальном редакторе)
	* - array $param['div_attributes'] - дополнительные атрибуты div-а, в который вложено поле с заголовом, например $param['div_attributes']['style'] = 'float: left;';
	* - mixed $param['value'] значение поля
	* @return int индекс добавленного поля
	*/
	function AddField($param)
	{
		$param = Core_Type_Conversion::toArray($param);

		if (!isset($param['tab_id']))
		{
			return false;
		}

		$item = array();

		if (isset($param['value']))
		{
			$item['value'] = $param['value'];
		}

		$this->fields[] = $param;

		return count($this->fields) - 1;
	}

	/**
	* Вставка кнопки в форму
	*
	* @param array $param массив параметров
	* - $param['name']
	* - $param['caption']
	* - $param['image']
	* - $param['type']
	* @return int индекс вставленной кнопки
	*/
	function AddButton($param)
	{
		$param = Core_Type_Conversion::toArray($param);

		/*
		$button['name'] = Core_Type_Conversion::toStr($param['name']);
		$button['caption'] = Core_Type_Conversion::toStr($param['caption']);
		$button['image'] = Core_Type_Conversion::toStr($param['image']);
		$button['type'] = trim(Core_Type_Conversion::toStr($param['type']));
		$this->buttons[] = $button;
		*/

		// Добавим кнопку в список кнопок
		$this->buttons[] = $param;

		return count($this->buttons) - 1;
	}

	/**
	* Вставка разделителя
	*
	*/
	function AddSeparator($tab_id)
	{
		$param = array();
		$param['tab_id'] = $tab_id;
		$param['type'] = 3;
		$this->AddField($param);
	}

	/**
	* Добавление верхнего меню
	*
	* @param array $menu массив с элементами меню
	* @return int индекс добавленного меню
	*/
	function AddMenu($menu = array())
	{
		$menu = Core_Type_Conversion::toArray($menu);
		$this->menus[] = $menu;

		return count($this->menus) - 1;
	}

	/**
	* Отображение формы центра администрирования
	*
	*/
	function ShowForm($admin_forms_id = 0, $operation = false)
	{
		$title = Core_Type_Conversion::toStr($this->form_params['title']);

		// 6.0
		?><div id="id_message"></div><?php

		if (mb_strlen(trim($title)) > 0)
		{
			// Объявление у метода должно быть таким же, как и у его родителя
			?><h1><?php echo htmlspecialchars($title)?></h1><?php
		}

		// 6.0
		$window_id = $this->GetWindowId();

		// Прорисовка верхнего меню.
		if (isset($this->menus))
		{
			if (is_array($this->menus))
			{
				reset($this->menus);
				foreach ($this->menus as $menu)
				{
					$this->CreateMainMenu($menu);
				}
			}
		}

		// Отображение хлебных крошек.
		$this->ShowBreadCrumbs();

		$attrib_str = '';

		// Создаем строку с атрибутами формы
		if (isset($this->form_params['form_attribs']) && is_array($this->form_params['form_attribs']))
		{
			// Если для формы не передан ID
			if (empty($this->form_params['form_attribs']['id']))
			{
				if (isset($this->form_params['form_attribs']['name']))
				{
					$this->form_params['form_attribs']['id'] = $this->form_params['form_attribs']['name'];
				}
				else
				{
					// Сгенерируем случайное значение
					$this->form_params['form_attribs']['id'] = 'Form' . rand(0, 99999);
				}
			}

			// Если для формы не передано имя
			if (empty($this->form_params['form_attribs']['name']))
			{
				// Положим равным ID
				$this->form_params['form_attribs']['name'] = $this->form_params['form_attribs']['id'];
			}

			// Если для формы не передано enctype
			if (!isset($this->form_params['form_attribs']['enctype']) && !isset($this->form_params['form_attribs']['ENCTYPE']))
			{
				// Положим enctype равным multipart/form-data
				$this->form_params['form_attribs']['enctype'] = 'multipart/form-data';
			}

			foreach ($this->form_params['form_attribs'] as $attr_name => $attr_value)
			{
				$attrib_str .= htmlspecialchars($attr_name).' = "'.
				htmlspecialchars($attr_value).'" ';
			}
		}
		?>

		<div id="box0">

		<!-- Форма -->
		<form <?php echo $attrib_str?>>
		<?php
		// Tab-ы выводим только если их больше 1-го.
		if (count($this->tabs) > 1)
		{
			// Добавим отступ сверху и снизу
			?><div align="right" id="tab">
				<img src="/admin/images/tab_top_fon_4_form_end.gif" style="position: absolute; right: 0px; bottom: 0px;">
				<ul><?php
				foreach ($this->tabs as $tab_id => $tab)
				{
					$class = $tab_id == 0 ? ' current_li' : '';

					?><li class="li_tab<?php echo $class?>" id="li_tab_page_<?php echo $tab_id?>" onclick="$.showTab('<?php echo $window_id?>', 'tab_page_<?php echo $tab_id?>')"><span><?php echo htmlspecialchars($tab['name'])?></span></li><?php
				}
				?>
				</ul>
			</div><?php
		}

		// Отображаем поля для кажной закладки.
		reset($this->tabs);
		foreach ($this->tabs as $tab_id => $tab)
		{
			?><div id="tab_page_<?php echo $tab_id?>"><?php

			// Цикл по списку полей
			reset($this->fields);

			foreach ($this->fields as $field_key => $field)
			{
				if (Core_Type_Conversion::toInt($field['tab_id']) === $tab_id)
				{
					$name = htmlspecialchars(Core_Type_Conversion::toStr($field['name']));
					$caption = Core_Type_Conversion::toStr($field['caption']);

					// Если идентификатор был передан явно, используем его, иначе генерируем сами.
					if (!empty($field['html_id']))
					{
						$html_id = htmlspecialchars(Core_Type_Conversion::toStr($field['html_id']));
					}
					elseif (!empty($field['id']))
					{
						$html_id = htmlspecialchars(Core_Type_Conversion::toStr($field['id']));
					}
					else
					{
						$html_id = "field_id_" . $field_key;
					}

					// Установим атрибуты div'a.
					$attrib_div_str = '';
					if (isset($field['div_attributes']))
					{
						if (is_array($field['div_attributes']))
						{
							foreach ($field['div_attributes'] as $attr_name => $attr_value)
							{
								$attrib_div_str .= htmlspecialchars($attr_name).'="'.htmlspecialchars($attr_value).'" ';
							}
						}
					}

					// Установим атрибуты тега.
					$attrib_str = '';

					if (isset($field['attributes']))
					{
						if (is_array($field['attributes']))
						{
							foreach ($field['attributes'] as $attr_name => $attr_value)
							{
								$attrib_str .= htmlspecialchars($attr_name).' = "'.
								htmlspecialchars($attr_value).'" ';
							}
						}
					}

					// Задан идентификатор элемента
					if (Core_Type_Conversion::toStr($field['div_id']) != '')
					{
						$div_id = ' id="'.$field['div_id'].'" ';
					}
					else // Не задан идентификатор элемента
					{
						$div_id = '';
					}

					// Поле для вывода ошибки выводим для всех, кроме поля "Разделитель".
					if (Core_Type_Conversion::toInt($field['type']) != 3)
					{
						$bShowItemDiv = Core_Type_Conversion::toInt($field['type']) != 15
						// Если задан ID у div, то выводим и при 15-м
						|| $field['div_id'] != '';

						if ($bShowItemDiv)
						{
							?><div class="item_div" <?php echo $div_id?> <?php echo $attrib_div_str?>><?php
						}

						switch (Core_Type_Conversion::toInt($field['type']))
						{
							case 0: // Поле ввода.
							{
								// Длина 100%, если у поля не указан размер
								if (!isset($field['attributes']['size']))
								{
									$attrib_str .= 'style = "width: 100%"';
								}

								if (!isset($field['attributes']['type']))
								{
									$attrib_str .= ' type="text"';
								}

								$value = htmlspecialchars(Core_Type_Conversion::toStr($field['value']));
								?>
								<span class="caption"><?php echo $caption?></span>
								<input name="<?php echo $name?>" id="<?php echo $html_id?>" value="<?php echo $value?>" <?php echo $attrib_str?> onkeydown="FieldCheck('<?php echo $window_id?>', this)" onkeyup="FieldCheck('<?php echo $window_id?>', this)" onblur="FieldCheck('<?php echo $window_id?>', this)" /><?php

								break;
							}
							case 1: // Флажок.
							{
								$value = Core_Type_Conversion::toInt($field['value']);

								//$lable = trim(htmlspecialchars(Core_Type_Conversion::toStr($field['lable'])));
								// htmlspecialchars() убран, т.к. портит <acronym>
								$lable = trim(Core_Type_Conversion::toStr($field['lable']));
								?>
								<input type="checkbox" name="<?php echo $name?>" id="<?php echo $html_id?>" <?php echo $attrib_str?><?php echo Core_Type_Conversion::toInt($value) != 0 ? ' checked="checked"' : ''?> />
								<span class="caption" style="display: inline"><label for="<?php echo $html_id?>"><?php echo $caption?></label></span>
								<?php
								if (!empty($lable))
								{
									?>
									<label for="<?php echo $html_id?>"><?php echo $lable?></label>
									<?php
								}
								break;
							}
							case 2: // Выпадающий список
							{
								// Длина 100%, если у поля не указан размер
								if (!isset($field['attributes']['style']))
								{
									$attrib_str .= 'style = "width: 100%"';
								}

								if (isset($field['attributes']['onchange']))
								{
									$attrib_str .= ' onChange="' . Core_Type_Conversion::toStr($field['attributes']['onchange']) . '" ';
								}

								$value = htmlspecialchars(Core_Type_Conversion::toStr($field['value']));
								$items = Core_Type_Conversion::toArray($field['items']);

								?><span class="caption"><?php echo $caption?></span><?php
								?><select name="<?php echo $name?>" id="<?php echo $html_id?>" <?php echo $attrib_str?>><?php
								foreach ($items as $item_value => $item_caption)
								{

									if ($item_value == $value)
									{
										$selected = ' selected';
									}
									else
									{
										$selected = '';
									}
									?><option value="<?php echo htmlspecialchars($item_value)?>" <?php echo $selected?>><?php echo $item_caption?></option><?php
								}
								?></select><?php

								if (isset($field['apply_filter']) && $field['apply_filter'])
								{
									?><div style="float: left; opacity: 0.7"><img src="/admin/images/filter.gif" class="img_line" style="margin-left: 10px" />
<input size="15" id="filer_<?php echo $html_id?>" onkeyup="clearTimeout(oSelectFilter<?php echo $this->SelectFilterCount?>.timeout); oSelectFilter<?php echo $this->SelectFilterCount?>.timeout = setTimeout(function(){oSelectFilter<?php echo $this->SelectFilterCount?>.Set(document.getElementById('filer_<?php echo $html_id?>').value); oSelectFilter<?php echo $this->SelectFilterCount?>.Filter();}, 500)" onkeypress="if (event.keyCode == 13) return false;" /> <input type="button" onclick="this.form.filer_<?php echo $html_id?>.value = '';oSelectFilter<?php echo $this->SelectFilterCount?>.Set('');oSelectFilter<?php echo $this->SelectFilterCount?>.Filter();" value="<?php echo $GLOBALS['MSG_admin_forms']['input_clear_filter']?>" /> <input id="IgnoreCase_<?php echo $html_id?>" type="checkbox" onclick="oSelectFilter<?php echo $this->SelectFilterCount?>.SetIgnoreCase(!this.checked);oSelectFilter<?php echo $this->SelectFilterCount?>.Filter()" /><label for="IgnoreCase_<?php echo $html_id?>"><?php echo $GLOBALS['MSG_admin_forms']['input_case_sensitive']?></label>
<script type="text/javascript">
var oSelectFilter<?php echo $this->SelectFilterCount?> = new cSelectFilter('<?php echo $window_id?>', "<?php echo $html_id?>");
</script></div><?php

									$this->SelectFilterCount++;
								}

								break;
							}
							case 3: // Зарезервировано для разделителя
							{
								break;
							}
							case 4: // Скрытое поле.
							{
								$value = htmlspecialchars(Core_Type_Conversion::toStr($field['value']));

								?><input type="hidden" name="<?php echo $name?>" value="<?php echo $value?>" /><?php

								break;
							}
							case 5: // Textarea.
							{
								// Длина 100%, если у поля не указан размер
								if (!isset($field['attributes']['size']))
								{
									$attrib_str .= 'style = "width: 100%"';
								}

								$value = htmlspecialchars(Core_Type_Conversion::toStr($field['value']));
								?><span class="caption"><?php echo $caption?></span><?php
								?><textarea name="<?php echo $name?>" id="<?php echo $html_id?>" <?php echo $attrib_str?> onkeydown="FieldCheck('<?php echo $window_id?>', this)" onkeyup="FieldCheck('<?php echo $window_id?>', this)" onblur="FieldCheck('<?php echo $window_id?>', this)"><?php echo $value?></textarea><?php
								break;
							}
							case 6: // Пароль.
							{
								// Длина 100%, если у поля не указан размер
								if (!isset($field['attributes']['size']))
								{
									$attrib_str .= 'style = "width: 100%"';
								}
								$value = htmlspecialchars(Core_Type_Conversion::toStr($field['value']));
								?><span class="caption"><?php echo $caption?></span><?php
								?><input type="password" name="<?php echo $name?>" id="<?php echo $html_id?>" value="<?php echo $value?>" <?php echo $attrib_str?> onkeydown="FieldCheck('<?php echo $window_id?>', this)" onkeyup="FieldCheck('<?php echo $window_id?>', this)" onblur="FieldCheck('<?php echo $window_id?>', this)" /><?php
								break;
							}
							case 7: // Визуальный редактор.
							{
								$value = htmlspecialchars(Core_Type_Conversion::toStr($field['value']));
								?><span class="caption"><?php echo $caption?></span><?php
								$wysiwyg = & singleton('wysiwyg');
								$wysiwyg->show_ws($value, Core_Type_Conversion::toInt($field['template']), $name);

								break;
							}

							case 8: // Текст
							{
								//$value = htmlspecialchars(Core_Type_Conversion::toStr($field['value']));
								$value = Core_Type_Conversion::toStr($field['value']);
								?><span class="caption"><?php echo $caption?></span><?php
								?><div id="<?php echo $html_id?>"><?php echo $value?></div><?php
								break;
							}

							case 9: // Поле загрузки файла.
							{
								// Параметры для компонента загрузки файла
								// $field['options']['make_small_image_from_big_show'] - отображать ли checkbox с подписью "Создать малое изображение из большого" (1 -  отображать (по умолчанию), 0 - не отображать);
								// $field['options']['make_small_image_from_big_checked'] - вид ображения checkbox'а с подписью "Создать малое изображение из большого" выбранным (1 -  отображать выбранным (по умолчанию), 0 - невыбранным);

								// $field['options']['load_small_image_show'] - отображать ли поле загрузки малого изображения (1 -  отображать (по умолчанию), 0 - не отображать);

								// $field['options']['image_big_max_width'] - значение максимальной ширины большого изображения;
								// $field['options']['image_big_max_height'] - значение максимальной высоты большого изображения;
								// $field['options']['image_small_max_width'] - значение максимальной ширины малого изображения;
								// $field['options']['image_small_max_height'] - значение максимальной высоты малого изображения;

								// $field['options']['used_watermark_big_image_show'] - отображать ли checkbox с подписью "Наложить водяной знак на большое изображение" (1 -  отображать (по умолчанию), 0 - не отображать);
								// $field['options']['used_watermark_big_image_checked'] - вид ображения checkbox'а с подписью "Наложить водяной знак на большое изображение" (1 -  отображать выбранным (по умолчанию), 0 - невыбранным);

								// $field['options']['watermark_position_x'] - значение поля ввода с подписью "По оси X"
								// $field['options']['watermark_position_y'] - значение поля ввода с подписью "По оси Y"

								// $field['options']['used_watermark_small_image_show'] - отображать ли checkbox с подписью "Наложить водяной знак на малое изображение" (1 -  отображать (по умолчанию), 0 - не отображать);
								// $field['options']['used_watermark_small_image_checked'] - вид ображения checkbox'а с подписью "Наложить водяной знак на малое изображение" (1 -  отображать выбранным (по умолчанию), 0 - невыбранным);

								// $field['options']['big_image_path'] - адрес большого загруженного изображения
								// $field['options']['small_image_path'] - адрес малого загруженного изображения

								// $field['options']['show_big_image_params'] - параметр, определяющий отображать ли настройки большого изображения
								// $field['options']['show_small_image_params'] - параметр, определяющий отображать ли настройки малого изображения

								// $field['options']['onclick_delete_big_image'] - значение onclick для удаления большой картинки
								// $field['options']['href_delete_big_image'] - значение href для удаления большой картинки
								// $field['options']['onclick_delete_small_image'] - значение onclick для удаления малой картинки
								// $field['options']['href_delete_small_image'] - значение href для удаления малой картинки

								if (!isset($field['options']['onclick_delete_big_image']))
								{
									$field['options']['onclick_delete_big_image'] = '';
								}

								if (!isset($field['options']['href_delete_big_image']))
								{
									$field['options']['href_delete_big_image'] = '';
								}

								if (!isset($field['options']['onclick_delete_small_image']))
								{
									$field['options']['onclick_delete_small_image'] = '';
								}

								if (!isset($field['options']['href_delete_small_image']))
								{
									$field['options']['href_delete_small_image'] = '';
								}

								//$value = htmlspecialchars(Core_Type_Conversion::toStr($field['value']));


								// Не задан параметр Отображать ли checkbox с подписью "Создать малое изображение из большого";
								if (!isset($field['options']['make_small_image_from_big_show']))
								{
									// Задаем значение по умолчанию
									$field['options']['make_small_image_from_big_show'] = 1;
								}
								else
								{
									$field['options']['make_small_image_from_big_show'] = Core_Type_Conversion::toInt($field['options']['make_small_image_from_big_show']);
								}

								// Не задан вид ображения checkbox'а с подписью "Создать малое изображение из большого"
								if (!isset($field['options']['make_small_image_from_big_checked']))
								{
									// Задаем значение по умолчанию
									$field['options']['make_small_image_from_big_checked'] = 1;
								}
								else
								{
									$field['options']['make_small_image_from_big_checked'] = Core_Type_Conversion::toInt($field['options']['make_small_image_from_big_checked']);
								}

								// Не задан параметр для отображения поля загрузки малого изображения
								if (!isset($field['options']['load_small_image_show']))
								{
									// Задаем значение по умолчанию
									$field['options']['load_small_image_show'] = 1;
								}
								else
								{
									$field['options']['load_small_image_show'] = Core_Type_Conversion::toInt($field['options']['load_small_image_show']);
								}

								// Параметр, определяющий отображать ли настройки большого изображения не задан
								if (!isset($field['options']['show_big_image_params']))
								{
									// По умолччанию - отображаем настройки
									$field['options']['show_big_image_params'] = 1;
								}
								else
								{
									$field['options']['show_big_image_params'] = Core_Type_Conversion::toInt($field['options']['show_big_image_params']);
								}

								// Параметр, определяющий отображать ли настройки малого изображения
								if (!isset($field['options']['show_small_image_params']))
								{
									// по умолчанию отображаем настройки малого изображения
									$field['options']['show_small_image_params'] = 1;
								}
								else
								{
									$field['options']['show_small_image_params'] = Core_Type_Conversion::toInt($field['options']['show_small_image_params']);
								}


								// Не задан параметр, содержащий заголовок поля загрузки малого изображения
								if (!isset($field['options']['load_small_image_caption']))
								{
									// Задаем значение по умолчанию
									$field['options']['load_small_image_caption'] = $GLOBALS['MSG_admin_forms']['information_groups_add_form_small_image'];
								}

								//  Не задано значение максимальной ширины большого изображения
								if (!isset($field['options']['image_big_max_width']))
								{
									// Задаем значение по умолчанию
									$field['options']['image_big_max_width'] = MAX_SIZE_LOAD_IMAGE_BIG;
								}
								else
								{
									$field['options']['image_big_max_width'] = Core_Type_Conversion::toInt($field['options']['image_big_max_width']);
								}

								//  Не задано значение максимальной высоты большого изображения
								if (!isset($field['options']['image_big_max_height']))
								{
									// Задаем значение по умолчанию
									$field['options']['image_big_max_height'] = MAX_SIZE_LOAD_IMAGE_BIG;
								}
								else
								{
									$field['options']['image_big_max_height'] = Core_Type_Conversion::toInt($field['options']['image_big_max_height']);
								}

								//  Не задано значение максимальной ширины малого изображения
								if (!isset($field['options']['image_small_max_width']))
								{
									// Задаем значение по умолчанию
									$field['options']['image_small_max_width'] = MAX_SIZE_LOAD_IMAGE;
								}
								else
								{
									$field['options']['image_small_max_width'] = Core_Type_Conversion::toInt($field['options']['image_small_max_width']);
								}

								//  Не задано значение максимальной высоты малого изображения
								if (!isset($field['options']['image_small_max_height']))
								{
									// Задаем значение по умолчанию
									$field['options']['image_small_max_height'] = MAX_SIZE_LOAD_IMAGE;
								}
								else
								{
									$field['options']['image_small_max_height'] = Core_Type_Conversion::toInt($field['options']['image_small_max_height']);
								}

								// Не задан параметр Отображать ли checkbox с подписью "Сохранять пропорции изображения" (1 -  отображать (по умолчанию), 0 - не отображать);
								if (!isset($field['options']['used_big_image_preserve_aspect_ratio']))
								{
									// Задаем значение по умолчанию
									$field['options']['used_big_image_preserve_aspect_ratio'] = 1;
								}
								else
								{
									$field['options']['used_big_image_preserve_aspect_ratio'] = Core_Type_Conversion::toInt($field['options']['used_big_image_preserve_aspect_ratio']);
								}

								// Не задан вид ображения checkbox'а с подписью "Наложить водяной знак на большое изображение" (1 -  отображать выбранным (по умолчанию), 0 - невыбранным);
								if (!isset($field['options']['used_big_image_preserve_aspect_ratio_checked']))
								{
									// Задаем значение по умолчанию
									$field['options']['used_big_image_preserve_aspect_ratio_checked'] = 1;
								}
								else
								{
									$field['options']['used_big_image_preserve_aspect_ratio_checked'] = Core_Type_Conversion::toInt($field['options']['used_big_image_preserve_aspect_ratio_checked']);
								}

								// Не задан параметр Отображать ли checkbox с подписью "Сохранять пропорции изображения" (1 -  отображать (по умолчанию), 0 - не отображать);
								if (!isset($field['options']['used_small_image_preserve_aspect_ratio']))
								{
									// Задаем значение по умолчанию
									$field['options']['used_small_image_preserve_aspect_ratio'] = 1;
								}
								else
								{
									$field['options']['used_small_image_preserve_aspect_ratio'] = Core_Type_Conversion::toInt($field['options']['used_small_image_preserve_aspect_ratio']);
								}

								// Не задан вид ображения checkbox'а с подписью "Наложить водяной знак на большое изображение" (1 -  отображать выбранным (по умолчанию), 0 - невыбранным);
								if (!isset($field['options']['used_small_image_preserve_aspect_ratio_checked']))
								{
									// Задаем значение по умолчанию
									$field['options']['used_small_image_preserve_aspect_ratio_checked'] = 1;
								}
								else
								{
									$field['options']['used_small_image_preserve_aspect_ratio_checked'] = Core_Type_Conversion::toInt($field['options']['used_small_image_preserve_aspect_ratio_checked']);
								}

								// Не задан параметр Отображать ли checkbox с подписью "Наложить водяной знак на большое изображение" (1 -  отображать (по умолчанию), 0 - не отображать);
								if (!isset($field['options']['used_watermark_big_image_show']))
								{
									// Задаем значение по умолчанию
									$field['options']['used_watermark_big_image_show'] = 1;
								}
								else
								{
									$field['options']['used_watermark_big_image_show'] = Core_Type_Conversion::toInt($field['options']['used_watermark_big_image_show']);
								}

								// Не задан параметр, определяющий показывать поле задания положения "водяного" знака по оси X
								if (!isset($field['options']['image_watermark_position_x_show']))
								{
									$field['options']['image_watermark_position_x_show'] = $field['options']['used_watermark_big_image_show'];
								}
								else
								{
									$field['options']['image_watermark_position_x_show'] = Core_Type_Conversion::toInt($field['options']['image_watermark_position_x_show']);
								}

								// Не задан параметр, определяющий показывать поле задания положения "водяного" знака по оси Y
								if (!isset($field['options']['image_watermark_position_y_show']))
								{
									$field['options']['image_watermark_position_y_show'] = $field['options']['used_watermark_big_image_show'];
								}
								else
								{
									$field['options']['image_watermark_position_y_show'] = Core_Type_Conversion::toInt($field['options']['image_watermark_position_y_show']);
								}

								// Не задан вид ображения checkbox'а с подписью "Наложить водяной знак на большое изображение" (1 -  отображать выбранным (по умолчанию), 0 - невыбранным);
								if (!isset($field['options']['used_watermark_big_image_checked']))
								{
									// Задаем значение по умолчанию
									$field['options']['used_watermark_big_image_checked'] = 1;
								}
								else
								{
									$field['options']['used_watermark_big_image_checked'] = Core_Type_Conversion::toInt($field['options']['used_watermark_big_image_checked']);
								}

								// Не задано значение поля ввода с подписью "По оси X"
								if (!isset($field['options']['watermark_position_x']))
								{
									// Задаем значение по умолчанию
									$field['options']['watermark_position_x'] = '50%';
								}
								else
								{
									$field['options']['watermark_position_x'] = htmlspecialchars(Core_Type_Conversion::toStr($field['options']['watermark_position_x']));
								}

								// Не задано значение поля ввода с подписью "По оси Y"
								if (!isset($field['options']['watermark_position_y']))
								{
									// Задаем значение по умолчанию
									$field['options']['watermark_position_y'] = '100%';
								}
								else
								{
									$field['options']['watermark_position_y'] = htmlspecialchars(Core_Type_Conversion::toStr($field['options']['watermark_position_y']));
								}

								// Не задано значение параметра Отображать ли checkbox с подписью "Наложить водяной знак на малое изображение" (1 -  отображать (по умолчанию), 0 - не отображать)
								if (!isset($field['options']['used_watermark_small_image_show']))
								{
									$field['options']['used_watermark_small_image_show'] = 1;
								}
								else
								{
									$field['options']['used_watermark_small_image_show'] = Core_Type_Conversion::toInt($field['options']['used_watermark_small_image_show']);
								}

								// Не задано значение параметра Вид ображения checkbox'а с подписью "Наложить водяной знак на малое изображение" (1 -  отображать выбранным (по умолчанию), 0 - невыбранным)
								if (!isset($field['options']['used_watermark_small_image_checked']))
								{
									$field['options']['used_watermark_small_image_checked'] = 1;
								}
								else
								{
									$field['options']['used_watermark_small_image_checked'] = Core_Type_Conversion::toInt($field['options']['used_watermark_small_image_checked']);
								}

								// Не задан путь к большому изображению
								if (!isset($field['options']['big_image_path']))
								{
									// Задаем значение по умолчанию
									$field['options']['big_image_path'] = '';
								}
								else
								{
									$field['options']['big_image_path'] = $field['options']['big_image_path'];
								}

								// Не задан путь к малому изображению
								if (!isset($field['options']['small_image_path']))
								{
									// Задаем значение по умолчанию
									$field['options']['small_image_path'] = '';
								}
								else
								{
									$field['options']['small_image_path'] = htmlspecialchars(Core_Type_Conversion::toStr($field['options']['small_image_path']));
								}

								// Идентификатор записи с названием файла изображения
								if (!isset($field['options']['image_id']))
								{
									$field['options']['image_id'] = 0;
								}
								else
								{
									$field['options']['image_id'] = Core_Type_Conversion::toInt($field['options']['image_id']);
								}

								// Строка параметров для удаления файла большого изображения
								if (!isset($field['options']['href_delete_big_image']))
								{
									$field['options']['href_delete_big_image'] = '';
								}
								else
								{
									$field['options']['href_delete_big_image'] = Core_Type_Conversion::toStr($field['options']['href_delete_big_image']);
								}

								// Строка параметров для удаления файла малого изображения
								if (!isset($field['options']['params_delete_small_image']))
								{
									$field['options']['params_delete_small_image'] = '';
								}
								else
								{
									$field['options']['params_delete_small_image'] = Core_Type_Conversion::toStr($field['options']['params_delete_small_image']);
								}

								// Ширина по умолчанию
								if (empty($attrib_str))
								{
									$attrib_str = ' size="30"';
								}
								?>
								<div style="float: left; margin-right: 10px;" class="item_div">
									<span class="caption"><?php echo $caption?></span>
									<div style="float: left; margin-right: 5px;"><input type="file" name="<?php echo $name?>" id="<?php echo $html_id?>" <?php echo $attrib_str?> onkeydown="FieldCheck('<?php echo $window_id?>', this)" onkeyup="FieldCheck('<?php echo $window_id?>', this)" onblur="FieldCheck('<?php echo $window_id?>', this)" /></div>
									<div style="float: left">
										<?php
										// Задан путь к большому изображению
										if ($field['options']['big_image_path'] != '')
										{

											// Блок с ID = "upload_big_..." - удаляется после загрузки
										?>
										<div id="upload_big_<?php echo $name?>" style="float: left">
											<a href="<?php echo $field['options']['big_image_path']?>" target="_blank"><img
											src="/admin/images/image_preview.gif" alt="Просмотр" title="Просмотр" /></a>&nbsp;

											<a
											href ="<?php echo $field['options']['href_delete_big_image']?>"
											onclick="res = confirm('<?php echo $GLOBALS['MSG_admin_forms']['msg_information_delete']?>'); if (res) {<?php echo $field['options']['onclick_delete_big_image']?>} else {return false;}"><img
											src="/admin/images/image_delete.gif" border="0"
											alt="<?php echo $GLOBALS['MSG_admin_forms']['msg_information_alt_delete']?>"
											title="<?php echo $GLOBALS['MSG_admin_forms']['msg_information_alt_delete']?>"></a>&nbsp;
										</div>
										<?php
										}

										// Отображать настройки большого изображения
										if ($field['options']['show_big_image_params'])
										{
											?><a href="JavaScript:void(0)" onclick="SlideWindow('watermark_<?php echo $name?>')"><img src="/admin/images/image_settings.gif" title="Настройки изображения"></a><?php
										}
										?>
									</div>
									<?php
									// Отображать настройки большого изображения
									if ($field['options']['show_big_image_params'])
									{
									?>
									<div id="watermark_<?php echo $name?>" style="margin-left: 10px;">
										<table border="0" class="no_border">
										<tr>
											<td><?php echo $GLOBALS['MSG_admin_forms']['big_image_max_width']?>:</td>
											<td><input type="text" name="big_image_max_width_<?php echo $name?>" size="5"  value="<?php echo $field['options']['image_big_max_width']?>"/></td>
										</tr>
										<tr>
											<td><?php echo $GLOBALS['MSG_admin_forms']['big_image_max_height']?>:</td>
											<td><input type="text" name="big_image_max_height_<?php echo $name?>" size="5" value="<?php echo $field['options']['image_big_max_height']?>"/></td>
										</tr>
										<?php

										// Отображать Сохранять пропорции изображения
										if ($field['options']['used_big_image_preserve_aspect_ratio'])
										{
											if ($field['options']['used_big_image_preserve_aspect_ratio_checked'] == 1)
											{
												$checked = ' checked="checked"';
											}
											else
											{
												$checked = '';
											}
										?>
										<tr>
										<td colspan="2"><input type="checkbox" name="big_image_preserve_aspect_ratio_<?php echo $name?>" id="big_image_preserve_aspect_ratio_<?php echo $name?>"<?php echo $checked?>>&nbsp;<label for="big_image_preserve_aspect_ratio_<?php echo $name?>"><?php echo $GLOBALS['MSG_admin_forms']['image_preserve_aspect_ratio']?></label></td>
										</tr>
										<?php
										}

										if ($field['options']['used_watermark_big_image_show'] == 1)
										{
											if ($field['options']['used_watermark_big_image_checked'] == 1)
											{
												$checked = ' checked="checked"';
											}
											else
											{
												$checked = '';
											}
										?>
										<tr>
										<td colspan="2"><input type="checkbox" name="big_image_is_use_watermark_<?php echo $name?>" id="big_image_is_use_watermark_<?php echo $name?>"<?php echo $checked?>>&nbsp;<label for="big_image_is_use_watermark_<?php echo $name?>"><?php echo $GLOBALS['MSG_admin_forms']['information_items_add_form_image_watermark_is_use']?></label></td>
										</tr>
										<?php
										}

										// Отображать поле положения "водяного" знака по оси X
										if ($field['options']['image_watermark_position_x_show'])
										{
										?>
										<tr>
											<td><?php echo $GLOBALS['MSG_admin_forms']['information_systems_add_form_watermark_position_x']?></td>
											<td><input name="image_watermark_position_x_<?php echo $name?>" size="5" value="<?php echo $field['options']['watermark_position_x']?>"></td>
										</tr>
										<?php
										}

										// Отображать поле положения "водяного" знака по оси Y
										if ($field['options']['image_watermark_position_y_show'])
										{
										?>
										<tr>
											<td><?php echo $GLOBALS['MSG_admin_forms']['information_systems_add_form_watermark_position_y']?></td>
											<td><input name="image_watermark_position_y_<?php echo $name?>" size="5" value="<?php echo $field['options']['watermark_position_y']?>"></td>
										</tr>
										<?php
										}
										?>
										</table>
									</div>

									<script type="text/javascript">
									CreateWindow('watermark_<?php echo $name?>', 'Большое изображение', '', '');
									</script>
									<?php
									}
									?>
									</div>

									<?php
									// Отображать поле загрузки малого изображения
									if ($field['options']['load_small_image_show'] == 1)
									{
									?>
									<div style="float: left; margin-right: 10px;">

										<span class="caption"><?php echo $field['options']['load_small_image_caption']?></span>

										<div style="float: left; margin-right: 5px;">
											<input type="file" name="<?php echo $name?>_small" id="<?php echo $html_id?>_small" <?php echo $attrib_str?> onkeydown="FieldCheck('<?php echo $window_id?>', this)" onkeyup="FieldCheck('<?php echo $window_id?>', this)" onblur="FieldCheck('<?php echo $window_id?>', this)" />
										</div>

										<div style="float: left; margin-right: 10px">
										<?php
										// Задан путь к малому изображению
										if ($field['options']['small_image_path'] != '')
										{
										?>
											<div id="upload_small_<?php echo $name?>" style="float: left">
												<a href="<?php echo Core_Type_Conversion::toStr($field['options']['small_image_path'])?>" target="_blank"><img
												src="/admin/images/image_preview.gif" alt="Просмотр" title="Просмотр"/></a>&nbsp;

												<a href ="<?php echo $field['options']['href_delete_small_image']?>"
												onclick="res = confirm('<?php echo $GLOBALS['MSG_admin_forms']['msg_information_delete']?>'); if (res) {<?php echo $field['options']['onclick_delete_small_image']?>} else {return false;}"><img
												src="/admin/images/image_delete.gif" border="0"
												alt="<?php echo $GLOBALS['MSG_admin_forms']['msg_information_alt_delete']?>"
												title="<?php echo $GLOBALS['MSG_admin_forms']['msg_information_alt_delete']?>"></a>
											</div>
										<?php
										}
										if ($field['options']['show_small_image_params'])
										{
										?>
										<a href="JavaScript:void(0)" onclick="JavaScript:SlideWindow('watermark_small_<?php echo $name?>');"><img src="/admin/images/image_settings.gif" title="Настройки малого изображения"></a>
										<?php
										}
										?>
										</div>

										<?php
										// Отображать настройки малого изображения
										if ($field['options']['show_small_image_params'])
										{
											?>
											<div id="watermark_small_<?php echo $name?>" style="display: none; ">
												<table border="0" class="no_border">
												<tr>
													<td><?php echo $GLOBALS['MSG_admin_forms']['small_image_max_width']?>:</td>
													<td><input type="text" name="small_image_max_width_<?php echo $name?>" size="5" value="<?php echo $field['options']['image_small_max_width']?>"/></td>
												</tr>
												<tr>
													<td><?php echo $GLOBALS['MSG_admin_forms']['small_image_max_height']?>:</td>
													<td><input type="text" name="small_image_max_height_<?php echo $name?>" size="5" value="<?php echo $field['options']['image_small_max_height']?>" /></td>
												</tr>

												<?php
												if ($field['options']['used_watermark_small_image_show'] == 1)
												{
													if ($field['options']['used_watermark_small_image_checked'] == 1)
													{
														$checked = ' checked="checked"';
													}
													else
													{
														$checked = '';
													}
													// Наложить "водяной знак" на малое изображение
													?>
													<tr>
													<td colspan="2">
													<input type="checkbox" name="small_image_is_use_watermark_<?php echo $name?>" id="small_image_is_use_watermark_<?php echo $name?>" <?php echo $checked?>>
													<label for="small_image_is_use_watermark_<?php echo $name?>"><?php echo $GLOBALS['MSG_admin_forms']['information_items_add_form_image_watermark_is_use']?></label>
													</td>
													</tr>
													<?php
												}

												// Отображать флажок "Создать малое изображение из большого"
												if ($field['options']['make_small_image_from_big_show'] == 1)
												{
													// Отображать поле флажка выбранным
													if ($field['options']['make_small_image_from_big_checked'] == 1)
													{
														$checked = ' checked="checked"';
													}
													else
													{
														$checked = '';
													}
													?><tr>
													<td colspan="2">
													<input type="checkbox" id="used_big_image_id_<?php echo $name?>" name="used_big_image_<?php echo $name?>"<?php echo $checked?> />
													<label for = "used_big_image_id_<?php echo $name?>"><?php echo $GLOBALS['MSG_admin_forms']['create_thumbnail']?></label>
													</td>
													</tr>
													<?php
												}

												// Отображать флажок "Создать малое изображение из большого"
												if ($field['options']['used_small_image_preserve_aspect_ratio'] == 1)
												{
													// Отображать поле флажка выбранным
													if ($field['options']['used_small_image_preserve_aspect_ratio_checked'] == 1)
													{
														$checked = ' checked="checked"';
													}
													else
													{
														$checked = '';
													}
													?><tr>
													<td colspan="2"><input type="checkbox" name="small_image_preserve_aspect_ratio_<?php echo $name?>" id="small_image_preserve_aspect_ratio_<?php echo $name?>"<?php echo $checked?>>&nbsp;<label for="small_image_preserve_aspect_ratio_<?php echo $name?>"><?php echo $GLOBALS['MSG_admin_forms']['image_preserve_aspect_ratio']?></label></td>
													</tr>
													<?php
												}
												?>

												</table>
											</div>

										<script type="text/javascript">
										CreateWindow('watermark_small_<?php echo $name?>', 'Малое изображение', '', '');
										</script>
									<?php
										}
								?>
								</div>
								<div style="clear: both"></div>
								<?php
									}
									break;
							}

							case 10: // Дата
							{

								$value = htmlspecialchars(Core_Type_Conversion::toStr($field['value']));
								?><span class="caption"><?php echo $caption?></span>
								<input type="text" size="9" name="<?php echo $name?>" id="<?php echo $html_id?>" value="<?php echo $value?>" <?php echo $attrib_str?> onkeydown="FieldCheck('<?php echo $window_id?>', this)" onkeyup="FieldCheck('<?php echo $window_id?>', this)" onblur="FieldCheck('<?php echo $window_id?>', this)" class="calendar_field" />

								<script type="text/javascript">
								Calendar.setup({inputField: '<?php echo $html_id?>',
								ifFormat: '%d.%m.%Y',
								showsTime: false,
								button: '<?php echo $html_id?>',
								align: 'Br',
								singleClick: true,
								timeFormat: 24,
								firstDay: 1});
								</script>
								<?php
								break;
							}

							case 11: //Дата-время
							{
								$value = htmlspecialchars(Core_Type_Conversion::toStr($field['value']));
								?><span class="caption"><?php echo $caption?></span>
								<input type="text" size="18" name="<?php echo $name?>" id="<?php echo $html_id?>" value="<?php echo $value?>" <?php echo $attrib_str?> onkeydown="FieldCheck('<?php echo $window_id?>', this)" onkeyup="FieldCheck('<?php echo $window_id?>', this)" onblur="FieldCheck('<?php echo $window_id?>', this)" class="calendar_field" />

								<script type="text/javascript">
								Calendar.setup({inputField: '<?php echo $html_id?>',
								ifFormat: '%d.%m.%Y %H:%M:00',
								showsTime: true,
								button: '<?php echo $html_id?>',
								align: 'Br',
								singleClick: true,
								timeFormat: 24,
								firstDay: 1});
								</script>
								<?php
								break;
							}

							case 12: // Оценка-радиогруппа со звездочками
							{
								?><span class="caption"><?php echo $caption?></span><?php

								$current_grade = Core_Type_Conversion::toInt($field['value']);

								$max_grade = Core_Type_Conversion::toInt($field['options']['max_grade']);

								for ($i = 0; $i <= $max_grade; $i++)
								{
									if ($i == $current_grade)
									{
										$checked = 'checked';
									}
									else
									{
										$checked = '';
									}
									?>
									<input type="radio" name="<?php echo $name?>" value="<?php echo $i?>" id="id_grade_<?php echo $i?>" <?php echo $checked?>/>&nbsp;
						    		<label for="id_grade_<?php echo $i?>">
						    		<img src="/admin/images/stars_<?php echo $i?>.gif" style="cursor: pointer; border: 0px;" />
						    		</label><br />
						    		<?php
								}
								break;
							}
							case 13: // Радиогруппа
							{
								?><span class="caption"><?php echo $caption?></span><?php

								if (!isset($field['separator']))
								{
									$field['separator'] = '<br />';
								}

								// Указать который элемент сделать активным по умолчанию
								$current_position = Core_Type_Conversion::toInt($field['value']);

								// Указать количество элементов
								$radio_count = Core_Type_Conversion::toInt($field['options']['count']);
								// Массив идентификаторов
								$index_array = Core_Type_Conversion::toArray($field['options']['index']);
								// Номер элемента за которым нужно закрепить поле ввода
								$textbox_pos = Core_Type_Conversion::toArray($field['options']['input_text']);

								// Массив надписей для каждого элемента
								$labels_array = Core_Type_Conversion::toArray($field['options']['labels']);

								$text_array = Core_Type_Conversion::toArray($field['options']['values']);

								if (isset($field['options']['onclick']))
								{
									$action = ' onclick="'.Core_Type_Conversion::toStr($field['options']['onclick']).'" ';
									// Начинаем обработку строки

									// Точка входа первой подстроки "%" в строку
									$first = mb_strpos($action, '%');
									if ($first !== false)
									{
										// Точка входа второй подстроки "%" в строку
										$second = mb_strrpos($action, '%');
										$first = Core_Type_Conversion::toInt($first);
										$second = Core_Type_Conversion::toInt($second);
										// Вырезаем текст между первой и второй точками входа
										$index_of_params = mb_substr($action, $first + 1, $second - $first - 1);
										$left_part = mb_substr($action, 0, $first);
										$right_part = mb_substr($action, $second + 1);
										$action = $left_part;
									}
									else
									{
										$index_of_params = '';
									}
								}
								else
								{
									$action = '';
									$index_of_params = '';
								}

								/*if (isset($field['options']['id_name']))
								{
									$name_id = Core_Type_Conversion::toStr($field['options']['id_name']);
								}
								else
								{
									$name_id = $name;
								}*/

								// $html_id

								for ($i = 0; $i < $radio_count; $i++)
								{
									// Задан массив соответствий
									if (isset($field['options']['ext_name']))
									{
										if ($field['options']['ext_name'][$i] == $current_position)
										{
											$checked = 'checked="checked" ';
										}
										else
										{
											$checked = '';
										}
									}
									else // Не задан массив соответствий
									{
										if ($i == $current_position)
										{
											$checked = 'checked="checked" ';
										}
										else
										{
											$checked = '';
										}
									}

									$radio_id = $html_id;

									$radio_id .= (isset($field['options']['ext_name']))
									? $field['options']['ext_name'][$i]
									: $index_array[$i];

									?><input type="radio" name="<?php echo $name?>" <?php echo (isset($field['options']["$index_of_params"]))
									? $action.$field['options']["$index_of_params"][$i].$right_part
									:''?> value="<?php echo isset($field['options']['ext_name'])
									? $field['options']['ext_name'][$i]
									: $i?>" id="<?php echo $radio_id?>" <?php echo $checked?>/>&nbsp;
									<label for="<?php echo $radio_id?>"<?php echo $attrib_str?>>
									<?php echo $labels_array[$i]?>
	    							</label><?php

	    							// Если установлена опция отображения поля ввода
	    							if (isset($field['options']['input_text']))
	    							{
	    								if (in_array($i, $textbox_pos))
	    								{
	    									?>&nbsp;<input type="text" size="6" name="<?php echo $name.'_text'?>" id="text_<?php echo $index_array[$i]?>" <?php
	    									if (isset($text_array[$i])) {
	    										?>value="<?php echo $text_array[$i]?>"<?php
	    									}?>><?php echo $field['separator'];
	    								}
	    								else
	    								{
	    									echo $field['separator'];
	    								}
	    							}
	    							// иначе
	    							else
	    							{
	    								echo $field['separator'];
	    							}

								}
								break;
							}
							case 14: // Компонент импорта из CSV-файла
							{
								?>
								<span class="caption"><?php echo $caption?></span>
								<?php
								// Количество элементов
								$items_count = Core_Type_Conversion::toInt($field['options']['count']);

								// Значения полей выпадающего списка
								$data_array = Core_Type_Conversion::toArray($field['options']['data_array']);

								// Массив значений цвета соответствующих элементов
								$color_array = Core_Type_Conversion::toArray($field['options']['color_array']);

								// Массив значений заголовков соответствующих элементов
								$labels_array = Core_Type_Conversion::toArray($field['options']['labels_array']);

								// Массив значений свойств соответствующих элементов
								$values_array = Core_Type_Conversion::toArray($field['options']['values_array']);
								// Количество элементов
								$values_count = count($values_array);

								$pos = 0;

								?>
								<table border="0">
								<tr>
								<?php
								for ($i = 0; $i < $items_count; $i++)
								{
									?><td><label for="id_group_<?php echo $i?>">
									<?php echo $labels_array[$i]?></label>&nbsp;&nbsp;&nbsp;</td>
	    							<td>
	    							<select style="padding: 0px;" name="<?php echo $name.$i?>" ><?php

	    							$isset_selected = false;

	    							for ($j = 0; $j < $values_count; $j++)
	    							{
	    								if (!$isset_selected
	    								&& ($labels_array[$i] == $data_array[$j]
	    								|| (strlen($data_array[$j]) > 0
	    									&& strlen($labels_array[$i]) > 0
	    									&&
	    									(strpos($labels_array[$i], $data_array[$j]) !== false
	    									|| strpos($data_array[$j], $labels_array[$i]) !== false)
	    									// Чтобы не было срабатывания "Город" -> "Городской телефон"
	    									// Если есть целиком подходящее поле
	    									&& !array_search($labels_array[$i], $data_array))
	    								))
	    								{
	    									$selected = " selected";

	    									// Для исключения двойного указания selected для одного списка
	    									$isset_selected = true;
	    								}
	    								else
	    								{
	    									$selected = "";
	    								}
	    								?><option style="padding: 2px; border-top: 1px solid #dddddd; <?php echo (!empty($color_array[$pos])) ? 'background-color: '.$color_array[$j].'; color: #ffffff;' : ''?>" <?php echo $selected?> value="<?php echo $values_array[$j]?>"><?php echo $data_array[$j]?></option><?php
	    								$pos++;
	    							}
	    							$pos = 0;
									?></select></td>
									</tr>
									<tr>
									<?php
								}
								?></tr></table><?php
								break;
							}

							case 15: // Как есть
							{
								if (!empty($caption))
								{
									?><span class="caption"><?php echo $caption?></span><?php
								}

								$value = Core_Type_Conversion::toStr($field['value']);
								echo $value;
								break;
							}
						}

						// Блок для ошибок выводим только при указании условий формата
						if (isset($field['format']))
						{
							?><div id="<?php echo $html_id?>_error" class="div_message_error"></div><?php
						}

						if ($bShowItemDiv)
						{
							?></div><?php
						}
					}

					if (Core_Type_Conversion::toInt($field['type']) == 3)
					{
						// Разделитель.
						?><div style="clear: both"></div><?php
					}
				}
			}

			?></div><?php
		}
		?><div style="clear: both"> </div><?php

		// Отображаем кнопки.
		if ($this->buttons)
		{
			?><div id="ControlElements"><?php

			foreach ($this->buttons as $button)
			{
				// Установим атрибуты кнопки.
				$attrib_str = '';

				if (isset($button['attributes']['onclick']))
				{
					$button['attributes']['onclick'] = "SetControlElementsStatus('{$window_id}', false); setTimeout(function(){SetControlElementsStatus('{$window_id}', true)}, 1000); " . $button['attributes']['onclick'];
				}

				if (isset($button['attributes']) && is_array($button['attributes']))
				{
					// Если тип не передан - укажим его
					if (empty($button['attributes']['type']))
					{
						$button['attributes']['type'] = 'button';
					}

					foreach ($button['attributes'] as $attr_name => $attr_value)
					{
						$attrib_str .= htmlspecialchars($attr_name).' = "'.
						htmlspecialchars($attr_value).'" ';
					}
				}

				$name = htmlspecialchars(Core_Type_Conversion::toStr($button['name']));
				$caption = htmlspecialchars(Core_Type_Conversion::toStr($button['caption']));
				$image = htmlspecialchars(Core_Type_Conversion::toStr($button['image']));

				if (empty($button['attributes']['type']))
				{
					$type = 'button';
				}
				else
				{
					$type = trim(htmlspecialchars(Core_Type_Conversion::toStr($button['attributes']['type'])));
				}
				?>
				<input name="<?php echo $name?>" value="<?php echo $caption?>" <?php echo $attrib_str?>/>
				<?php
			}

			?>
			</div>
			<?php
		}
		?>
		</form>
		</div>
		<?php echo $this->external_html?>

		<?php
		// Конец формы
		// Инициализация закладок
		reset($this->tabs);
		$first_tab = each($this->tabs);

		?><script type="text/javascript">
		fieldType = new Array();
		fieldMessage = new Array();
		// Получаем элемент формы
		var FormElement = $("#<?php echo $window_id?> #<?php echo Core_Type_Conversion::toStr($this->form_params['form_attribs']['id'])?>");

		if (FormElement.length > 0)
		{
			// Скрываем саму форму
			FormElement.css('display', 'none');

			// fix bug t.win.document has no properties
			window.setTimeout(function()
			{
				$.showTab('<?php echo $window_id?>', 'tab_page_<?php echo $first_tab['key']?>');
				FormElement.css('display', 'block');
			}, 500);
		}
		<?php
		// Обрабатываем требования к полям
		foreach ($this->fields as $field_key => $field)
		{
			$array_field = array();
			$message_field = array();

			if (isset($field['format']))
			{
				if (isset($field['format']['minlen']['value']))
				{
					$array_field[] = "'minlen' : " . intval($field['format']['minlen']['value']);
				}

				if (isset($field['format']['maxlen']['value']))
				{
					$array_field[] = "'maxlen' : " . intval($field['format']['maxlen']['value']);
				}

				if (isset($field['format']['reg']['value']))
				{
					$array_field[] = "'reg' : " . $field['format']['reg']['value'];

					// Было указано сообщение для формата
					if (isset($field['format']['reg']['message']))
					{
						$message_field[] = "'reg' : \"" . quote_smart($field['format']['reg']['message']) . "\"";
					}
				}

				if (isset($field['format']['fieldEquality']['value']))
				{
					$array_field[] = "'fieldEquality' : \"" . $field['format']['fieldEquality']['value'] . "\"";

					// Было указано сообщение для формата
					if (isset($field['format']['fieldEquality']['message']))
					{
						$message_field[] = "'fieldEquality' : \"" . quote_smart($field['format']['fieldEquality']['message']) . "\"";
					}
				}

				if (isset($field['format']['lib']['value']))
				{
					// Определяем соответсвие библиотеке
					switch ($field['format']['lib']['value'])
					{
						case "ip":
							$reg = '/^([0-9]|[0-9][0-9]|[01][0-9][0-9]|2[0-4][0-9]|25[0-5])(\.([0-9]|[0-9][0-9]|[01][0-9][0-9]|2[0-4][0-9]|25[0-5])){3}$/';
							break;

						case "email":
							$reg = '/^[a-zA-Z0-9_\.\-]+@[a-zA-Z0-9\-]+\.[a-zA-Z0-9\-\.]+$/';
							break;

						case "url":
							$reg = '/^([A-Za-z]+:\/\/)?([A-Za-z0-9]+(:[A-Za-z0-9]+)?@)?([a-zA-Z0-9][-A-Za-z0-9.]*\.[A-Za-z]{2,7})(:[0-9]+)?(\/[-_.A-Za-z0-9]+)?(\?[A-Za-z0-9%&=]+)?(#\w+)?$/';
							break;

						case "integer":
							$reg = '/^[0-9]*$/';
							break;

						case "path":
							$reg = '/^[а-яА-ЯёЁA-Za-z0-9_ \-\.\/]+$/';
							break;

						case "latinBase":
							$reg = '/^[A-Za-z0-9_\-]+$/';
							break;

						case "decimal":
							$reg = '/^[-+]?[0-9]{1,}\\.{0,1}[0-9]*$/';
							break;

						case "date":
							$reg = '/^([0-2][0-9]|[3][0-1])\.([0][0-9]|[1][0-2])\.\d{2,4}$/';
							break;

						case "datetime":
							$reg = '/^([0-2][0-9]|[3][0-1])\.([0][0-9]|[1][0-2])\.\d{2,4} ([0-1][0-9]|[2][0-3]):([0-5][0-9]):([0-5][0-9])$/';
							break;
					}

					// Соответствие было найдено
					if (isset($reg))
					{
						$array_field[] = "'reg' : " . $reg;

						// Было указано сообщение для формата
						if (isset($field['format']['lib']['message']))
						{
							$message_field[] = "'reg' : \"" . quote_smart($field['format']['lib']['message']) . "\"";
						}
					}
				}

				?>fieldType['field_id_<?php echo $field_key?>'] = {<?php echo implode(",\n", $array_field)?>};<?php

				if (count($message_field) > 0)
				{
					?>fieldMessage['field_id_<?php echo $field_key?>'] = {<?php echo implode(",\n", $message_field)?>};<?php
				}
			}
		}
		?>

		// Массив статусов элементов, если элемент соответствует
		// требованиям - значение true, иначе false
		var fieldsStatus = new Array();
		// End проверка форматов полей

		// Проверяем все поля формы
		CheckAllField('<?php echo $window_id?>', "<?php echo Core_Type_Conversion::toStr($this->form_params['form_attribs']['name'])?>");
		</script>
		<?php
	}

	/**
	* Копирование поля формы центра администрирования
	*
	* @param int $admin_form_field_id идентификатор поля формы центра администрирования
	* @return mixed идентификатор копии поля формы центра администрирования в случае успешного выполнения, false - в противном случае
	* <code>
	* <?php
	* $admin_forms_fields = new admin_forms_fields();
	*
	* $admin_form_field_id = 683;
	*
	* $newid = $admin_forms_fields->CopyAdminFormField($admin_form_field_id);
	*
	* // Распечатаем результат
	* echo $newid;
	* ?>
	* </code>
	*/
	function CopyAdminFormField($admin_form_field_id)
	{
		$admin_form_field_id = Core_Type_Conversion::toInt($admin_form_field_id);

		if (!$admin_form_field_id)
		{
			return false;
		}

		$oAdmin_Form_Field = Core_Entity::factory('Admin_Form_Field', $admin_form_field_id);
		$newObject = $oAdmin_Form_Field->copy();

		return $newObject->id;
	}

	/**
	* Копирование события формы центра администрирования
	*
	* @param int $admin_form_event_id идентификатор события формы центра администрирования
	* @return mixed идентификатор копии события формы центра администрирования в случае успешного выполнения, false - в противном случае
	* <code>
	* <?php
	* $admin_forms_fields = new admin_forms_fields();
	*
	* $admin_form_event_id = 399;
	*
	* $newid = $admin_forms_fields->CopyAdminFormEvent($admin_form_event_id);
	*
	* // Распечатаем результат
	* echo $newid;
	* ?>
	* </code>
	*/
	function CopyAdminFormEvent($admin_form_event_id)
	{
		$admin_form_event_id = Core_Type_Conversion::toInt($admin_form_event_id);

		if (!$admin_form_event_id)
		{
			return false;
		}

		$DataBase = & singleton('DataBase');

		// Получаем данные о копируемом поле
		if (!$admin_form_event_result = $this->GetAdminFormsEvent($admin_form_event_id))
		{
			// Произошла ошибка - завершаем выполнение метода
			return false;
		}

		// Делаем копию записи в таблице admin_words_table
		$query = "INSERT INTO admin_words_table (`users_id`)
					SELECT  `users_id` FROM admin_words_table
					WHERE admin_words_id = '{$admin_form_event_result['admin_words_id']}'";

		if (!$DataBase->query($query))
		{
			return false;
		}

		// Идентификатор скопированной записи таблицы admin_words_table
		$new_admin_words_id = mysql_insert_id();

		// Копируем соответствующие записи в таблице admin_words_value_table
		$query = "INSERT INTO admin_words_value_table (`admin_words_id`, `admin_language_id`, `admin_words_value_name`, `admin_words_value_description`)
					SELECT  $new_admin_words_id, `admin_language_id`,  CONCAT(`admin_words_value_name`, ' [Копия ".date('d.m.Y H:i:s')."]') AS admin_words_value_name,
					`admin_words_value_description`
					FROM admin_words_value_table WHERE `admin_words_id` = '{$admin_form_event_result['admin_words_id']}' ";

		if (!$DataBase->query($query))
		{
			return false;
		}

		// Копируем запись в таблице admin_forms_field_table
		$query = "INSERT INTO admin_forms_events_table (`admin_words_id`, `admin_forms_id`, `admin_forms_events_function`,
					`admin_forms_events_picture`, `admin_forms_events_show_button`, `admin_forms_events_group_operation`,
					`admin_forms_events_order`, `admin_forms_events_dataset_id`, `admin_forms_events_ask`,
					`users_id`)
					VALUES ( '{$new_admin_words_id}', '".quote_smart($admin_form_event_result['admin_forms_id'])."',
					'".quote_smart($admin_form_event_result['admin_forms_events_function'])."', '".quote_smart($admin_form_event_result['admin_forms_events_picture'])."',
					'".quote_smart($admin_form_event_result['admin_forms_events_show_button'])."', '".quote_smart($admin_form_event_result['admin_forms_events_group_operation'])."',
					'".Core_Type_Conversion::toInt($admin_form_event_result['admin_forms_events_order'])."', '".quote_smart($admin_form_event_result['admin_forms_events_dataset_id'])."',
					'".quote_smart($admin_form_event_result['admin_forms_events_ask'])."','".Core_Type_Conversion::toInt($admin_form_event_result['users_id'])."')";

		if ($DataBase->query($query))
		{
			return  mysql_insert_id();
		}
		else
		{
			return false;
		}
	}

	/**
	 * Создание тегов <option> для каждого элемента ассоциативного массива
	 *
	 * @param $aOptions массив значений
	 * @param $sCurrentValue текущее значение
	 */
	function CreateOptions($aOptions, $sCurrentValue)
	{
		$result = '';
		if (is_array($aOptions) && count($aOptions) > 0)
		{
			foreach ($aOptions as $value => $name)
			{
				$result .= '<option value="' . htmlspecialchars($value) . '"'
				. (($value == $sCurrentValue) ? ' selected="selected"' : '') . '>'
				. htmlspecialchars($name)
				. '</option>';
			}
		}

		return $result;
	}
}