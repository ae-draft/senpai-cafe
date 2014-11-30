<?php
/**
 * Система управления сайтом HostCMS v. 5.xx
 *
 * Copyright © 2005-2011 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 *
 * Модуль: shop.
 *
 * Файл: /modules/shop/lng/ru/ru.php
 *
 * @package HostCMS 5
 * @author Hostmake LLC
 * @version 5.x
 */
$GLOBALS['MSG_shops'] = array();

$GLOBALS['MSG_shops']['menu'] = "Интернет-магазин";

$GLOBALS['MSG_shops']['error_dir'] = 'Ошибка! Отсутствует директория ';
$GLOBALS['MSG_shops']['apply'] = 'Применить';
$GLOBALS['MSG_shops']['save'] = 'Сохранить';
$GLOBALS['MSG_shops']['parent_shop_id'] = '<acronym title="Интернет магазин">Интернет магазин</acronym>';

$GLOBALS['MSG_shops']['all'] = 'Все';
$GLOBALS['MSG_shops']['tab'] = 'Параметры';
$GLOBALS['MSG_shops']['tab2_company'] = 'Руководство';
$GLOBALS['MSG_shops']['tab3_company'] = 'Банковские реквизиты';
$GLOBALS['MSG_shops']['tab3_producer'] = 'Банковские реквизиты';
$GLOBALS['MSG_shops']['tab4_company'] = 'Контактные данные';
$GLOBALS['MSG_shops']['tab4_producer'] = 'Контактные данные';
$GLOBALS['MSG_shops']['tab2'] = 'Контактные данные';
$GLOBALS['MSG_shops']['tab3'] = 'Описание заказа';
$GLOBALS['MSG_shops']['tab_sort'] = 'Сортировка';
$GLOBALS['MSG_shops']['tab2_sort'] = 'Экспорт';
$GLOBALS['MSG_shops']['tab2_watermark'] = 'Изображение';
$GLOBALS['MSG_shops']['tab2_save_proportions'] = 'Сохранять пропорции изображения';

$GLOBALS['MSG_shops']['tab_formats'] = 'Форматы';
$GLOBALS['MSG_shops']['all_list_of_comments'] = 'Все отзывы';
$GLOBALS['MSG_shops']['list_of_properties_success_delete'] = 'Данные о дополнительном свойстве успешно удалены';
$GLOBALS['MSG_shops']['lists_of_properties_success_delete'] = 'Данные о дополнительных свойствах успешно удалены';
$GLOBALS['MSG_shops']['dir_lists_of_properties_success_delete'] = 'Данные о разделах дополнительных свойств успешно удалены';
$GLOBALS['MSG_shops']['dir_list_of_properties_success_delete'] = 'Данные о разделе дополнительных свойств успешно удалены';

$GLOBALS['MSG_shops']['shop_eitems_form_title'] = 'Электронный товар "%s"';
$GLOBALS['MSG_shops']['shop_eitems_menu_title'] = 'Электронный товар';
$GLOBALS['MSG_shops']['eitems_show_add_new_link'] = "Добавить";
$GLOBALS['MSG_shops']['eitems_edit_title'] = "Редактирование электронного товара";
$GLOBALS['MSG_shops']['eitems_add_title'] = "Добавление электронного товара";

$GLOBALS['MSG_shops']['shop_order_item_delivery'] = 'Доставка "%s"';
$GLOBALS['MSG_shops']['shop_order_item_discount'] = 'Скидка';

$GLOBALS['MSG_shops']['shop_order_admin_subject'] = 'Заказ N %1$s от %3$s в "%2$s"';
$GLOBALS['MSG_shops']['shop_order_user_subject'] = 'Заказ N %1$s от %3$s';

$GLOBALS['MSG_shops']['shop_order_confirm_admin_subject'] = 'Подтверждение оплаты, заказ N %1$s от %3$s в магазине "%2$s"';
$GLOBALS['MSG_shops']['shop_order_confirm_user_subject'] = 'Подтверждение оплаты, заказ N %1$s от %3$s';

$GLOBALS['MSG_shops']['users_account_table'] = 'Лицевые счета пользователя "%s" в магазинах';
$GLOBALS['MSG_shops']['transaction_user_account'] = 'Транзакции пользователя "%s" магазина "%s"';
$GLOBALS['MSG_shops']['transaction_menu_header'] = 'Транзакция';
$GLOBALS['MSG_shops']['add_comment_error_empty_item_field'] = 'Содержимое поля "Идентификатор товара" равно 0, невозможно добавить комментарий к неизвестному товару!';

$GLOBALS['MSG_shops']['transaction_add_form'] = 'Добавление транзакции';

$GLOBALS['MSG_shops']['transaction_edit_form'] = 'Редактирование транзакции';

$GLOBALS['MSG_shops']['su_users_link'] = 'Пользователи сайта';

$GLOBALS['MSG_shops']['su_users_control_title'] = 'Журнал пользователей';

$GLOBALS['MSG_shops']['transaction_menu_add'] = 'Добавить';


$GLOBALS['MSG_shops']['transaction_description'] = "<acronym title=\"Текстовое описание транзакции\">Описание транзакции</acronym>";

$GLOBALS['MSG_shops']['transaction_active'] = "<acronym title=\"Статус активности транзакции\">Активность транзакции</acronym>";

$GLOBALS['MSG_shops']['transaction_datetime'] = "<acronym title=\"Время проведения транзакции\">Время транзакции</acronym>";

$GLOBALS['MSG_shops']['transaction_sum'] = "<acronym title=\"Количество денежных единиц транзакции (знак '-' указывается при снятии со счета денежных средств)\">Сумма</acronym>";

$GLOBALS['MSG_shops']['transaction_order_id'] = "<acronym title=\"Идентификатор заказа, оплаченного с использованием транзакции\">Номер заказа</acronym>";

$GLOBALS['MSG_shops']['transaction_insert_error'] = "Ошибка добавления транзакции!";
$GLOBALS['MSG_shops']['transaction_insert_error_undef_shop'] = "Ошибка добавления транзакции! Магазин не определен!";
$GLOBALS['MSG_shops']['transaction_insert_success'] = "Информация о транзакции успешно добавлена!";
$GLOBALS['MSG_shops']['transaction_update_success'] = "Информация о транзакции успешно обновлена!";

$GLOBALS['MSG_shops']['error_cant_find_parent_catalog'] = "Ошибка! Не удается обнаружить родительскую директорию с идентификатором %s для товара %s!";

$GLOBALS['MSG_shops']['eitems_add_edit_form_name'] = "<acronym title=\"Имя или описание электронного товара\">Описание</acronym>";
$GLOBALS['MSG_shops']['eitems_add_edit_form_text'] = "<acronym title=\"Текст электронного товара\">Текст</acronym>";
$GLOBALS['MSG_shops']['eitems_add_edit_form_file'] = "<acronym title=\"Файл электронного товара\">Файл</acronym>";
$GLOBALS['MSG_shops']['eitems_add_edit_form_count'] = "<acronym title=\"Количество копий электронного товара. Если значение -1, значит товар не ограничен\">Количество</acronym>";
$GLOBALS['MSG_shops']['eitems_add_edit_form_error'] = "Не удалось загрузить файл!";
$GLOBALS['MSG_shops']['eitems_delete_success'] = "Информация об электронных товарах успешно удалена!";
$GLOBALS['MSG_shops']['eitem_delete_success'] = "Информация об электронном товаре успешно удалена!";


$GLOBALS['MSG_shops']['error_add_discount_w'] = 'Ошибка добавления скидки! Процент скидки может быть от 0 до 100%';

$GLOBALS['MSG_shops']['error_add_coupon_w'] = 'Ошибка добавления купона! Необходимо указать скидку!';
$GLOBALS['MSG_shops']['error_watermark_extention_add_edit_shop'] = 'Файл не является png-файлом';
$GLOBALS['MSG_shops']['tab_shop'] = 'Параметры';


$GLOBALS['MSG_shops']['error_insert_property_cml'] = 'Ошибка! Дополнительное свойство c данным идентификатором системы CommerceML уже существует, свойство не было добавлено!';


$GLOBALS['MSG_shops']['tab_seo'] = 'SEO';
$GLOBALS['MSG_shops']['tab_tags'] = 'Метки';
$GLOBALS['MSG_shops']['tab_yandex_market'] = 'Экспорт/Импорт';
$GLOBALS['MSG_shops']['tab_description'] = 'Описание';
$GLOBALS['MSG_shops']['order_discount_form_type_0'] = 'Процент';
$GLOBALS['MSG_shops']['order_discount_form_type_1'] = 'Сумма';
$GLOBALS['MSG_shops']['tab_group'] = 'Параметры';
$GLOBALS['MSG_shops']['tab_group_description'] = 'Описание';
$GLOBALS['MSG_shops']['tab_group_seo'] = 'SEO';

$GLOBALS['MSG_shops']['tab_cond_of_delivery_settings'] = 'Условия доставки';


$GLOBALS['MSG_shops']['item_type_selection_group_buttons_name'] = "<acronym title=\"Выбор типа товара (обычный или электронный)\">Тип товара</acronym>";
$GLOBALS['MSG_shops']['item_type_selection_group_buttons_name_simple'] = "Обычный товар";
$GLOBALS['MSG_shops']['item_type_selection_group_buttons_name_electronic'] = "Электронный товар";
$GLOBALS['MSG_shops']['item_type_selection_group_buttons_name_divisible'] = "Делимый товар";

$GLOBALS['MSG_shops']['shop_add_form_small_image'] = "<acronym title=\"Малое изображение для магазина\">Малое изображение</acronym>";
$GLOBALS['MSG_shops']['header_admin_forms'] = "Интернет-магазины";
$GLOBALS['MSG_shops']['header_admin__list_forms'] = "Список интернет-магазинов";
$GLOBALS['MSG_shops']['shop_dir_top_menu_title'] = "Раздел";
$GLOBALS['MSG_shops']['shop_dir_sub_menu_add'] = "Добавить";


$GLOBALS['MSG_shops']['show_shops_link'] = "Добавить";
$GLOBALS['MSG_shops']['success_add_shop'] = "Данные об интернет-магазине успешно добавлены!";
$GLOBALS['MSG_shops']['success_edit_shop'] = "Данные об интернет-магазине успешно обновлены!";
$GLOBALS['MSG_shops']['error_add_shop'] = "Ошибка вставки данных об интернет-магазине!";
$GLOBALS['MSG_shops']['error_isset_shop_with_structure'] = "Ошибка! Для данного сайта уже существует магазин, с выбранным Вами узлом структуры. С одним узлом структуры не может быть связано несколько магазинов.";
$GLOBALS['MSG_shops']['shops_add_form_title'] = "Добавление информации об интернет-магазине";
$GLOBALS['MSG_shops']['shops_edit_form_title'] = "Редактирование информации об интернет-магазине";

$GLOBALS['MSG_shops']['shop_dir_add_form_title'] = "Добавление информации о разделе интернет-магазинов";
$GLOBALS['MSG_shops']['shop_dir_edit_form_title'] = "Редактирование информации о разделе интернет-магазинов";
$GLOBALS['MSG_shops']['shop_dir_add_form_name'] = "<acronym title=\"Название раздела интернет-магазинов\">Название раздела интеренет-магазинов</acronym>";
$GLOBALS['MSG_shops']['shop_dir_add_form_description'] = "<acronym title=\"Описание раздела интернет-магазинов\">Описание раздела интернет-магазинов</acronym>";
$GLOBALS['MSG_shops']['shop_dir_add_form_group'] = "<acronym title=\"Родительский раздел интернет-магазинов\">Раздел</acronym>";

$GLOBALS['MSG_shops']['shops_add_form_link'] = "Список интернет-магазинов";
$GLOBALS['MSG_shops']['shop_shops_path'] = "<acronym title=\"Узел структуры, в котором будет выводиться интернет-магазин\">Узел структуры</acronym>";
$GLOBALS['MSG_shops']['shop_shops_add_form_access'] = '<acronym title="Группа пользователей, имеющая права доступа к интернет-магазину">Группа доступа</acronym>';

$GLOBALS['MSG_shops']['shops_add_form_name'] = "<acronym title=\"Название интернет-магазина\">Название интернет-магазина</acronym>";
$GLOBALS['MSG_shops']['shops_add_form_description'] = "<acronym title=\"Описание интернет-магазина\">Описание интернет-магазина</acronym>";
$GLOBALS['MSG_shops']['shops_add_form_yandex_name'] = "<acronym title=\"Название магазина для системы Yandex.Market\">Название для Yandex.Market</acronym>";
$GLOBALS['MSG_shops']['shops_add_form_yandex_sales_notes_default'] = "<acronym title=\"Значение по умолчанию тега &lt;sales_notes&gt; при экспорте в Yandex.Market\">Значение по умолчанию тега &lt;sales_notes&gt; для Yandex.Market</acronym>";

$GLOBALS['MSG_shops']['shops_add_form_admin_mail'] = "<acronym title=\"E-mail (группа e-mail, через запятую), на который(е) будет отправляться информация о поступившем заказе\">E-mail куратора магазина</acronym>";
$GLOBALS['MSG_shops']['shops_add_form_items_on_page'] = '<acronym title="Свойство, определяющее число элементов на странице">Число элементов на странице</acronym>';
$GLOBALS['MSG_shops']['shop_add_form_button_value1'] = "Добавить";
$GLOBALS['MSG_shops']['shop_success_delete'] = "Данные об интернет-магазине успешно удалены!";
$GLOBALS['MSG_shops']['shop_success_deletes'] = "Данные об интернет-магазинах успешно удалены!";

$GLOBALS['MSG_shops']['shop_dir_success_delete'] = "Данные о разделе интернет-магазинов успешно удалены!";
$GLOBALS['MSG_shops']['shop_dir_success_deletes'] = "Данные о разделах интернет-магазинов успешно удалены!";

$GLOBALS['MSG_shops']['add_group_link'] = "Добавить";
$GLOBALS['MSG_shops']['add_group_link1'] = "Добавить";

$GLOBALS['MSG_shops']['error_show_group'] = 'Ошибка, магазина с таким идентификатором не существует!';
$GLOBALS['MSG_shops']['groups_add_form_title'] = "Добавление информации о группе товаров";
$GLOBALS['MSG_shops']['groups_edit_form_title'] = "Редактирование информации о группе товаров";

$GLOBALS['MSG_shops']['success_add_group'] = "Данные о группе товаров успешно добавлены!";
$GLOBALS['MSG_shops']['success_edit_group'] = "Данные о группе товаров успешно изменены!";
$GLOBALS['MSG_shops']['groups_add_form_name'] = "<acronym title=\"Название группы товаров\">Название группы</acronym>";
$GLOBALS['MSG_shops']['groups_add_form_description'] = "<acronym title=\"Описание группы товаров\">Описание группы</acronym>";
$GLOBALS['MSG_shops']['groups_image'] = "<acronym title=\"Изображение для группы товаров\">Изображение группы</acronym>";

$GLOBALS['MSG_shops']['items_catalog_image'] = "<acronym title=\"Изображение товара\">Изображение товара</acronym>";


$GLOBALS['MSG_shops']['seller_image'] = "<acronym title=\"Изображение продавца\">Изображение продавца</acronym>";
$GLOBALS['MSG_shops']['seller_small_image'] = "<acronym title=\"Малое изображение продавца\">Малое изображение продавца</acronym>";


$GLOBALS['MSG_shops']['items_catalog_image_small'] = "<acronym title=\"Малое изображение товара\">Малое изображение товара</acronym>";


$GLOBALS['MSG_shops']['groups_add_form_seo_title'] = "<acronym title=\"Значение мета-тега title для группы товаров\">Заголовок (title)</acronym>";
$GLOBALS['MSG_shops']['groups_add_form_seo_description'] = "<acronym title=\"Значение мета-тега description для группы товаров\">Описание (description)</acronym>";
$GLOBALS['MSG_shops']['groups_add_form_seo_keywords'] = "<acronym title=\"Значение мета-тега keywords для группы товаров\">Ключевые слова (keywords)</acronym>";



$GLOBALS['MSG_shops']['shops_form_image_big_max_width'] = '<acronym title="Максимальная ширина большого изображения">Максимальная ширина большого изображения</acronym>';
$GLOBALS['MSG_shops']['shops_form_image_big_max_height'] = '<acronym title="Максимальная высота большого изображения">Максимальная высота большого изображения</acronym>';
$GLOBALS['MSG_shops']['shops_add_form_default_country'] = "<acronym title=\"Страна, указываемая по умолчанию в списке стран\">Страна по умолчанию</acronym>";
$GLOBALS['MSG_shops']['shops_add_form_default_currency'] = "<acronym title=\"Валюта по умолчанию для интернет-магазина\">Валюта по умолчанию</acronym>";
$GLOBALS['MSG_shops']['shops_add_form_default_order_status'] = '<acronym title="Статус заказа по умолчанию для интернет-магазина">Статус заказа по умолчанию</acronym>';
$GLOBALS['MSG_shops']['shops_add_form_mesures'] = "<acronym title=\"Единица измерения, в которой указывается вес товара\">Единица измерения веса товара</acronym>";
$GLOBALS['MSG_shops']['shops_add_form_send_order_mail_admin'] = "<acronym title=\"Отправлять письмо с информацией о поступившем заказе администратору\">Отправлять письмо с информацией о поступившем заказе администратору</acronym>";
$GLOBALS['MSG_shops']['shops_add_form_send_order_mail_user'] = "<acronym title=\"Отправлять письмо с информацией об оформленном заказе пользователю\">Отправлять письмо с информацией об оформленном заказе пользователю</acronym>";
$GLOBALS['MSG_shops']['shops_add_form_comment_active'] = '<acronym title="Использовать постмодерацию для отзывов на товары">Постмодерировать отзывы на товары</acronym>';


