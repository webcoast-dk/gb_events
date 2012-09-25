<?php
if (!defined ('TYPO3_MODE')) {
  die ('Access denied.');
}

Tx_Extbase_Utility_Extension::configurePlugin(
  $_EXTKEY,
  'Main',
  array(
    'Event' => 'list, calendar, upcoming, show',
  ),
  array(
    'Event' => 'list, calendar, upcoming, show',
  )
);

# ke_search indexer
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ke_search']['customIndexer'][] = 'EXT:gb_events/Classes/Hooks/EventIndexer.php:user_gbevents_kesearch_event';
