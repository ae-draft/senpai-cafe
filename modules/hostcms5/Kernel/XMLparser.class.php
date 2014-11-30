<?php 
/**
 * Система управления сайтом HostCMS v. 5.xx
 * 
 * Copyright © 2005-2011 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 *
 * Ядро, методы анализа XML.
 * 
 * Файл: /modules/Kernel/XMLparser.class.php
 * 
 * @package HostCMS 5
 * @author Hostmake LLC
 * @version 5.x
 */
class XMLparser
{
	/**
	 * @param string $text
	 * @return string
	 */
	function ParseArgs($text)
	{
		$pars = array();
		$result = array();

		preg_match_all("' ([a-zA-Z0-9_]+)=(((\"(.*)\")|(\'(.*)\')|([^ ]*)))'Uu",$text,$pars);
		if (count($pars[0])!=0)
		{
			$i=0;
			foreach ($pars[1] AS $name)
			{
				$result[$name] = $pars[5][$i];
				$i++;
			}
			return $result;
		}
		return '';
	}
	
	/**
	 * @param string $text
	 * @return array
	 */
	function ParseValue($text)
	{
		$text1 = $text;
		//$text1 = str_replace("\n", " ", $text1);
		//$text1 = str_replace("\t", " ", $text1);
		$text1 = trim($text1);
		
		if (mb_substr($text1, 0, 1) == '<'
		&& mb_substr($text1, mb_strlen($text1) - 1, 1) == '>')
		{
			preg_match_all("'<([a-zA-Z0-9_]+)(( ([a-zA-Z0-9_]+)=(((\"(.*)\")|(\'(.*)\')|([^ ]*))))?)((>([.\s\w\W]*)</\\1>)|(/>))'U", $text1, $pars);
			//preg_match_all("'<([a-zA-Z0-9_]+)(( ([a-zA-Z0-9_]+)=(((\"(.*)\")|(\'(.*)\')|([^ ]*)))|))((>(.*)</\\1>)|(/>))'U", $text1, $pars);
			if (count($pars[0]) != 0)
			{
				$i = 0;
				foreach ($pars[0] as $value)
				{
					//$result[$i]['name']=$pars[1][$i];
					$result[$pars[1][$i]][]['arg'] = $this->ParseArgs($pars[2][$i]);
					$j = count($result[$pars[1][$i]])-1;
					$result[$pars[1][$i]][$j]['value'] = $this->ParseValue($pars[14][$i]);
					$i++;
				}
				return $result;
			}
			return $text;
		}
		return $text;
	}

	/**
	 * @param string $tags
	 * @return string
	 */
	function CreateXML($tags)
	{
		$text = '';
		
		$count=count($tags);
		
		if (is_array($tags) && $count != 0)
		{
			foreach ($tags as $name => $atr)
			{
				$count=count($tags[$name]);
				for ($i=0;$i<$count;$i++)
				{
					$arg="";
					if (is_array($atr[$i]['arg']))
					{
						foreach ($atr[$i]['arg'] AS $name_arg => $value)
						{
							$arg.=" ".$name_arg.'="'.$value.'"';
						}
					}
					$text.='<'.$name.$arg.'>';
					if (is_array($atr[$i]['value']))
					{
						$text.="\r\n";
						$text.=$this->CreateXML($atr[$i]['value']);
					}
					else
					{
						$text.=$atr[$i]['value'];
					}
					$text.='</'.$name.'>'."\r\n";
				}
			}
		}
		elseif (!is_array($tags))
		{
			$text=$tags;
		}
		return $text;
	}
}
