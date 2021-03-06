<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'GuteBotschafter.GbEvents',
    'Main',
    [
        'Event' => 'list, calendar, upcoming, show, export',
        'Archive' => 'list',
        'Calendar' => 'show',
        'Export' => 'list, show',
    ],
    [
        'Event' => 'list, calendar, upcoming, show, export',
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
    ],
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

// Update scripts
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/install']['update']['gbevents_fal'] =
    \GuteBotschafter\GbEvents\Updates\FalUpdateWizard::class;