$GLOBALS['MSG_shops']['show_tax_link'] = "Налоги";
$GLOBALS['MSG_shops']['show_currency_link'] = "Валюты";
$GLOBALS['MSG_shops']['show_producers_link'] = "Производители";
$GLOBALS['MSG_shops']['show_producer_link'] = "Производитель";
$GLOBALS['MSG_shops']['show_producer_id_filter_change_price_for'] = "Производитель";

$GLOBALS['MSG_shops']['producer_add_form_seo_title'] = "<acronym title=\"Значение мета-тега title для производителя товаров\">Заголовок (title)</acronym>";
$GLOBALS['MSG_shops']['producer_add_form_seo_description'] = "<acronym title=\"Значение мета-тега description для производителя товаров\">Описание (description)</acronym>";
$GLOBALS['MSG_shops']['producer_add_form_seo_keywords'] = "<acronym title=\"Значение мета-тега keywords для производителя товаров\">Ключевые слова (keywords)</acronym>";

$GLOBALS['MSG_shops']['show_discount_link'] = "Скидки на товары";
$GLOBALS['MSG_shops']['show_tax_title'] = "Справочник налогов";
$GLOBALS['MSG_shops']['show_tax_link1'] = "Добавить";
$GLOBALS['MSG_shops']['tax_add_form_title'] = "Добавление информации о налоге";
$GLOBALS['MSG_shops']['tax_edit_form_title'] = "Редактирование информации о налоге";
$GLOBALS['MSG_shops']['tax_add_form_link'] = "Список налогов";
$GLOBALS['MSG_shops']['tax_add_form_name'] = "<acronym title=\"Название налога\">Название налога</acronym>";
$GLOBALS['MSG_shops']['tax_add_form_rate'] = "<acronym title=\"Ставка налога\">Ставка налога</acronym>";

$GLOBALS['MSG_shops']['tax_is_in_price'] = "<acronym title=\"Входит ли в цену\">В цене</acronym>";
$GLOBALS['MSG_shops']['success_add_tax'] = "Данные о налоге успешно добавлены!";
$GLOBALS['MSG_shops']['success_update_tax'] = "Данные о налоге успешно обновлены!";
$GLOBALS['MSG_shops']['error_update_tax'] = "Ошибка обновления данных о налоге!";
$GLOBALS['MSG_shops']['error_update_tax'] = "Ошибка редактирования данных!";
$GLOBALS['MSG_shops']['success_update_tax'] = "Данные о налоге успешно обновлены!";
$GLOBALS['MSG_shops']['tax_success_delete'] = "Данные о налоге успешно удалены!";
$GLOBALS['MSG_shops']['taxes_success_delete'] = "Данные о налогах успешно удалены!";
$GLOBALS['MSG_shops']['show_currency_title'] = "Справочник валют";
$GLOBALS['MSG_shops']['show_currency_link1'] = "Добавить";
$GLOBALS['MSG_shops']['currency_add_form_title'] = "Добавление информации о валюте";
$GLOBALS['MSG_shops']['currency_edit_form_title'] = "Редактирование информации о валюте";
$GLOBALS['MSG_shops']['currency_add_form_link'] = "Список валют";
$GLOBALS['MSG_shops']['currency_add_form_name'] = "<acronym title=\"Название валюты\">Название</acronym>";
$GLOBALS['MSG_shops']['currency_add_form_value_in_basic_currency'] = "<acronym title=\"Сколько единиц в базовой валюте дают за указанную валюту\">Курс</acronym>";


$GLOBALS['MSG_shops']['currency_is_default'] = "<acronym title=\"Является ли валюта базовой\">Базовая</acronym>";
$GLOBALS['MSG_shops']['success_add_currency'] = "Данные о валюте успешно добавлены!";
$GLOBALS['MSG_shops']['success_update_currency'] = "Данные о валюте успешно обновлены!";
$GLOBALS['MSG_shops']['error_update_currency'] = "Ошибка добавления информации о валюте.";
$GLOBALS['MSG_shops']['currency_success_delete'] = "Данные о валюте успешно удалены!";
$GLOBALS['MSG_shops']['shop_orders_status'] = "Статусы заказов";

$GLOBALS['MSG_shops']['discount_add_form_link'] = 'Справочник "Скидки"';

$GLOBALS['MSG_shops']['discount_add_form_from'] = "<acronym title=\"Дата начала действия скидки\">Действует от</acronym>";
$GLOBALS['MSG_shops']['discount_add_form_to'] = "<acronym title=\"Дата окончания действия скидки\">Действует до</acronym>";
$GLOBALS['MSG_shops']['discount_is_active'] = "<acronym title=\"Активна ли скидка\">Активность</acronym>";
$GLOBALS['MSG_shops']['discount_add_form_percent'] = "<acronym title=\"Процент скидки\">Процент</acronym>";
$GLOBALS['MSG_shops']['success_add_discount'] = "Данные о скидке успешно добавлены!";
$GLOBALS['MSG_shops']['success_edit_discount'] = "Данные о скидке успешно обновлены!";
$GLOBALS['MSG_shops']['error_add_discount'] = "Ошибка добавления данных о скидке!";
$GLOBALS['MSG_shops']['error_edit_discount'] = "Ошибка редактирования данных о скидке!";
$GLOBALS['MSG_shops']['show_mesures_link'] = "Справочник единиц измерения";
$GLOBALS['MSG_shops']['show_sds_link'] = "Справочники";
$GLOBALS['MSG_shops']['show_finance'] = "Финансы";
$GLOBALS['MSG_shops']['show_delivery_on'] = "Доставка";
$GLOBALS['MSG_shops']['show_order_status_link'] = 'Справочник статусов заказа';
$GLOBALS['MSG_shops']['show_type_of_delivery_link'] = "Типы доставки";
$GLOBALS['MSG_shops']['discount_success_delete'] = "Данные о скидке успешно удалены!";
$GLOBALS['MSG_shops']['discounts_success_delete'] = "Данные о скидках успешно удалены!";
$GLOBALS['MSG_shops']['mesures'] = "Единицы измерения";
$GLOBALS['MSG_shops']['show_mesures_link1'] = "Добавить";
$GLOBALS['MSG_shops']['success_add_mesures'] = "Данные о единице измерения успешно добавлены!";
$GLOBALS['MSG_shops']['success_edit_mesures'] = "Данные о единице измерения успешно обновлены!";
$GLOBALS['MSG_shops']['error_add_mesures'] = "Ошибка добавления данных о единице измерения!";
$GLOBALS['MSG_shops']['error_edit_mesures'] = "Ошибка редактирования данных о единице измерения!";
$GLOBALS['MSG_shops']['mesures_add_form_title'] = "Добавление информации о единице измерения";
$GLOBALS['MSG_shops']['mesures_edit_form_title'] = "Редактирование информации о единице измерения";
$GLOBALS['MSG_shops']['mesures_add_form_link'] = "Список единиц измерения";

$GLOBALS['MSG_shops']['mesures_add_form_name'] = "<acronym title=\"Название единицы измерения\">Название</acronym>";
$GLOBALS['MSG_shops']['mesures_add_form_description'] = "<acronym title=\"Описание единицы измерения\">Описание</acronym>";
$GLOBALS['MSG_shops']['show_order_status_link1'] = "Добавить";
$GLOBALS['MSG_shops']['success_add_order_status'] = "Данные о статусе доставки успешно добавлены!";
$GLOBALS['MSG_shops']['success_edit_order_status'] = "Данные о статусе доставки успешно обновлены!";
$GLOBALS['MSG_shops']['error_add_order_status'] = "Ошибка добавления данных о статусе заказа!";
$GLOBALS['MSG_shops']['error_edit_order_status'] = "Ошибка редактирования данных о статусе заказа!";
$GLOBALS['MSG_shops']['order_status_add_form_title'] = "Добавление информации о статусе заказа";
$GLOBALS['MSG_shops']['order_status_edit_form_title'] = "Редактирование информации о статусе заказа";
$GLOBALS['MSG_shops']['order_status_add_form_link'] = "Список статусов заказа";

$GLOBALS['MSG_shops']['order_status_add_form_name'] = '<acronym title="Название статуса заказа">Название</acronym>';
$GLOBALS['MSG_shops']['order_status_add_form_description'] = '<acronym title="Описание статуса заказа">Описание</acronym>';
$GLOBALS['MSG_shops']['order_status_success_delete'] = "Данные о статусе заказа успешно удалены!";
$GLOBALS['MSG_shops']['order_status_success_deletes'] = "Данные о статусах заказа успешно удалены!";
$GLOBALS['MSG_shops']['show_system_of_pay_link'] = "Справочник платежных систем";
$GLOBALS['MSG_shops']['system_of_pays'] = "Платежные системы";
$GLOBALS['MSG_shops']['system_of_pay_menu'] = "Платежная система";

$GLOBALS['MSG_shops']['show_system_of_pay_link1'] = "Добавить";
$GLOBALS['MSG_shops']['system_of_pay_add_form_title'] = "Редактирование информации о платежной системе";
$GLOBALS['MSG_shops']['system_of_pay_add_form_link'] = "Список платежных систем";
$GLOBALS['MSG_shops']['system_of_pay_add_form_name'] = "<acronym title=\"Название платежной системы\">Название</acronym>";
$GLOBALS['MSG_shops']['system_of_pay_add_form_description'] = "<acronym title=\"Описание платежной системы\">Описание</acronym>";
$GLOBALS['MSG_shops']['system_of_pay_add_form_is_active'] = "<acronym title=\"Активна ли платежная система\">Активность</acronym>";
$GLOBALS['MSG_shops']['system_of_pay_currency'] = "<acronym title=\"Валюта, в которой проводится расчет в данной платежной системе\">Валюта</acronym>";

$GLOBALS['MSG_shops']['system_of_pay_order'] = "<acronym title=\"Порядок сортировки платежной системы\">Порядок сортировки</acronym>";

$GLOBALS['MSG_shops']['system_of_pay_add_form_handler'] = "<acronym title=\"Обработчик для платежной системы\">Обработчик</acronym>";
$GLOBALS['MSG_shops']['success_add_system_of_pay'] = "Данные о платежной системе успешно добавлены!";
$GLOBALS['MSG_shops']['success_update_system_of_pay'] = "Данные о платежной системе успешно обновлены!";
$GLOBALS['MSG_shops']['error_add_system_of_pay'] = "Ошибка добавления данных о платежной системе!";
$GLOBALS['MSG_shops']['error_update_system_of_pay'] = "Ошибка обновления данных о платежной системе!";
$GLOBALS['MSG_shops']['success_update_system_of_pay'] = "Данные о платежной системе успешно обновлены!";
$GLOBALS['MSG_shops']['error_update_system_of_pay'] = "Ошибка обновления данных о платежной системе!";
$GLOBALS['MSG_shops']['system_of_pay_success_delete'] = "Данные о платежной системе успешно удалены!";
$GLOBALS['MSG_shops']['systems_of_pay_success_delete'] = "Данные о платежных системах успешно удалены!";

$GLOBALS['MSG_shops']['show_location_title'] = "Местоположения страны";
$GLOBALS['MSG_shops']['show_location_link1'] = "Добавить";

$GLOBALS['MSG_shops']['success_add_location'] = "Данные о местоположении успешно добавлены!";
$GLOBALS['MSG_shops']['success_edit_location'] = "Данные о местоположении успешно обновлены!";
$GLOBALS['MSG_shops']['error_add_location'] = "Ошибка добавления данных о местоположении!";
$GLOBALS['MSG_shops']['error_edit_location'] = "Ошибка редактирования данных о местоположении!";
$GLOBALS['MSG_shops']['location_add_form_title'] = "Добавление информации о местоположении";
$GLOBALS['MSG_shops']['location_edit_form_title'] = "Редактирование информации о местоположении";
$GLOBALS['MSG_shops']['location_add_form_link'] = "Список местоположений";

$GLOBALS['MSG_shops']['location_add_form_name'] = "<acronym title=\"Название местоположения\">Название</acronym>";

$GLOBALS['MSG_shops']['location_success_delete'] = "Данные о местоположении успешно удалены!";

$GLOBALS['MSG_shops']['show_reports_title'] = 'Отчеты';
$GLOBALS['MSG_shops']['show_sales_order_link'] = 'Отчет о продажах';
$GLOBALS['MSG_shops']['show_sales_order_title'] = 'Отчет о продажах';
$GLOBALS['MSG_shops']['form_sales_order_show_list_items'] = '<acronym title="Выводит список товаров из каждого заказа">Выводить товары из заказа</acronym>';

$GLOBALS['MSG_shops']['form_sales_order_show_paid_items'] = '<acronym title="Выводит список товаров только оплаченных заказов">Только оплаченные</acronym>';
$GLOBALS['MSG_shops']['form_sales_order_sallers'] = '<acronym title="Ограничение заказаных товаров по продавцу">Продавец:</acronym>';
$GLOBALS['MSG_shops']['form_sales_order_sop'] = '<acronym title="Ограничение заказаных товаров по платежной системе">Платежная система:</acronym>';
$GLOBALS['MSG_shops']['form_sales_order_status'] = '<acronym title="Ограничение заказаных товаров по статусу заказа">Статус заказа:</acronym>';

$GLOBALS['MSG_shops']['form_sales_order_select_grouping'] = '<acronym title="Указывает период группировки заказов в отчете">Группировать:</acronym>';
$GLOBALS['MSG_shops']['form_sales_order_grouping_monthly'] = 'ежемесячно';
$GLOBALS['MSG_shops']['form_sales_order_grouping_weekly'] = 'еженедельно';
$GLOBALS['MSG_shops']['form_sales_order_grouping_daily'] = 'ежедневно';
$GLOBALS['MSG_shops']['form_sales_order_begin_date'] = '<acronym title="Начальная дата отчетного периода">Начальная дата</acronym>';
$GLOBALS['MSG_shops']['form_sales_order_end_date'] = '<acronym title="Конечная дата отчетного периода">Конечная дата</acronym>';
$GLOBALS['MSG_shops']['form_sales_order_period_2_months'] = '2 месяца';
$GLOBALS['MSG_shops']['form_sales_order_period_month'] = 'месяц';
$GLOBALS['MSG_shops']['form_sales_order_period_week'] = 'неделю';
$GLOBALS['MSG_shops']['form_sales_order_period'] = '<acronym title="Быстрый выбор отчетного периода">Отобразить отчет за:</acronym>';

$GLOBALS['MSG_shops']['form_sales_order_count_orders'] = 'Заказов';
$GLOBALS['MSG_shops']['form_sales_order_count_items'] = 'Кол-во товара';
$GLOBALS['MSG_shops']['form_sales_order_total_summ'] = 'Сумма заказа';

$GLOBALS['MSG_shops']['form_sales_order_orders_number'] = 'Заказ № <b>%s</b> от <b>%s</b>';
$GLOBALS['MSG_shops']['form_sales_order_empty_orders'] = 'За указанный период оплаченные заказы отсутствуют.';
$GLOBALS['MSG_shops']['form_sales_order_date_of_paid'] = ', оплачен <b>%s</b>';

$GLOBALS['MSG_shops']['form_sales_order_month_january'] = 'Январь';
$GLOBALS['MSG_shops']['form_sales_order_month_february'] = 'Февраль';
$GLOBALS['MSG_shops']['form_sales_order_month_march'] = 'Март';
$GLOBALS['MSG_shops']['form_sales_order_month_april'] = 'Апрель';
$GLOBALS['MSG_shops']['form_sales_order_month_may'] = 'Май';
$GLOBALS['MSG_shops']['form_sales_order_month_june'] = 'Июнь';
$GLOBALS['MSG_shops']['form_sales_order_month_july'] = 'Июль';
$GLOBALS['MSG_shops']['form_sales_order_month_august'] = 'Август';
$GLOBALS['MSG_shops']['form_sales_order_month_september'] = 'Сентябрь';
$GLOBALS['MSG_shops']['form_sales_order_month_october'] = 'Октябрь';
$GLOBALS['MSG_shops']['form_sales_order_month_november'] = 'Ноябрь';
$GLOBALS['MSG_shops']['form_sales_order_month_december'] = 'Декабрь';

$GLOBALS['MSG_shops']['show_prices_title'] = "Цены";
$GLOBALS['MSG_shops']['show_prices_link1'] = "Добавить";

$GLOBALS['MSG_shops']['prices_add_form_title'] = "Добавление информации о цене";
$GLOBALS['MSG_shops']['prices_edit_form_title'] = "Редактирование информации о цене";

$GLOBALS['MSG_shops']['items_catalog_user_group'] = "<acronym title=\"Группа пользователей, для которой определена цена\">Группа</acronym>";

