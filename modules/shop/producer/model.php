<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Online shop.
 *
 * @package HostCMS 6\Shop
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2013 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Shop_Producer_Model extends Core_Entity
{
	/**
	 * Backend property
	 * @var int
	 */
	public $img=1;

	/**
	 * One-to-many or many-to-many relations
	 * @var array
	 */
	protected $_hasMany = array(
		'shop_item' => array()
	);

	/**
	 * List of preloaded values
	 * @var array
	 */
	protected $_preloadValues = array(
		'sorting' => 0,
		'active' => 1
	);

	/**
	 * Belongs to relations
	 * @var array
	 */
	protected $_belongsTo = array(
		'shop' => array(),
		'shop_producer_dir' => array(),
	);

	/**
	 * Default sorting for models
	 * @var array
	 */
	protected $_sorting = array(
		'shop_producers.sorting' => 'ASC',
		'shop_producers.name' => 'ASC'
	);

	/**
	 * Constructor.
	 * @param int $id entity ID
	 */
	public function __construct($id = NULL)
	{
		parent::__construct($id);

		if (is_null($id))
		{
			$oUserCurrent = Core_Entity::factory('User', 0)->getCurrent();
			$this->_preloadValues['user_id'] = is_null($oUserCurrent) ? 0 : $oUserCurrent->id;
		}
	}

	/**
	 * Get producer path
	 * @return string
	 */
	public function getProducerPath()
	{
		return $this->Shop->getPath() . '/producers/';
	}

	/**
	 * Change item status
	 */
	public function changeStatus()
	{
		$this->active = 1 - $this->active;
		return $this->save();
	}

	/**
	 * Get producer href
	 * @return string
	 */
	public function getProducerHref()
	{
		return '/' . $this->Shop->getHref() . '/producers/';
	}

	/**
	 * Get the path to the small image of the producer
	 * @return string
	 */
	public function getSmallFilePath()
	{
		return $this->getProducerPath() . $this->image_small;
	}

	/**
	 * Get producer small file href
	 * @return string
	 */
	public function getSmallFileHref()
	{
		return $this->getProducerHref() . rawurlencode($this->image_small);
	}

	/**
	 * Get the path to the large image of the producer
	 * @return string
	 */
	public function getLargeFilePath()
	{
		return $this->getProducerPath() . $this->image_large;
	}

	/**
	 * Get producer large file href
	 * @return string
	 */
	public function getLargeFileHref()
	{
		return $this->getProducerHref() . rawurlencode($this->image_large);
	}

	/**
	 * Set large image sizes
	 * @return self
	 */
	public function setLargeImageSizes()
	{
		$path = $this->getLargeFilePath();

		if (is_file($path))
		{
			$aSizes = Core_Image::instance()->getImageSize($path);
			if ($aSizes)
			{
				$this->image_large_width = $aSizes['width'];
				$this->image_large_height = $aSizes['height'];
				$this->save();
			}
		}

		return $this;
	}

	/**
	 * Specify large image for producer
	 * @param string $fileSourcePath source file
	 * @param string $fileName target file name
	 * @return self
	 */
	public function saveLargeImageFile($fileSourcePath, $fileName)
	{
		$fileName = Core_File::filenameCorrection($fileName);
		$this->createDir();

		$this->image_large = $fileName;
		$this->save();
		Core_File::upload($fileSourcePath, $this->getProducerPath() . $fileName);
		$this->setLargeImageSizes();
		return $this;
	}

	/**
	 * Set small image sizes
	 * @return self
	 */
	public function setSmallImageSizes()
	{
		$path = $this->getSmallFilePath();

		if (is_file($path))
		{
			$aSizes = Core_Image::instance()->getImageSize($path);
			if ($aSizes)
			{
				$this->image_small_width = $aSizes['width'];
				$this->image_small_height = $aSizes['height'];
				$this->save();
			}
		}

		return $this;
	}

	/**
	 * Make url path
	 */
	public function makePath()
	{
		if ($this->Shop->url_type == 1)
		{
			try {
				$this->path = Core_Str::transliteration(
					Core::$mainConfig['translate']
						? Core_Str::translate($this->name)
						: $this->name
				);
			} catch (Exception $e) {
				$this->path = Core_Str::transliteration($this->name);
			}
		}
		elseif ($this->id)
		{
			$this->path = $this->id;
		}
		else
		{
			$this->path = Core_Guid::get();
		}

		return $this;
	}

	/**
	 * Save object.
	 *
	 * @return Core_Entity
	 */
	public function save()
	{
		is_null($this->path) && $this->makePath();

		parent::save();

		$this->path == '' && !$this->deleted && $this->makePath()->save();

		return $this;
	}

	/**
	 * Specify small image for producer
	 * @param string $fileSourcePath source file
	 * @param string $fileName target file name
	 * @return self
	 */
	public function saveSmallImageFile($fileSourcePath, $fileName)
	{
		$fileName = Core_File::filenameCorrection($fileName);
		$this->createDir();

		$this->image_small = $fileName;
		$this->save();
		Core_File::upload($fileSourcePath, $this->getProducerPath() . $fileName);
		$this->setSmallImageSizes();
		return $this;
	}

	/**
	 * Create directory for producer
	 * @return self
	 */
	public function createDir()
	{
		if (!is_dir($this->getProducerPath()))
		{
			try
			{
				Core_File::mkdir($this->getProducerPath(), CHMOD, TRUE);
			} catch (Exception $e) {}
		}

		return $this;
	}

	/**
	 * Delete producer's large image
	 */
	public function deleteLargeImage()
	{
		try
		{
			Core_File::delete($this->getLargeFilePath());
		} catch (Exception $e) {}

		$this->image_large = '';
		$this->save();
	}

	/**
	 * Delete producer's small image
	 * @return self
	 */
	public function deleteSmallImage()
	{
		try
		{
			Core_File::delete($this->getSmallFilePath());
		} catch (Exception $e) {}

		$this->image_small = '';
		$this->save();
	}

	/**
	 * Copy object
	 * @return Core_Entity
	 */
	public function copy()
	{
		$newObject = parent::copy();

		try
		{
			Core_File::copy($this->getLargeFilePath(), $newObject->getLargeFilePath());
		} catch (Exception $e) {}

		try
		{
			Core_File::copy($this->getSmallFilePath(), $newObject->getSmallFilePath());
		} catch (Exception $e) {}

		return $newObject;
	}

	/**
	 * Get XML for entity and children entities
	 * @return string
	 * @hostcms-event shop_producer.onBeforeRedeclaredGetXml
	 */
	public function getXml()
	{
		Core_Event::notify($this->_modelName . '.onBeforeRedeclaredGetXml', $this);

		$this->clearXmlTags()
			->addXmlTag('dir', $this->getProducerHref());

		return parent::getXml();
	}
}