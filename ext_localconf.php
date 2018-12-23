<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'GuteBotschafter.GbEvents',
    'Main',
    [
        'Event' => 'list, show',
        'Archive' => 'list',
        'Calendar' => 'show',
        'Export' => 'list, show',
    ]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'GuteBotschafter.GbEvents',
    'Upcoming',
    [
        'Upcoming' => 'list',
    ]
);

// ke_search indexer
if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('ke_search')) {
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ke_search']['registerIndexerConfiguration'][] =
        'EXT:gb_events/Classes/Hooks/EventIndexer.php:' . \GuteBotschafter\GbEvents\Hooks\EventIndexer::class;
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ke_search']['customIndexer'][] =
        \GuteBotschafter\GbEvents\Hooks\EventIndexer::class;
}