$GLOBALS['MSG_shops']['prices_add_form_name'] = "<acronym title=\"Название цены\">Название</acronym>";
$GLOBALS['MSG_shops']['prices_add_form_percent_to_basic'] = "<acronym title=\"Сколько процентов составляет данная цена по отношению к базовой\">Процент к базовой</acronym>";
$GLOBALS['MSG_shops']['prices_add_form_recalculate'] = "<acronym title=\"При выборе этого параметра будет произведен пересчет цены для всех товаров магазина, для которых она установлена\">Пересчитать установленные цены</acronym>";
$GLOBALS['MSG_shops']['prices_add_form_apply_for_all'] = "<acronym title=\"При выборе этого параметра добавляемая цена будет применена ко всем товарам магазина\">Установить для всех товаров</acronym>";
$GLOBALS['MSG_shops']['success_add_prices'] = "Данные о цене успешно добавлены!";
$GLOBALS['MSG_shops']['success_edit_prices'] = "Данные о цене успешно обновлены!";
$GLOBALS['MSG_shops']['error_add_prices'] = "Ошибка добавления данных о цене!";
$GLOBALS['MSG_shops']['error_edit_prices'] = "Ошибка обновления данных о цене!";
$GLOBALS['MSG_shops']['error_add_edit_prices_for_group'] = "Ошибка! Для данной группы цена уже задана, для каждой группы может быть указана только одна цена.";
$GLOBALS['MSG_shops']['error_add_edit_prices_for_group_100'] = "Невозможно добавить цену с процентом к базовой цене равным, большем 100 или меньшем 0.";
$GLOBALS['MSG_shops']['price_success_delete'] = "Данные о цене успешно удалены!";
$GLOBALS['MSG_shops']['prices_success_delete'] = "Данные о ценах успешно удалены!";
$GLOBALS['MSG_shops']['prices_error_delete'] = "Ошибка удаления цены!";

$GLOBALS['MSG_shops']['show_cond_of_delivery_title'] = "Список условий доставки типа доставки \"%s\"";
$GLOBALS['MSG_shops']['show_cond_of_delivery_link1'] = "Добавить";
$GLOBALS['MSG_shops']['show_cond_of_delivery_link2'] = "Импорт";

$GLOBALS['MSG_shops']['show_cond_of_delivery_weight_from'] = "Вес заказа от (%s), при указании 0 &mdash; минимальный вес заказа не ограничен";
$GLOBALS['MSG_shops']['show_cond_of_delivery_weight_to'] = "Вес заказа до (%s), при указании 0 &mdash; максимальный вес заказа не ограничен";
$GLOBALS['MSG_shops']['show_cond_of_delivery_price_from'] = "Цена заказа от, при указании 0 &mdash; минимальная цена заказа не ограничена";
$GLOBALS['MSG_shops']['show_cond_of_delivery_price_to'] = "Цена заказа до, при указании 0 &mdash; максимальная цена заказа не ограничена";

// Форма добавления/редактирования условий доставки
$GLOBALS['MSG_shops']['cond_of_delivery_add_form_title'] = "Добавление информации об условиях доставки";
$GLOBALS['MSG_shops']['cond_of_delivery_edit_form_title'] = "Редактирование информации об условиях доставки";
$GLOBALS['MSG_shops']['cond_of_delivery_add_form_name'] = "<acronym title=\"Название условия доставки\">Название</acronym>";
$GLOBALS['MSG_shops']['type_of_delivery_id'] = "<acronym title=\"Тип доставки, к которому относится условие доставки\">Тип доставки, к которому относится условие доставки</acronym>";
$GLOBALS['MSG_shops']['cond_of_delivery_add_form_country'] = "<acronym title=\"Страна доставки\">Страна</acronym>";
$GLOBALS['MSG_shops']['location_id'] = "<acronym title=\"Местоположение доставки\">Местоположение</acronym>";
$GLOBALS['MSG_shops']['cond_of_delivery_add_form_city'] = "<acronym title=\"Город доставки\">Город</acronym>";
$GLOBALS['MSG_shops']['cond_of_delivery_add_form_city_area'] = "<acronym title=\"Район доставки\">Район</acronym>";
$GLOBALS['MSG_shops']['cond_of_delivery_add_form_description'] = "<acronym title=\"Описание условия доставки\">Описание</acronym>";

$GLOBALS['MSG_shops']['links_groups'] = "Группа";
$GLOBALS['MSG_shops']['links_items'] = "Товар";
$GLOBALS['MSG_shops']['cond_of_delivery_add_form_price_order'] = "<acronym title=\"Сумма заказанных товаров\">Сумма заказа</acronym>";
$GLOBALS['MSG_shops']['cond_of_delivery_add_form_price'] = "<acronym title=\"Цена доставки\">Цена доставки</acronym>";
$GLOBALS['MSG_shops']['cond_of_delivery_success_delete'] = "Данные об условиях доставки успешно удалены!";
$GLOBALS['MSG_shops']['error_add_cond_of_delivery'] = "Ошибка удаления условия доставки!";
$GLOBALS['MSG_shops']['success_add_cond_of_delivery'] = "Данные об условиях доставки успешно добавлены!";
$GLOBALS['MSG_shops']['success_edit_cond_of_delivery'] = "Данные об условиях доставки успешно обновлены!";
$GLOBALS['MSG_shops']['error_add_cond_of_delivery'] = "Ошибка добавления данных об условиях доставки!";
$GLOBALS['MSG_shops']['error_edit_cond_of_delivery'] = "Ошибка обновления данных об условиях доставки!";
$GLOBALS['MSG_shops']['groups_add_form_order'] = "<acronym title=\"Порядок сортировки групп\">Порядок сортировки</acronym>";
$GLOBALS['MSG_shops']['groups_add_form_indexation'] = "<acronym title=\"Разрешить индексация групп модулем поиска по сайту\">Индексировать группу</acronym>";
$GLOBALS['MSG_shops']['groups_add_form_activity'] = "<acronym title=\"Статус активности группы магазина\">Статус</acronym>";
$GLOBALS['MSG_shops']['groups_add_form_access'] = '<acronym title="Группа, имеющая права доступа к группе товаров">Группа доступа</acronym>';

$GLOBALS['MSG_shops']['shop_users_group_all'] = 'Все';
$GLOBALS['MSG_shops']['shop_users_group_parrent'] = 'Как у родителя';

$GLOBALS['MSG_shops']['groups_add_form_activity_true'] = 'Активна';
$GLOBALS['MSG_shops']['groups_add_form_activity_false'] = 'Неактивна';

$GLOBALS['MSG_shops']['items_catalog_add_form_title'] = "Добавление информации о товаре";
$GLOBALS['MSG_shops']['items_catalog_edit_form_title'] = "Редактирование информации о товаре";
$GLOBALS['MSG_shops']['items_catalog_add_form_comment_link'] = 'Отзывы';
$GLOBALS['MSG_shops']['items_catalog_add_form_group'] = '<acronym title="Группа, которой принадлежит товар">Группа</acronym>';
$GLOBALS['MSG_shops']['items_catalog_add_form_modifications_list'] = 'Список модификаций';
$GLOBALS['MSG_shops']['items_catalog_add_form_name'] = "<acronym title=\"Название товара\">Название</acronym>";
$GLOBALS['MSG_shops']['items_catalog_add_form_marking'] = "<acronym title=\"Артикул товара\">Артикул</acronym>";
$GLOBALS['MSG_shops']['items_catalog_add_form_description'] = "<acronym title=\"Краткое описание товара\">Описание</acronym>";
$GLOBALS['MSG_shops']['items_catalog_add_form_text'] = "<acronym title=\"Детальное описание товара\">Текст</acronym>";

$GLOBALS['MSG_shops']['items_catalog_add_form_order'] = "<acronym title=\"Порядок сортировки товара\">Порядок сортировки</acronym>";
$GLOBALS['MSG_shops']['items_catalog_add_form_ves'] = "<acronym title=\"Вес товара\">Вес</acronym>";
$GLOBALS['MSG_shops']['items_catalog_add_form_rest'] = "<acronym title=\"Количество оставшегося на складе товара\">Количество</acronym>";
$GLOBALS['MSG_shops']['items_catalog_add_form_indexation'] = "<acronym title=\"Разрешает индексацию товара встроенной поисковой системой\">Индексировать товар</acronym>";

$GLOBALS['MSG_shops']['items_catalog_discount'] = "<acronym title=\"Валюта цены\">Валюта</acronym>";
$GLOBALS['MSG_shops']['items_catalog_tax'] = '<acronym title="Налог на товар">Налог</acronym>';

$GLOBALS['MSG_shops']['items_catalog_producer'] = "<acronym title=\"Производитель товара\">Производитель</acronym>";
$GLOBALS['MSG_shops']['items_catalog_add_form_is_active'] = "<acronym title=\"Активный товар доступен для заказа и публикуется в каталоге\">Товар активен</acronym>";
$GLOBALS['MSG_shops']['items_catalog_add_form_access'] = '<acronym title="Группа, имеющая права доступа к товару">Группа доступа</acronym>';

$GLOBALS['MSG_shops']['items_catalog'] = 'Свойства';
$GLOBALS['MSG_shops']['items_catalogs'] = 'Свойство';
$GLOBALS['MSG_shops']['company_success_delete'] = "Информация о компании успешно удалена";
$GLOBALS['MSG_shops']['companys_success_delete'] = "Информация о компаниях успешно удалена";
$GLOBALS['MSG_shops']['groups_success_delete'] = "Информация о группе товаров успешно удалена!";
$GLOBALS['MSG_shops']['groups_success_deletes'] = "Информация о группах товаров успешно удалена!";
$GLOBALS['MSG_shops']['currency_success_delete'] = "Информация о валюте успешно удалена!";
$GLOBALS['MSG_shops']['currencys_success_delete'] = "Информация о валютах успешно удалена!";

$GLOBALS['MSG_shops']['items_catalog_success_delete'] = "Информация о товаре успешно удалена!";
$GLOBALS['MSG_shops']['items_catalogs_success_delete'] = "Информация о товарах успешно удалена!";
$GLOBALS['MSG_shops']['success_add_items_catalog'] = "Информация о товаре успешно добавлена!";
$GLOBALS['MSG_shops']['error_marking_add_items_catalog'] = 'Уже существует товар с таким артикулом!';
$GLOBALS['MSG_shops']['success_edit_items_catalog'] = "Информация о товаре успешно обновлена!";

$GLOBALS['MSG_shops']['error_add_items_catalog_chmod'] = 'Ошибка при создании директории. Проверьте права доступа к папке ';
$GLOBALS['MSG_shops']['items_catalog_add_form_additional_titleform'] = "Дополнительные свойства";
$GLOBALS['MSG_shops']['shops_add_form_link_properties'] = "Свойства товара";
$GLOBALS['MSG_shops']['shops_add_form_link_properties_for_group'] = "Свойства групп";
$GLOBALS['MSG_shops']['show_producers_title'] = "Список производителей";
$GLOBALS['MSG_shops']['show_producers_link1'] = "Добавить";
$GLOBALS['MSG_shops']['show_producers_order'] = "Порядок сортировки";
$GLOBALS['MSG_shops']['show_invalid_characters_integer'] = "Указаны недопустимые символы (разрешенный диапазон символов 0-9)";
$GLOBALS['MSG_shops']['show_invalid_characters_decimal'] = 'Указаны недопустимые символы (разрешенный диапазон символов 0-9, а также символ ".")';
$GLOBALS['MSG_shops']['show_invalid_characters_path'] = 'Указаны недопустимые символы (разрешенный диапазон символов а-я, А-Я, a-z, A-Z, 0-9, символ "_", "-" и ".")';
$GLOBALS['MSG_shops']['show_invalid_characters_latin_base'] = 'Указаны недопустимые символы (разрешенный диапазон символов a-z, A-Z, 0-9 а также символ "_")';
$GLOBALS['MSG_shops']['show_small_image'] = '(Малое изображение)';

$GLOBALS['MSG_shops']['producer_add_form_title'] = "Добавление информации о производителе";
$GLOBALS['MSG_shops']['producer_edit_form_title'] = "Редактирование информации о производителе";
$GLOBALS['MSG_shops']['producer_name'] = "<acronym title=\"Название производителя\">Название</acronym>";
$GLOBALS['MSG_shops']['producer_description'] = "<acronym title=\"Описание производителя\">Описание</acronym>";
$GLOBALS['MSG_shops']['producer_image'] = "<acronym title=\"Большое изображение производителя\">Большое изображение</acronym>";
$GLOBALS['MSG_shops']['producer_small_image'] = "<acronym title=\"Малое изображение производителя\">Малое изображение</acronym>";

$GLOBALS['MSG_shops']['producer_order'] = "<acronym title=\"Порядок сортировки производителей\">Порядок сортировки</acronym>";

$GLOBALS['MSG_shops']['success_add_producer'] = "Информация о производителе успешно добавлена!";
$GLOBALS['MSG_shops']['success_edit_producer'] = "Информация о производителе успешно обновлена!";
$GLOBALS['MSG_shops']['error_add_producer'] = "Ошибка добавления информации о производителе!";
$GLOBALS['MSG_shops']['error_edit_producer'] = "Ошибка обновления информации о производителе!";
$GLOBALS['MSG_shops']['producer_success_delete'] = "Информация о производителе успешно удалена!";
$GLOBALS['MSG_shops']['producers_success_delete'] = "Информация о производителях успешно удалена!";

$GLOBALS['MSG_shops']['success_update_producer'] = "Информация о производителе успешно обновлена!";
$GLOBALS['MSG_shops']['error_update_producer'] = "Ошибка обновления информации о производителе!";
$GLOBALS['MSG_shops']['show_groups_discount'] = "Скидка";

$GLOBALS['MSG_shops']['shop_menu_title'] = "Скидки";
$GLOBALS['MSG_shops']['show_groups_comment'] = 'Отзывы';

$GLOBALS['MSG_shops']['show_groups_modification'] = 'Модификация';
$GLOBALS['MSG_shops']['show_item_discount_title'] = "Список скидок для товара \"%s\"";
$GLOBALS['MSG_shops']['show_item_comment_title'] = "Список отзывов о товаре \"%s\"";
$GLOBALS['MSG_shops']['add_item_discount_link'] = "Добавить";
$GLOBALS['MSG_shops']['modifications'] = 'Модификации товара';

$GLOBALS['MSG_shops']['item_discount_add_form_title'] = "Добавление информации о скидке на товар";
$GLOBALS['MSG_shops']['item_discount_add_form_title'] = "Добавление информации о скидке на товар";
$GLOBALS['MSG_shops']['item_discount_edit_form_title'] = "Редактирование информации о скидке на товар";
$GLOBALS['MSG_shops']['item_discount_name'] = "<acronym title=\"Список скидок\">Название скидки</acronym>";
$GLOBALS['MSG_shops']['success_add_item_discount'] = "Информация о скидке для товара успешно добавлена/обновлена!";
$GLOBALS['MSG_shops']['error_add_item_discount'] = "Ошибка добавления/изменения информации о скидке на товар!";
$GLOBALS['MSG_shops']['show_list_of_properties_title'] = "Список свойств товара интернет-магазина ";

$GLOBALS['MSG_shops']['list_of_property_for_group'] = "Список свойств групп товаров";

$GLOBALS['MSG_shops']['show_list_of_properties_link1'] = "Добавить";

$GLOBALS['MSG_shops']['form_add_property_for_group'] = "Добавление информации о свойстве групп товаров";
$GLOBALS['MSG_shops']['form_edit_property_for_group'] = "Редактирование информации о свойстве групп товаров";

$GLOBALS['MSG_shops']['form_add_property_dir_for_group'] = "Добавление информации о разделе группы свойств";
$GLOBALS['MSG_shops']['form_edit_property_dir_for_group'] = "Редактирование информации о разделе группы свойств";

$GLOBALS['MSG_shops']['list_of_properties_add_form_title'] = "Добавление информации о свойстве товара";
$GLOBALS['MSG_shops']['list_of_properties_edit_form_title'] = "Редактирование информации о свойстве товара";
$GLOBALS['MSG_shops']['list_of_properties_add_form_link'] = "Список свойств товара";
$GLOBALS['MSG_shops']['list_of_properties_add_form_name'] = "<acronym title=\"Название дополнительного свойства товара\">Название</acronym>";
$GLOBALS['MSG_shops']['list_of_properties_add_form_xml_name'] = "<acronym title=\"Название XML-тега, который будет содержать значение свойства\">Название XML-тега</acronym>";
$GLOBALS['MSG_shops']['list_of_properties_add_form_type'] = "<acronym title=\"Тип свойства\">Тип</acronym>";
$GLOBALS['MSG_shops']['list_of_properties_add_form_order'] = "<acronym title=\"Порядок сортировки свойства\">Порядок сортировки</acronym>";
$GLOBALS['MSG_shops']['list_of_properties_add_form_prefics'] = "<acronym title=\"Префикс свойства\">Префикс</acronym>";
$GLOBALS['MSG_shops']['list_of_properties_add_form_show_kind'] = '<acronym title="Способ отображения свойства в фильтре">Способ отображения свойства в фильтре</acronym>';
$GLOBALS['MSG_shops']['list_of_properties_add_form_default_value'] = "<acronym title=\"Значение свойства по умолчанию\">Значение по умолчанию</acronym>";

$GLOBALS['MSG_shops']['list_of_properties_add_checkbox_value'] = "<acronym title=\"Отображать элемент выбранным или нет\">Выбран</acronym>";

$GLOBALS['MSG_shops']['list_of_properties_mesures'] = "<acronym title=\"Единица измерения свойства\">Единица измерения</acronym>";

$GLOBALS['MSG_shops']['success_add_group_properties'] = "Информация о свойстве групп товаров успешно добавлена!";

$GLOBALS['MSG_shops']['success_edit_group_properties'] = "Информация о свойстве групп товаров успешно обновлена!";


$GLOBALS['MSG_shops']['success_edit_group_for_dir'] = "Информация о разделе дополнительных свойств успешно изменена";
$GLOBALS['MSG_shops']['success_add_group_for_dir'] = "Информация о разделе дополнительных свойств успешно добавлена";
$GLOBALS['MSG_shops']['error_edit_group_for_dir'] = "Ошибка изменения информации о разделе дополнительных свойств";
$GLOBALS['MSG_shops']['error_add_group_for_dir'] = "Ошибка добавления информации о разделе дополнительных свойств";




