<?php 
/**
 * Information systems.
 *
 * @package HostCMS 6\Informationsystem
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2013 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
return array(
	'model_name' => 'Информационные элементы',
	'show_information_groups_title' => 'Информационная система "%s"',
	'information_system_top_menu_items' => 'Информационный элемент',
	'show_information_groups_link2' => 'Добавить',
	'show_information_groups_link3' => 'Дополнительные свойства',
	'show_all_comments_top_menu' => 'Комментарии',
	'show_comments_link_show_all_comments' => 'Все комментарии',

	'information_items_add_form_title' => 'Добавление информационного элемента',
	'information_items_edit_form_title' => 'Редактирование информационного элемента',

	'markDeleted' => 'Удалить информационный элемент',

	'id' => 'Идентификатор',
	'informationsystem_id' => 'Идентификатор информационной системы',
	'shortcut_id' => 'Идентификатор родительского элемента',

	'name' => '<acronym title="Название информационного элемента">Название информационного элемента</acronym>',
	'informationsystem_group_id' => '<acronym title="Группа, к которой принадлежит информационный элемент">Группа</acronym>',
	'datetime' => '<acronym title="Дата добавления/редактирования информационного элемента">Дата</acronym>',
	'start_datetime' => '<acronym title="Дата публикации информационного элемента">Дата публикации</acronym>',
	'end_datetime' => '<acronym title="Дата завершения публикации информационного элемента">Дата завершения публикации</acronym>',
	'description' => '<acronym title="Описание информационного элемента">Описание информационного элемента</acronym>',
	'exec_typograph_description' => '<acronym title="Применить типографирование к описанию">Типографировать описание</acronym>',
	'use_trailing_punctuation' => '<acronym title="Оптическое выравнивание текста перемещает символы пунктуации за границу набора">Оптическое выравнивание</acronym>',
	'active' => '<acronym title="Статус активности информационного элемента">Активен</acronym>',
	'sorting' => '<acronym title="Порядок сортировки информационного элемента">Порядок сортировки</acronym>',
	'ip' => '<acronym title="IP-адрес компьютера отправителя информационного элемента, например XXX.XXX.XXX.XXX, где XXX - число от 0 до 255">IP-адрес</acronym>',
	'showed' => '<acronym title="Число просмотров информационного элемента">Число просмотров</acronym>',
	'siteuser_id' => '<acronym title="Идентификатор пользователя сайта, создавшего информационный элемент">Код пользователя</acronym>',
	'image_large' => '<acronym title="Большое изображение для информационного элемента">Большое изображение</acronym>',
	'image_small' => '<acronym title="Малое изображение для информационного элемента">Малое изображение</acronym>',

	'path' => '<acronym title="Название элемента в URL">Название элемента в URL</acronym>',
	'maillist' => '<acronym title="Элемент информационной системы можно добавить как выпуск в рассылку">Разместить в рассылке</acronym>',
	'maillist_default_value' => '-- Не рассылать --',

	'siteuser_group_id' => '<acronym title="Группа, имеющая права доступа к информационному элементу">Группа доступа</acronym>',

	'indexing' => '<acronym title="Флаг, указывающий индексировать элемент информационной системы или нет">Индексировать</acronym>',
	'text' => '<acronym title="Текст информационного элемента">Текст</acronym>',
	'exec_typograph_for_text' => '<acronym title="Применить типографирование к тексту">Типографировать текст</acronym>',
	'use_trailing_punctuation_for_text' => '<acronym title="Оптическое выравнивание текста перемещает символы пунктуации за границу набора">Оптическое выравнивание</acronym>',

	'tab_1' => 'Описание',
	'tab_2' => 'SEO',
	'tab_3' => 'Метки',
	'tab_4' => 'Дополнительные свойства',

	'seo_title' => '<acronym title="Значение мета-тега <title> для информационного элемента">Заголовок (Title)</acronym>',
	'seo_description' => '<acronym title="Значение мета-тега <description> для информационного элемента">Описание (Description)</acronym>',
	'seo_keywords' => '<acronym title="Значение мета-тега <keywords> для информационного элемента">Ключевые слова (Keywords)</acronym>',

	'tags' => '<acronym title="Метки (теги) информационного элемента, разделяются запятой, например процессоры, AMD, Athlon64">Метки (теги)</acronym>',

	'error_information_group_URL_item' => 'В группе уже существует информационный элемент с таким названием в URL!',
	'error_information_group_URL_item_URL' => 'В группе существует подгруппа с URL, совпадающим с названием элемента в URL!',

	'edit_success' => 'Информационный элемент изменен.',
	'apply_success' => 'Информация изменена.',
	'copy_success' => 'Информационный элемент скопирован!',

	'changeActive_success' => 'Активность информационного элемента изменена.',
	'changeIndexation_success' => 'Индексация информационной группы изменена.',

	// Перенос информационных элементов и групп
	'move_items_groups_title' => 'Перенос групп и элементов',
	'move_items_groups_information_groups_id' => '<acronym title="Группа, в которую будут перенесены элементы и группы">Родительская группа</acronym>',

	// Ярлыки информационных элементов
	'add_information_item_shortcut_title' => 'Создание ярлыка',
	'add_item_shortcut_information_groups_id' => '<acronym title="Группа, в которой размещается ярлык информационного элемента">Родительская группа</acronym>',
	'shortcut_success' => 'Ярлык успешно создан.',
	'markDeleted_success' => 'Информационный элемент удален.',
	'markDeleted_error' => 'Информационный элемент не удален!',
	'move_success' => 'Информационные элементы перенесены.',

	'show_comments_title' => 'Комментарии к информационному элементу "%s"',
	'shortcut_success' => 'Ярлык товара успешно добавлен',

	'show_information_propertys_title' => 'Дополнительные свойства элементов информационной системы "%s"',
	'delete_success' => 'Элемент удален!',
	'undelete_success' => 'Элемент восстановлен!',
);