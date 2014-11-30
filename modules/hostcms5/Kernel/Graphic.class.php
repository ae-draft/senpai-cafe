<?php
/**
 * Система управления сайтом HostCMS v. 5.xx
 *
 * Copyright © 2005-2011 ООО "Хостмэйк"(Hostmake LLC), http://www.hostcms.ru
 *
 * Ядро, методы построения графиков.
 *
 * Файл: /modules/Kernel/graphic.class.php
 *
 * @package HostCMS 5
 * @author Hostmake LLC
 * @version 5.x
 */
class graphic
{
	/**
	 * Метод вывода линейного графика
	 *
	 * @param array $data Массив со значениями и подписями по оси х
	 * $data['x'] str подписи
	 * $data[0] int значения
	 * @param array $legend Массив с легендой
	 * @param array $param Массив дополнительных параметров
	 * - $param['cheakpoint'] boolean Нужно ли отражать значения кружками
	 * - $param['null'] boolean Нужно ли отображать нулевую отметку на графике
	 * - $param['divis'] int Число делений по оси у
	 * - $param['invers'] инвесия графика
	 */
	function Graphic_Linear($data, $legend, $param = array())
	{
		$oCore_Diagram = new Core_Diagram();

		if(count($data) > 1 && isset($data[0]) && count($data[0]) > 1 && isset($data['x']) && count($data['x']) > 1)
		{
			isset($param['cheakpoint']) && $oCore_Diagram->showPoints($param['cheakpoint']);
			isset($param['null']) && $oCore_Diagram->showOrigin($param['null']);
			isset($param['divis']) && $oCore_Diagram->scaleDivision($param['divis'] === FALSE ? NULL : $param['divis']);
			isset($param['invers']) && $oCore_Diagram->inversion($param['invers']);

			$abscissa = $data['x'];
			unset($data['x']);

			$oCore_Diagram
				->abscissa($abscissa)
				->legend($legend)
				->values($data)
				->lineChart();
		}
		else
		{
			$oCore_Diagram->emptyImage();
		}
		return TRUE;
	}

	/**
	 * Метод вывода круговой диаграммы
	 *
	 * @param array $data значения(массив со значениями)
	 * @param array $legend подписи(значения легенды, массив строк)
	 * @param int $width - ширина изображения в пикселях
	 */
	function graphic_diagramma($data, $legend, $width, $maxLegendLength = 15)
	{
		$oCore_Diagram = new Core_Diagram();
		$oCore_Diagram
			->legend($legend)
			->values($data)
			->pieChart($width, $maxLegendLength);
	}

	/**
	 * Вывод гистограммы
	 *
	 * @param int $width ширина гистограммы в пикселях
	 * @param int $height высота гистограммы в пикселях
	 * @param array $data массив со значенями для гистограммы
	 * @param array $legend массив подписей для легенды
	 * @return boolean истина при удачном выводе, ложь - в случае возникновения ошибки
	 */
	function graphic_gistogramma($width, $height, $data, $legend = array('Хиты','Сессии','Хосты'), $param = array())
	{
		$oCore_Diagram = new Core_Diagram();
		$oCore_Diagram
			->legend($legend)
			->values($data);

		isset($param['horizontal_orientation']) && $oCore_Diagram->horizontalOrientation($param['horizontal_orientation']);

		$oCore_Diagram->histogram($width, $height);
	}
}