$GLOBALS['MSG_shops']['error_add_group_properties'] = "Ошибка добавления информации о свойстве групп товаров!";

$GLOBALS['MSG_shops']['error_edit_group_properties'] = "Ошибка обновления информации о свойстве групп товаров!";



$GLOBALS['MSG_shops']['group_properties_success_delete'] = "Информация о свойстве групп товаров успешно удалена!";

$GLOBALS['MSG_shops']['groups_properties_success_delete'] = "Информация о свойствах групп товаров успешно удалена!";




$GLOBALS['MSG_shops']['list_of_properties_edit_error'] = "Ошибка редактирования информации о свойстве товара!";
$GLOBALS['MSG_shops']['shops_add_form_link_orders'] = "Оформленные заказы";
$GLOBALS['MSG_shops']['shops_link_orders'] = "Заказы";
$GLOBALS['MSG_shops']['shops_link_order'] = "Заказ";
$GLOBALS['MSG_shops']['show_order_title'] = "Заказы магазина \"%s\"";
$GLOBALS['MSG_shops']['add_order_link'] = "Добавить";
$GLOBALS['MSG_shops']['show_order_number'] = "<acronym title=\"Номер оформленного заказа\">Номер заказа</acronym>";
$GLOBALS['MSG_shops']['show_order_status'] = '<acronym title="Статус заказа">Статус заказа</acronym>';
$GLOBALS['MSG_shops']['show_order_change_status_date'] = '<acronym title="Дата-время именения статуса заказа">Дата изменения статуса заказа</acronym>';
$GLOBALS['MSG_shops']['show_order_ip'] = '<acronym title="IP-адрес заказчика">IP-адрес заказчика</acronym>';
$GLOBALS['MSG_shops']['form_order_name'] = '<acronym title="Имя заказчика">Имя</acronym>';
$GLOBALS['MSG_shops']['form_order_surname'] = '<acronym title="Фамилия заказчика">Фамилия</acronym>';
$GLOBALS['MSG_shops']['form_order_patronymic'] = '<acronym title="Отчество заказчика">Отчество</acronym>';
$GLOBALS['MSG_shops']['form_order_email'] = '<acronym title="E-mail заказчика">E-mail</acronym>';
$GLOBALS['MSG_shops']['form_order_company'] = '<acronym title="Название компании заказчика">Компания</acronym>';
$GLOBALS['MSG_shops']['form_order_fax'] = '<acronym title="Факс заказчика">Факс</acronym>';
$GLOBALS['MSG_shops']['menu_location'] = "Местоположение";
$GLOBALS['MSG_shops']['print'] = "Печать";


$GLOBALS['MSG_shops']['order_add_form_title'] = "Добавление информации о заказе";
$GLOBALS['MSG_shops']['order_edit_form_title'] = "Редактирование информации о заказе";
$GLOBALS['MSG_shops']['order_date_time'] = "<acronym title=\"Дата и время, когда был сделан заказ\">Дата заказа</acronym>";

$GLOBALS['MSG_shops']['add_site_users'] = "<acronym title=\"Код пользователя (заказчика)\">Код пользователя</acronym>";

$GLOBALS['MSG_shops']['order_add_form_country'] = "<acronym title=\"Страна\">Страна</acronym>";
$GLOBALS['MSG_shops']['order_add_form_location'] = "<acronym title=\"Область\">Область</acronym>";
$GLOBALS['MSG_shops']['order_add_form_city'] = "<acronym title=\"Город\">Город</acronym>";
$GLOBALS['MSG_shops']['order_add_form_city_area'] = "<acronym title=\"Район\">Район</acronym>";
$GLOBALS['MSG_shops']['order_status_of_pay'] = '<acronym title="Статус оплаты товара">Оплачен</acronym>';
$GLOBALS['MSG_shops']['cancel_status'] = '<acronym title="Статус отмены товара">Отменен</acronym>';
$GLOBALS['MSG_shops']['order_date_of_pay'] = "<acronym title=\"Дата, когда был оплачен заказ\">Дата оплаты</acronym>";
$GLOBALS['MSG_shops']['order_currency'] = "<acronym title=\"Валюта, в которой будет оплачиваться заказ\">Валюта</acronym>";
$GLOBALS['MSG_shops']['system_of_pay'] = "<acronym title=\"Платежная система, с помощью которой будет оплачен заказ\">Платежная система</acronym>";
$GLOBALS['MSG_shops']['order_address'] = "<acronym title=\"Адрес, по которому нужно доставить заказ\">Адрес</acronym>";
$GLOBALS['MSG_shops']['order_index'] = "<acronym title=\"Почтовый индекс адреса, по которому нужно доставить заказ\">Индекс</acronym>";

$GLOBALS['MSG_shops']['order_phone'] = "<acronym title=\"Контактный телефон заказчика\">Телефон</acronym>";
$GLOBALS['MSG_shops']['type_of_delivery'] = "<acronym title=\"Типы доставок\">Типы доставок</acronym>";
$GLOBALS['MSG_shops']['cond_of_delivery'] = "<acronym title=\"Условия доставки\">Условия доставки</acronym>";

$GLOBALS['MSG_shops']['order_success_delete'] = "Информация о заказе успешно удалена!";
$GLOBALS['MSG_shops']['orders_success_delete'] = "Информация о заказах успешно удалена!";

$GLOBALS['MSG_shops']['form_order_description'] = '<acronym title="Описание заказа">Описание заказа</acronym>';
$GLOBALS['MSG_shops']['form_order_system_information'] = '<acronym title="Дополнительная информация о заказе">Информация о заказе</acronym>';
$GLOBALS['MSG_shops']['form_order_sending_data'] = '<acronym title="Информация об отправлении">Информация об отправлении</acronym>';
$GLOBALS['MSG_shops']['show_shop'] = "Интернет-магазин ";
$GLOBALS['MSG_shops']['success_add_order'] = "Информация о заказе успешно добавлена!";
$GLOBALS['MSG_shops']['success_edit_order'] = "Информация о заказе успешно обновлена!";
$GLOBALS['MSG_shops']['error_add_order'] = "Ошибка добавления информации о заказе!";
$GLOBALS['MSG_shops']['show_order_items_title'] = "Список товаров в заказе № %s";
$GLOBALS['MSG_shops']['order_items_add_form_link'] = "Добавить";
$GLOBALS['MSG_shops']['order_items_success_delete'] = "Информация о товаре в заказе успешно удалена!";
$GLOBALS['MSG_shops']['orders_items_success_delete'] = "Информация о товаре в заказе успешно удалена!";

$GLOBALS['MSG_shops']['order_items_add_form_title'] = "Добавление информации о товаре в заказе %s";
$GLOBALS['MSG_shops']['order_items_edit_form_title'] = "Редактирование информации о товаре в заказе %s";
$GLOBALS['MSG_shops']['shop_items_catalog_item_name'] = "<acronym title=\"Название заказанного товара\">Название товара</acronym>";

$GLOBALS['MSG_shops']['shop_items_seller_id'] = "<acronym title=\"Идентификатор продавца\">Идентификатор продавца</acronym>";

$GLOBALS['MSG_shops']['items_catalog_item_id'] = "<acronym title=\"Идентификатор заказанного товара\">Идентификатор товара</acronym>";

$GLOBALS['MSG_shops']['shop_items_seller_name'] = "<acronym title=\"Имя продавца\">Имя продавца</acronym>";

$GLOBALS['MSG_shops']['shop_items_catalog_item_marking'] = "<acronym title=\"Артикул заказанного товара\">Артикул</acronym>";
$GLOBALS['MSG_shops']['shop_items_catalog_vendorCode'] = "<acronym title=\"Код товара (указывается код производителя), размещается в элементе 'vendorCode' при экспорте в Яндекс.Маркет\">Код товара</acronym>";
$GLOBALS['MSG_shops']['shop_mesure_delete_success'] = "Информация о единице измерения успешно удалена";
$GLOBALS['MSG_shops']['shop_mesures_delete_success'] = "Информация о единицах измерения успешно удалена";
$GLOBALS['MSG_shops']['list_of_coupons'] = 'Список купонов';

$GLOBALS['MSG_shops']['coupon'] = 'Купон';

$GLOBALS['MSG_shops']['shop_order_items_quantity'] = "<acronym title=\"Количество товара в заказе\">Количество</acronym>";
$GLOBALS['MSG_shops']['shop_order_items_price'] = "<acronym title=\"Цена товара в заказе в валюте заказа\">Цена</acronym>";

$GLOBALS['MSG_shops']['shop_order_items_currency'] = "<acronym title=\"Валюта заказа\">Валюта</acronym>";

$GLOBALS['MSG_shops']['success_add_order_items'] = "Информация о товаре в заказе успешно добавлена";
$GLOBALS['MSG_shops']['success_edit_order_items'] = "Информация о товаре в заказе успешно обновлена";
$GLOBALS['MSG_shops']['error_add_order_items'] = "Ошибка добавления информации о товаре в заказе";
$GLOBALS['MSG_shops']['error_edit_order_items'] = "Ошибка обновления информации о товаре в заказе";

$GLOBALS['MSG_shops']['show_country_link'] = "Справочник стран";
$GLOBALS['MSG_shops']['city_success_delete'] = "Информация о городе успешно удалена!";
$GLOBALS['MSG_shops']['citys_success_delete'] = "Информация о городах успешно удалена!";
$GLOBALS['MSG_shops']['show_city_title'] = "Города местоположения";
$GLOBALS['MSG_shops']['show_city_link1'] = "Добавить";

$GLOBALS['MSG_shops']['citys'] = "Город";
$GLOBALS['MSG_shops']['show_city_area_link'] = "Район";

$GLOBALS['MSG_shops']['show_city_area_title'] = "Районы города";

$GLOBALS['MSG_shops']['city_area_add_form_link'] = "Добавить";
$GLOBALS['MSG_shops']['add_city_area_tytle'] = "Добавление района города";
$GLOBALS['MSG_shops']['edit_city_area_tytle'] = "Редактирование района города";
$GLOBALS['MSG_shops']['add_city_area_form_name'] = "<acronym title=\"Название района\">Название</acronym>";
$GLOBALS['MSG_shops']['add_city_area_form_button'] = "Добавить";
$GLOBALS['MSG_shops']['add_city_area_success'] = "Информация о районе города успешно добавлена.";
$GLOBALS['MSG_shops']['edit_city_area_success'] = "Информация о районе города успешно изменена.";
$GLOBALS['MSG_shops']['add_edit_city_area_error'] = "Ошибка добавления/редактирования информации о районе города!";
$GLOBALS['MSG_shops']['delete_city_area_success'] = "Информация о районе города успешно удалена.";
$GLOBALS['MSG_shops']['delete_city_areas_success'] = "Информация о районах города успешно удалена.";
$GLOBALS['MSG_shops']['city_add_form_title'] = "Добавление информации о городе";
$GLOBALS['MSG_shops']['city_add_form_title'] = "Редактирование информации о городе";
$GLOBALS['MSG_shops']['city_add_form_name'] = "<acronym title=\"Название города\">Название</acronym>";
$GLOBALS['MSG_shops']['success_add_city'] = "Информация о городе успешно добавлена!";
$GLOBALS['MSG_shops']['error_add_city'] = "Ошибка добавления информации о городе!";
$GLOBALS['MSG_shops']['list_of_properties_edit_error'] = "Ошибка редактирования данных о городе!";
$GLOBALS['MSG_shops']['success_update_city'] = "Информация о городе успешно обновлена!";
$GLOBALS['MSG_shops']['type_of_delivery_success_delete'] = "Информация о типе доставки успешно удалена!";
$GLOBALS['MSG_shops']['type_of_deliverys_success_delete'] = "Информация о типах доставки успешно удалена!";
$GLOBALS['MSG_shops']['show_type_of_delivery_title'] = "Список типов доставки";
$GLOBALS['MSG_shops']['show_type_of_delivery_link1'] = "Добавить";
$GLOBALS['MSG_shops']['type_of_delivery_add_form_title'] = "Добавление информации о типе доставки";
$GLOBALS['MSG_shops']['type_of_delivery_edit_form_title'] = "Редактирование информации о типе доставки";
$GLOBALS['MSG_shops']['type_of_delivery_name'] = "<acronym title=\"Название типа доставки\">Название</acronym>";
$GLOBALS['MSG_shops']['type_of_delivery_description'] = "<acronym title=\"Описание типа доставки\">Описание</acronym>";
$GLOBALS['MSG_shops']['type_of_delivery_image'] = "<acronym title=\"Изображение типа доставки\">Изображение</acronym>";
$GLOBALS['MSG_shops']['success_add_type_of_delivery'] = "Информация о типе доставки успешно добавлена!";
$GLOBALS['MSG_shops']['success_edit_type_of_delivery'] = "Информация о типе доставки успешно обновлена!";
$GLOBALS['MSG_shops']['error_add_type_of_delivery'] = "Ошибка добавления информации о типе доставки!";

$GLOBALS['MSG_shops']['show_cond_of_delivery'] = "Условия доставки";
// Сообщения для загрузки прайс листов
$GLOBALS['MSG_shops']['import_price_list_link'] = "Импорт товаров";
$GLOBALS['MSG_shops']['import_delivery_link'] = "Импорт условий доставки";
$GLOBALS['MSG_shops']['import_coupons_link'] = "Импорт купонов";

$GLOBALS['MSG_shops']['export_shop'] = "Экспорт товаров";

$GLOBALS['MSG_shops']['import_price_list_title'] = "Загрузка прайс-листов";
// Первая форма
$GLOBALS['MSG_shops']['export_file_type'] = "<acronym title=\"Тип выгружаемого файла\">Выберите тип файла</acronym>";

$GLOBALS['MSG_shops']['load_parent_group'] = '--- Корневая ---';

$GLOBALS['MSG_shops']['import_price_list_file_type'] = "<acronym title=\"Тип загружаемого файла\">Выберите тип файла</acronym>";
$GLOBALS['MSG_shops']['import_price_list_file_type1'] = "CSV-файл";
$GLOBALS['MSG_shops']['import_price_list_file_type2'] = "CommerceML";

$GLOBALS['MSG_shops']['export_price_list_file_type1'] = "CSV-файл";
$GLOBALS['MSG_shops']['export_price_list_file_type2'] = "CommerceML v. 1.xx";
$GLOBALS['MSG_shops']['export_price_list_file_type3_import'] = "CommerceML v. 2.0x (import.xml)";
$GLOBALS['MSG_shops']['export_price_list_file_type3_offers'] = "CommerceML v. 2.0x (offers.xml)";

$GLOBALS['MSG_shops']['import_price_list_file'] = "<acronym title=\"Выберите файл с компьютера\">Выберите файл с компьютера</acronym>";
$GLOBALS['MSG_shops']['import_price_list_name_field_f'] = "<acronym title=\"Флаг, указывающий на то, содержит ли первая строка имена полей\">Первая строка содержит имена полей</acronym>";
$GLOBALS['MSG_shops']['import_price_list_separator'] = "<acronym title=\"Разделитель для столбцов\">Разделитель</acronym>";
$GLOBALS['MSG_shops']['import_price_list_separator1'] = "Запятая";
$GLOBALS['MSG_shops']['import_price_list_separator2'] = "Точка с запятой";
$GLOBALS['MSG_shops']['import_price_list_separator3'] = "Табуляция";
$GLOBALS['MSG_shops']['import_price_list_separator4'] = 'Другой</label> <input type="text" name="import_price_separator_text" size="5" />';
$GLOBALS['MSG_shops']['price_list_encoding'] = "Кодировка";
$GLOBALS['MSG_shops']['import_price_list_stop'] = "<acronym title=\"Ограничитель для полей\">Ограничитель</acronym>";
$GLOBALS['MSG_shops']['import_price_list_stop1'] = "Кавычки";
$GLOBALS['MSG_shops']['import_price_list_stop2'] = 'Другой</label> <input type="text" name="import_price_stop_text" size="5" />';
$GLOBALS['MSG_shops']['import_price_list_button_load'] = "Загрузить";
$GLOBALS['MSG_shops']['export_items_catalog'] = "Экспортировать";

$GLOBALS['MSG_shops']['import_price_list_max_time'] = "<acronym title=\"Максимальное время выполнения (в секундах)\">Максимальное время выполнения</acronym>";
$GLOBALS['MSG_shops']['import_price_list_max_count'] = "<acronym title=\"Максимальное количество импортируемых за шаг товаров\">Максимальное кол-во импортируемых за шаг</acronym>";

// Вторая форма
$GLOBALS['MSG_shops']['import_price_list_import_param'] = "Импортируемые поля";
$GLOBALS['MSG_shops']['import_price_list_action_items'] = "<acronym title=\"Действие для существующих товаров\">Действие для существующих товаров</acronym>";
$GLOBALS['MSG_shops']['import_price_list_action_delete_image'] = "<acronym title=\"Установка данного флага позволяет удалять изображения для элементов товара, если эти изображения не переданы или пусты\">Удалять изображения для товаров при обновлении</acronym>";
//$GLOBALS['MSG_shops']['import_price_list_parent_group'] = "<acronym title=\"Вы можете загружать товары как в корневую группу каталога, так и в указанную в данном списке\">Родительская группа для загрузки товаров</acronym>";

$GLOBALS['MSG_shops']['import_price_list_parent_group'] = "<acronym title=\"Вы можете выгружать товары из указанного каталога, включая все подкаталоги\">Родительская группа для выгрузки товаров</acronym>";

$GLOBALS['MSG_shops']['import_price_list_parent_group_name'] = '--- Корневая ---';

