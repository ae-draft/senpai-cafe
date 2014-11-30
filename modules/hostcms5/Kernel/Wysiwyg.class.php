<?php
/**
 * Система управления сайтом HostCMS v. 5.xx
 *
 * Copyright © 2005-2011 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 *
 * Ядро, класс визуального редактора.
 *
 * Файл: /modules/Kernel/wysiwyg.class.php
 *
 * @package HostCMS 5
 * @author Hostmake LLC
 * @version 5.x
 */
class wysiwyg
{
	/**
	 * Массив настроек для tinyMCE.init, заполняется в конструкторе, пример:
	 * $this->init['mode'] = "exact";
	 *
	 * @var array
	 */
	var $init = array();

	/**
	 * Добавление записи в блок tinyMCE.init
	 *
	 * <code>
	 * $wysiwyg = & singleton('wysiwyg');
	 * $wysiwyg->AddInit('mode', '"exact"');
	 * </code>
	 *
	 * @param string $name название параметра
	 * @param string $value значение
	 */
	function AddInit($name, $value)
	{
		$this->init[$name] = $value;
	}

	/**
	 * Удаление записи из блока tinyMCE.init
	 *
	 * <code>
	 * $wysiwyg = & singleton('wysiwyg');
	 * $wysiwyg->RemoveInit('mode');
	 * </code>
	 *
	 * @param string $name название параметра
	 */
	function RemoveInit($name)
	{
		if (isset($this->init[$name]))
		{
			unset($this->init[$name]);
		}
	}

	/**
	 * Конструктор
	 *
	 */
	function wysiwyg()
	{
		// См. modules/core/config/wysiwyg.php
		$this->init = Core_Config::instance()->get('core_wysiwyg');
	}

	/**
	 * Метод отображает визуальный редактор
	 *
	 * @param string $main_text - содержит текст, включаемый в редактор
	 * @param int $templates_id - идентификатор шаблона
	 * @param string $ws_name
	 */
	function show_ws($main_text, $templates_id = 0, $ws_name = 'textarea')
	{
		/* Определяем шаблон по преденному ID */
		$templates_id = Core_Type_Conversion::toInt($templates_id);
		$main_text = Core_Type_Conversion::toStr($main_text);

		// Отменяем htmlspecialchars, сделанный до вызова функции, чтобы отменить оптическое выравнивание
		$main_text = html_entity_decode($main_text, ENT_COMPAT, 'UTF-8');

		/* Убираем оптическое выравнивание */
		if (Core::moduleIsActive('typograph'))
		{

			$main_text = Typograph_Controller::instance()->eraseOpticalAlignment($main_text);
		}

		/* Заново преобразуем в сущности */
		$main_text = htmlspecialchars($main_text);

		$template = & singleton ('templates');
		$row = $template->GetTemplate($templates_id);

		if ($row)
		{
			$CSS = "/templates/template{$row['templates_id']}/style.css";
		}
		else
		{
			$CSS = '';
		}
		?>
		<textarea name="<?php echo quote_smart($ws_name)?>" id="<?php echo quote_smart($ws_name)?>_ID" style="width: 100%; height: 300px"><?php echo $main_text?></textarea>
		<?php

		if (!defined('USE_WYSIWYG') || defined('USE_WYSIWYG') && USE_WYSIWYG)
		{
			$lng = isset($_SESSION["current_lng"]) ? $_SESSION["current_lng"] : 'ru';

			$this->init['language'] = '"' . $lng . '"';
			$this->init['docs_language'] = '"' . $lng . '"';
			$this->init['elements'] = '"' . quote_smart($ws_name)."_ID" . '"';

			$this->init['content_css'] = '"' . $CSS . '"';

			// Создаем структуру для внутренних ссылок
			$level = -1;
			$menu_id = false;
			$parent_id = 0;

			$structure = & singleton('Structure');

			$mass = $structure->GetStructure("&nbsp;",CURRENT_SITE, $menu_id, $level, $parent_id);

			$tinyMCELinkList = 'var tinyMCELinkList = new Array(';

			$tinyMCELinkListArray = array();

			foreach ($mass as $value)
			{
				// Внешняя ссылка есть, если значение внешней ссылки не пустой
				((mb_strlen((trim($value['structure_external_link'])))==0)
				? $link = $value['full_path']
				: $link = $value['structure_external_link']);

				$tinyMCELinkListArray[] = '["'.addslashes($value['name_with_separator']).'","'.$link.'"]';
			}

			$tinyMCELinkList .= implode(",", $tinyMCELinkListArray);

			$tinyMCELinkList .= ');';

			unset($tinyMCELinkListArray);

			// Передаем в конфигураци
			$this->init['external_link_list'] = '"' . addslashes($tinyMCELinkList) . '"';

			?>
			<!-- TinyMCE -->
			<script type="text/javascript">
			tinyMCE.init({
				<?php

				if (count($this->init) > 0)
				{
					$aInit = array();
					foreach ($this->init as $init_name => $init_value)
					{
						$aInit[] = "$init_name : $init_value";
					}

					echo implode(", \n", $aInit);
				}
				?>
			});

			function convertWord (type, content) {
				switch (type) {
					case "before":
					break;
					case "after":
					content = content.replace(/<!(?:--[\s\S]*?--\s*)?>\s*/g,'');
					break;
				}
				return content;
			}
			</script><?php
		}
	}
}
