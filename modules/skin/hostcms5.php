<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Skin.
 *
 * @package HostCMS 6\Skin
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2014 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Skin_Hostcms5 extends Core_Skin
{
	/**
	 * Name of the skin
	 * @var string
	 */
	protected $_skinName = 'hostcms5';

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		parent::__construct();

		$lng = $this->getLng();

		$this
			->addJs('/admin/js/jquery/jquery.elastic.js')
			->addJs('/admin/js/jquery/jquery.tools.js')
			->addJs('/admin/js/jquery/jquery.form.js')
			->addJs('/admin/js/main.js')
			->addJs('/admin/js/hostcms6.js')
			->addJs('/admin/js/ui/jquery-ui.js')
			->addJs('/admin/js/ui/i18n/jquery.ui.datepicker-' . $lng . '.js')
			->addJs('/admin/js/ui/functions.js')
			->addJs('/admin/js/ui/jquery-HostCMSWindow.js')
			->addJs('/admin/js/ui/timepicker/timepicker.js')
			->addJs('/admin/js/ui/timepicker/i18n/jquery-ui-timepicker-' . $lng . '.js')
			->addJs('/admin/js/ui/stars/jquery.ui.stars.js')
			->addJs('/admin/js/fusionchart/FusionCharts.js')
			->addJs('/admin/js/typeahead-bs2.min.js')
			->addJs('/admin/js/bootstrap-tag.js')
			->addJs('/admin/js/ace.js')
			->addJs('/admin/js/codemirror/lib/codemirror.js')
			->addJs('/admin/js/codemirror/mode/css/css.js')
			->addJs('/admin/js/codemirror/mode/htmlmixed/htmlmixed.js')
			->addJs('/admin/js/codemirror/mode/javascript/javascript.js')
			->addJs('/admin/js/codemirror/mode/clike/clike.js')
			->addJs('/admin/js/codemirror/mode/php/php.js')
			->addJs('/admin/js/codemirror/mode/xml/xml.js')
			->addJs('/admin/js/codemirror/addon/selection/active-line.js')
			;

		//if (defined('USE_HOSTCMS_5') && USE_HOSTCMS_5)
		//{
			$this
				->addJs('/modules/skin/' . $this->_skinName . '/js/JsHttpRequest.js')
				->addJs('/modules/skin/' . $this->_skinName . '/js/hostcms5.js');
		//}

		$this
			->addCss('/modules/skin/' . $this->_skinName . '/css/style.css')
			->addCss('/admin/js/ui/themes/base/jquery.ui.all.css')
			->addCss('/admin/js/ui/ui.css')
			->addCss('/admin/js/ui/stars/ui.stars.css')
			->addCss('/admin/js/codemirror/lib/codemirror.css')
			;
	}

	/**
	 * Show HTML head
	 */
	public function showHead()
	{
		$timestamp = $this->_getTimestamp();

		$lng = $this->getLng();

		foreach ($this->_css as $sPath)
		{
			?><link type="text/css" href="<?php echo $sPath . '?' . $timestamp?>" rel="stylesheet"></link><?php
			echo PHP_EOL;
		}?>

		<!--[if IE 7]>
			<link rel="stylesheet" type="text/css" href="/modules/skin/<?php echo $this->_skinName?>/css/ie_hacks.css" />
		<![endif]-->

		<script type="text/javascript">if(!window.jQuery) {document.write('<scri'+'pt type="text/javascript" src="/admin/js/jquery/jquery.js"></scr'+'ipt>');}</script>

		<?php
		$this->addJs("/admin/js/lng/{$lng}/{$lng}.js");
		foreach ($this->_js as $sPath)
		{
			Core::factory('Core_Html_Entity_Script')
				->type("text/javascript")
				->src($sPath . '?' . $timestamp)
				->execute();
		}
		?>

		<script type="text/javascript">
		$(function() {
			$.datepicker.setDefaults({showAnim: 'slideDown', dateFormat: 'dd.mm.yy', showButtonPanel: true});
			$.afterContentLoad($("#id_content"));
			CreateWindow('copyright', '', '310', '80');
			timeout_copiright = 0;
		});
		</script>

		<?php /*if (defined('DENY_GZIP_WYSIWYG') && DENY_GZIP_WYSIWYG)
		{
		?><script type="text/javascript" src="/admin/wysiwyg/tiny_mce_src.js?<?php echo $timestamp?>"></script><?php
		}
		else
		{
			?><script type="text/javascript" src="/admin/wysiwyg/tiny_mce_gzip.js"></script>
			<script type="text/javascript">
			tinyMCE_GZ.init({
				plugins : 'safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,inlinepopups',
				themes : 'advanced',
				languages : '<?php echo $lng?>',
				disk_cache : true,
				compress : true,
				suffix : '_src',
				debug : false
			});
			</script><?php
		}*/ ?>
		<script type="text/javascript"><?php
		if (Core_Auth::logged())
		{
			// Получаем данные о корневом пути для группы, в которой размещен текущий пользователь
			$oUser = Core_Entity::factory('User')->getCurrent();
			?>var HostCMSFileManager = new HostCMSFileManager('<?php echo Core_Str::escapeJavascriptVariable($oUser
				? $oUser->User_Group->root_dir
				: '')?>');<?php
		}
		?></script>
		<script type="text/javascript" src="/admin/wysiwyg/jquery.tinymce.js"></script>
		<?php

		return $this;
	}

	/**
	 * Show header
	 */
	public function header()
	{
		//$timestamp = $this->_getTimestamp();

		?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru">
<head>
<title><?php echo $this->_title?></title>
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7"></meta>
<meta content="text/html; charset=UTF-8" http-equiv="Content-Type"></meta>
<link rel="shortcut icon" href="/admin/favicon.ico"></link>
<?php $this->showHead()?>
</head>
<body class="hostcmsWindow backendBody">
<div id="header">
<div class="left_box">

<div class="logo"><a href="/admin/"
<?php echo (isset($_SESSION['valid_user']) ? 'onclick="'."$.adminLoad({path: '/admin/index.php'}); return false".'"' : '')?>><img
	src="/admin/images/logo.gif" alt="(^) HostCMS"
	title="HostCMS <?php echo (isset($_SESSION["valid_user"])) ? 'v. ' . strip_tags(CURRENT_VERSION) : ''?>" /></a>
</div>

<?php
if (Core_Auth::logged())
{
	$oUser = Core_Entity::factory('User')->getCurrent();

	if (is_null($oUser))
	{
		throw new Core_Exception('Undefined user.', array(), 0, FALSE, 0, FALSE);
	}

	?><div class="box3" style="float: right; margin: 0px 10px 10px 0px;"><img class="left_img" src="/admin/images/language.gif" /> <?php

	$oAdmin_Languages = Core_Entity::factory('Admin_Language')->findAll();

	$aOptions = array();

	foreach ($oAdmin_Languages as $oAdmin_Language)
	{
		if ($oAdmin_Language->active == 1)
		{
			$aOptions[$oAdmin_Language->shortname] = $oAdmin_Language->name;
		}
	}

	Core::factory('Core_Html_Entity_Select')
		->style('margin-right: 10px')
		->name('lng_value')
		->onchange("window.location=('?lng_value='+this.options[this.selectedIndex].value)")
		->options($aOptions)
		->value(Core_Array::get($_SESSION, 'current_lng'))
		->execute();

?></div>

<div class="box3" style="float: right; margin: 0px 10px 0px 0px;"><img
	class="left_img" src="/admin/images/sites.gif" />

	<?php
	$aSites = $oUser->getSites();

	$aOptions = array();
	foreach ($aSites as $oSite)
	{
		$aOptions[$oSite->id] = Core_Str::cut($oSite->name, 35);
	}

	Core::factory('Core_Html_Entity_Select')
		->name("changeSiteId")
		->onchange("window.location=('?changeSiteId='+this.options[this.selectedIndex].value)")
		->options($aOptions)
		->value(Core_Array::get($_SESSION, 'current_site_id'))
		->execute();

	if (isset($_SESSION['current_site_id']))
	{
		$oSite_Alias = Core_Entity::factory('Site', $_SESSION['current_site_id'])->getCurrentAlias();
		$alias = $oSite_Alias
			? $oSite_Alias->name
			: '';
	}
	else
	{
		$alias = '';
	}

	?><a href="http://<?php echo $alias?>" target="_blank" title="<?php echo Core::_('Admin.viewSite')?>"><img class="right_img" src="/admin/images/eye.gif" alt="<&bull;>" title="<&bull;>" /></a></div><?php
}
?></div><?php

if (Core_Auth::logged())
{
?><div class="right_box">
<div class="box3" style="float: left; margin-bottom: 8px">
<div style="margin: 4px 0px"><img class="left_img" src="<?php echo ($oUser && $oUser->superuser == 1)
? "/admin/images/superuser.gif"
: "/admin/images/user.gif"?>" /> <?php echo $_SESSION['valid_user']?>
<a href="/admin/logout.php"><img class="right_img" src="/admin/images/exit.gif" alt="&rarr;" title="&rarr;" style="margin-left: 10px" /></a></div>
</div>

	<?php
	if (!((~Core_Array::get(Core::$config->get('core_hostcms'), 'hostcms')) & (~1835217467)))
	{
		!defined('HTTP_PURCHASE') && define('HTTP_PURCHASE', 'www.hostcms.ru/orders/');
		?><div class="box3" style="float: left; width: 180px;">
<img class="left_img" src="/admin/images/buy.gif" /> <a href="http://<?php echo HTTP_PURCHASE?>" class="order" target="_blank"><?php echo Core::_('Core.purchase_commercial_version')?></a></div>
<?php
	}
}
?></div>
</div>

<div id="body"><!-- Левый блок -->
<div class="left_box" id="body_left_box" style="margin-right: 0px; border-right: 0px">
<div style="padding-right: 30px"><?php

		return $this;
	}

	/**
	 * Show authorization form
	 */
	public function authorization()
	{
		$this->_mode = 'authorization';

		?><h1><?php echo Core::_('Admin.authorization_form_title')?></h1>

		<table cellpadding="0" cellspacing="0" border="0" class="t_authorization">
		<tr>
			<td width="350">
			<div class="box0" style="margin-top: 30px; float: left;">
			<form method="post" action="/admin/index.php" id="authorization" style="padding: 15px">

				<div><?php echo Core::_('Admin.authorization_form_login')?></div>
				<div><input type="text" name="login" id="login" class="large"></div>

				<div style="margin-top: 10px"><?php echo Core::_('Admin.authorization_form_password')?></div>
				<div><input type="password" name="password" class="large"></div>

				<div><input type="checkbox" name="ip" id="ip" class="checkbox_authorization" checked="checked">&nbsp;<label for="ip"><?php echo Core::_('Admin.authorization_form_ip')?></label></div>

				<div>
					<input name="submit" value="<?php echo Core::_('Admin.authorization_form_button')?>"
					type="submit" class="authorization" />
				</div>
			</form>

			<script type="text/javascript">
			$("#authorization #login").focus();
			</script></div>
			</td>
			<td>
			<div class="authorization_message"><?php echo Core::_('Admin.authorization_notice')?>
			<?php echo Core::_('Admin.authorization_notice2')?></div>
			</td>
		</tr>
		</table><?php
	}

	/**
	 * Show footer
	 */
	public function footer()
	{
		?></div>
</div><?php

?><div id="body_right_box" class="right_box"><?php
if (Core_Auth::logged())
{
	// Список основных меню
	$aCore_Config = Core::$mainConfig;

	$oUser = Core_Entity::factory('User')->getCurrent();

	if (isset($aCore_Config['adminMenu']))
	{
		$aModules = $this->_getAllowedModules();
		foreach ($aModules as $oModule)
		{
			$oCore_Module = Core_Module::factory($oModule->path);

			if ($oCore_Module && is_array($oCore_Module->menu))
			{
				foreach ($oCore_Module->menu as $aMenu)
				{
					$aCore_Config['adminMenu']
						[$aMenu['block']]
						['sub'][] = $aMenu + array('sorting' => 0, 'block' => 0);
				}
			}
		}

		$oCore_Html_Entity_Div = Core::factory('Core_Html_Entity_Div')
			->id('MainMenu');

		foreach ($aCore_Config['adminMenu'] as $key => $aAdminMenu)
		{
			$aAdminMenu += array(
				'image' => '/admin/images/system.gif'
			);

			$key = intval($key);

			if (isset($aAdminMenu['sub']))
			{
				array_multisort($aAdminMenu['sub']);

				$oCore_Html_Entity_Div_Box = Core::factory('Core_Html_Entity_Div')
					->class("box{$key}")
					->add(
						Core::factory('Core_Html_Entity_Img')
							->class('left_img')
							->src($aAdminMenu['image'])
					)
					->add(
						Core::factory('Core_Html_Entity_Span')
							->onclick("SubMenu('id_{$key}')")
							->value(nl2br(htmlspecialchars(Core::_("Core.admin_menu_{$key}"))))
					);

				// Sub menu
				$oCore_Html_Entity_Div_SubMenu = Core::factory('Core_Html_Entity_Div')
					->id("id_{$key}")
					->class('sub_menu');

				$oCore_Html_Entity_Ul = Core::factory('Core_Html_Entity_Ul');

				foreach ($aAdminMenu['sub'] as $aSubMenu)
				{
					$oCore_Html_Entity_Ul
						->add(
							Core::factory('Core_Html_Entity_Li')
								->add(
									Core::factory('Core_Html_Entity_A')
										->value($aSubMenu['name'])
										->href($aSubMenu['href'])
										->onclick($aSubMenu['onclick'])
								)
						);
				}

				$oCore_Html_Entity_Div_SubMenu
					->add($oCore_Html_Entity_Ul);

				$oCore_Html_Entity_Div
					->add($oCore_Html_Entity_Div_Box)
					->add($oCore_Html_Entity_Div_SubMenu);

				// Скрываем меню, если надо
				if (Core_Bit::getBit($oUser->settings, $key) == 1)
				{
					$oCore_Html_Entity_Div
						->add(
							Core::factory('Core_Html_Entity_Script')
								->type("text/javascript")
								->value(
									"var divId = 'id_{$key}';" .
									"var div = document.getElementById(divId);" .
									"aHeights[divId] = div.clientHeight;" .
									"div.style.height = '0px';"
								)
						);
				}
			}
		}

		$oCore_Html_Entity_Div->execute();
	}

	$oMainMenuShowHide_Div = Core::factory('Core_Html_Entity_Div')
		->id('MainMenuShowHide')
		->add(
			Core::factory('Core_Html_Entity_Img')
				->class('right_img')
				->id("MainMenuImg")
				->onclick("MainMenu('MainMenu')")
				->src("/admin/images/menu_show.gif")
				->alt("<>")
				->title("<>")
		)
		->execute();

	// Скрываем основное меню, если надо. Его состояние хранится в 7 бите
	if (Core_Bit::getBit($oUser->settings, 7) == 1)
	{
		Core::factory('Core_Html_Entity_Script')
			->type("text/javascript")
			->value("HideMainMenu('MainMenu');")
			->execute();
	}
}
?></div>

</div>

<div id="copyright"
	onmousemove="clear_timeout_copiright();"
	onmouseout="set_timeout_copyright();" style="white-space: nowrap;">

	<div class="shadow_tail"><img src="/admin/images/shadow_tail.gif"></div>

	<strong><?php echo Core::_('Core.info_cms')?> HostCMS v. 6</strong>
	<br />
	<br />
	<?php echo Core::_('Core.info_cms_site')?> <a href="http://www.hostcms.ru" target="_blank">www.hostcms.ru</a>
	<br />
	<?php echo Core::_('Core.info_cms_support')?> <a href="mailto:support@hostcms.ru">support@hostcms.ru</a>
	<br />
	<?php echo Core::_('Core.info_cms_sales')?> <a href="mailto:sales@hostcms.ru">sales@hostcms.ru</a>
	</div>

<div id="footer">
	<div>Copyright &copy; 2005&ndash;2014 <span onmousemove="ShowWindow('copyright'); copyright_position('copyright');" onmouseout="set_timeout_copyright();">ООО &laquo;Хостмэйк&raquo;</span></div>
</div>

<!--[if lte IE 6]>
<script type="text/javascript" src="/modules/skin/<?php echo $this->_skinName?>/js/ie_resize.js"></script>
<![endif]-->

</body>
</html>
<?php
	}

	/**
	 * Change language
	 */
	public function changeLanguage()
	{
		$this->_mode = 'changeLanguage';

		?>
		<h1><?php echo Core::_('Install.changeLanguage')?></h1>

		<form name="authorization" action="./index.php" method="post">
		<p>
			<select name="lng_value" style="font-size: 150%">
			<?php

			$aInstallConfig = Core_Config::instance()->get('install_config');
			$aLng = Core_Array::get($aInstallConfig, 'lng', array());

			foreach ($aLng as $shortname => $name)
			{
				?><option value="<?php echo htmlspecialchars($shortname)?>"><?php echo htmlspecialchars($name)?></option><?php
			}
			?>
			</select>
		</p>
		<div style="float: right">
			<input name="step_0" value="<?php echo Core::_('Install.next')?>" type="submit" class="next" />
		</div>
		</form>
		<?php
	}

	/**
	 * Show back-end index page
	 * @return self
	 */
	public function index()
	{
		$this->_mode = 'index';

		$oUser = Core_Entity::factory('User')->getCurrent();

		if (is_null($oUser))
		{
			return FALSE;
		}

		$show_sub_menu = Core_Array::getPost('show_sub_menu');
		$hide_sub_menu = Core_Array::getPost('hide_sub_menu');
		$main_menu = Core_Array::getPost('main_menu');

		// Если было изменение состояния меню - сохраняем
		// 0 - меню отображается, 1 - меню скрыто
		if (!is_null($show_sub_menu) || !is_null($hide_sub_menu) || !is_null($main_menu))
		{
			if (!is_null($show_sub_menu))
			{
				// Устанавливаем в настройках бит
				$oUser->settings = Core_Bit::setBit($oUser->settings, $show_sub_menu, 0);
			}
			elseif (!is_null($hide_sub_menu))
			{
				// Сбрасываем в настройках бит
				$oUser->settings = Core_Bit::setBit($oUser->settings, $hide_sub_menu, 1);
			}
			else
			{
				// Скрытие/показ основного меню, 0 - показали, 1 - скрыли
				// Для состояния скрытие-открытие меню зарезервирован 7 бит
				$oUser->settings = Core_Bit::setBit($oUser->settings, 7, $main_menu);
			}

			$oUser->save();

			$oAdmin_Answer = Core_Skin::instance()->answer();
			$oAdmin_Answer
				->ajax(Core_Array::getPost('_', FALSE))
				->execute();
			exit();
		}

		$oSite = Core_Entity::factory('Site', CURRENT_SITE);
		if (Core_Array::getRequest('save_notes'))
		{
			ob_start();
			if (defined('READ_ONLY') && READ_ONLY || $oUser->read_only)
			{
				Core_Message::show(Core::_('User.demo_mode'));
			}
			else
			{
				if ($oUser->checkModuleAccess(array('site'), $oSite))
				{
					$oSite->notes = Core_Array::getRequest('notes');
					$oSite->save();

					Core_Message::show(Core::_('Admin.notes_save'));
				}
			}

			$oAdmin_Answer = Core_Skin::instance()->answer();
			$oAdmin_Answer
				->ajax(Core_Array::getRequest('_', FALSE))
				->message(ob_get_clean())
				->execute();

			exit();
		}

		/**
		 * Функция проверки необходимости вывода новой строки таблицы
		 *
		 * @param int $count_td
		 * @package HostCMS 5
		 * @author Hostmake LLC
		 * @version 5.x
		 */
		function NewTr($count_td)
		{
			if (($count_td % 2) == 0)
			{
				?></tr><tr><?php
			}
		}

		if (Core_Entity::factory('User')->getByLoginAndPassword('admin', 'admin'))
		{
			Core_Message::show(Core::_('Admin.index_events_bad_password'), 'error');
		}

		// Проверка на наличие временной директории
		if (!is_dir(CMS_FOLDER . TMP_DIR))
		{
			Core_Message::show(Core::_('Admin.index_unset_tmp_dir', TMP_DIR), 'error');
		}

		if (class_exists('update')
		&& defined('HOSTCMS_USER_LOGIN')
		&& defined('HOSTCMS_CONTRACT_NUMBER')
		&& defined('HOSTCMS_PIN_CODE'))
		{
			try
			{
				$update = new update();

				// Проверяем наличие обновлений
				$update_file = $update->GetUpdatePath() . '/updatelist.xml';

				if (!file_exists($update_file)
				|| time() >= @filemtime($update_file) + 4 * 60 * 60)
				{
					$login = HOSTCMS_USER_LOGIN;
					$contract = HOSTCMS_CONTRACT_NUMBER;
					$pin = HOSTCMS_PIN_CODE;
					$cms_folder = CMS_FOLDER;
					$php_version = phpversion();
					$mysql_version = Core_DataBase::instance()->getVersion();

					$oHOSTCMS_UPDATE_NUMBER = Core_Entity::factory('Constant')->getByName('HOSTCMS_UPDATE_NUMBER');
					$update_id = is_null($oHOSTCMS_UPDATE_NUMBER) ? 0 : $oHOSTCMS_UPDATE_NUMBER->value;

					$oSite_Alias = $oSite->getCurrentAlias();

					$domain = $oSite_Alias
						? $oSite_Alias->name
						: '';

					if (!$update->GetUpdate(array (
							'login' => $login,
							'contract' => $contract,
							'pin' => $pin,
							'cms_folder' => $cms_folder,
							'php_version' => $php_version,
							'mysql_version' => $mysql_version,
							'update_id' => $update_id,
							'domain' => $domain,
							'update_file' => $update_file,
							'update_server' => HOSTCMS_UPDATE_SERVER
					)))
					{
						Core_Message::show(Core::_('Update.error_write_file_update', $update_file), 'error');
					}
				}

				$count_r = 0;

				if (file_exists($update_file) && is_readable($update_file))
				{
					$kernel = & singleton('kernel');
					$data_array = $kernel->Xml2Array(@ file_get_contents($update_file));

					if (isset ($data_array['children'])
					&& isset ($data_array['children'][0])
					&& isset ($data_array['children'][0]['children'])
					&& $data_array['children'][0]['children'])
					{
						foreach ($data_array['children'][0]['children'] as $key => $value)
						{
							if (isset($value['name']) && $value['name'] == 'update')
							{
								// Количество строк для отображения
								$count_r++;
							}
						}
					}
				}

				if ($count_r)
				{
					Core_Message::show(Core::_('Admin.updates_count_access', $count_r, $kernel->declension($count_r, Core::_('Admin.base_word'), array(
						Core::_('Admin.add_word0'), Core::_('Admin.add_word1'),
						Core::_('Admin.add_word2'), Core::_('Admin.add_word2'),
						Core::_('Admin.add_word2'), Core::_('Admin.add_word0'),
						Core::_('Admin.add_word0'), Core::_('Admin.add_word0'),
						Core::_('Admin.add_word0'), Core::_('Admin.add_word0')
					))));
				}
			}
			catch (Exception $e){
				Core_Message::show($e->getMessage(), 'error');
			}
		}

		// Вывод информации на главную страницу
		$count_td = 0;
		?>
		<table cellpadding="0" cellspacing="5" width="100%" border="0">
		<tr>
		<?php

		$oCore_Log = Core_Log::instance();
		$file_name = $oCore_Log->getLogName(date('Y-m-d'));

		if ((!defined('SHOW_SYSTEM_EVENTS') || SHOW_SYSTEM_EVENTS) && is_file($file_name))
		{
			?>
			<td width="50%" valign="top" class="index_table_td">

			<div class="main_div"><span class="div_title"><?php echo Core::_('Admin.index_systems_events')?></span>
			<div class="div_content"><?php

			// Открываем log-файл
			if ($fp = @fopen($file_name, 'r'))
			{
				$aLines = array();
				$iSize = @filesize($file_name);

				$iSlice = 10240;

				if ($iSize > $iSlice)
				{
					fseek($fp, $iSize - $iSlice);
				}

				// [0]-дата/время, [1]-имя пользователя, [2]-события, [3]-статус события, [4]-сайт, [5]-страница
				while (!feof($fp))
				{
					$event = fgetcsv($fp, $iSlice);

					if (empty($event[0]) || count($event) < 3)
					{
						continue;
					}

					$aLines[] = $event;
				}

				if (count($aLines) > 3)
				{
					$aLines = array_slice($aLines, -3);
				}

				$aLines = array_reverse($aLines);

				?><table cellspacing="2" cellpadding="2" width="100%" class="admin_table">
				<tr class="admin_table_title">
					<td><b><?php echo Core::_('Admin.index_events_journal_date')?></b></td>
					<td><b><?php echo Core::_('Admin.index_events_journal_user')?></b></td>
					<td><b><?php echo Core::_('Admin.index_events_journal_event')?></b></td>
				</tr>
				<?php
				foreach ($aLines as $aLine)
				{
					if (count($aLine) > 2)
					{
						switch (Core_Type_Conversion::toInt($aLine[3]))
						{
							case 4 :
								$style = 'color: #8e0700; font-weight: bold;';
							break;
							case 3 :
								$style = 'color: #a25b00;';
							break;
							case 2 :
								$style = 'color: #b8b600;';
							break;
							case 1 :
								$style = 'color: #339933;';
							break;
							case 0 :
							default:
								$style = 'color: #444444;';
							break;
						}
						?><tr class="row">
						<td width="120"><?php echo htmlspecialchars(Core_Date::sql2datetime($aLine[0]))?></td>
						<td><b><?php echo htmlspecialchars($aLine[1])?></b></td>
						<td style="<?php echo $style?>"><?php echo htmlspecialchars(Core_Str::cut($aLine[2], 70))?></td>
						</tr><?php
					}
				}
				unset($aLines);

				?></table><?php

				if (Core::moduleIsActive('eventlog'))
				{
					?><span style="margin: 5px 0px 5px 0px"><a href="/admin/eventlog/index.php" onclick="$.adminLoad({path: '/admin/eventlog/index.php'}); return false"><?php echo Core::_('Admin.index_events_journal_link')?></a></span>
					<?php
				}
			}
			else
			{
				Core_Message::show(Core::_('Admin.index_error_open_log') . $file_name, 'error');
			}
			?></div>
			</div>
			</td>
			<?php
			$count_td++;
			NewTr($count_td);
		}

		?><td width="50%" valign="top" class="index_table_td">

		<div class="main_div"><span class="div_title"><?php echo Core::_('Admin.index_title2')?></span>
		<div class="div_content">

		<table border="0">
			<tr>
				<td valign="top"><?php echo Core::_('Admin.index_tech_date_hostcms')?>
				<span class="success"><?php echo htmlspecialchars(strip_tags(Core::_('Core.redaction' . Core_Array::get(Core::$config->get('core_hostcms'), 'integration', 0)) . ' ' . CURRENT_VERSION))?> </span> <br />
				<?php echo Core::_('Admin.index_tech_date_php')?>
				<span class="<?php echo version_compare(phpversion(), '5.2.2', ">=") ? 'success' :'error'?>">
					<?php echo htmlspecialchars(phpversion())?> </span> <br />
				<?php
					$dbVersion = Core_DataBase::instance()->getVersion();
				?>
				<?php echo Core::_('Admin.index_tech_date_sql')?> <span class="<?php echo version_compare($dbVersion, '5.0.0', ">=") ? 'success' :'error'?>">
					<?php echo htmlspecialchars($dbVersion)?> </span> <br />
				<?php
					$gdVersion = Core_Image::instance('gd')->getVersion();
				?>
				<?php echo Core::_('Admin.index_tech_date_gd')?> <span class="<?php echo version_compare($gdVersion, '2.0', ">=") ? 'success' :'error'?>">
					<?php echo htmlspecialchars($gdVersion)?> </span> <br />
				<?php echo Core::_('Admin.index_tech_date_mb')?> <span class="<?php echo (function_exists('mb_internal_encoding')) ? 'success' : 'error'?>">
					<?php echo function_exists('mb_internal_encoding') ? Core::_('Admin.index_on') : Core::_('Admin.index_off')?>
				</span> <br />
					<?php echo Core::_('Admin.index_tech_date_max_date_size')?>
				<span class="<?php echo (ini_get('post_max_size') >= 2) ? 'success' : 'warning'?>">
					<?php echo ini_get('post_max_size') ? htmlspecialchars(ini_get('post_max_size')) :Core::_('Admin.unknown')?>
				</span> <?php

				if (strlen(trim(ini_get("upload_tmp_dir"))) > 0)
				{
					?> <br />
					<?php echo Core::_('Admin.index_tech_date_session_save_path')
					?><span class="success"><?php echo htmlspecialchars(session_save_path())?></span>
				<?php
				}
				?></td>
				<td style="padding-left: 20px" valign="top" width="50%"><?php

				if (strlen(trim(ini_get("upload_tmp_dir"))) > 0)
				{
					?> <?php echo Core::_('Admin.index_tmp_dir')?> <span
					class="<?php echo (strlen(trim(ini_get("upload_tmp_dir"))) > 0) ? 'success' : 'warning' ?>">
					<?php echo (strlen(trim(ini_get("upload_tmp_dir"))) > 0) ? htmlspecialchars(ini_get('upload_tmp_dir')) : Core::_('Admin.index_tmp_not_dir')
					?> </span> <br />
					<?php
				}

				if (function_exists('disk_free_space') && Core::isFunctionEnable('disk_free_space'))
				{
				?> <?php echo Core::_('Admin.index_free_space')?> <span
					class="<?php echo (round(disk_free_space(CMS_FOLDER)/1024/1024,2) > 30) ? 'success' : 'warning' ?>">
					<?php echo round(disk_free_space(CMS_FOLDER)/1024/1024,2)?><?php echo Core::_('Admin.index_memory_unit')?>
				</span> <br />
				<?php
				}
				?>
				<?php echo Core::_('Admin.index_memory_limit')?> <span
					class="<?php echo ini_get('memory_limit') ? 'success' : 'warning' ?>">
					<?php echo (ini_get('memory_limit'))
					? htmlspecialchars(ini_get('memory_limit'))
					: Core::_('Admin.index_memory_not_limit')?>
				</span> <br />
				<?php echo Core::_('Admin.index_safe_mode')?> <span
					class="<?php echo (ini_get('safe_mode') == 1 || mb_strtolower(ini_get('safe_mode'))=='on') ? 'error' : 'success' ?>">
					<?php echo (ini_get('safe_mode') == 1 || mb_strtolower(ini_get('safe_mode'))=='on')
					? Core::_('Admin.index_on')
					: Core::_('Admin.index_off')?>
				</span> <br />
				<?php echo Core::_('Admin.index_register_globals')?> <span
					class="<?php echo ini_get('register_globals') == 1 ? 'error' : 'success'?>">
					<?php echo (ini_get('register_globals') == 1)
					? Core::_('Admin.index_on')
					: Core::_('Admin.index_off')?>
				</span> <br />
				<?php echo Core::_('Admin.index_magic_quotes')?> <span
					class="<?php echo (ini_get('magic_quotes_gpc') == 1 || mb_strtolower(ini_get('magic_quotes_gpc'))=='on') ? 'error' : 'success'?>">
					<?php echo (ini_get('magic_quotes_gpc')==1 || mb_strtolower(ini_get('magic_quotes_gpc'))=='on')
					? Core::_('Admin.index_on')
					: Core::_('Admin.index_off')?>
				</span> <br />
				<?php echo Core::_('Admin.index_tech_date_max_time', ini_get('max_execution_time')
					? htmlspecialchars(ini_get('max_execution_time')) . " " . Core::_('Admin.seconds')
					: Core::_('Admin.unknown'))?>
				</span>

				</td>
			</tr>
		</table>
		</div>
		</div>

		</td>
		<?php
		$count_td++;
		NewTr($count_td);

		$aModules = $this->_getAllowedModules();
		foreach ($aModules AS $oModule)
		{
			$sSkinModuleName = "Skin_{$this->_skinName}_Module_{$oModule->path}_Module";

			$Core_Module = class_exists($sSkinModuleName)
				? new $sSkinModuleName
				: $oModule->Core_Module;

			if ($oModule->active
				&& !is_null($Core_Module)
				&& method_exists($Core_Module, 'adminPage')
				&& $oUser->checkModuleAccess(array($oModule->path), $oSite))
			{
				if ($Core_Module->adminPage())
				{
					$count_td++;
					NewTr($count_td);
				}
			}
		}

		if (!((~Core_Array::get(Core::$config->get('core_hostcms'), 'hostcms')) & (~1835217467)))
		{
			?><td valign="top" class="index_table_td">
			<div class="main_div"><span class="div_title"><?php echo Core::_('Admin.free')?></span>
			<div class="div_content">
			<ul>
				<li>техническую поддержку в течение года;</li>
				<li>систему поиска с учетом морфологии русского языка;</li>
				<li>модули для ускорения загрузки сайта;</li>
				<li>множество дополнительных модулей для расширения функционала вашего сайта, предусмотренных выбранной Вами редакцией системы управления.</li>
			</ul>
			При переходе с <a href="http://www.hostcms.ru/hostcms/editions/free/" target="_blank">HostCMS.Халява</a> на <a href="http://www.hostcms.ru/hostcms/editions/" target="_blank">другие
			редакции</a>, содержимое сайта сохраняется.</div>
			</div>
			</td>
			<?php

			$count_td++;
			NewTr($count_td);
		}

		?></tr></table><?php
		return $this;
	}

	/**
	 * Get message.
	 *
	 * <code>
	 * echo Core_Message::get(Core::_('constant.name'));
	 * echo Core_Message::get(Core::_('constant.message', 'value1', 'value2'));
	 * </code>
	 * @param $message Message text
	 * @param $type Message type
	 * @see Core_Message::show()
	 * @return string
	 */
	public function getMessage($message, $type = 'message')
	{
		$return = '<div id="' . $type . '">' . $message . '</div>';
		return $return;
	}
}