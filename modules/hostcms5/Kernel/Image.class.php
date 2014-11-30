<?php
/**
 * Система управления сайтом HostCMS v. 5.xx
 *
 * Copyright © 2005-2011 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 *
 * Ядро, класс для работы с графическими изображениями.
 *
 * Файл: /modules/Kernel/image.class.php
 *
 * @package HostCMS 5
 * @author Hostmake LLC
 * @version 5.x
 */
class Image
{
	/**
	 * Метод определяет высоту и ширину изображения
	 *
	 * @param string $path полный путь к изображению
	 * <code>
	 * <?php
	 * $Image = new Image();
	 *
	 * $path = 'file.jpg';
	 *
	 * $row = $Image->GetImageSize($path);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return array ассоциативный массив, содержащий высоту и ширину изображения
	 * <br />array['width'] - ширина
	 * <br />array['['height'] - высота
	 * или false, если файл не существует
	 */
	function GetImageSize($path)
	{
		return Core_Image::instance()->getImageSize($path);
	}

	/**
	 * Служебный метод для наложения водяного знака
	 *
	 * @param resource $source_id исходное изображение
	 * @param resource $watermark_img_obj водяной знак
	 * @param int $watermark_x позиция по оси X
	 * @param int $watermark_y позиция по оси Y
	 * @return resource
	 */
	function CreateWatermark(& $source_id, & $watermark_img_obj, $watermark_x = false, $watermark_y = false)
	{
		throw new Core_Exception('Method CreateWatermark() does not allow');
	}

	/**
	 * Метод накладывает watermark на изображение. Если файл вартермарка не существует, метод скопирует исходное изображение в файл получателя
	 *
	 * @param string $source путь к файлу источнику
	 * @param string $target_file путь к файлу получателю
	 * @param string $watermark путь к файлу watermark-а (в формате PNG)
	 * @param string $watermark_x позиция по оси X (в пикселях или процентах)
	 * @param string $watermark_y позиция по оси Y (в пикселях или процентах)
	 * <code>
	 * <?php
	 * $Image = new Image();
	 *
	 * $source = 'file1.jpg';
	 * $target_file = 'file2.jpg';
	 * $watermark = CMS_FOLDER . 'information_system_watermark1.png';
	 *
	 * $result = $Image->CopyImgWithWatermark($source, $target_file, $watermark);
	 *
	 * // Распечатаем результат
	 * echo $result;
	 * ?>
	 * </code>
	 * @return bool результат работы ф-ции.
	 */
	function CopyImgWithWatermark($source, $target_file, $watermark, $watermark_x = FALSE, $watermark_y = FALSE)
	{
		return Core_Image::instance()->addWatermark($source, $target_file, $watermark,
			$watermark_x === FALSE ? NULL : $watermark_x,
			$watermark_y === FALSE ? NULL : $watermark_y
		);
	}

	/**
	 * Метод для пропорционального масштабирования изображения
	 *
	 * @param string $source_file путь к исходному файлу
	 * @param int $max_width максимальная ширина картинки
	 * @param int $max_height максимальная высота картинки
	 * @param string $target_file путь к результирующему файлу
	 * @param int $img_quality качество JPEG/PNG файла, если не передано, то берется значение JPG_QUALITY
	 * @param int $image_preserve_aspect_ratio сохранять пропорции изображения, по умолчанию true
	 * <code>
	 * <?php
	 * $Image = new Image();
	 *
	 * $source_file = CMS_FOLDER . 'file1.jpg';
	 * $max_width = 100;
	 * $max_height = 50;
	 * $target_file = CMS_FOLDER . 'file2.jpg';
	 * $jpeg_quality = JPG_QUALITY;
	 *
	 * $result = $Image->ResizeToFileEx($source_file, $max_width, $max_height, $target_file, $jpeg_quality);
	 *
	 * // Распечатаем результат
	 * echo $result;
	 * ?>
	 * </code>
	 * @return bool результат
	 */
	function ResizeToFileEx($source_file, $max_width, $max_height, $target_file, $img_quality = FALSE, $image_preserve_aspect_ratio = true)
	{
		return Core_Image::instance()->resizeImage($source_file, $max_width, $max_height, $target_file,
			$img_quality === FALSE ? NULL : $img_quality,
			$image_preserve_aspect_ratio
		);
	}

	/**
	 * Метод изменения размера изображения
	 *
	 * @param string $sourcefile путь к исходному файлу
	 * @param int $maxsize максимальный размер в одном из измерений
	 * @param string $target_file путь для размещения преобразованного файла
	 * @param int $jpegqual качество JPG
	 * @return boolean
	 */
	function resizeToFile($sourcefile, $maxsize, $target_file, $jpegqual)
	{
		return $this->ResizeToFileEx($sourcefile, $maxsize, $maxsize, $target_file, $jpegqual);
	}

	/**
	 * Установка прозрачности для $new_image, равной прозрачности $image_source
	 * @param $new_image Изображение получатель
	 * @param $image_source Изображение источник
	 */
	function setTransparency($new_image, $image_source)
	{
		return Core_Image::instance()->setTransparency($new_image, $image_source);
	}
}
