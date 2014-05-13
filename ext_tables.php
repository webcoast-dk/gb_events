<?php
if (!defined ('TYPO3_MODE')){
  die ('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
  'GuteBotschafter.' . $_EXTKEY,
  'Main',
  'LLL:EXT:gb_events/Resources/Private/Language/locallang_db.xlf:tx_gbevents.main.title'
);

$TCA['tt_content']['types']['list']['subtypes_addlist']['gbevents_main'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue('gbevents_main', 'FILE:EXT:' . $_EXTKEY . '/Configuration/FlexForms/flexform_main.xml');
$TCA['tt_content']['types']['list']['subtypes_excludelist']['gbevents_main'] = 'layout,select_key,recursive';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'Terminkalender');


\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_gbevents_domain_model_event', 'EXT:gb_events/Resources/Private/Language/locallang_csh_tx_gbevents_domain_model_event.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_gbevents_domain_model_event');
$TCA['tx_gbevents_domain_model_event'] = array(
  'ctrl' => array(
    'title'                     => 'LLL:EXT:gb_events/Resources/Private/Language/locallang_db.xlf:tx_gbevents_domain_model_event',
    'label'                     => 'title',
    'tstamp'                    => 'tstamp',
    'crdate'                    => 'crdate',
    'dividers2tabs'             => TRUE,
    'versioningWS'              => 2,
    'versioning_followPages'    => TRUE,
    'origUid'                   => 't3_origuid',
    'languageField'             => 'sys_language_uid',
    'transOrigPointerField'     => 'l10n_parent',
    'transOrigDiffSourceField'  => 'l10n_diffsource',
    'delete'                    => 'deleted',
    'enablecolumns'             => array(
      'disabled'  => 'hidden',
      'starttime' => 'starttime',
      'endtime'   => 'endtime',
    ),
    'dynamicConfigFile'         => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'Configuration/TCA/Event.php',
    'iconfile'                  => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_gbevents_domain_model_event.gif'
  ),
);