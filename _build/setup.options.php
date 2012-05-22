<?php

switch ($options[xPDOTransport::PACKAGE_ACTION]) {
	case xPDOTransport::ACTION_INSTALL:
	case xPDOTransport::ACTION_UPGRADE:
		$y_ch = $n_ch = '';
		if (!file_exists($modx->getOption('core_path') . 'components/msearch/phpmorphy/dicts/installed')) {$y_ch = 'checked';} else {$n_ch = 'checked';}
		$output = '
		<label for="language">Скачать и установить русские словари (около 5Мб)</label><br/>
		<p>
			<input type="radio" name="download" value="yes" '.$y_ch.'/>Да
			<br/><br/>
			<input type="radio" name="download" value="no" '.$n_ch.'/>Нет
		</p>
		';

	break;
	case xPDOTransport::ACTION_UNINSTALL: break;
}

return $output;
