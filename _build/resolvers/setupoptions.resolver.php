<?php

$success= false;
switch ($options[xPDOTransport::PACKAGE_ACTION]) {
	case xPDOTransport::ACTION_INSTALL:

		if (isset($options['download']) && $options['download'] == 'yes') {
			$core = $object->xpdo->getOption('core_path');
			$path = $core.'components/msearch/';
			if (!file_exists($path)) {
				mkdir($path);
				mkdir($path.'phpmorphy/');
				mkdir($path.'phpmorphy/dicts/');
			}

			file_put_contents($path . 'phpmorphy/dicts/dict_ru.zip', file_get_contents('http://bezumkin.ru/modx/msearch/dict_ru.zip'));

			require $core.'xpdo/compression/pclzip.lib.php';
			$file = new PclZip($path . 'phpmorphy/dicts/dict_ru.zip');

			if ($file->extract(PCLZIP_OPT_PATH, $path.'phpmorphy/dicts/')) {
				file_put_contents($path . 'phpmorphy/dicts/installed', date('Y-m-d H:i:s'));
				unlink($path . 'phpmorphy/dicts/dict_ru.zip');
			}
			else {
				 $object->xpdo->log(xPDO::LOG_LEVEL_ERROR, 'Could not extract dictionaries to ' . $path . 'phpmorphy/dicts/');
			}
		}
		$success = true;
		break;

	case xPDOTransport::ACTION_UPGRADE: $success = true; break;
	case xPDOTransport::ACTION_UNINSTALL: $success = true; break;
}
return $success;