$GLOBALS['MSG_shops']['import_price_list_cml_insert_tax_mask'] = "%s, %d%%";

// Сообщения для справочника стран
$GLOBALS['MSG_shops']['show_country_link1'] = "Добавить";
$GLOBALS['MSG_shops']['countrys'] = "Страны";
$GLOBALS['MSG_shops']['country_add_form_title'] = "Добавление информации о стране";
$GLOBALS['MSG_shops']['country_edit_form_title'] = "Редактирование информации о стране";
$GLOBALS['MSG_shops']['country_add_form_link1'] = "Список стран";
$GLOBALS['MSG_shops']['country_add_form_name'] = "<acronym title=\"Название страны\">Название страны</acronym>";
$GLOBALS['MSG_shops']['success_add_country'] = "Информация о стране успешно добавлена!";
$GLOBALS['MSG_shops']['success_edit_country'] = "Информация о стране успешно обновлена!";
$GLOBALS['MSG_shops']['error_add_country'] = "Ошибка добавления информации о стране!";
$GLOBALS['MSG_shops']['error_edit_country'] = "Ошибка обновления информации о стране!";
$GLOBALS['MSG_shops']['country_success_delete'] = "Информация о стране успешно удалена";
$GLOBALS['MSG_shops']['location_success_delete'] = "Информация об области успешно удалена";
$GLOBALS['MSG_shops']['locations_success_delete'] = "Информация об областях успешно удалена";
$GLOBALS['MSG_shops']['countrys_success_delete'] = "Информация о странах успешно удалена";
$GLOBALS['MSG_shops']['items_catalog_add_form_prices_titleform'] = "Цены на товар";
$GLOBALS['MSG_shops']['show_tying_products_title'] = "Сопутствующие товары товара \"%s\"";
$GLOBALS['MSG_shops']['show_tying_products_menu'] = "Сопутствующие товары";

$GLOBALS['MSG_shops']['tying_products_edit_form_title'] = "Редактирование информации о сопутствующих товарах";
$GLOBALS['MSG_shops']['tying_products_name'] = "<acronym title=\"Список сопутствующих товаров\">Название сопутствующего товара</acronym>";
$GLOBALS['MSG_shops']['success_add_tying_products'] = "Информация о сопутствующем товаре успешно добавлена!";
$GLOBALS['MSG_shops']['success_edit_tying_products'] = "Информация о сопутствующем товаре успешно обновлена!";
$GLOBALS['MSG_shops']['error_add_tying_products'] = "Ошибка добавления информации о сопутствующем товаре!";
$GLOBALS['MSG_shops']['error_edit_tying_products'] = "Ошибка обновления информации о сопутствующем товаре!";

$GLOBALS['MSG_shops']['list_of_properties_string'] = "Строка";
$GLOBALS['MSG_shops']['list_of_properties_file'] = "Файл";
$GLOBALS['MSG_shops']['list_of_properties_list'] = "Список";
$GLOBALS['MSG_shops']['list_of_properties_add_form_lists'] = "<acronym title=\"Списки свойств товаров\">Списки</acronym>";
$GLOBALS['MSG_shops']['error_setup_picture'] = "Ошибка уменьшения изображения до максимально допустимого размера.";

$GLOBALS['MSG_shops']['msg_download_price'] = "Следующий этап загрузки прайс-листа произойдет через 1 секунду.";
$GLOBALS['MSG_shops']['error_open_bakfile'] = "Ошибка не найден временный файл с данными! Проверьте существование дирректории для хранения временных данных.";

$GLOBALS['MSG_shops']['error_dir'] = 'Ошибка! Отсутствует директория ';

$GLOBALS['MSG_shops']['big_text'] = 'Большое текстовое поле';
$GLOBALS['MSG_shops']['visual_edit'] = 'Визуальный редактор';
$GLOBALS['MSG_shops']['checkbox'] = 'Флажок';
$GLOBALS['MSG_shops']['date'] = 'Дата';
$GLOBALS['MSG_shops']['date_time'] = 'ДатаВремя';

$GLOBALS['MSG_shops']['error_criate_write_file'] = 'Ошибка создания и записи в файл ';
$GLOBALS['MSG_shops']['error_load_file'] = 'Ошибка загрузки файла %s';

$GLOBALS['MSG_shops']['download_elements'] = 'Загрузка завершена!';
$GLOBALS['MSG_shops']['count_insert_item'] = 'Загружено товаров';
$GLOBALS['MSG_shops']['count_update_item'] = 'Обновлено товаров';
$GLOBALS['MSG_shops']['create_catalog'] = 'Создано разделов каталога';
$GLOBALS['MSG_shops']['msg_download_file'] = 'Файл успешно загружен.';
$GLOBALS['MSG_shops']['msg_import_file'] = 'Не указан файл импорта!';
$GLOBALS['MSG_shops']['!download'] = '-- Не загружать --';
$GLOBALS['MSG_shops']['item_id'] = 'Идентификатор товара';
$GLOBALS['MSG_shops']['tax_id'] = 'Идентификатор налога для товара';
$GLOBALS['MSG_shops']['groups_id'] = 'Идентификатор раздела';
$GLOBALS['MSG_shops']['name_groups'] = 'Название раздела';
$GLOBALS['MSG_shops']['groups_path'] = 'Путь для раздела';
$GLOBALS['MSG_shops']['groups_description'] = 'Описание раздела';
$GLOBALS['MSG_shops']['shop_groups_cml_id'] = 'CML GROUP ID идентификатор группы товаров';
$GLOBALS['MSG_shops']['shop_groups_parent_cml_id'] = 'CML GROUP ID идентификатор родительской группы товаров';
$GLOBALS['MSG_shops']['currency_id'] = 'Идентификатор валюты';

$GLOBALS['MSG_shops']['producers_id'] = 'Идентификатор производителя';
$GLOBALS['MSG_shops']['producers_name'] = 'Название производителя';

$GLOBALS['MSG_shops']['sellers_id'] = 'Идентификатор продавца';
$GLOBALS['MSG_shops']['sellers_name'] = 'Название продавца';

$GLOBALS['MSG_shops']['mesures_id'] = 'Идентификатор единицы измерения';
$GLOBALS['MSG_shops']['mesures_value'] = 'Название единицы измерения';
$GLOBALS['MSG_shops']['catalog_value'] = 'Название товара';
$GLOBALS['MSG_shops']['catalog_marking'] = 'Артикул товара';

$GLOBALS['MSG_shops']['catalog_date'] = 'Дата';

$GLOBALS['MSG_shops']['catalog_description'] = 'Описание товара';
$GLOBALS['MSG_shops']['catalog_text'] = 'Текст для товара';
$GLOBALS['MSG_shops']['catalog_image'] = 'Файл изображения для товара';
$GLOBALS['MSG_shops']['catalog_small_image'] = 'Файл малого изображения для товара';
$GLOBALS['MSG_shops']['catalog_image_group'] = 'Файл изображения для группы товаров';
$GLOBALS['MSG_shops']['catalog_small_image_group'] = 'Файл малого изображения для группы товаров';

$GLOBALS['MSG_shops']['catalog_label'] = 'Метки';

$GLOBALS['MSG_shops']['catalog_weight'] = 'Вес товара';
$GLOBALS['MSG_shops']['catalog_rest'] = 'Количество на складе';
$GLOBALS['MSG_shops']['catalog_price'] = 'Цена товара';
$GLOBALS['MSG_shops']['catalog_is_active'] = 'Активность товара';
$GLOBALS['MSG_shops']['group_is_active'] = 'Активность группы';
$GLOBALS['MSG_shops']['transaction_is_active'] = 'Активность транзакции';
$GLOBALS['MSG_shops']['catalog_order'] = 'Порядок сортировки товара';
$GLOBALS['MSG_shops']['catalog_path'] = 'Путь к товару';
$GLOBALS['MSG_shops']['catalog_seo_title'] = "<acronym title=\"Значение мета-тега title для страницы с товаром\">Заголовок (title)</acronym>";
$GLOBALS['MSG_shops']['catalog_seo_description'] = "<acronym title=\"Значение мета-тега description для страницы с товаром\">Описание (description)</acronym>";
$GLOBALS['MSG_shops']['catalog_seo_keywords'] = "<acronym title=\"Значение мета-тега keywords для страницы с товаром\">Ключевые слова (keywords)</acronym>";
$GLOBALS['MSG_shops']['catalog_indexation'] = 'Флаг индексации';
$GLOBALS['MSG_shops']['export_yandex_market'] = 'Флаг "Экспортировать в Яндекс.Маркет"';
$GLOBALS['MSG_shops']['export_rambler_pokupki'] = 'Флаг "Экспортировать в Рамблер-Покупки"';
$GLOBALS['MSG_shops']['yandex_market_base_price'] = 'Яндекс.Маркет основная расценка';
$GLOBALS['MSG_shops']['yandex_market_card_of_models'] = 'Яндекс.Маркет расценка для карточек моделей';
$GLOBALS['MSG_shops']['item_parent_mark'] = 'Артикул родительского товара для модификации';

$GLOBALS['MSG_shops']['catalog_date'] = 'Дата';

$GLOBALS['MSG_shops']['catalog_item_date'] = '<acronym title="Дата добавления товара">Дата</acronym>';

$GLOBALS['MSG_shops']['catalog_item_put_off'] = '<acronym title="Дата публикации товара">Дата публикации</acronym>';

$GLOBALS['MSG_shops']['catalog_item_put_end'] = '<acronym title="Дата завершения публикации товара">Дата завершения публикации</acronym>';

/* Типограф */
$GLOBALS['MSG_shops']['exec_typograph_for_description'] = '<acronym title="Применить типографирование к описанию">Типографировать описание</acronym>';
$GLOBALS['MSG_shops']['use_trailing_punctuation_for_text'] = '<acronym title="Оптическое выравнивание текста перемещает символы пунктуации за границу набора">Оптическое выравнивание</acronym>';
$GLOBALS['MSG_shops']['exec_typograph_for_text'] = '<acronym title="Применить типографирование к тексту">Типографировать текст</acronym>';
$GLOBALS['MSG_shops']['use_trailing_punctuation_for_prop'] = '<acronym title="Оптическое выравнивание текста перемещает символы пунктуации за границу набора">Оптическое выравнивание</acronym>';

/* Yandex.Маркет */
$GLOBALS['MSG_shops']['yandex_market_allow'] = '<acronym title="Экспортировать товар в систему Яндекс.Маркет">Экспортировать в Яндекс.Маркет</acronym>';
$GLOBALS['MSG_shops']['rambler_pokupki_checkbox'] = '<acronym title="Экспортировать товар в систему Рамблер-Покупки">Экспортировать в Рамблер-Покупки</acronym>';
$GLOBALS['MSG_shops']['yandex_market_bid'] = '<acronym title="Основная расценка для системы Яндекс.Маркет (указывается в центах)">Яндекс.Маркет - основная расценка</acronym>';
$GLOBALS['MSG_shops']['yandex_market_cid'] = '<acronym title="Расценка для карточек моделей системы Яндекс.Маркет (указывается в центах)">Яндекс.Маркет - расценка для карточек моделей</acronym>';


$GLOBALS['MSG_shops']['yandex_market_sales_notes'] = '<acronym title="Тег &lt;sales_notes&gt;, экспортируемый в Яндекс.Маркет">Отличие товара от других (значение тега &lt;sales_notes&gt;)</acronym>';


/* Продавцы */
$GLOBALS['MSG_shops']['show_sellers_link'] = 'Продавцы';
$GLOBALS['MSG_shops']['show_seller_link'] = 'Продавец';

$GLOBALS['MSG_shops']['show_sellers_list'] = 'Список продавцов';
$GLOBALS['MSG_shops']['show_add_seller_link'] = 'Добавить';

$GLOBALS['MSG_shops']['edit_sellers_error'] = 'Ошибка! Продавца с указанным идентификатором не существует!';
$GLOBALS['MSG_shops']['form_sellers_add_title'] = "Добавление информации о продавце";
$GLOBALS['MSG_shops']['form_sellers_edit_title'] = "Редактирование информации о продавце";
$GLOBALS['MSG_shops']['form_sellers_name'] = "<acronym title=\"Продавец товара\">Продавец</acronym>";
$GLOBALS['MSG_shops']['form_sellers_description'] = "<acronym title=\"Описание продавца\">Описание</acronym>";

$GLOBALS['MSG_shops']['form_sellers_contact_person'] = "<acronym title=\"Контактное лицо продавца\">Контактное лицо</acronym>";
$GLOBALS['MSG_shops']['form_sellers_address'] = "<acronym title=\"Адрес продавца\">Адрес</acronym>";
$GLOBALS['MSG_shops']['form_sellers_phone'] = "<acronym title=\"Телефон продавца\">Телефон</acronym>";
$GLOBALS['MSG_shops']['form_sellers_fax'] = "<acronym title=\"Факс продавца\">Факс</acronym>";
$GLOBALS['MSG_shops']['form_sellers_http'] = "<acronym title=\"Сайт продавца\">Сайт</acronym>";
$GLOBALS['MSG_shops']['form_sellers_email'] = "<acronym title=\"E-Mail продавца\">E-Mail</acronym>";
$GLOBALS['MSG_shops']['form_sellers_inn'] = "<acronym title=\"ИНН продавца\">ИНН</acronym>";
$GLOBALS['MSG_shops']['add_sellers_success'] = "Данные о продавце успешно добавлены!";
$GLOBALS['MSG_shops']['edit_sellers_success'] = "Данные о продавце успешно изменены!";
$GLOBALS['MSG_shops']['add_sellers_error'] = "Ошибка добавления информации о продавце!";
$GLOBALS['MSG_shops']['edit_sellers_error'] = "Ошибка обновления информации о продавце!";
/* Сообщения для файла изображения */
$GLOBALS['MSG_shops']['comment_delete_success'] = 'Информация о комментарии успешно удалена!';
$GLOBALS['MSG_shops']['comments_delete_success'] = 'Информация о комментариях успешно удалена!';
/* Для атрибутов сортировки */
$GLOBALS['MSG_shops']['sort_by_date'] = 'Дата';
$GLOBALS['MSG_shops']['sort_by_name'] = 'Название';
$GLOBALS['MSG_shops']['sort_by_order'] = 'Порядок сортировки';
$GLOBALS['MSG_shops']['sort_field_title'] = '<acronym title="Поле, по которому будут сортироваться товары магазина">Поле сортировки товара</acronym>';
$GLOBALS['MSG_shops']['sort_order_type'] = '<acronym title="Направление сортировки товаров магазина">Направление сортировки товара</acronym>';
$GLOBALS['MSG_shops']['sort_field_group_title'] = '<acronym title="Поле, по которому будут сортироваться группы товаров магазина">Поле сортировки групп</acronym>';
$GLOBALS['MSG_shops']['sort_order_group_type'] = '<acronym title="Направление сортировки групп товаров магазина">Направление сортировки групп</acronym>';
$GLOBALS['MSG_shops']['sort_to_increase'] = 'По-возрастанию';
$GLOBALS['MSG_shops']['sort_to_decrease'] = 'По-убыванию';
$GLOBALS['MSG_shops']['shops_form_company'] = '<acronym title="Название компании, которой принадлежит магазин">Компания</acronym>';
/* Свойства товара, доступные для группы. */
$GLOBALS['MSG_shops']['properties_item_for_groups_link'] = 'Свойства товаров для группы';

$GLOBALS['MSG_shops']['change_prices_for_shop_group'] = 'Изменение цен';

$GLOBALS['MSG_shops']['multiply_price_to_digit'] = 'Цену умножить на ';

$GLOBALS['MSG_shops']['add_price_to_digit'] = 'Цену увеличить на ';

$GLOBALS['MSG_shops']['accepted_prices'] = 'Обновление информации о ценах для товаров прошло успешно!';

$GLOBALS['MSG_shops']['select_price_form'] = '<acronym title="Укажите вариант изменения">Вариант изменения: </acronym>';

$GLOBALS['MSG_shops']['select_discount_type'] = '<acronym title="Выберите скидку, для установки к выбранной группе товаров">Установить скидку</acronym>';

$GLOBALS['MSG_shops']['select_parent_group'] = '<acronym title="Выберите группу товаров, начиная с которой следует произвести изменение цен">Родительская группа</acronym>';

$GLOBALS['MSG_shops']['select_parent_group_name'] = '--- Корневая ---';

$GLOBALS['MSG_shops']['properties_item_for_groups_root_title'] = 'Свойства товара, доступные для текущей группы товаров';
/* Тип отображения свойства в фильтре */
$GLOBALS['MSG_shops']['properties_show_kind_none'] = 'Не отображать';
$GLOBALS['MSG_shops']['properties_show_kind_text'] = 'Поле ввода';
$GLOBALS['MSG_shops']['properties_show_kind_list'] = 'Список - списком';
$GLOBALS['MSG_shops']['properties_show_kind_radio'] = 'Список - переключателями';
$GLOBALS['MSG_shops']['properties_show_kind_checkbox'] = 'Список - флажками';
$GLOBALS['MSG_shops']['properties_show_kind_checkbox_one'] = 'Флажок';
$GLOBALS['MSG_shops']['properties_show_kind_from_to'] = 'От.. до..';
$GLOBALS['MSG_shops']['properties_show_kind_listbox'] = 'Список - список с множественным выбором';

/* Комментарий к товару */

