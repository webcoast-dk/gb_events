<?php

# Main Plugin (List and Details view, Export)
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

ExtensionUtility::registerPlugin(
    'GuteBotschafter.GbEvents',
    'Main',
    'LLL:EXT:gb_events/Resources/Private/Language/locallang_db.xlf:tx_gbevents.main.title'
);
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['gbevents_main'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    'gbevents_main',
    'FILE:EXT:gb_events/Configuration/FlexForms/Main.xml'
);
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['gbevents_main'] = 'layout,select_key,recursive';

# Upcoming Plugin (List of upcoming events)
ExtensionUtility::registerPlugin(
    'GuteBotschafter.GbEvents',
    'Upcoming',
    'LLL:EXT:gb_events/Resources/Private/Language/locallang_db.xlf:tx_gbevents.upcoming.title'
);
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['gbevents_upcoming'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    'gbevents_upcoming',
    'FILE:EXT:gb_events/Configuration/FlexForms/Upcoming.xml'
);
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['gbevents_upcoming'] = 'layout,select_key,recursive';
