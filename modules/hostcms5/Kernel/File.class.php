<?php
/**
 * Система управления сайтом HostCMS v. 5.xx
 *
 * Copyright © 2005-2011 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 *
 * Ядро, класс для работы с файлами.
 *
 * Файл: /modules/Kernel/File.class.php
 *
 * @package HostCMS 5
 * @author Hostmake LLC
 * @version 5.x
 */
class File
{
	var $chmod_file = null;

	/**
	 * Constructor.
	 */
	function __construct()
	{
		if (defined('CHMOD_FILE'))
		{
			$this->SetChmodFile(CHMOD_FILE);
		}
	}

	/**
	 * Установка прав доступа к создаваемых объектом файлов
	 *
	 * @param int $chmod_file права доступа к создаваемых файлам, например, 0644
	 */
	function SetChmodFile($chmod_file)
	{
		$this->chmod_file = $chmod_file;
	}

	/**
	 * Получение прав доступа к создаваемым объектом файлов
	 */
	function GetChmodFile()
	{
		return $this->chmod_file;
	}

	function flush()
	{
		Core_File::flush();
	}

	/**
	* Получение строки прав доступа к файлу
	*
	* @param string $filename имя файла
	* @param int $type тип строки (0 - "-rw-rw-rw-"; 1 - "0755")
	* <code>
	* <?php
 	* $File = new File();
	*
	* $filename = 'index.php';
	* $type = 1;
	*
	* $newstr = $File->get_file_perms($filename, $type);
	*
	* // Распечатаем результат
	* echo $newstr;
	* ?>
	* </code>
	* @return string строка прав доступа к файлу
	*/
	function get_file_perms($filename, $type)
	{
		return Core_File::getFilePerms($filename, $type == 1);
	}

	/**
	* Получение содержимого файла
	*
	* @param string $filename абсолютный путь к файлу
	* <code>
	* <?php
	* $File = new File();
	*
	* $filename = 'file.txt';
	*
	* $contents = $File->GetFileContent($filename);
	*
	* // Распечатаем результат
	* echo $contents;
	* ?>
	* </code>
	* @return string содержимое файла, если такой файл существует или false, если файл не существует или его невозможно прочитать
	*/
	function GetFileContent($filename)
	{
		try
		{
			return Core_File::read($filename);
		}
		catch (Exception $e)
		{
			return false;
		}
	}

	/**
	* Метод возвращает содержимое файла в поток вместе с заголовком в соответствии с типом файла
	*
	* @param string $file абсолютный путь к файлу
	* @param string $filename имя файла
	* @param array $param массив дополнительных параметров
	* - $param['content_disposition'] заголовок, определяющий вывод файла (inline - открывается в браузере (по умолчанию), attachment - скачивается)
	* <code>
	* <?php
	* $File = new File();
	*
	* $file = CMS_FOLDER . 'file.dat';
	* $filename = 'Пользовательское_имя_файла.dat';
	*
	* $File->Download($file, $filename);
	*
	* exit();
	* ?>
	* </code>
	* @return boolean
	*/
	function Download($file, $filename, $param = array())
	{
		return Core_File::download($file, $filename, $param);
	}

	/**
	 * Удаление файла с проверкой на его существованием
	 * @param string $filepath путь к файлу
	 * <code>
	 * <?php
	 * $File = new File();
	 *
	 * $filepath = CMS_FOLDER . 'dir/myfile.txt';
	 *
	 * $File->DeleteFile($filepath);
	 *
	 * ?>
	 * </code>
	 * @return boolean
	 */
	function DeleteFile($filepath)
	{
		try
		{
			Core_File::delete($filepath);
		}
		catch (Exception $e)
		{
			return false;
		}
	}

	/**
	* Метод удаления директории вместе с вложенными файлами и директориями.
	* При указании $delete_self в true удаляет переданную дерикторию тоже.
	*
	* @param string $dir абсолютный путь к директории
	* @param boolean $delete_self необязательный флаг удаления переданной директории, по умолчанию true
	* <code>
	* <?php
	* $File = new File();
	*
	* $dir = CMS_FOLDER . 'dir';
	*
	* $File->DeleteDir($dir, true);
	*
	* ?>
	* </code>
	* @return boolean
	*/
	function DeleteDir($dir, $delete_self = TRUE)
	{
		$dir = $this->PathCorrection($dir);

		if (mb_strtolower($dir) == mb_strtolower(CMS_FOLDER))
		{
			return false;
		}

		if (is_dir($dir) && !is_link($dir))
		{
			if ($dh = @opendir($dir))
			{
				while (($file = @readdir($dh)) !== false)
				{
					if ($file != '.' && $file!='..')
					{
						clearstatcache();

						$this->DeleteFile($dir . DIRECTORY_SEPARATOR . $file);

						if (is_dir($dir . DIRECTORY_SEPARATOR . $file))
						{
							$this->DeleteDir($dir . DIRECTORY_SEPARATOR . $file /*. DIRECTORY_SEPARATOR*/, true);
						}
					}
				}
				@closedir($dh);

				clearstatcache();

				// удаляем каталог и возвращаем результат
				if ($delete_self && is_dir($dir))
				{
					return @rmdir($dir);
				}

				return true;
			}
		}
		else
		{
			return false;
		}

		return false;
	}

	/**
	 * Копирование директории
	 * @param string $source директория источник
	 * @param string target директория получатель
	 *
	 * @return boolean
	 */
	function CopyDir($source, $target)
	{
		return Core_File::copyDir($source, $target);
	}

	/**
	* Метод удаляет потенциально опасные символы из пути или имени файла
	*
	* @param string $path путь
	* <code>
	* <?php
	* $File = new File();
	*
	* $path = CMS_FOLDER . 'dir//subdir/../...//.//../file.txt';
	*
	* $newpath = $File->PathCorrection($path);
	*
	* // Распечатаем результат
	* echo $newpath;
	* ?>
	* </code>
	* @return string путь без потенциально опасных символов
	*/
	function PathCorrection($path)
	{
		return Core_File::pathCorrection($path);
	}

	/**
	 * Сохранение содержимого $content в файл с путем $filepath
	 *
	 * @param string $filepath путь к файлу
	 * @param string $content содержимое файла
	 *
	 * @return boolean
	 */
	function SaveToFile($filepath, $content)
	{
		try
		{
			return Core_File::write($filepath, $content);
		}
		catch (Exception $e)
		{
			return false;
		}
	}
}
