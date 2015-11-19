<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'GbEvents',
    'Main',
    'LLL:EXT:gb_events/Resources/Private/Language/locallang_db.xlf:tx_gbevents.main.title'
);

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['gbevents_main'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    'gbevents_main',
    'FILE:EXT:gb_events/Configuration/FlexForms/flexform_main.xml'
);
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['gbevents_main'] = 'layout,select_key,recursive';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'gb_events',
    'Configuration/TypoScript',
    'Terminkalender'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
    'tx_gbevents_domain_model_event',
    'EXT:gb_events/Resources/Private/Language/locallang_csh_tx_gbevents_domain_model_event.xlf'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_gbevents_domain_model_event');
