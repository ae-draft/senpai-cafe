<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * SEO.
 *
 * @package HostCMS 6\Seo
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2013 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Seo_Controller_Report extends Admin_Form_Action_Controller_Type_Edit
{
	/**
	 * Set object
	 * @param object $object object
	 * @return self
	 */
	public function setObject($object)
	{
		parent::setObject($object);

		$this->title(Core::_('Seo.report_title', Core_Entity::factory('Site', $this->_object->site_id)->name));

		$oMainTab = Admin_Form_Entity::factory('Tab')
			->caption(Core::_('admin_form.form_forms_tab_1'))
			->name('main');
		$this->addTab($oMainTab);

		$oMainTab->add(Core::factory('Admin_Form_Entity_Datetime')
			->caption(Core::_('Seo.start_datetime'))
			->name('start_datetime')
			->value(Core_Date::timestamp2sql(time() - 2678400))
		)
		->add($this->getField('datetime')
			->caption(Core::_('Seo.end_datetime'))
		)
		->add(Admin_Form_Entity::factory('Input')
			->caption(Core::_('Seo.count'))
			->name('count')
			->style('width: 150px')
			->value(10)
			->format(array(
					'lib' => array('value' => 'positiveInteger'),
					'minlen' => array('value' => 1)
				)
			)
		)
		->add(Admin_Form_Entity::factory('Checkbox')
			->caption(Core::_('Seo.tcy'))
			->name('tcy')
			->value(1)
		)
		->add(Admin_Form_Entity::factory('Checkbox')
			->caption(Core::_('Seo.pr'))
			->name('pr')
			->value(1)
		);

		// Закладка обратных ссылок
		$oLinksTab = Admin_Form_Entity::factory('Tab')
			->caption(Core::_('Seo.tab_links'))
			->name('links');
		$this->addTabAfter($oLinksTab, $oMainTab);

		// Закладка обратных ссылок
		$oLinksTab->add(Admin_Form_Entity::factory('Checkbox')
				->caption(Core::_('Seo.yandex'))
				->name('yandex_links')
				->value(1))
			->add(Admin_Form_Entity::factory('Checkbox')
				->caption(Core::_('Seo.google'))
				->name('google_links')
				->value(1))
			/*->add(Admin_Form_Entity::factory('Checkbox')
				->caption(Core::_('Seo.yahoo'))
				->name('yahoo_links')
				->value(1))*/
			->add(Admin_Form_Entity::factory('Checkbox')
				->caption(Core::_('Seo.bing'))
				->name('bing_links')
				->value(1));

		// Закладка проиндексированных страниц
		$oIndexedTab = Admin_Form_Entity::factory('Tab')
			->caption(Core::_('Seo.tab_indexed'))
			->name('indexed');
		$this->addTabAfter($oIndexedTab, $oLinksTab);

		// Закладка проиндексированных страниц
		$oIndexedTab->add(Admin_Form_Entity::factory('Checkbox')
				->caption(Core::_('Seo.yandex'))
				->name('yandex_indexed')
				->value(1))
			->add(Admin_Form_Entity::factory('Checkbox')
				->caption(Core::_('Seo.google'))
				->name('google_indexed')
				->value(1))
			->add(Admin_Form_Entity::factory('Checkbox')
				->caption(Core::_('Seo.yahoo'))
				->name('yahoo_indexed')
				->value(1))
			->add(Admin_Form_Entity::factory('Checkbox')
				->caption(Core::_('Seo.bing'))
				->name('bing_indexed')
				->value(1))
			/*->add(Admin_Form_Entity::factory('Checkbox')
				->caption(Core::_('Seo.rambler'))
				->name('rambler_indexed')
				->value(1))*/;

		// Закладка каталогов
		$oCatalogTab = Admin_Form_Entity::factory('Tab')
			->caption(Core::_('Seo.tab_catalog'))
			->name('catalog');
		$this->addTabAfter($oCatalogTab, $oIndexedTab);

		// Закладка каталогов
		$oCatalogTab->add($this->getField('yandex_catalog')->value(1))
			->add($this->getField('rambler_catalog')->value(1))
			->add($this->getField('dmoz_catalog')->value(1))
			->add($this->getField('aport_catalog')->value(1))
			->add($this->getField('mail_catalog')->value(1));

		// Закладка счетчиков
		$oCounterTab = Admin_Form_Entity::factory('Tab')
			->caption(Core::_('Seo.tab_counter'))
			->name('counter');
		$this->addTabAfter($oCounterTab, $oCatalogTab);

		// Закладка счетчиков
		$oCounterTab->add($this->getField('rambler_counter')->value(1))
			->add($this->getField('spylog_counter')->value(1))
			->add($this->getField('hotlog_counter')->value(1))
			->add($this->getField('liveinternet_counter')->value(1))
			->add($this->getField('mail_counter')->value(1));

		// Позиции в поисковых системах
		$oPositionTab = Admin_Form_Entity::factory('Tab')
			->caption('Позиции в поисковых системах')
			->name('position');
		$this->addTabAfter($oPositionTab, $oCounterTab);

		$oPositionTab->add(Admin_Form_Entity::factory('Checkbox')
				->caption(Core::_('Seo.yandex'))
				->name('yandex_position')
				->value(1))
			->add(Admin_Form_Entity::factory('Checkbox')
				->caption(Core::_('Seo.rambler'))
				->name('rambler_position')
				->value(1))
			->add(Admin_Form_Entity::factory('Checkbox')
				->caption(Core::_('Seo.google'))
				->name('google_position')
				->value(1))
			->add(Admin_Form_Entity::factory('Checkbox')
				->caption(Core::_('Seo.yahoo'))
				->name('yahoo_position')
				->value(1))
			->add(Admin_Form_Entity::factory('Checkbox')
				->caption(Core::_('Seo.bing'))
				->name('bing_position')
				->value(1));

		return $this;
	}

	/**
	 * Add form buttons
	 * @return Admin_Form_Entity_Buttons
	 */
	protected function _addButtons()
	{
		// Кнопки
		$oAdmin_Form_Entity_Buttons = Admin_Form_Entity::factory('Buttons');

		// Кнопка "Отправить"
		$oAdmin_Form_Entity_Button_Send = Admin_Form_Entity::factory('Button')
			->name('generate')
			->class('applyButton')
			->value(Core::_('Seo.generate'))
			->onclick($this->_Admin_Form_Controller->getAdminSendForm(NULL, 'generate'));

		$oAdmin_Form_Entity_Buttons->add($oAdmin_Form_Entity_Button_Send);

		return $oAdmin_Form_Entity_Buttons;
	}

	/**
	 * Executes the business logic.
	 * @param mixed $operation Operation name
	 * @return boolean
	 */
	public function execute($operation = NULL)
	{
		switch ($operation)
		{
			case NULL: // Показ формы
				$this->_Admin_Form_Controller->title(
					$this->title
				);

				return $this->_showEditForm();

			case 'generate':

				ob_start();

				// Заголовок формы добавляется до вывода крошек, которые могут быть добавлены в контроллере
				Admin_Form_Entity::factory('Title')
					->name($this->title)
					->execute();

				$start_datetime = Core_Date::datetime2sql(Core_Array::get($this->_formValues, 'start_datetime', Core_Date::timestamp2sql(time() - 2678400)));
				$end_datetime = Core_Date::datetime2sql(Core_Array::get($this->_formValues, 'datetime', $this->_object->datetime));

				$oSite = Core_Entity::factory('Site', $this->_object->site_id);

				$aSeo = $oSite->Seos->getByDatetime($start_datetime, $end_datetime);

				$count = intval(Core_Array::get($this->_formValues, 'count', 10));

				$param = array('arrow' => TRUE);

				$oCore_Html_Entity_Table_For_Clone = Core::factory('Core_Html_Entity_Table')
					->border(0)
					->cellpadding(2)
					->cellspacing(2)
					->class('admin_table');

				//Если в отчете должен быть тИЦ
				if (intval(Core_Array::get($this->_formValues, 'tcy')))
				{
					Core::factory('Core_Html_Entity_H2')
						->value(Core::_('Seo.tcy'))
						->execute();

					Core::factory('Core_Html_Entity_Img')
						->src("/admin/seo/img.php?id_report=1&site_id={$oSite->id}&start_datetime={$start_datetime}&end_datetime={$end_datetime}")
						->align('center')
						->execute();

					$oCore_Html_Entity_Table = clone $oCore_Html_Entity_Table_For_Clone;

					// Не показываем некоторые столбцы, если общее количество таковых больше максимально разрешенного
					$report = $this->_buildMassReport($aSeo, "tcy", $count);

					// Дата
					$oCore_Html_Entity_Table->add($this->_showTableTitleReport($report));

					// тИЦ
					$oCore_Html_Entity_Table->add($this->_showTableRow($report, Core::_('Seo.tcy'), "tcy", $param));

					$oCore_Html_Entity_Table->execute();
				}

				//Если в отчете должен быть PageRank
				if (intval(Core_Array::get($this->_formValues, 'pr')))
				{
					Core::factory('Core_Html_Entity_H2')
						->value(Core::_('Seo.pr'))
						->execute();

					Core::factory('Core_Html_Entity_Img')
						->src("/admin/seo/img.php?id_report=5&site_id={$oSite->id}&start_datetime={$start_datetime}&end_datetime={$end_datetime}")
						->execute();

					$oCore_Html_Entity_Table = clone $oCore_Html_Entity_Table_For_Clone;

					// Не показываем некоторые столбцы, если общее количество таковых больше максимально разрешенного
					$report = $this->_buildMassReport($aSeo, "pr", $count);

					// Дата
					$oCore_Html_Entity_Table->add($this->_showTableTitleReport($report));

					// PageRank
					$oCore_Html_Entity_Table->add($this->_showTableRow($report, Core::_('Seo.pr'), "pr", $param));

					$oCore_Html_Entity_Table->execute();
				}

				$google_links = intval(Core_Array::get($this->_formValues, 'google_links', 0));
				$yandex_links = intval(Core_Array::get($this->_formValues, 'yandex_links', 0));
				//$yahoo_links = intval(Core_Array::get($this->_formValues, 'yahoo_links', 0));
				$bing_links = intval(Core_Array::get($this->_formValues, 'bing_links', 0));

				if ($google_links || $yandex_links /*|| $yahoo_links*/ || $bing_links)
				{
					Core::factory('Core_Html_Entity_H2')
						->value(Core::_('Seo.report_links'))
						->execute();

					Core::factory('Core_Html_Entity_Img')
						->src("/admin/seo/img.php?id_report=2&site_id={$oSite->id}&start_datetime={$start_datetime}&end_datetime={$end_datetime}&google_links={$google_links}&yandex_links={$yandex_links}&bing_links={$bing_links}")
						->execute();

					$oCore_Html_Entity_Table = clone $oCore_Html_Entity_Table_For_Clone;

					// Не показываем некоторые столбцы, если общее количество таковых больше максимально разрешенного
					$report = $this->_buildMassReport($aSeo, "links", $count);

					// Дата
					$oCore_Html_Entity_Table->add($this->_showTableTitleReport($report));

					if ($google_links)
					{
						$oCore_Html_Entity_Table->add($this->_showTableRow($report, Core::_('Seo.google'), "google_links", $param));
					}

					if ($yandex_links)
					{
						$oCore_Html_Entity_Table->add($this->_showTableRow($report, Core::_('Seo.yandex'), "yandex_links", $param));
					}

					/*if ($yahoo_links)
					{
						$oCore_Html_Entity_Table->add($this->_showTableRow($report, Core::_('Seo.yahoo'), "yahoo_links", $param));
					}*/

					if ($bing_links)
					{
						$oCore_Html_Entity_Table->add($this->_showTableRow($report, Core::_('Seo.bing'), "bing_links", $param));
					}

					$oCore_Html_Entity_Table->execute();
				}
				
				$yandex_indexed = intval(Core_Array::get($this->_formValues, 'yandex_indexed', 0));
				$yahoo_indexed = intval(Core_Array::get($this->_formValues, 'yahoo_indexed', 0));
				$bing_indexed = intval(Core_Array::get($this->_formValues, 'bing_indexed', 0));
				//$rambler_indexed = intval(Core_Array::get($this->_formValues, 'rambler_indexed', 0));
				$google_indexed = intval(Core_Array::get($this->_formValues, 'google_indexed', 0));

				if ($yandex_indexed || $yahoo_indexed || $bing_indexed /*|| $rambler_indexed*/ || $google_indexed)
				{
					Core::factory('Core_Html_Entity_H2')
						->value(Core::_('Seo.report_indexed'))
						->execute();

					Core::factory('Core_Html_Entity_Img')
						->src("/admin/seo/img.php?id_report=3&site_id={$oSite->id}&start_datetime={$start_datetime}&end_datetime={$end_datetime}&yandex_indexed={$yandex_indexed}&yahoo_indexed={$yahoo_indexed}&bing_indexed={$bing_indexed}&google_indexed={$google_indexed}")
						->execute();

					// Не показываем некоторые столбцы, если общее количество таковых больше максимально разрешенного
					$report = $this->_buildMassReport($aSeo, "indexed", $count);

					$oCore_Html_Entity_Table = clone $oCore_Html_Entity_Table_For_Clone;

					// Дата
					$oCore_Html_Entity_Table->add($this->_showTableTitleReport($report));

					// Yandex
					if ($yandex_indexed)
					{
						$oCore_Html_Entity_Table->add($this->_showTableRow($report, Core::_('Seo.yandex'), "yandex_indexed", $param));
					}

					// Rambler
					/*if ($rambler_indexed)
					{
						$oCore_Html_Entity_Table->add($this->_showTableRow($report, Core::_('Seo.rambler'), "rambler_indexed", $param));
					}*/

					// Google
					if ($google_indexed)
					{
						$oCore_Html_Entity_Table->add($this->_showTableRow($report, Core::_('Seo.google'), "google_indexed", $param));
					}

					// Yahoo
					if ($yahoo_indexed)
					{
						$oCore_Html_Entity_Table->add($this->_showTableRow($report, Core::_('Seo.yahoo'), "yahoo_indexed", $param));
					}

					// Bing.com
					if ($bing_indexed)
					{
						$oCore_Html_Entity_Table->add($this->_showTableRow($report, Core::_('Seo.bing'), "bing_indexed", $param));
					}

					$oCore_Html_Entity_Table->execute();
				}

				$yandex_catalog = intval(Core_Array::get($this->_formValues, 'yandex_catalog', 0));
				$rambler_catalog = intval(Core_Array::get($this->_formValues, 'rambler_catalog', 0));
				$dmoz_catalog = intval(Core_Array::get($this->_formValues, 'dmoz_catalog', 0));
				$aport_catalog = intval(Core_Array::get($this->_formValues, 'aport_catalog', 0));
				$mail_catalog = intval(Core_Array::get($this->_formValues, 'mail_catalog', 0));

				$param['status'] = TRUE;

				//Если в отчете должны быть данные о каталогах
				if ($yandex_catalog || $rambler_catalog || $dmoz_catalog || $aport_catalog || $mail_catalog)
				{
					Core::factory('Core_Html_Entity_H2')
						->value(Core::_('Seo.report_catalog'))
						->execute();

					$oCore_Html_Entity_Table = clone $oCore_Html_Entity_Table_For_Clone;

					// Не показываем некоторые столбцы, если общее количество таковых больше максимально разрешенного
					$report = $this->_buildMassReport($aSeo, "catalog", $count);

					// Дата
					$oCore_Html_Entity_Table->add($this->_showTableTitleReport($report));

					// Яндекс каталог
					if ($yandex_catalog)
					{
						$oCore_Html_Entity_Table->add($this->_showTableRow($report, Core::_('Seo.yandex'), "yandex_catalog", $param));
					}

					// Рамблер каталог
					if ($rambler_catalog)
					{
						$oCore_Html_Entity_Table->add($this->_showTableRow($report, Core::_('Seo.rambler'), "rambler_catalog", $param));
					}
					
					// Dmoz каталог
					if ($dmoz_catalog)
					{
						$oCore_Html_Entity_Table->add($this->_showTableRow($report, Core::_('Seo.dmoz_catalog'), "dmoz_catalog", $param));
					}
					
					// Апорт каталог
					if ($aport_catalog)
					{
						$oCore_Html_Entity_Table->add($this->_showTableRow($report, Core::_('Seo.aport_catalog'), "aport_catalog", $param));
					}

					// Mail каталог
					if ($mail_catalog)
					{
						$oCore_Html_Entity_Table->add($this->_showTableRow($report, Core::_('Seo.mail_catalog'), "mail_catalog", $param));
					}

					$oCore_Html_Entity_Table->execute();
				}

				$spylog_counter = intval(Core_Array::get($this->_formValues, 'spylog_counter', 0));
				$rambler_counter = intval(Core_Array::get($this->_formValues, 'rambler_counter', 0));
				$hotlog_counter = intval(Core_Array::get($this->_formValues, 'hotlog_counter', 0));
				$liveinternet_counter = intval(Core_Array::get($this->_formValues, 'liveinternet_counter', 0));
				$mail_counter = intval(Core_Array::get($this->_formValues, 'mail_counter', 0));

				// Если в отчете должны быть данные о наличии счетчиков
				if ($spylog_counter || $rambler_counter || $hotlog_counter || $liveinternet_counter || $mail_counter)
				{
					Core::factory('Core_Html_Entity_H2')
						->value(Core::_('Seo.report_counter'))
						->execute();

					$oCore_Html_Entity_Table = clone $oCore_Html_Entity_Table_For_Clone;

					// Не показываем некоторые столбцы, если общее количество таковых больше максимально разрешенного
					$report = $this->_buildMassReport($aSeo, "counter", $count);

					// Дата
					$oCore_Html_Entity_Table->add($this->_showTableTitleReport($report));

					// Счетчик SpyLog
					if ($spylog_counter)
					{
						$oCore_Html_Entity_Table->add($this->_showTableRow($report, Core::_('Seo.spylog_counter'), "spylog_counter", $param));
					}

					//счетчик Rambler's top 100
					if ($rambler_counter)
					{
						$oCore_Html_Entity_Table->add($this->_showTableRow($report, Core::_('Seo.rambler_counter'), "rambler_counter", $param));
					}

					//счетчик HotLog
					if ($hotlog_counter)
					{
						$oCore_Html_Entity_Table->add($this->_showTableRow($report, Core::_('Seo.hotlog_counter'), "hotlog_counter", $param));
					}

					//счетчик LiveInternet
					if ($liveinternet_counter)
					{
						$oCore_Html_Entity_Table->add($this->_showTableRow($report, Core::_('Seo.liveinternet_counter'), "liveinternet_counter", $param));
					}

					//счетчик Mail.ru
					if ($mail_counter)
					{
						$oCore_Html_Entity_Table->add($this->_showTableRow($report, Core::_('Seo.mail_counter'), "mail_counter", $param));
					}

					$oCore_Html_Entity_Table->execute();
				}

				$param['status'] = FALSE;

				$aSeo_Queries = $oSite->Seo_Queries->findAll();

				$yandex_position = intval(Core_Array::get($this->_formValues, 'yandex_position', 0));
				$rambler_position = intval(Core_Array::get($this->_formValues, 'rambler_position', 0));
				$google_position = intval(Core_Array::get($this->_formValues, 'google_position', 0));
				$yahoo_position = intval(Core_Array::get($this->_formValues, 'yahoo_position', 0));
				$bing_position = intval(Core_Array::get($this->_formValues, 'bing_position', 0));

				//Если в отчете должны быть данные о поисковых запросах
				if (count($aSeo_Queries) && ($yandex_position || $rambler_position ||$google_position || $yahoo_position || $bing_position ))
				{
					Core::factory('Core_Html_Entity_H1')
						->value(Core::_('Seo.report_position'))
						->execute();

					foreach ($aSeo_Queries as $oSeo_Query)
					{
						Core::factory('Core_Html_Entity_H2')
							->value(Core::_('Seo.report_position_text', $oSeo_Query->query))
							->execute();

						Core::factory('Core_Html_Entity_Img')
							->src("/admin/seo/img.php?id_report=4&seo_query_id={$oSeo_Query->id}&start_datetime={$start_datetime}&end_datetime={$end_datetime}&yandex_position={$yandex_position}&rambler_position={$rambler_position}&google_position{$google_position}&yahoo_position={$yahoo_position}&bing_position={$bing_position}")
							->execute();

						$param['inverse'] = true;
						$param['type'] = "position";

						$oCore_Html_Entity_Table = clone $oCore_Html_Entity_Table_For_Clone;

						$aSeo_Query_Positions = $oSeo_Query
							->Seo_Query_Positions
							->getByDatetime($start_datetime, $end_datetime);

						// Не показываем некоторые столбцы, если общее количество таковых больше максимально разрешенного
						$report = $this->_buildMassReport($aSeo_Query_Positions, "position", $count);

						// Дата
						$oCore_Html_Entity_Table->add($this->_showTableTitleReport($report));

						if ($yandex_position)
						{
							$oCore_Html_Entity_Table->add($this->_showTableRow($report, Core::_('Seo.yandex'), "yandex", $param));
						}

						if ($rambler_position)
						{
							$oCore_Html_Entity_Table->add($this->_showTableRow($report, Core::_('Seo.rambler'), "rambler", $param));
						}

						if ($google_position)
						{
							$oCore_Html_Entity_Table->add($this->_showTableRow($report, Core::_('Seo.google'), "google", $param));
						}

						if ($yahoo_position)
						{
							$oCore_Html_Entity_Table->add($this->_showTableRow($report, Core::_('Seo.yahoo'), "yahoo", $param));
						}

						if ($bing_position)
						{
							$oCore_Html_Entity_Table->add($this->_showTableRow($report, Core::_('Seo.bing'), "bing", $param));
						}

						$oCore_Html_Entity_Table->execute();
					}
				}

				$this->addContent(ob_get_clean());
				return TRUE;

			default:
				return FALSE; // Показываем форму
		}
	}

	/**
	* Графическое отображение статуса наличия счетчиков и страницы в каталогах в отчете
	*
	* @param bool $value Наличие сайта в каталоге, либо счетчика на странице - true, false - иначе
	* <code>
	* <?php
	* $Seo = new Seo();
	*
	* $value = true;
	*
	* $Seo->DrawStatusReport($value);
	* ?>
	* </code>
	*/
	protected function _drawStatusReport($value)
	{
		$oCore_Html_Entity_Img = Core::factory('Core_Html_Entity_Img');

		$value ? $oCore_Html_Entity_Img->src('/admin/images/check.gif')
			: $oCore_Html_Entity_Img->src('/admin/images/not_check.gif');

		ob_start();
			$oCore_Html_Entity_Img->execute();
		return ob_get_clean();
	}

	/**
	* Отображение шапки таблицы в отчете
	*
	* @param array $report Массив данных
	* - $report[]['seo_characteristic_date_time'] str Дата
	* - $report[]['seo_position_search_query_date_time'] str Дата
	* @param array $param Массив дополнительных параметров
	*/
	protected function _showTableTitleReport($report, $param = array())
	{
		$count = count($report);

		if (isset($param['count']))
		{
			$count = intval($param['count']);
		}
		else
		{
			$count = count($report);
		}

		$oCore_Html_Entity_Tr = Core::factory('Core_Html_Entity_Tr')
			->class('admin_table_title')
			->add(Core::factory('Core_Html_Entity_Td')->width('150px'));

		for($i = 0; $i < $count; $i++)
		{
			if (isset($report[$i]))
			{
				//Дата
				$oCore_Html_Entity_Tr->add(Core::factory('Core_Html_Entity_Td')
					->value(Core_Date::sql2date($report[$i]['datetime']))
				);
			}
		}

		return $oCore_Html_Entity_Tr;
	}

	/**
	 * Отображение строк таблицы в Отчете
	 *
	 * @param array $report Массив данных
	 * - $report[]['seo_characteristic_yc'] int тИЦ
	 * - $report[]['seo_characteristic_pr'] int PR
	 * - $report[]['seo_characteristic_links_google'] int Ссылающиеся страницы по данным Google
	 * - $report[]['seo_characteristic_links_yandex'] int Ссылающиеся страницы по данным Yandex
	 * - $report[]['seo_characteristic_links_yahoo'] int Ссылающиеся страницы по данным Yahoo
	 * - $report[]['seo_characteristic_links_msn'] int Ссылающиеся страницы по данным Bing.com
	 * - $report[]['seo_characteristic_indexed_aport'] int Индексированные страницы сервисом Aport
	 * - $report[]['seo_characteristic_indexed_yandex'] int Индексированные страницы сервисом Yandex
	 * - $report[]['seo_characteristic_indexed_yahoo'] int Индексированные страницы сервисом Yahoo
	 * - $report[]['seo_characteristic_indexed_msn'] int Индексированные страницы сервисом Bing.com
	 * - $report[]['seo_characteristic_indexed_rambler'] int Индексированные страницы сервисом Rambler
	 * - $report[]['seo_characteristic_indexed_google'] int Индексированные страницы сервисом Google
	 * - $report[]['seo_characteristic_catalog_yandex'] bool Наличие страницы в каталоге Yandex
	 * - $report[]['seo_characteristic_catalog_rambler'] bool Наличие страницы в каталоге Rambler
	 * - $report[]['seo_characteristic_catalog_mail'] bool Наличие страницы в каталоге Mail
	 * - $report[]['seo_characteristic_catalog_dmoz'] bool Наличие страницы в каталоге Dmoz
	 * - $report[]['seo_characteristic_catalog_aport'] bool Наличие страницы в каталоге Aport
	 * - $report[]['seo_characteristic_counter_rambler'] bool Наличие счетчика Rambler
	 * - $report[]['seo_characteristic_counter_spylog'] bool Наличие счетчика SpyLog
	 * - $report[]['seo_characteristic_counter_hotlog'] bool Наличие счетчика HotLog
	 * - $report[]['seo_characteristic_counter_mail'] bool Наличие счетчика Mail
	 * - $report[]['seo_characteristic_counter_liveinternet'] bool Наличие счетчика LiveInternet
	 * @param str $field_name Название строки
	 * @param str $field_value Название поля БД
	 * @param array $param Массив дополнительных параметров
	 * - $param['arrow'] bool Отображение стрелочек динамики изменения значений
	 * - $param['status'] bool Графическое отображение статуса наличия счетчиков и страницы в каталогах
	 * - $param['inverse'] bool Инвертирование отображения динамики изменения значений
	 * - $param['count'] int Количество строк в массиве данных
	 */
	protected function _showTableRow($report, $field_name, $field_value, $param = array())
	{
		// Не задана инверсия значений
		if (!isset($param['inverse']))
		{
			$param['inverse'] = false;
		}

		// Количество строк в массиве данных
		if (isset($param['count']))
		{
			$count = intval($param['count']);
		}
		else
		{
			$count = count($report);
		}

		// Не отображать стрелочки динамики изменения значений
		if (!isset($param['arrow']))
		{
			$param['arrow'] = false;
		}

		// Не отображать графически статус наличия счетчиков и страницы в каталогах
		if (!isset($param['status']))
		{
			$param['status'] = false;
		}

		// Не задан тип набора значений
		if (!isset($param['type']))
		{
			$param['type'] = false;
		}

		$oCore_Html_Entity_Tr = Core::factory('Core_Html_Entity_Tr')
			->class('row')
			->add(Core::factory('Core_Html_Entity_Td')->width('150px')
				->value($field_name));

		$prev = false;

		// Значения
		for($i = 0; $i < $count; $i++)
		{
			if ($param['status'])
			{
				$oCore_Html_Entity_Tr->add(Core::factory('Core_Html_Entity_Td')
					->align('center')
					->value($this->_drawStatusReport($report[$i][$field_value])));
			}
			elseif (isset($report[$i]))
			{
				$sValue = ($param['type'] == 'position' && $report[$i][$field_value] == 0)
					? '&mdash;'
					: $report[$i][$field_value];

				// Рисуем стрелочки
				if ($param['arrow'])
				{
					$sValue .= $param['inverse']
						? $this->_showArrowQuery($prev, $report[$i][$field_value])
						: $this->_showArrow($prev, $report[$i][$field_value]);
				}

				$oCore_Html_Entity_Tr->add(Core::factory('Core_Html_Entity_Td')
					->align('center')
					->value($sValue));
			}

			if (isset($report[$i]))
			{
				$prev = $report[$i][$field_value];
			}
		}

		return $oCore_Html_Entity_Tr;
	}

	/**
	* Игнорирование столбцов таблицы
	*
	* @param array $report Массив данных
	* @param str $value_type Тип поля значений
	* @param int $column_count column count
	* @return array
	*/
	protected function _buildMassReport($report, $value_type, $column_count)
	{
		$field_value = array();

		// Тип поля значений
		switch ($value_type)
		{
			default:
					break;
			case 'tcy':
				$field_value[] = "tcy";
				break;
			case 'pr':
				$field_value[] = "pr";
				break;
			case 'links':
				$field_value[] = "google_links";
				$field_value[] = "yandex_links";
				//$field_value[] = "yahoo_links";
				$field_value[] = "bing_links";
				break;
			case 'indexed':
				$field_value[] = "yandex_indexed";
				//$field_value[] = "rambler_indexed";
				$field_value[] = "google_indexed";
				$field_value[] = "yahoo_indexed";
				$field_value[] = "bing_indexed";
				break;
			case 'catalog':
				$field_value[] = "yandex_catalog";
				$field_value[] = "rambler_catalog";
				$field_value[] = "dmoz_catalog";
				$field_value[] = "aport_catalog";
				$field_value[] = "mail_catalog";
				break;
			case 'counter':
				$field_value[] = "spylog_counter";
				$field_value[] = "rambler_counter";
				$field_value[] = "hotlog_counter";
				$field_value[] = "liveinternet_counter";
				$field_value[] = "mail_counter";
				break;
			case 'position':
				$field_value[] = "yandex";
				$field_value[] = "rambler";
				$field_value[] = "google";
				$field_value[] = "yahoo";
				$field_value[] = "bing";
				break;
		}

		// Количество элементов массива
		$count = count($report);
		$column_count = min($count, intval($column_count));

		// Формируем массив значений
		$array = array();

		for ($i = 0; $i < $count; $i++)
		{
			for ($j = 0; $j < count($field_value); $j++)
			{
				$array[$i][$field_value[$j]] = $report[$i]->$field_value[$j];
			}
		}

		// Итоговый массив элементов
		$array_value_final = array();

		if (count($array) == 0)
		{
			return $array_value_final;
		}

		// Формируем массив столбцов, входящих в диапазон отображаемых
		$array_value = array();

		// Массив с индексами элементов массива, которые учитываются (Нужен для корректного добавления дат в массив)
		$index = array();

		// Итоговый массив индексов учитываемых элементов (Нужен для корректного добавления дат в массив)
		$index_final = array();

		// Индекс массива $array_value
		$j = 0;

		// Первое значение
		$array_value[$j] = $array[0];
		$index[$j] = 0;

		// число невошедших элементов
		$count_deleted = 0;

		// -2 - Первый элемент (уже добавлен в массив) + Последний элемент (будет добавлен позже)
		for ($i = 1; $i < count($array) - 1; $i++)
		{
			// В первую очередь отбрасываем одинаковые столбцы
			if ($count - $count_deleted > $column_count)
			{
				// Если элемент массива $report неравен предыдущему добавленному элементу массива $array_value
				if ($array[$i] !== $array_value[$j])
				{
					$array_value[++$j] = $array[$i];
					$index[$j] = $i;
				}
				else
				{
					$count_deleted++;
				}
			}
			else
			{
				// Добавляем очередной элемент в массив
				$array_value[++$j] = $array[$i];
				$index[$j] = $i;
			}
		}

		// Если число элементов все еще не меньше разрешенного
		if (count($array_value) >= $column_count && $column_count > 1)
		{
			// Определяем, каждый какой элемент будем учитывать
			$quotient = count($array_value) / ($column_count - 1); // -1 Оставляем "место" для последнего элемента

			$j = 0;
			$array_value_final[$j] = $array_value[0];
			$index_final[$j] = 0;

			// Индекс первого учитываемого элемента
			$ind = $quotient;
			
			// Формируем массив из учитываемых элементов
			while (floor($ind) < count($array_value))
			{
				if (count($array_value_final) < $column_count - 1) // -1 Оставляем "место" для последнего элемента
				{
					$array_value_final[++$j] = $array_value[floor($ind)];
					$index_final[$j] = $index[floor($ind)];
					$ind += $quotient;
				}
				else
				{
					break;
				}
			}
		}
		else
		{
			$array_value_final = $array_value;
			$index_final = $index;
		}

		// Последний элемент
		$array_value_final[++$j] = end($array);
		$index_final[$j] = count($array) - 1;

		// Добавляем в массив даты
		foreach ($array_value_final as $key => $val)
		{
			$array_value_final[$key]['datetime'] = $report[$index_final[$key]]->datetime;
		}

		return $array_value_final;
	}

	/**
	* Отображение стрелочек динамики изменения значений
	*
	* @param int $prev_value предыдущее значение
	* @param int $current_value текущее значение
	* <code>
	* <?php
	* $Seo = new Seo();
	*
	* $prev_value = 1;
	* $current_value = 10;
	*
	* $Seo->ShowArrow($prev_value, $current_value);
	* ?>
	* </code>
	*/
	protected function _showArrow($prev_value, $current_value)
	{
		if ($prev_value != false && $current_value != false)
		{
			$oCore_Html_Entity_Span = Core::factory('Core_Html_Entity_Span');

			if ($prev_value < $current_value)
			{
				$oCore_Html_Entity_Span->class('arrow_up')
					->value('&uarr;');
			}
			elseif ($prev_value > $current_value)
			{
				$oCore_Html_Entity_Span->class('arrow_down')
					->value('&darr;');
			}

			ob_start();
				$oCore_Html_Entity_Span->execute();
			return ob_get_clean();
		}
	}

	/**
	* Отображение стрелочек динамики изменения значений поисковых запросов
	*
	* @param int $prev_value предыдущее значение
	* @param int $current_value текущее значение
	* <code>
	* <?php
	* $Seo = new Seo();
	*
	* $prev_value = 1;
	* $current_value = 10;
	*
	* $Seo->ShowArrowQuery($prev_value, $current_value);
	* ?>
	* </code>
	*/
	protected function _showArrowQuery($prev_value, $current_value)
	{
		if ($prev_value !== false && $current_value !== false)
		{
			if($current_value != 0)
			{
				$oCore_Html_Entity_Span = Core::factory('Core_Html_Entity_Span');

				if ($prev_value > $current_value || $prev_value == 0)
				{
					$oCore_Html_Entity_Span->class('arrow_up')
						->value('&uarr;');
				}
				elseif ($prev_value < $current_value && $prev_value !== 0)
				{
					$oCore_Html_Entity_Span->class('arrow_down')
						->value('&darr;');
				}

				ob_start();
					$oCore_Html_Entity_Span->execute();
				return ob_get_clean();
			}
		}
	}
}