$GLOBALS['MSG_shops']['comment_show_add_link'] = 'Добавить';
$GLOBALS['MSG_shops']['comment_form_title_add'] = 'Добавление отзыва о товаре';
$GLOBALS['MSG_shops']['comment_form_title_edit'] = 'Редактирование отзыва о товаре';
$GLOBALS['MSG_shops']['comment_form_id'] = '<acronym title="Идентификатор товара">Идентификатор товара</acronym>';
$GLOBALS['MSG_shops']['comment_form_date_time'] = '<acronym title="Дата/время добавления отзыва">Дата/время</acronym>';
$GLOBALS['MSG_shops']['comment_form_user_name'] = '<acronym title="Автор отзыва о товаре">ФИО автора</acronym>';
$GLOBALS['MSG_shops']['comment_form_subject'] = '<acronym title="Тема отзыва о товаре">Тема</acronym>';
$GLOBALS['MSG_shops']['comment_form_text'] = '<acronym title="Текст отзыва о товаре">Текст</acronym>';
$GLOBALS['MSG_shops']['comment_form_ip'] = '<acronym title="IP-адрес компьютера автора комментария, например XXX.XXX.XXX.XXX, где XXX - число от 0 до 255">IP-адрес</acronym>';

$GLOBALS['MSG_shops']['comment_form_site_users_id'] = '<acronym title="Идентификатор пользователя, оставившего отзыв о товаре">Код пользователя</acronym>';

$GLOBALS['MSG_shops']['comment_form_grade'] = '<acronym title="Оценка товара">Оценка</acronym>';
$GLOBALS['MSG_shops']['comment_form_active'] = 'Показывать комментарий';
$GLOBALS['MSG_shops']['comment_form_add'] = 'Применить';
$GLOBALS['MSG_shops']['comment_insert_success'] = 'Данные об отзыве успешно добавлены.';
$GLOBALS['MSG_shops']['comment_edit_success'] = 'Данные об отзыве успешно обновлены.';
$GLOBALS['MSG_shops']['comment_insert_error'] = 'Ошибка добавления данных об отзыве!';
$GLOBALS['MSG_shops']['comment_edit_error'] = 'Ошибка обновления данных об отзыве!';

/* Сведения о компании */
$GLOBALS['MSG_shops']['company_show_link2'] = 'Компании';
$GLOBALS['MSG_shops']['company_show_add_new_link'] = 'Добавить';
$GLOBALS['MSG_shops']['company_show_title'] = 'Компании';
$GLOBALS['MSG_shops']['company_show_title2'] = 'Список компаний';
$GLOBALS['MSG_shops']['company_form_add_title'] = 'Добавление сведений о компании';
$GLOBALS['MSG_shops']['company_form_edit_title'] = 'Редактирование сведений о компании';

$GLOBALS['MSG_shops']['company_form_name'] = '<acronym title="Название компании">Название</acronym>';
$GLOBALS['MSG_shops']['company_form_description'] = '<acronym title="Описание компании">Описание</acronym>';

$GLOBALS['MSG_shops']['company_form_inn'] = '<acronym title="ИНН компании">ИНН</acronym>';
$GLOBALS['MSG_shops']['company_form_kpp'] = '<acronym title="КПП компании">КПП</acronym>';
$GLOBALS['MSG_shops']['company_form_ogrn'] = '<acronym title="ОГРН компании">ОГРН</acronym>';
$GLOBALS['MSG_shops']['company_form_okpo'] = '<acronym title="ОКПО компании">ОКПО</acronym>';
$GLOBALS['MSG_shops']['company_form_okved'] = '<acronym title="ОКВЭД компании">ОКВЭД</acronym>';
$GLOBALS['MSG_shops']['company_form_bik'] = '<acronym title="БИК компании">БИК</acronym>';

$GLOBALS['MSG_shops']['producer_form_inn'] = '<acronym title="ИНН производителя">ИНН</acronym>';
$GLOBALS['MSG_shops']['producer_form_kpp'] = '<acronym title="КПП производителя">КПП</acronym>';
$GLOBALS['MSG_shops']['producer_form_ogrn'] = '<acronym title="ОГРН производителя">ОГРН</acronym>';
$GLOBALS['MSG_shops']['producer_form_okpo'] = '<acronym title="ОКПО производителя">ОКПО</acronym>';
$GLOBALS['MSG_shops']['producer_form_okved'] = '<acronym title="ОКВЭД производителя">ОКВЭД</acronym>';
$GLOBALS['MSG_shops']['producer_form_bik'] = '<acronym title="БИК производителя">БИК</acronym>';

$GLOBALS['MSG_shops']['company_form_account'] = '<acronym title="Номер счета компании">Номер счета</acronym>';
$GLOBALS['MSG_shops']['company_form_corr_account'] = '<acronym title="Номер корр. счета компании">Номер корр. счета</acronym>';
$GLOBALS['MSG_shops']['company_form_bank_name'] = '<acronym title="Название банка">Название банка</acronym>';
$GLOBALS['MSG_shops']['company_form_bank_address'] = '<acronym title="Адрес банка">Адрес банка</acronym>';


$GLOBALS['MSG_shops']['producer_form_account'] = '<acronym title="Номер счета производителя">Номер счета</acronym>';
$GLOBALS['MSG_shops']['producer_form_corr_account'] = '<acronym title="Номер корр. счета производителя">Номер корр. счета</acronym>';
$GLOBALS['MSG_shops']['producer_form_bank_name'] = '<acronym title="Название банка">Название банка</acronym>';
$GLOBALS['MSG_shops']['producer_form_bank_address'] = '<acronym title="Адрес банка">Адрес банка</acronym>';


$GLOBALS['MSG_shops']['company_form_fio'] = '<acronym title="ФИО директора компании">ФИО директора</acronym>';
$GLOBALS['MSG_shops']['company_form_accountant_fio'] = '<acronym title="ФИО главного бухгалтера компании">ФИО главного бухгалтера</acronym>';

$GLOBALS['MSG_shops']['company_form_address'] = '<acronym title="Адрес компании">Адрес</acronym>';
$GLOBALS['MSG_shops']['company_form_phone'] = '<acronym title="Телефон компании">Телефон</acronym>';
$GLOBALS['MSG_shops']['company_form_fax'] = '<acronym title="Факс компании">Факс</acronym>';
$GLOBALS['MSG_shops']['company_form_site'] = '<acronym title="Сайт компании">Сайт</acronym>';
$GLOBALS['MSG_shops']['parent_site_info'] = '<acronym title="Родительский магазин элемента">Магазин</acronym>';
$GLOBALS['MSG_shops']['company_form_email'] = '<acronym title="E-Mail компании">E-Mail</acronym>';

$GLOBALS['MSG_shops']['producer_form_address'] = '<acronym title="Адрес производителя">Адрес</acronym>';
$GLOBALS['MSG_shops']['producer_form_phone'] = '<acronym title="Телефон производителя">Телефон</acronym>';
$GLOBALS['MSG_shops']['producer_form_fax'] = '<acronym title="Факс производителя">Факс</acronym>';
$GLOBALS['MSG_shops']['producer_form_site'] = '<acronym title="Сайт производителя">Сайт</acronym>';
$GLOBALS['MSG_shops']['producer_form_email'] = '<acronym title="E-Mail производителя">E-Mail</acronym>';

$GLOBALS['MSG_shops']['company_insert_success'] = 'Данные о компании успешно добавлены.';
$GLOBALS['MSG_shops']['company_edit_success'] = 'Данные о компании успешно обновлены.';
$GLOBALS['MSG_shops']['company_insert_error'] = 'Ошибка добавления данных о компании!';
$GLOBALS['MSG_shops']['company_edit_error'] = 'Ошибка обновления данных о компании!';
/* Watermark для shop'a */
$GLOBALS['MSG_shops']['shop_form_watermark_file'] = '<acronym title="Файл изображения, используемого в качестве watermark">Изображение для watermark</acronym>';
$GLOBALS['MSG_shops']['shop_form_watermark_default_use_big'] = '<acronym title="Свойство, определяющее будет ли использоваться watermark по умолчанию">Использовать watermark по умолчанию</acronym>';
$GLOBALS['MSG_shops']['shop_form_watermark_default_use_small'] = '<acronym title="Свойство, определяющее будет ли использоваться watermark по умолчанию для малых изображений">Использовать watermark по умолчанию для малых изображений</acronym>';
/* Watermark для товара */
$GLOBALS['MSG_shops']['shop_groups_add_form_small_image'] = "<acronym title=\"Малое изображение для группы товаров\">Малое изображение</acronym>";
$GLOBALS['MSG_shops']['items_add_form_image_watermark_apply_error'] = 'Ошибка при наложении водяного знака!';
/* Скидки для заказов */
$GLOBALS['MSG_shops']['order_discount_show_title'] = 'Скидки от суммы заказа';

$GLOBALS['MSG_shops']['order_discount_form_shop_link'] = 'Скидки от суммы заказа';
$GLOBALS['MSG_shops']['add_order_discount_form_title'] = 'Добавление скидки от суммы заказа';
$GLOBALS['MSG_shops']['edit_order_discount_form_title'] = 'Редактирование скидки от суммы заказа';
$GLOBALS['MSG_shops']['order_discount_form_name'] = '<acronym title="Название скидки от общей суммы заказа">Название</acronym>';
$GLOBALS['MSG_shops']['order_discount_form_active'] = '<acronym title="Активность скидки от общей суммы заказа">Активность</acronym>';
$GLOBALS['MSG_shops']['order_discount_form_type'] = '<acronym title="Тип скидки - процент или сумма в выбранной валюте">Тип скидки</acronym>';
$GLOBALS['MSG_shops']['order_discount_form_value'] = '<acronym title="Величина скидки, может измеряться в процентах или в фиксированном размере">Величина скидки</acronym>';

$GLOBALS['MSG_shops']['order_discount_form_date_from_to_from'] = '<acronym title="Время начала активности скидки">Скидка активна с</acronym>';
$GLOBALS['MSG_shops']['order_discount_form_date_from_to_to'] = '<acronym title="Время окончания активности скидки">Скидка активна до</acronym>';

$GLOBALS['MSG_shops']['order_discount_form_price_from'] = '<acronym title="Начальный интервал цены товара в корзине">Начальный интервал цены</acronym>';
$GLOBALS['MSG_shops']['order_discount_form_price_to'] = '<acronym title="Конечный интервал цены товара в корзине">Конечный интервал цены</acronym>';

$GLOBALS['MSG_shops']['order_discount_count_from'] = '<acronym title="Начальный интервал количества товара в корзине">Начальный интервал кол-ва</acronym>';
$GLOBALS['MSG_shops']['order_discount_count_to'] = '<acronym title="Конечный интервал количества товара в корзине">Конечный интервал кол-ва</acronym>';
$GLOBALS['MSG_shops']['order_discount_case_and'] = 'И';
$GLOBALS['MSG_shops']['order_discount_case_or'] = 'ИЛИ';

$GLOBALS['MSG_shops']['order_discount_form_is_coupon'] = '<acronym title="Применять скидку только при наличии купона">Применять только с купоном</acronym>';
$GLOBALS['MSG_shops']['order_discount_insert_success_add'] = 'Данные о скидке успешно добавлены.';
$GLOBALS['MSG_shops']['order_discount_insert_success_edit'] = 'Данные о скидке успешно обновлены.';
$GLOBALS['MSG_shops']['order_discount_insert_error_add'] = 'Ошибка добавления!';
$GLOBALS['MSG_shops']['order_discount_insert_error_edit'] = 'Ошибка обновления данных о скидке!';
/* Модификации товара */
$GLOBALS['MSG_shops']['item_modification_title'] = 'Модификации товара "%s"';
$GLOBALS['MSG_shops']['item_modification_add_item'] = 'Добавить';
/* Купоны */
$GLOBALS['MSG_shops']['coupon_show_add_new_link'] = 'Добавить';
$GLOBALS['MSG_shops']['coupon_group_link'] = 'Купоны на скидку';
$GLOBALS['MSG_shops']['coupon_form_table_title_add'] = 'Добавление купона';
$GLOBALS['MSG_shops']['coupon_form_table_title_edit'] = 'Редактирование купона';
$GLOBALS['MSG_shops']['coupon_form_discount'] = '<acronym title="Скидка, которую предоставляет купон">Скидка</acronym>';
$GLOBALS['MSG_shops']['coupon_form_name'] = '<acronym title="Название купона">Название</acronym>';
$GLOBALS['MSG_shops']['coupon_form_active'] = '<acronym title="Активность купона">Активность</acronym>';
$GLOBALS['MSG_shops']['coupon_form_count'] = '<acronym title="Количество купонов, -1 &mdash; не ограничено">Количество</acronym>';
$GLOBALS['MSG_shops']['coupon_form_text'] = '<acronym title="Код, который должен ввести покупатель для получения скидки по купону. Вы можете его заменить на текстово-числовое значение, например \'SKIDKA-2008\' или \'8 марта\'">Код купона</acronym>';
$GLOBALS['MSG_shops']['coupon_insert_success_add'] = 'Данные о купоне успешно добавлены.';
$GLOBALS['MSG_shops']['coupon_insert_success_edit'] = 'Данные о купоне успешно обновлены.';
$GLOBALS['MSG_shops']['coupon_insert_error_add'] = 'Ошибка добавления данных о купоне!';
$GLOBALS['MSG_shops']['coupon_insert_error_edit'] = 'Ошибка обновления данных о купоне!';
$GLOBALS['MSG_shops']['coupon_insert_error_already_exists'] = 'Ошибка! Купон с таким текстом уже существует.';
$GLOBALS['MSG_shops']['coupon_delete_success'] = 'Данные о купоне успешно удалены!';
$GLOBALS['MSG_shops']['coupons_delete_success'] = 'Данные о купонах успешно удалены!';

$GLOBALS['MSG_shops']['shops_form_image_small_max_width'] = '<acronym title="Максимальная ширина малого изображения">Максимальная ширина малого изображения</acronym>';
$GLOBALS['MSG_shops']['shops_form_image_big_max_width'] = '<acronym title="Максимальная ширина большого изображения">Максимальная ширина большого изображения</acronym>';
$GLOBALS['MSG_shops']['shops_form_image_small_max_height'] = '<acronym title="Максимальная высота малого изображения">Максимальная высота малого изображения</acronym>';
$GLOBALS['MSG_shops']['shops_form_image_big_max_height'] = '<acronym title="Максимальная высота большого изображения">Максимальная высота большого изображения</acronym>';

$GLOBALS['MSG_shops']['shops_form_image_small_max_width_group'] = '<acronym title="Максимальная ширина малого изображения для группы">Максимальная ширина малого изображения для группы</acronym>';
$GLOBALS['MSG_shops']['shops_form_image_big_max_width_group'] = '<acronym title="Максимальная ширина большого изображения для группы">Максимальная ширина большого изображения для группы</acronym>';
$GLOBALS['MSG_shops']['shops_form_image_small_max_height_group'] = '<acronym title="Максимальная высота малого изображения для группы">Максимальная высота малого изображения для группы</acronym>';
$GLOBALS['MSG_shops']['shops_form_image_big_max_height_group'] = '<acronym title="Максимальная высота большого изображения для группы">Максимальная высота большого изображения для группы</acronym>';

$GLOBALS['MSG_shops']['tying_products_add_form_title'] = "Добавление сопутствующего товара";
$GLOBALS['MSG_shops']['tying_products_edit_form_title'] = "Редактирование сопутствующего товара";
$GLOBALS['MSG_shops']['import_price_action_items0'] = "Удалить существующие товары (во всех группах)";
$GLOBALS['MSG_shops']['import_price_action_items1'] = "Обновить информацию для существующих товаров";
$GLOBALS['MSG_shops']['import_price_action_items2'] = "Оставить без изменений";


$GLOBALS['MSG_shops']['success_edit_list_of_properties'] = "Дополнительное свойство успешно изменено";
$GLOBALS['MSG_shops']['success_add_list_of_properties'] = "Дополнительное свойство успешно добавлено";

$GLOBALS['MSG_shops']['error_edit_list_of_properties'] = "Ошибка обновления информации о дополнительном свойстве";
$GLOBALS['MSG_shops']['error_add_list_of_properties'] = "Ошибка добавления информации о дополнительном свойстве";


$GLOBALS['MSG_shops']['shop_shops_url_type'] = '<acronym title="Тип формирования URL">Тип формирования URL</acronym>';
$GLOBALS['MSG_shops']['shop_shops_apply_tags_automatic'] = '<acronym title="Автоматическое формирование тегов (меток) товара из его названия, описания и текста">Автоматически применять метки (теги)</acronym>';
$GLOBALS['MSG_shops']['shop_shops_writeoff_payed_items'] = '<acronym title="Уменьшать остаток товаров при оплате">Списывать товары при оплате</acronym>';
$GLOBALS['MSG_shops']['shop_shops_add_form_file_name_conversion'] = '<acronym title="Преобразование названий всех загружаемых файлов для всех объектов интернет-магазина - товаров, групп, дополнительных свойств товаров и групп">Изменять названия загружаемых файлов</acronym>';

$GLOBALS['MSG_shops']['shop_shops_url_type_element_0'] = 'Идентификатор';
$GLOBALS['MSG_shops']['shop_shops_url_type_element_1'] = 'Транслитерация';


$GLOBALS['MSG_shops']['shop_typograph_item_by_default'] = '<acronym title="Параметр, позволяющий задать типографирование товарам по умолчанию">Типографировать товары</acronym>';
$GLOBALS['MSG_shops']['shop_typograph_group_by_default'] = '<acronym title="Параметр, позволяющий задать типографирование группам товаров по умолчанию">Типографировать группы товаров</acronym>';

