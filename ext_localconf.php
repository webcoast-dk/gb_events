<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'GuteBotschafter.GbEvents',
    'Main',
    [
        \GuteBotschafter\GbEvents\Controller\EventController::class => 'list, show',
        \GuteBotschafter\GbEvents\Controller\ArchiveController::class => 'list',
        \GuteBotschafter\GbEvents\Controller\CalendarController::class => 'show',
        \GuteBotschafter\GbEvents\Controller\ExportController::class => 'list, show',
    ]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'GuteBotschafter.GbEvents',
    'Upcoming',
    [
        \GuteBotschafter\GbEvents\Controller\UpcomingController::class => 'list',
    ]
);

// ke_search indexer
if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('ke_search')) {
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ke_search']['registerIndexerConfiguration'][] =
        'EXT:gb_events/Classes/Hooks/EventIndexer.php:' . \GuteBotschafter\GbEvents\Hooks\EventIndexer::class;
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ke_search']['customIndexer'][] =
        \GuteBotschafter\GbEvents\Hooks\EventIndexer::class;
}

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/install']['update'][\GuteBotschafter\GbEvents\Update\EventsSlugUpdater::class] = \GuteBotschafter\GbEvents\Update\EventsSlugUpdater::class;
