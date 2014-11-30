<?php
/**
 * 1PS.
 *
 * @package HostCMS
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2014 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
require_once('../../bootstrap.php');

Core_Auth::authorization('oneps');

$sAdminFormAction = '/admin/oneps/index.php';

// Контроллер формы
$oAdmin_Form_Controller = Admin_Form_Controller::create();
$oAdmin_Form_Controller
	->setUp()
	->path($sAdminFormAction)
	->title(Core::_('oneps.title'));

ob_start();

Admin_Form_Entity::factory('Title')
	->name(Core::_('oneps.title'))
	->execute();

Core_Message::show(Core::_('oneps.introduction'));

$sHtml = '

<style>
.ps p {
	margin: 5px 15px 5px 0;
	text-align: justify;
}

.ps a {
	font-size: 14pt;
}

.ps td {
	vertical-align: top;
}

.ps img {
	margin: 0 5px -5px 0;
}
</style>

<table class="ps">
	<tr>
		<td>
			<p><img src="/modules/oneps/image/add.png"/><a href="http://go.1ps.ru/pr/p.php?610331&amp;http://1ps.ru/info/" target="_blank">Регистрация в каталогах</a></p>
		</td>
		<td>
			<p><img src="/modules/oneps/image/chart_up.png"/><a href="http://go.1ps.ru/pr/p.php?610331&amp;http://1ps.ru/cost/copyright/" target="_blank">Написание оптимизированных текстов</a></p>
		</td>
	</tr>
	<tr>
		<td>
			<p>Регистрация в каталогах помогает улучшить видимость сайта в поисковиках. Регистрация сайта в каталогах необходима для увеличения ссылочной массы. Это недорогой способ получения множества ссылок на Ваш сайт с нужными ключевыми словами на тематических страницах. Информация о сайте отправляется в более чем 11 000 каталогов сайтов и поисковых систем. </p>
		</td>
		<td>
			<p>	Мы напишем для Вас продающие тексты, которые помогут увеличить конверсию, а значит, повысить продажи, а также информационные и оптимизированные тексты, необходимые для продвижения Вашего сайта. Новые статьи помогают продвижению, а посетители видят, что ресурс обновляется, а значит, повышается доверие к нему.</p>
			<p>Хорошие уникальные тексты – основа эффективного продвижения!</p>
		</td>
	</tr>
	<tr>
		<td>
			<input type="submit" value="Заказать" class="applyButton" onclick="window.open(\'http://go.1ps.ru/pr/p.php?610331&amp;http://1ps.ru/info/\'); return false" />
		</td>
		<td>
			<input type="submit" value="Заказать" class="applyButton" onclick="window.open(\'http://go.1ps.ru/pr/p.php?610331&amp;http://1ps.ru/cost/copyright/\'); return false" />
		</td>
	</tr>
	<tr>
		<td>
			<p><img src="/modules/oneps/image/school_board.png"/><a href="http://go.1ps.ru/pr/p.php?610331&amp;http://1ps.ru/cost/posting/" target="_blank">Регистрация в каталогах статей</a></p>
		</td>
		<td>
			<p><img src="/modules/oneps/image/mail.png"/><a href="http://go.1ps.ru/pr/p.php?610331&amp;http://1ps.ru/cost/#dop" target="_blank">Внутренняя оптимизация сайта</a></p>
		</td>
	</tr>
	<tr>
		<td>
			<p>Регистрация сайта в каталогах статей также помогает улучшить видимость сайта в поисковиках. Вы получаете качественные и весомые ссылки на сайт, т.к. все отобранные каталоги хорошо индексируются поисковиками и посещаются реальными людьми. Каталоги не требуют взамен размещения своей ссылки на Вашем сайте и допускают размещение до трех ссылок со статьи.</p>
		</td>
		<td>
			<p>Для достижения максимального эффекта от продвижения, в первую очередь необходимо уделить внимание внутренней оптимизации сайта. Составление семантического ядра сайта, прописывание метатегов и title, расстановка заголовков h1, p, h3 на всех станицах, заполнение тегов alt и title для всех картинок, создание семантической разметки и карты сайта и это еще далеко не весь список обязательных процедур.</p>
			<p>Напишите нам, и специалисты 1PS.RU помогут подготовить Ваш сайт к продвижению - проведут все необходимые процедуры по внутренней оптимизации сайта.</p>
		</td>
	</tr>
	<tr>
		<td>
			<input type="submit" value="Заказать" class="applyButton" onclick="window.open(\'http://go.1ps.ru/pr/p.php?610331&amp;http://1ps.ru/cost/posting/\'); return false"/>
		</td>
		<td>
			<input type="submit" value="Написать" class="applyButton" onclick="window.open(\'http://go.1ps.ru/pr/p.php?610331&amp;http://1ps.ru/cost/#dop\'); return false"/>
		</td>
	</tr>
</table>
';

$oAdmin_Form_Entity_Form = new Admin_Form_Entity_Form($oAdmin_Form_Controller);

$oAdmin_Form_Entity_Form
	->action($sAdminFormAction)
	->add(
		Admin_Form_Entity::factory('Code')
			->html($sHtml)
	)
	->execute();

$oAdmin_Answer = Core_Skin::instance()->answer();

$oAdmin_Answer
	->ajax(Core_Array::getRequest('_', FALSE))
	->content(ob_get_clean())
	->message('')
	->title(Core::_('oneps.title'))
	->execute();