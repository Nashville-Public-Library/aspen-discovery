<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {implode} function plugin
 *
 * Name:     implode<br>
 * Purpose:  glue an array together as a string, with supplied string glue, and assign it to the template
 * @link http://smarty.php.net/manual/en/language.function.implode.php {implode}
 *       (Smarty online manual)
 * @author Will Mason <will at dontblinkdesign dot com>
 * @param array $params
 * @param UInterface $smarty
 * @return null|string
 */
function smarty_function_implode($params, &$smarty) {
	if (!isset($params['subject'])) {
		$smarty->trigger_error("implode: missing 'subject' parameter");
		return "implode: missing 'subject' parameter";
	}

	if (!isset($params['glue'])) {
		$params['glue'] = ", ";
	}
	$translate = false;
	if (isset($params['translate'])) {
		$translate = $params['translate'];
	}
	$escapeValues = false;
	if (isset($params['escape'])) {
		$escapeValues = $params['escape'];
	}
	$removeTrailingPunctuationFromTerms = false;
	if (isset($params['removeTrailingPunctuationFromTerms'])) {
		$removeTrailingPunctuationFromTerms = $params['removeTrailingPunctuationFromTerms'];
	}

	$subject = $params['subject'];

	require_once ROOT_DIR . '/sys/Utils/StringUtils.php';
	if (is_array($subject)) {
		if ($removeTrailingPunctuationFromTerms) {
			foreach ($subject as $key => $item) {
				$subject[$key] = StringUtils::removeTrailingPunctuation($item);
			}
		}
		if ($translate) {
			if (isset($params['isPublicFacing'])) {
				$isPublicFacing = $params['isPublicFacing'];
			} else {
				$isPublicFacing = false;
			}
			if (isset($params['isAdminFacing'])) {
				$isAdminFacing = $params['isAdminFacing'];
			} else {
				$isAdminFacing = false;
			}
			if (isset($params['isMetadata'])) {
				$isMetadata = $params['isMetadata'];
			} else {
				$isMetadata = false;
			}
			foreach ($subject as $index => $value) {
				$subject[$index] = translate([
					'text' => $value,
					'isPublicFacing' => $isPublicFacing,
					'isAdminFacing' => $isAdminFacing,
					'isMetadata' => $isMetadata,
				]);
			}
		}
		if ($escapeValues) {
			foreach ($subject as $index => $value) {
				$subject[$index] = htmlspecialchars($value);
			}
		}
		if (isset($params['sort'])) {
			sort($subject);
		}
		$implodedValue = implode($params['glue'], $subject);
	} else {
		if ($removeTrailingPunctuationFromTerms) {
			$subject = StringUtils::removeTrailingPunctuation($subject);
		}
		if ($translate) {
			if (isset($params['isPublicFacing'])) {
				$isPublicFacing = $params['isPublicFacing'];
			} else {
				$isPublicFacing = false;
			}
			if (isset($params['isAdminFacing'])) {
				$isAdminFacing = $params['isAdminFacing'];
			} else {
				$isAdminFacing = false;
			}
			$implodedValue = translate([
				'text' => $subject,
				'isPublicFacing' => $isPublicFacing,
				'isAdminFacing' => $isAdminFacing,
			]);
		} else {
			$implodedValue = $subject;
		}
		if ($escapeValues) {
			$implodedValue = htmlspecialchars($subject);
		}
	}

	if (!isset($params['assign'])) {
		return $implodedValue;
	} else {
		$smarty->assign($params['assign'], $implodedValue);
	}
	return null;
}