$GLOBALS['MSG_shops']['menu_main_group_for_external_properties'] = 'Раздел';
$GLOBALS['MSG_shops']['menu_add_group_for_external_properties'] = 'Добавить';
$GLOBALS['MSG_shops']['form_edit_add_shop_properties_items_dir_name'] = '<acronym title="Название раздела">Название</acronym>';
$GLOBALS['MSG_shops']['form_edit_add_shop_properties_items_dir_parent_id'] = '<acronym title="Родительский раздел создаваемого раздела">Родительский раздел</acronym>';
$GLOBALS['MSG_shops']['form_edit_add_shop_properties_items_dir_parent_id_elements'] = '<acronym title="Родительский раздел свойства">Раздел дополнительных свойств</acronym>';
$GLOBALS['MSG_shops']['form_edit_add_shop_properties_items_dir_description'] = '<acronym title="Описание раздела">Описание</acronym>';
$GLOBALS['MSG_shops']['form_edit_add_shop_properties_items_dir_order'] = '<acronym title="Порядок сортировки">Порядок сортировки</acronym>';

$GLOBALS['MSG_shops']['form_edit_add_title_add'] = 'Добавление раздела дополнительных свойств товара';
$GLOBALS['MSG_shops']['form_edit_add_title_edit'] = 'Редактирование раздела дополнительных свойств товара';

$GLOBALS['MSG_shops']['add_success'] = 'Информация о разделе успешно добавлена';
$GLOBALS['MSG_shops']['edit_success'] = 'Информация о разделе успешно изменена';
$GLOBALS['MSG_shops']['error_add_success'] = 'Ошибка добавления информации о разделе';
$GLOBALS['MSG_shops']['error_edit_success'] = 'Ошибка редактирования информации о разделе';

$GLOBALS['MSG_shops']['menu2_caption'] = 'Раздел';
$GLOBALS['MSG_shops']['submenu2_caption'] = 'Добавить';

$GLOBALS['MSG_shops']['edit_group_dir'] = 'Редактирование информации о группе дополнительных свойств';
$GLOBALS['MSG_shops']['add_group_dir'] = 'Добавление информации о группе дополнительных свойств';

$GLOBALS['MSG_shops']['shop_properties_groups_dir_parent_id'] = '<acronym title="Родительская директория">Родительская директория</acronym>';
$GLOBALS['MSG_shops']['shop_properties_groups_dir_name'] = '<acronym title="Название директории">Название</acronym>';
$GLOBALS['MSG_shops']['shop_properties_groups_dir_description'] = '<acronym title="Описание директории">Описание</acronym>';
$GLOBALS['MSG_shops']['shop_properties_groups_dir_order'] = '<acronym title="Порядок сортировки директории">Сортировка</acronym>';
$GLOBALS['MSG_shops']['seller_site_user'] = '<acronym title="Идентификатор пользователя сайта">Идентификатор пользователя сайта</acronym>';

$GLOBALS['MSG_shops']['success_delete_dir_dirs'] = 'Информация о группе дополнительных свойств успешно удалена';
$GLOBALS['MSG_shops']['success_delete_dirs_dirs'] = 'Информация о группах дополнительных свойств успешно удалена';

$GLOBALS['MSG_shops']['affiliate_menu_title'] = 'Партнерские программы';
$GLOBALS['MSG_shops']['affiliate_form_title'] = 'Партнерские программы';
$GLOBALS['MSG_shops']['affiliate_add_menu_title'] = 'Партнерская программа';
$GLOBALS['MSG_shops']['affiliate_add_submenu1_title'] = 'Добавить';
$GLOBALS['MSG_shops']['affiliate_values_form_title'] = 'Уровни партнерской программы "%s"';
$GLOBALS['MSG_shops']['affiliate_values_menu_title'] = 'Уровень партнерской программы';
$GLOBALS['MSG_shops']['affiliate_values_submenu1_title'] = 'Добавить';
$GLOBALS['MSG_shops']['affiliate_form_add'] = 'Добавление партнерской программы';
$GLOBALS['MSG_shops']['affiliate_form_edit'] = 'Редактирование партнерской программы';

$GLOBALS['MSG_shops']['add_success_message'] = 'Информация о партнерской программе успешно добавлена';
$GLOBALS['MSG_shops']['edit_success_message'] = 'Информация о партнерской программе успешно изменена';
$GLOBALS['MSG_shops']['add_error_message'] = 'Ошибка добавления информации о партнерской программе';
$GLOBALS['MSG_shops']['edit_error_message'] = 'Ошибка изменения информации о партнерской программе';
$GLOBALS['MSG_shops']['delete_error_message'] = 'Ошибка удаления информации о партнерской программе';
$GLOBALS['MSG_shops']['delete_success_message'] = 'Информация о партнерской программе успешно удалена';
$GLOBALS['MSG_shops']['delete_success_messages_affiliate_value'] = 'Информация об уровне партнерской программы успешно удалена';
$GLOBALS['MSG_shops']['delete_success_messages_affiliate_values'] = 'Информация об уровнях партнерской программы успешно удалена';
$GLOBALS['MSG_shops']['delete_error_messages_affiliate_value'] = 'Ошибка удаления информации об уровне партнерской программы';
$GLOBALS['MSG_shops']['delete_success_messages'] = 'Информация о партнерских программах успешно удалена';

$GLOBALS['MSG_shops']['form_edit_add_site_id'] = '<acronym title="Сайт, на котором будет доступна партнерская программа">Сайт</acronym>';
$GLOBALS['MSG_shops']['form_edit_add_affiliate_plans_name'] = '<acronym title="Название партнерской программы">Название</acronym>';
$GLOBALS['MSG_shops']['form_edit_add_affiliate_plans_description'] = '<acronym title="Описание партнерской программы">Описание</acronym>';
$GLOBALS['MSG_shops']['form_edit_add_affiliate_plans_activity'] = '<acronym title="Флажок активности партнерской программы">Активность</acronym>';
$GLOBALS['MSG_shops']['form_edit_add_site_users_group_id'] = '<acronym title="Группа пользователей сайта, которой будет доступна партнерская программа">Группа пользователей сайта</acronym>';
$GLOBALS['MSG_shops']['form_edit_add_affiliate_plans_last_change_datetime'] = '<acronym title="Дата последнего изменения партнерской программы">Дата последнего изменения</acronym>';
$GLOBALS['MSG_shops']['form_edit_add_affiliate_plans_min_num_of_items'] = '<acronym title="Минимальное число товаров, которое пользователь должен купить за один раз, чтобы получить начисления по партнерской программе">Минимальное число товаров в заказе</acronym>';
$GLOBALS['MSG_shops']['form_edit_add_affiliate_plans_min_sum_of_items'] = '<acronym title="Минимальная сумма в валюте магазина, на которую пользователь сайта должен приобрести товаров за один раз, чтобы получить начисления по партнерской программе">Минимальная сумма в валюте магазина</acronym>';
$GLOBALS['MSG_shops']['form_edit_add_affiliate_plans_delivery_on'] = '<acronym title="Учитывать ли доставку товара, при просчете комиссии аффилиату">Доставка товара</acronym>';

$GLOBALS['MSG_shops']['error_message_attention'] = 'Для некоторых товаров цены не изменены! Идентификаторы товаров, цены которых поменять не удалось: %s. Возможно у Вас не хватает прав доступа.';

$GLOBALS['MSG_shops']['edit_affiliate_value'] = 'Редактирование уровня';
$GLOBALS['MSG_shops']['add_affiliate_value'] = 'Добавление уровня';

$GLOBALS['MSG_shops']['form_edit_add_affiliate_plans_id'] = '<acronym title="Партнерская программа, которой принадлежит уровень">Партнерская программа</acronym>';
$GLOBALS['MSG_shops']['form_edit_add_affiliate_values_inner_level'] = '<acronym title="Уровень партнера в пирамиде">Уровень</acronym>';
$GLOBALS['MSG_shops']['form_edit_add_affiliate_values_percent'] = '<acronym title="Процент, начисляемый партнеру при просчете комиссии на текущем уровне">Процент</acronym>';
$GLOBALS['MSG_shops']['form_edit_add_affiliate_values_value'] = '<acronym title="Фиксированная сумма, начисляемая партнеру при просчете комиссии на текущем уровне">Сумма</acronym>';
$GLOBALS['MSG_shops']['form_edit_affiliate_values_type'] = '<acronym title="Тип комиссии">Тип комиссии</acronym>';

$GLOBALS['MSG_shops']['form_edit_affiliate_values_type_percent'] = 'Процент';
$GLOBALS['MSG_shops']['form_edit_affiliate_values_type_summ'] = 'Сумма';

$GLOBALS['MSG_shops']['add_success_affiliate_values'] = 'Информация об уровне успешно добавлена';
$GLOBALS['MSG_shops']['edit_success_affiliate_values'] = 'Информация об уровне успешно изменена';
$GLOBALS['MSG_shops']['add_error_affiliate_values'] = 'Ошибка добавления информации об уровне';
$GLOBALS['MSG_shops']['edit_error_affiliate_values'] = 'Ошибка изменения информации об уровне';

$GLOBALS['MSG_shops']['affiliate_assotiation_form_title'] = 'Партнерские программы, доступные магазину "%s"';
$GLOBALS['MSG_shops']['special_prices_tab'] = 'Специальные цены';

$GLOBALS['MSG_shops']['form_edit_add_shop_special_prices_from'] = '<acronym title="Минимальное количество товара, которое нужно купить за один раз, чтобы задействовать цену">Количество товара от</acronym>';
$GLOBALS['MSG_shops']['form_edit_add_shop_special_prices_to'] = '<acronym title="Максимальное количество товара, которое нужно купить за один раз, чтобы задействовать цену">Количество товара до</acronym>';
$GLOBALS['MSG_shops']['form_edit_add_shop_special_pricess_price'] = '<acronym title="Цена за единицу товара, купленного в определенном количестве">Цена</acronym>';
$GLOBALS['MSG_shops']['form_edit_add_shop_special_pricess_percent'] = '<acronym title="Процент от базовой цены. Например для скидки 15% процент от базовой цены будет 85">% от цены</acronym>';
$GLOBALS['MSG_shops']['form_edit_add_shop_special_pricess_delete'] = 'Удалить специальную цену';

$GLOBALS['MSG_shops']['form_edit_add_shop_special_prices_price'] = 'Партнерское вознаграждение по заказу %s';
$GLOBALS['MSG_shops']['copy_shops'] = 'Информация о магазинах успешно скопирована';
$GLOBALS['MSG_shops']['copy_shop'] = 'Информация о магазине успешно скопирована';
$GLOBALS['MSG_shops']['shop_items_catalog_show_count'] = '<acronym title="Количество показов товара">Счетчик показов</acronym>';
$GLOBALS['MSG_shops']['site_users_id'] = '<acronym title="Идентификатор пользователя сайта, добавившего товар">Код пользователя</acronym>';

$GLOBALS['MSG_shops']['special_prices_add_fields'] = "Еще";
$GLOBALS['MSG_shops']['eitem_import_plain_text'] = "Текст электронного товара";
$GLOBALS['MSG_shops']['eitem_import_file'] = "Файл электронного товара";
$GLOBALS['MSG_shops']['eitem_import_count'] = "Количество электронного товара";

$GLOBALS['MSG_shops']['prices_crossed'] = "Произошло пересечение множеств, на которых доступны цены. %s";
$GLOBALS['MSG_shops']['error_affiliate_percent_over_range'] = "Процент, начисляемый партнеру превысил допустимый диапазон.";
$GLOBALS['MSG_shops']['error_affiliate_value_over_range'] = "Фиксированная сумма, начисляемая партнеру превысила допустимый диапазон.";
$GLOBALS['MSG_shops']['error_special_price_over_range'] = "Цена за единицу товара, купленного в определенном количестве превысила допустимый диапазон.";
$GLOBALS['MSG_shops']['error_special_price_incorrect_interval'] = "Некорректно задан интервал количества товаров. %s";
$GLOBALS['MSG_shops']['special_price_deleted'] = "Цена удалена.";
$GLOBALS['MSG_shops']['special_price_not_added'] = "Информация о цене не добавлена.";
$GLOBALS['MSG_shops']['special_price_cross_deleted'] = "Все пересечения удалены.";

$GLOBALS['MSG_shops']['subject_report_for_comment'] = 'Добавление комментария о товаре';

$GLOBALS['MSG_shops']['shop_shops_apply_keywords_automatic'] = '<acronym title="Автоматическое формирование ключевых слов товара и категории из их названия, описания и текста">Автоматически генерировать ключевые слова</acronym>';

$GLOBALS['MSG_shops']['shop_shops_attach_file_eitem'] = '<acronym title="Отправлять файл электроного товара в письме с информацией об оформленном заказе пользователю как вложение. Если не выбран - будет генерироваться ссылка для скачивания файла">Отправлять файл электронного товара в письме пользователю</acronym>';

$GLOBALS['MSG_shops']['payed_item'] = "Произошла оплата закончившегося электронного товара с идентификатором %d. Код заказа %d.";

$GLOBALS['MSG_shops']['copy_one_item_success'] = "Товар успешно скопирован!";
$GLOBALS['MSG_shops']['copy_many_items_success'] = "Товары успешно скопированы!";
$GLOBALS['MSG_shops']['copy_item_error'] = "Ошибка копирования товара.";

$GLOBALS['MSG_shops']['create_modification'] = "Создать модификации";
$GLOBALS['MSG_shops']['create_modification_title'] = "Создание модификаций из товара";
$GLOBALS['MSG_shops']['create_modification_property_enable'] = "Использовать свойство \"%s\" {P%s}";
$GLOBALS['MSG_shops']['create_modification_property_name_template'] = "%s {P%s}";
$GLOBALS['MSG_shops']['create_modification_price'] = "<acronym title=\"Цена модификаций\">Цена</acronym>";
$GLOBALS['MSG_shops']['create_modification_rest'] = "<acronym title=\"Количество модификаций\">Количество</acronym>";
$GLOBALS['MSG_shops']['create_modification_mark'] = "<acronym title=\"Шаблон для генерации артикулов модификаций\">Артикул</acronym>";
$GLOBALS['MSG_shops']['create_modification_name'] = "<acronym title=\"Шаблон для генерации названий модификаций\">Название</acronym>";
$GLOBALS['MSG_shops']['create_modification_button'] = "Создать модификации";
$GLOBALS['MSG_shops']['create_modification_mark_template'] = "%s-{N}";
$GLOBALS['MSG_shops']['create_modification_copy_main_properties'] = "<acronym title=\"Копировать основные изображения, текст, описание товара\">Копировать основные атрибуты товара</acronym>";
$GLOBALS['MSG_shops']['create_modification_copy_specials_prices_to_item'] = "<acronym title=\"Копировать специальные цены товара\">Копировать специальные цены товара</acronym>";
$GLOBALS['MSG_shops']['create_modification_copy_prices_to_item'] = "<acronym title=\"Копировать дополнительные цены товара\">Копировать дополнительные цены товара</acronym>";
$GLOBALS['MSG_shops']['create_modification_copy_tying_products'] = "<acronym title=\"Копировать сопутствующие товары\">Копировать сопутствующие товары</acronym>";
$GLOBALS['MSG_shops']['create_modification_copy_eitems'] = "<acronym title=\"Копировать электронные товары\">Копировать электронные товары</acronym>";
$GLOBALS['MSG_shops']['create_modification_copy_seo'] = "<acronym title=\"Копировать значения SEO-полей\">Копировать значения SEO-полей</acronym>";
$GLOBALS['MSG_shops']['create_modification_copy_export_import'] = "<acronym title=\"Копировать параметры экспорта/импорта товара\">Копировать параметры экспорта/импорта товара</acronym>";
$GLOBALS['MSG_shops']['create_modification_copy_external_property'] = "<acronym title=\"Копировать значения остальных дополнительных свойств товара\">Копировать дополнительные свойства товара</acronym>";
$GLOBALS['MSG_shops']['create_modification_copy_tags'] = "<acronym title=\"Копировать метки (теги) товара\">Копировать метки (теги) товара</acronym>";
$GLOBALS['MSG_shops']['create_modification_name_template_info'] = "Возможна подстановка к названию значений свойств. Например, \"%s, цвет {P17}\" даст результат \"%s, цвет Синий\"";
$GLOBALS['MSG_shops']['create_modification_mark_template_info'] = "{N} &mdash; порядковый номер";
$GLOBALS['MSG_shops']['create_modification_success'] = "Модификации успешно добавлены!";
$GLOBALS['MSG_shops']['create_modification_error'] = "Для автоматического создания модификаций необходимо хотя бы одно дополнительное свойство товаров типа \"Список\".";

$GLOBALS['MSG_shops']['sales_report_title'] = "Отчет о продажах магазина %s за период с&nbsp;%s&nbsp;по&nbsp;%s%s";
$GLOBALS['MSG_shops']['sales_report_title_saller'] = ", продавец %s";

$GLOBALS['MSG_shops']['items_catalog_putend_date_not_defined'] = 'Не определена';

$GLOBALS['MSG_shops']['error_group_URL'] = 'Ошибка! В группе уже существует  подгруппа с таким URL';
$GLOBALS['MSG_shops']['error_item_group_URL'] = 'Ошибка! В группе уже существует товар с таким URL';

$GLOBALS['MSG_shops']['shop_items_catalog_type'] = 'Тип товара';

$GLOBALS['MSG_shops']['import_property_group_root'] = 'Корневая группа';

$GLOBALS['MSG_shops']['add_shop_item_shortcut_title'] = "Ярлык для %s";
$GLOBALS['MSG_shops']['add_item_shortcut_shop_groups_id'] = "<acronym title=\"Группа, в которой размещается ярлык товара\">Родительская группа</acronym>";
$GLOBALS['MSG_shops']['add_shop_item_shortcut_success'] = "Ярлык товара успешно добавлен";
$GLOBALS['MSG_shops']['edit_shop_item_shortcut_error'] = "Ошибка! Ярлык товара не добавлен!";

