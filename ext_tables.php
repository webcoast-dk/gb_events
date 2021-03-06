<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

# Main Plugin (List and Details view, Export)
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'GuteBotschafter.GbEvents',
    'Main',
    'LLL:EXT:gb_events/Resources/Private/Language/locallang_db.xlf:tx_gbevents.main.title'
);

$TCA['tt_content']['types']['list']['subtypes_addlist']['gbevents_main'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    'gbevents_main',
    'FILE:EXT:gb_events/Configuration/FlexForms/Main.xml'
);
$TCA['tt_content']['types']['list']['subtypes_excludelist']['gbevents_main'] = 'layout,select_key,recursive';

# Upcoming Plugin (List of upcoming events)
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'GuteBotschafter.GbEvents',
    'Upcoming',
    'LLL:EXT:gb_events/Resources/Private/Language/locallang_db.xlf:tx_gbevents.upcoming.title'
);

$TCA['tt_content']['types']['list']['subtypes_addlist']['gbevents_upcoming'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    'gbevents_upcoming',
    'FILE:EXT:gb_events/Configuration/FlexForms/Upcoming.xml'
);
$TCA['tt_content']['types']['list']['subtypes_excludelist']['gbevents_upcoming'] = 'layout,select_key,recursive';

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
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::makeCategorizable(
    'gb_events',
    'tx_gbevents_domain_model_event',
    'categories',
    []
);
