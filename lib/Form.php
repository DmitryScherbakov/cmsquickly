<?php

class Form
{

	private static $formErrors = [];

	public static function uploadFile($fieldName, $filePath, $keepFileName = false)
	{
		if ($_FILES[$fieldName]['error'] > 0) {
			self::uploadError($_FILES[$fieldName]['error']);
			return false;
		}

		$filename = $keepFileName ? basename($_FILES[$fieldName]['name']) : uniqid() . self::getExtension($_FILES[$fieldName]['name']);
		$uploadedFile = $filePath . $filename;
		if (move_uploaded_file($_FILES[$fieldName]['tmp_name'], $uploadedFile)) {
			chmod($uploadedFile, 0664);
			return $filename;
		} else {
			self::addError('File ' . $uploadedFile . ' cannot be moved after upload');
		}
		return false;
	}

	private static function getExtension($sFileName)
	{
		$iEnd = strrpos($sFileName, '.');
		$iEnd = ($iEnd !== false) ? $iEnd : strlen($sFileName);
		$sExt = substr($sFileName, $iEnd);
		return ($sExt == '') ? '.txt' : strtolower($sExt);
	}

	private static function uploadError($errorCodeNum)
	{
		$sErrorText = '';
		switch ($errorCodeNum) {
			case 1:
				$sErrorText = _t('The uploaded file exceeds the upload_max_filesize directive in php.ini');
				break;
			case 2:
				$sErrorText = _t('The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form');
				break;
			case 3:
				$sErrorText = _t('The uploaded file was only partially uploaded');
				break;
			case 4:
				$sErrorText = _t('No file was uploaded');
				break;
			case 6:
				$sErrorText = _t('Missing a temporary folder');
				break;
			case 7:
				$sErrorText = _t('Failed to write file to disk');
				break;
			default:
				$sErrorText = _t('Unknown error occured during file upload');
				break;
		}
		self::addError($sErrorText);
	}

	private static function addError($text)
	{
		self::$formErrors[] = $text;
	}

	public static function getErrors()
	{
		return self::$formErrors;
	}

	public static function getPost($tblFields, $lang = '')
	{
		$post = [];
		foreach ($tblFields as $name => $type) {
			$value = $lang == '' ? $_POST[$name] : $_POST[$name][$lang];
			switch ($type) {
				case 'checkbox':
					$post[$name] = isset($value) ? 1 : 0;
					break;
				case 'int':
					$post[$name] = intval($value);
					break;
				case 'float':
					$post[$name] = floatval($value);
					break;
				case 'html':
					$post[$name] = trim($value);
					break;
				case 'raw':
					$post[$name] = $value;
					break;
				case 'string':
				default:
					$post[$name] = str_replace('"', '&quot;', trim($value));
					break;
			}
		}
		return $post;
	}

	/**
	 * Prints html field
	 * @param  $name	Field name
	 * @param  $title	Title user will see close to the field
	 * @param  $type	Type of the field
	 * @param  $value	Value of the field
	 * @param  $placeholder Place holder if set
	 * @param  $id		Html id of the field, if none 'field_' . $name is set
	 * @param  $extra	Extra parameters: help, prefix, postfix
	 * @return      none
	 */
	public static function printField($name, $title, $type, $value = '', $placeholder = '', $id = '', $extra = [])
	{
		if (!$id) {
			$id = 'field_' . $name;
		}
		$extratext = empty($extra['help']) ? '' : '<p class="help-block">' . $extra['help'] . '</p>';
		$prefixtext = empty($extra['prefix']) ? '' : '<div class="input-group-addon">' . $extra['prefix'] . '</div>';
		$postfixtext = empty($extra['postfix']) ? '' : '<div class="input-group-addon">' . $extra['postfix'] . '</div>';

		switch ($type) {
			case 'check':
			case 'checkbox':
				$checked = $value === true ? ' checked' : '';
				echo <<<EOT
<div class="checkbox">
	<label>
		<input type="checkbox" id="$id" name="$name"$checked> $title
	</label>
</div>
EOT;
				break;
			case 'radio':
			case 'radiobutton':
				$checked = $placeholder === true ? ' checked' : '';
				echo <<<EOT
<div class="radio">
	<label>
		<input type="radio" name="$name" id="$id" value="$value"$checked> $title
	</label>
</div>
EOT;
				break;
			case 'file':
				echo <<<EOT
<div class="form-group">
	<label for="$id">$title</label>
	<input type="file" name="$name" id="$id">
	$extratext
</div>
EOT;
				break;
			case 'htmleditor':
				echo <<<EOT
<div class="form-group">
	<label for="$id">$title</label>
	<textarea class="htmleditor" name="$name" id="$id" style="visibility:visible">$value</textarea>
	$extratext
</div>
EOT;
				break;
			case 'textarea':
				echo <<<EOT
<div class="form-group">
	<label for="$id">$title</label>
	<textarea class="form-control" name="$name" id="$id">$value</textarea>
	$extratext
</div>
EOT;
				break;
			default:
				if ($prefixtext || $postfixtext) {
					echo <<<EOT
<div class="form-group">
	<label for="$id">$title</label>
	<div class="input-group">
EOT;
					if ($prefixtext) {
						echo $prefixtext;
					}
					echo '<input type="' . $type . '" class="form-control" id="' . $id . '" name="' . $name . '" value="' . $value . '">';
					if ($postfixtext) {
						echo $postfixtext;
					}
					echo <<<EOT
	</div>
$extratext
</div>
EOT;
				} else {
					echo <<<EOT
<div class="form-group">
	<label for="$id">$title</label>
	<input type="$type" class="form-control" id="$id" name="$name" value="$value">
	$extratext
</div>
EOT;
				}
				break;
		}
	}

	public static function printSelect($name, $title, $type, $value = '', $options = [], $id = '', $extra = [])
	{
		if (!$id) {
			$id = 'field_' . $name;
		}
		$extratext = empty($extra['help']) ? '' : '<p class="help-block">' . $extra['help'] . '</p>';

		switch ($type) {
			case 'treeselect':
				echo <<<EOT
<div class="form-group">
	<label for="$id">$title</label>
	<select class="form-control" name="$name" id="$id">
EOT;
				self::printOptions($options, 0, $value);
				echo '</select>', "\n", $extratext, "\n", '</div>';
				break;
			default:
				echo <<<EOT
<div class="form-group">
	<label for="$id">$title</label>
	<select class="form-control" name="$name" id="$id">
EOT;
				foreach ($options as $optionVal => $optionText) {
					echo '<option value="' . $optionVal . '"' . ($optionVal == $value ? ' selected' : '') . '>' . $optionText . '</option>', "\n";
				}
				echo '</select>', "\n", $extratext, "\n", '</div>';
				break;
		}
	}

	private static function printOptions($aOptions, $level, $selectedOption = 0)
	{
		for ($i = 0; $i < sizeof($aOptions); $i++) {
			echo '<option style="padding-left:' . ($level * 10) . 'px;" value="' . $aOptions[$i]['id'] . '"' . ($aOptions[$i]['id'] == $selectedOption ? '   selected="selected"' : '') . '>' . $aOptions[$i]['title'] . '</option>', "\n";
			if ($aOptions[$i]['children']) {
				self::printOptions($aOptions[$i]['children'], $level + 1, $selectedOption);
			}
		}
		return;
	}

}
