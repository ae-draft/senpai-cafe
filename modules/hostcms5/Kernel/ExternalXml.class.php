<?php
/**
 * Система управления сайтом HostCMS v. 5.xx
 *
 * Copyright © 2005-2011 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 *
 * Ядро, класс формирует дерево XML по переданному массиву.
 *
 * Файл: /modules/Kernel/ExternalXml.class.php
 *
 * @package HostCMS 5
 * @author Hostmake LLC
 * @version 5.x
 */
class ExternalXml
{
	var $xml = '';
	var $flag = false;

	/**
	 * Генерация XML для переданного массива
	 *
	 * @param array $array массив с информацией о дереве
	 * @param int $type тип переданного массива<br />
	 * Тип 0:
	 * $x['xml_tag_name'] = "value"; <br />
	 * Тип 1:
	 * $x[0]['xml_name'] = 'xml_tag_name'; <br />
	 * $x[0]['value'] = 'value'; // value может быть массивом неограниченного уровня вложенности <br />
	 * $x[0]['attribute'] = array('name' => 'value', 'name2' => 'value2'); <br />
	 *
	 * //Пример 1
	 * <code>
	 * <?php
	 * $ExternalXml = new ExternalXml();
	 *
	 * $array['xml_tag_name'] = "value";
	 * $array['xml_tag_name2'] = "value2";
	 * $type = 0;
	 *
	 * $newxml = $ExternalXml->GenXml($array, $type);
	 *
	 * // Распечатаем результат
	 * echo htmlspecialchars($newxml);
	 * ?>
	 * </code>
	 *
	 * //Пример 2
	 * <code>
	 * <?php
	 * $ExternalXml = new ExternalXml();
	 *
	 * $array[0]['xml_name'] = 'xml_tag_name';
	 * $array[0]['value'] = 'value';
	 *
	 * $array[1]['xml_name'] = 'xml_tag_name2';
	 * $array[1]['value'] = 'value2';
	 *
	 * $array[2]['xml_name'] = 'xml_tag_name3';
	 * $array[2]['value'] = array(0 =>array('xml_name' => 'children_tag1',	'value' => 'children_value'));
	 *
	 * $type = 1;
	 *
	 * $newxml = $ExternalXml->GenXml($array, $type);
	 *
	 * // Распечатаем результат
	 * echo htmlspecialchars($newxml);
	 * ?>
	 * </code>
	 * @return string XML-код
	 */
	function GenXml($array, $type = 0)
	{
		$this->xml = '';

		if ($type == 0)
		{
			reset($array);

			$each = each($array);

			// Автоматическое определение типа
			if (isset($each['key']) && $each['key'] == 'xml_name')
			{
				$type = 1;
			}
		}

		$this->GenXml4Teg($array, $type);
		return $this->xml;
	}

	/**
	 * Генерация XML для определенного массива, используется вызов себя рекурсивно. Служебный метод.
	 *
	 * @param mixed $tag узел
	 * @see GenXml()
	 * @access private
	 *
	 */
	function GenXml4Teg($tag, $type)
	{
		if ($type == 0)
		{
			if (is_array($tag))
			{
				foreach ($tag as $key => $value)
				{
					if (!empty($key))
					{
						$this->xml .= "<$key>";
						$this->GenXml4Teg($value,$type);

						if ($this->flag)
						{
							$this->xml .= "\n";
						}

						$this->xml .= "</$key>\n";
						$this->flag = true;
					}
				}
			}
			else
			{
				$this->flag = false;
				$this->xml .= str_for_xml($tag);
			}
		}
		elseif ($type == 1)
		{

			if (is_array($tag))
			{
				foreach ($tag as $key => $array)
				{
					if (!empty($array['xml_name']))
					{
						$attribute_str = '';

						if (isset($array['attribute']) && is_array($array['attribute'])
						&& count($array['attribute']) > 0)
						{
							foreach ($array['attribute'] as $attribute_name => $attribute_value)
							{
								$attribute_str .= " {$attribute_name}=\"".str_for_xml($attribute_value)."\"";
							}
						}

						$this->xml .= "<".Core_Type_Conversion::toStr($array['xml_name'])."{$attribute_str}>";

						if (isset($array['value']) && is_array($array['value']))
						{
							if ($this->flag)
							{
								$this->xml .= "\n";
							}
							$this->GenXml4Teg($array['value'],$type);
						}
						else
						{
							$this->flag = false;
							$this->xml .= str_for_xml(Core_Type_Conversion::toStr($array['value']));
						}

						$this->xml .= "</".Core_Type_Conversion::toStr($array['xml_name']).">\n";
						$this->flag = true;
					}
				}
			}
		}
	}
}