$GLOBALS['MSG_shops']['flag_delete_discount'] = '<acronym title="При установке данного флажка будет производиться удаление скидки для товара, если она была установлена">Удалить выбранную скидку</acronym>';

$GLOBALS['MSG_shops']['move_items_groups_title'] = 'Перенос групп и товаров';
$GLOBALS['MSG_shops']['move_items_groups_shop_groups_id'] = "<acronym title=\"Группа, в которую будут перенесены товары и группы\">Родительская группа</acronym>";
$GLOBALS['MSG_shops']['move_items_groups_form_button_name'] = 'Перенести';
$GLOBALS['MSG_shops']['move_shop_groups_success'] = 'Группы товаров перенесены';
$GLOBALS['MSG_shops']['move_shop_items_success'] = 'Товары перенесены';

$GLOBALS['MSG_shops']['groups_add_form_seo_title_import_field'] = "Заголовок раздела(title)";
$GLOBALS['MSG_shops']['groups_add_form_seo_description_import_field'] = "Описание раздела(description)";
$GLOBALS['MSG_shops']['groups_add_form_seo_keywords_import_field'] = "Ключевые слова раздела(keywords)";

$GLOBALS['MSG_shops']['recalc_order_delivery_sum'] = "Пересчитать стоимость доставки";
$GLOBALS['MSG_shops']['order_card'] = "Карточка заказа";
$GLOBALS['MSG_shops']['order_items_link'] = "Товары";
$GLOBALS['MSG_shops']['order_card_date_from'] = " от ";

$GLOBALS['MSG_shops']['company_phone'] = "Телефон";

$GLOBALS['MSG_shops']['order_card_supplier'] = "Поставщик";
$GLOBALS['MSG_shops']['order_card_inn_kpp'] = "ИНН/КПП";
$GLOBALS['MSG_shops']['order_card_ogrn'] = 'ОГРН';
$GLOBALS['MSG_shops']['order_card_address'] = "Адрес";
$GLOBALS['MSG_shops']['order_card_phone'] = 'Телефон';
$GLOBALS['MSG_shops']['order_card_fax'] = "Факс";
$GLOBALS['MSG_shops']['order_card_email'] = "E-Mail";
$GLOBALS['MSG_shops']['order_card_site'] = "Сайт";
$GLOBALS['MSG_shops']['order_card_contact_person'] = "Контактное лицо";
$GLOBALS['MSG_shops']['order_card_site_user'] = "Пользователь";
$GLOBALS['MSG_shops']['order_card_site_user_id'] = "код";
$GLOBALS['MSG_shops']['order_card_site_user_phone'] = "Телефон";
$GLOBALS['MSG_shops']['order_card_site_user_fax'] = "Факс";
$GLOBALS['MSG_shops']['order_card_site_user_email'] = "E-mail";

$GLOBALS['MSG_shops']['order_card_system_of_pay'] = "Платежная система";
$GLOBALS['MSG_shops']['order_card_status_of_pay'] = "Оплачен";
$GLOBALS['MSG_shops']['order_card_status_of_pay_yes'] = "Да";
$GLOBALS['MSG_shops']['order_card_status_of_pay_no'] = "Нет";
$GLOBALS['MSG_shops']['order_card_cancel'] = "Отменен";
$GLOBALS['MSG_shops']['order_card_cancel_yes'] = "Да";
$GLOBALS['MSG_shops']['order_card_cancel_no'] = "Нет";
$GLOBALS['MSG_shops']['order_card_order_status'] = "Статус заказа";
$GLOBALS['MSG_shops']['order_card_type_of_delivery'] = "Тип доставки";
$GLOBALS['MSG_shops']['order_card_description'] = "Описание заказа";
$GLOBALS['MSG_shops']['order_card_information'] = "Информация о заказе";

$GLOBALS['MSG_shops']['order'] = "Заказ";
$GLOBALS['MSG_shops']['payer'] = "Плательщик";
$GLOBALS['MSG_shops']['table_description'] = "Наименование";
$GLOBALS['MSG_shops']['table_mark'] = "Артикул";
$GLOBALS['MSG_shops']['table_amount'] = "Кол-во";
$GLOBALS['MSG_shops']['table_price'] = "Цена";
$GLOBALS['MSG_shops']['table_nds'] = "В том числе налог:";
$GLOBALS['MSG_shops']['table_nds_tax'] = "Ставка налога";
$GLOBALS['MSG_shops']['table_all_to_pay'] = "Всего к оплате:";
$GLOBALS['MSG_shops']['table_mesures'] = "Ед. изм.";
$GLOBALS['MSG_shops']['table_nds_value'] = "Налог";
$GLOBALS['MSG_shops']['table_nds_for_delivery'] = "Налог";
$GLOBALS['MSG_shops']['table_amount_value'] = "Сумма";

$GLOBALS['MSG_shops']['external_property_big_width'] = '<acronym title="Максимальная ширина большого изображения">Максимальная ширина большого изображения</acronym>';
$GLOBALS['MSG_shops']['external_property_big_height'] = '<acronym title="Максимальная высота большого изображения">Максимальная высота большого изображения</acronym>';
$GLOBALS['MSG_shops']['external_property_small_width'] = '<acronym title="Максимальная ширина малого изображения">Максимальная ширина малого изображения</acronym>';
$GLOBALS['MSG_shops']['external_property_small_height'] = '<acronym title="Максимальная высота малого изображения">Максимальная высота малого изображения</acronym>';

$GLOBALS['MSG_shops']['external_property_big_width_group'] = '<acronym title="Максимальная ширина большого изображения">Максимальная ширина большого изображения</acronym>';
$GLOBALS['MSG_shops']['external_property_big_height_group'] = '<acronym title="Максимальная высота большого изображения">Максимальная высота большого изображения</acronym>';
$GLOBALS['MSG_shops']['external_property_small_width_group'] = '<acronym title="Максимальная ширина малого изображения">Максимальная ширина малого изображения</acronym>';
$GLOBALS['MSG_shops']['external_property_small_height_group'] = '<acronym title="Максимальная высота малого изображения">Максимальная высота малого изображения</acronym>';

$GLOBALS['MSG_shops']['order_new_cond_of_delivery_accept'] = 'Условие доставки было изменено, проверьте пересчитанную стоимость доставки!';
$GLOBALS['MSG_shops']['order_new_cond_of_delivery_error'] = 'Текущее условие доставки удовлетворяет заданным параметрам';
$GLOBALS['MSG_shops']['order_new_cond_of_delivery_not_found'] = 'Для заданных параметров условие доставки не найдено';
$GLOBALS['MSG_shops']['order_is_delivery_flag'] = '<acronym title="Флаг, указывающий, что данный товар является доставкой">Доставка</acronym>';


$GLOBALS['MSG_shops']['shop_add_form_format_date'] = '<acronym title="Формат отображения даты, например %d.%m.%Y">Формат даты</acronym>';
$GLOBALS['MSG_shops']['shop_add_form_format_datetime'] = '<acronym title="Формат отображения даты/времени, например %d.%m.%Y %H:%M:%S">Формат даты/времени</acronym>';
$GLOBALS['MSG_shops']['shops_add_form_guid_name'] = "<acronym title=\"Уникальный идентификатор магазина в формате GUID, например 6F9619FF-8B86-D011-B42D-00CF4FC964FF\">Идентификатор GIUD</acronym>";
$GLOBALS['MSG_shops']['default_x_position'] = "<acronym title=\"Свойство, определяющее положение водяного знака по оси X (по умолчанию), например 200 (в пикселях) или 50% (в процентах)\">Позиция по оси X (по умолчанию)</acronym>";
$GLOBALS['MSG_shops']['default_y_position'] = "<acronym title=\"Свойство, определяющее положение водяного знака по оси Y (по умолчанию), например 200 (в пикселях) или 50% (в процентах)\">Позиция по оси Y (по умолчанию)</acronym>";
$GLOBALS['MSG_shops']['tax_add_form_1c_commerce_ml'] = '<acronym title="Идентификатор налога для формата CommerceML, например ID00006831">Идентификатор налога CommerceML</acronym>';
$GLOBALS['MSG_shops']['currency_add_form_international_name'] = "<acronym title=\"Название валюты в международном формате, например RUB\">Интернациональное название валюты</acronym>";
$GLOBALS['MSG_shops']['1c_commerce_ml_prop'] = '<acronym title="Идентификатор дополнительного свойства для формата CommerceML, например ID00006831">Идентификатор свойства CommerceML</acronym>';
$GLOBALS['MSG_shops']['1c_commerce_ml_group'] = '<acronym title="Идентификатор группы товаров для формата CommerceML, например ID00006831">Идентификатор группы товаров CommerceML</acronym>';
$GLOBALS['MSG_shops']['1c_commerce_ml_price'] = '<acronym title="Идентификатор цены для формата CommerceML, например ID00006831">Идентификатор цены CommerceML</acronym>';
$GLOBALS['MSG_shops']['items_catalog_add_form_path'] = "<acronym title=\"Путь, например item_30312\">Путь</acronym>";
$GLOBALS['MSG_shops']['1c_commerce_ml'] = '<acronym title="Идентификатор товара для формата CommerceML, например ID00029527">Идентификатор товара CommerceML</acronym>';
$GLOBALS['MSG_shops']['items_catalog_tags'] = "<acronym title=\"Метки (теги) товара, разделяются запятой, например кухня, бытовая техника, холодильник, Indesit\">Метки (теги)</acronym>";
$GLOBALS['MSG_shops']['import_price_list_images_path'] = "<acronym title=\"Путь для внешних файлов, например /upload_images/\">Путь для внешних файлов</acronym>";
$GLOBALS['MSG_shops']['producer_path'] = "<acronym title=\"URL путь, например some_path_12345\">Путь</acronym>";

$GLOBALS['MSG_shops']['edit_shop_dir_success'] = "Раздел интернет-магазинов успешно изменен!";
$GLOBALS['MSG_shops']['add_shop_dir_success'] = "Раздел интернет-магазинов успешно добавлен!";
$GLOBALS['MSG_shops']['error_add_edit_shop_dir'] = 'Ошибка добавления/изменения раздела интернет-магазинов';

$GLOBALS['MSG_shops']['order_item_type_caption0'] = 'Товар';
$GLOBALS['MSG_shops']['order_item_type_caption1'] = 'Доставка';
$GLOBALS['MSG_shops']['order_item_type_caption2'] = 'Пополнение лицевого счета';
$GLOBALS['MSG_shops']['order_item_type_option_caption'] = 'Тип товара';

$GLOBALS['MSG_shops']['currency_order'] = "<acronym title=\"Порядок сортировки\">Порядок сортировки</acronym>";

$GLOBALS['MSG_shops']['status_not_specify'] = "&mdash;";
$GLOBALS['MSG_shops']['shop_site_users_account_type'] = "Тип транзакции";
$GLOBALS['MSG_shops']['shop_site_users_account_type_typical'] = "Обычная";
$GLOBALS['MSG_shops']['shop_site_users_account_type_bonus'] = "Бонус";

$GLOBALS['MSG_shops']['shop_item_catalog_modification_flag'] = "Модификация для товара";
$GLOBALS['MSG_shops']['shop_type_of_delivery_order_field'] = "Порядок сортировки";
$GLOBALS['MSG_shops']['shop_group_order_import_field'] = "Порядок сортировки раздела";

$GLOBALS['MSG_shops']['shop_apply_modification_discount'] = "Применить к модификациям";

$GLOBALS['MSG_shops']['import_delivery_result'] = "Было проимпортировано %s элементов";
$GLOBALS['MSG_shops']['import_coupons_result'] = "Было проимпортировано %s элементов";

$GLOBALS['MSG_shops']['import_small_images'] = "Малое изображение для ";
$GLOBALS['MSG_shops']['import_big_group_images'] = "Большое изображение группы для ";
$GLOBALS['MSG_shops']['import_small_group_images'] = "Малое изображение группы для ";

$GLOBALS['MSG_shops']['import_additional_item_group'] = "Дополнительный раздел";

$GLOBALS['MSG_shops']['import_special_price_count_item_from'] = "Цена товара от";
$GLOBALS['MSG_shops']['import_special_price_count_item_to'] = "Цена товара до";
$GLOBALS['MSG_shops']['import_special_price_price_value'] = "Значение цены";
$GLOBALS['MSG_shops']['import_special_price_price_percent'] = "Процент от цены";
$GLOBALS['MSG_shops']['import_item_cml_id'] = "CML ID идентификатор товара";

$GLOBALS['MSG_shops']['import_field_group_activity'] = "Активность группы";

$GLOBALS['MSG_shops']['main_menu_warehouses_list'] = "Склады";
$GLOBALS['MSG_shops']['main_menu_warehouses_add_caption'] = "Склад";
$GLOBALS['MSG_shops']['main_menu_warehouses_add'] = "Добавить";
$GLOBALS['MSG_shops']['main_menu_warehouses_edit'] = "Редактирование склада";
$GLOBALS['MSG_shops']['main_menu_warehouses_edit_add'] = "Добавление склада";
$GLOBALS['MSG_shops']['form_edit_add_warehouse_name'] = "<acronym title=\"Текстовое название склада\">Наименование склада</acronym>";
$GLOBALS['MSG_shops']['form_edit_add_warehouse_activity'] = "<acronym title=\"Флаг, определяющий активен ли склад\">Активность склада</acronym>";
$GLOBALS['MSG_shops']['form_edit_add_warehouse_default'] = "<acronym title=\"Флаг, определяющий склад по умолчанию\">Склад \"по умолчанию\"</acronym>";
$GLOBALS['MSG_shops']['form_edit_add_warehouse_country'] = "<acronym title=\"Страна, в которой расположен склад\">Страна</acronym>";
$GLOBALS['MSG_shops']['form_edit_add_warehouse_location'] = "<acronym title=\"Область, в которой расположен склад\">Область</acronym>";
$GLOBALS['MSG_shops']['form_edit_add_warehouse_city'] = "<acronym title=\"Город, в котором расположен склад\">Город</acronym>";
$GLOBALS['MSG_shops']['form_edit_add_warehouse_city_area'] = "<acronym title=\"Район, в котором расположен склад\">Район</acronym>";
$GLOBALS['MSG_shops']['form_edit_add_warehouse_city_address'] = "<acronym title=\"Адрес склада\">Адрес</acronym>";
$GLOBALS['MSG_shops']['form_edit_add_warehouse_order'] = "<acronym title=\"Порядок сортировки склада\">Порядок сортировки</acronym>";
$GLOBALS['MSG_shops']['warehouse_edit_success'] = "Информация успешно сохранена";
$GLOBALS['MSG_shops']['warehouse_add_success'] = "Информация успешно добавлена";
$GLOBALS['MSG_shops']['warehouse_edit_error'] = "Ошибка сохранения данных";
$GLOBALS['MSG_shops']['warehouse_add_error'] = "Ошибка добавления информации";
$GLOBALS['MSG_shops']['warehouse_delete_success'] = "Информация успешно удалена";
$GLOBALS['MSG_shops']['warehouse_item_count'] = "Количество товара на складе \"%s\"";
$GLOBALS['MSG_shops']['warehouse_mesure_item'] = "Единица измерения";
$GLOBALS['MSG_shops']['warehouse_import_field'] = "Склад \"%s\"";
$GLOBALS['MSG_shops']['warehouse_default_not_exist'] = "Внимание! Склад \"по умолчанию\" отсутствует, добавьте склад \"по умолчанию\"!";
$GLOBALS['MSG_shops']['warehouse_default_not_set_activity'] = "Внимание! Склад \"по умолчанию\" нельзя делать неактивным!";
$GLOBALS['MSG_shops']['warehouse_from_which_the_product_was_written_off'] = "Склад, с которого был списан товар";

$GLOBALS['MSG_shops']['export_external_properties_allow_items'] = "Экспортировать дополнительные свойства товаров";
$GLOBALS['MSG_shops']['export_external_properties_allow_groups'] = "Экспортировать дополнительные свойства групп";
$GLOBALS['MSG_shops']['export_modifications_allow'] = "Экспортировать модификации";

$GLOBALS['MSG_shops']['warehouse_copy_from'] = "%s копия от %s";
$GLOBALS['MSG_shops']['search_event_indexation_import'] = "Использовать событийную индексацию при вставке групп товаров и товаров";
$GLOBALS['MSG_shops']['alternative_file_pointer_form_import'] = "<acronym title=\"Задайте относительный путь к файлу от директории системы, например, tmp/myfile.csv\">или укажите путь к файлу на сервере</acronym>";
$GLOBALS['MSG_shops']['shop_properties_group_cml'] = "<acronym title=\"Уникальный идентификатор элемента\">GUID</acronym>";

$GLOBALS['MSG_shops']['shop_parent_mark_id_soput'] = "Артикул родительского товара для сопутствующего товара";
$GLOBALS['MSG_shops']['shop_upload_csv_marking_error'] = "Товару с артикулом %s указан такой же артикул родительского товара. Товар не может быть модификацией самому себе! Товар проимпортируется как обычный товар.";
$GLOBALS['MSG_shops']['shop_upload_csv_site_user_id'] = "Идентификатор пользователя сайта";

$GLOBALS['MSG_shops']['shop_upload_csv_eitem_name'] = "Название электронного товара";
$GLOBALS['MSG_shops']['shop_form_comment_add_edit_email'] = "E-mail";
