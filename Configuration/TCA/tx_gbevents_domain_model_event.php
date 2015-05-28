<?php
if (!defined('TYPO3_MODE')) {
  die('Access denied.');
}

return array(
  'ctrl' => array(
    'title' => 'LLL:EXT:gb_events/Resources/Private/Language/locallang_db.xlf:tx_gbevents_domain_model_event',
    'label' => 'title',
    'tstamp' => 'tstamp',
    'crdate' => 'crdate',
    'dividers2tabs' => TRUE,
    'versioningWS' => 2,
    'versioning_followPages' => TRUE,
    'origUid' => 't3_origuid',
    'languageField' => 'sys_language_uid',
    'transOrigPointerField' => 'l10n_parent',
    'transOrigDiffSourceField' => 'l10n_diffsource',
    'delete' => 'deleted',
    'enablecolumns' => array(
      'disabled' => 'hidden',
      'starttime' => 'starttime',
      'endtime' => 'endtime',
    ) ,
    'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('gb_events') . 'Resources/Public/Icons/tx_gbevents_domain_model_event.gif'
  ) ,
  'interface' => array(
    'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, title, teaser, description, location, event_date, event_time, event_stop_date, images, downloads, recurring_weeks, recurring_days, recurring_stop, recurring_exclude_holidays, recurring_exclude_dates',
  ) ,
  'types' => array(
    '1' => array(
      'showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, title, teaser, description;;;richtext[]:rte_transform[mode=ts_css|imgpath=uploads/tx_gbevents/rte/], location, event_date, event_time, event_stop_date, images, downloads,--div--;LLL:EXT:gb_events/Resources/Private/Language/locallang_db.xlf:tx_gbevents_domain_model_event.recurring, recurring_weeks, recurring_days, recurring_stop, recurring_exclude_holidays, recurring_exclude_dates,--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.access,starttime, endtime'
    ) ,
  ) ,
  'palettes' => array(
    '1' => array(
      'showitem' => ''
    ) ,
  ) ,
  'columns' => array(
    'sys_language_uid' => array(
      'exclude' => 1,
      'label' => 'LLL:EXT:lang/locallang_general.php:LGL.language',
      'config' => array(
        'type' => 'select',
        'foreign_table' => 'sys_language',
        'foreign_table_where' => 'ORDER BY sys_language.title',
        'items' => array(
          array(
            'LLL:EXT:lang/locallang_general.php:LGL.allLanguages', -1
          ) ,
          array(
            'LLL:EXT:lang/locallang_general.php:LGL.default_value',
            0
          )
        ) ,
      ) ,
    ) ,
    'l10n_parent' => array(
      'displayCond' => 'FIELD:sys_language_uid:>:0',
      'exclude' => 1,
      'label' => 'LLL:EXT:lang/locallang_general.php:LGL.l18n_parent',
      'config' => array(
        'type' => 'select',
        'items' => array(
          array(
            '',
            0
          ) ,
        ) ,
        'foreign_table' => 'tx_gbevents_domain_model_event',
        'foreign_table_where' => 'AND tx_gbevents_domain_model_event.pid=###CURRENT_PID### AND tx_gbevents_domain_model_event.sys_language_uid IN (-1,0)',
      ) ,
    ) ,
    'l10n_diffsource' => array(
      'config' => array(
        'type' => 'passthrough',
      ) ,
    ) ,
    't3ver_label' => array(
      'displayCond' => 'FIELD:t3ver_label:REQ:true',
      'label' => 'LLL:EXT:lang/locallang_general.php:LGL.versionLabel',
      'config' => array(
        'type' => 'none',
        'cols' => 27,
      ) ,
    ) ,
    'hidden' => array(
      'exclude' => 1,
      'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
      'config' => array(
        'type' => 'check',
      ) ,
    ) ,
    'starttime' => array(
      'exclude' => 1,
      'l10n_mode' => 'mergeIfNotBlank',
      'label' => 'LLL:EXT:lang/locallang_general.php:LGL.starttime',
      'config' => array(
        'type' => 'input',
        'size' => '10',
        'max' => '20',
        'eval' => 'datetime',
        'checkbox' => '0',
        'default' => '0',
      ) ,
    ) ,
    'endtime' => array(
      'exclude' => 1,
      'l10n_mode' => 'mergeIfNotBlank',
      'label' => 'LLL:EXT:lang/locallang_general.php:LGL.endtime',
      'config' => array(
        'type' => 'input',
        'size' => '8',
        'max' => '20',
        'eval' => 'datetime',
        'checkbox' => '0',
        'default' => '0',
        'range' => array(
          'upper' => mktime(0, 0, 0, 12, 31, date('Y') + 10) ,
          'lower' => mktime(0, 0, 0, date('m') - 1, date('d') , date('Y'))
        ) ,
      ) ,
    ) ,
    'title' => array(
      'exclude' => 0,
      'label' => 'LLL:EXT:gb_events/Resources/Private/Language/locallang_db.xlf:tx_gbevents_domain_model_event.title',
      'config' => array(
        'type' => 'input',
        'size' => 30,
        'eval' => 'trim,required'
      ) ,
    ) ,
    'teaser' => array(
      'exclude' => 0,
      'label' => 'LLL:EXT:gb_events/Resources/Private/Language/locallang_db.xlf:tx_gbevents_domain_model_event.teaser',
      'config' => array(
        'type' => 'text',
        'cols' => 40,
        'rows' => 15,
        'eval' => 'trim'
      ) ,
    ) ,
    'description' => array(
      'exclude' => 0,
      'label' => 'LLL:EXT:gb_events/Resources/Private/Language/locallang_db.xlf:tx_gbevents_domain_model_event.description',
      'config' => array(
        'type' => 'text',
        'cols' => 40,
        'rows' => 15,
        'eval' => 'trim,required'
      ) ,
    ) ,
    'location' => array(
      'exclude' => 0,
      'label' => 'LLL:EXT:gb_events/Resources/Private/Language/locallang_db.xlf:tx_gbevents_domain_model_event.location',
      'config' => array(
        'type' => 'input',
        'size' => 30,
        'eval' => 'trim'
      ) ,
    ) ,
    'event_date' => array(
      'exclude' => 0,
      'label' => 'LLL:EXT:gb_events/Resources/Private/Language/locallang_db.xlf:tx_gbevents_domain_model_event.event_date',
      'config' => array(
        'type' => 'input',
        'size' => 12,
        'max' => 20,
        'eval' => 'date,required',
        'checkbox' => 1
      ) ,
    ) ,
    'event_time' => array(
      'exclude' => 0,
      'label' => 'LLL:EXT:gb_events/Resources/Private/Language/locallang_db.xlf:tx_gbevents_domain_model_event.event_time',
      'config' => array(
        'type' => 'input',
        'size' => 30,
        'eval' => 'trim'
      ) ,
    ) ,
    'event_stop_date' => array(
      'exclude' => 0,
      'label' => 'LLL:EXT:gb_events/Resources/Private/Language/locallang_db.xlf:tx_gbevents_domain_model_event.event_stop_date',
      'config' => array(
        'type' => 'input',
        'size' => 12,
        'max' => 20,
        'eval' => 'date',
        'checkbox' => 1
      ) ,
    ) ,
    'recurring_weeks' => array(
      'exclude' => 0,
      'label' => 'LLL:EXT:gb_events/Resources/Private/Language/locallang_db.xlf:tx_gbevents_domain_model_event.recurring_weeks',
      'config' => array(
        'type' => 'check',
        'items' => array(
          array(
            'LLL:EXT:gb_events/Resources/Private/Language/locallang_db.xlf:tx_gbevents_domain_model_event.recurring_weeks.0',
            ''
          ) ,
          array(
            'LLL:EXT:gb_events/Resources/Private/Language/locallang_db.xlf:tx_gbevents_domain_model_event.recurring_weeks.1',
            ''
          ) ,
          array(
            'LLL:EXT:gb_events/Resources/Private/Language/locallang_db.xlf:tx_gbevents_domain_model_event.recurring_weeks.2',
            ''
          ) ,
          array(
            'LLL:EXT:gb_events/Resources/Private/Language/locallang_db.xlf:tx_gbevents_domain_model_event.recurring_weeks.3',
            ''
          ) ,
          array(
            'LLL:EXT:gb_events/Resources/Private/Language/locallang_db.xlf:tx_gbevents_domain_model_event.recurring_weeks.4',
            ''
          ) ,
          array(
            'LLL:EXT:gb_events/Resources/Private/Language/locallang_db.xlf:tx_gbevents_domain_model_event.recurring_weeks.5',
            ''
          ) ,
        ) ,
        'suppress_icons' => 1,
      ) ,
    ) ,
    'recurring_days' => array(
      'exclude' => 0,
      'label' => 'LLL:EXT:gb_events/Resources/Private/Language/locallang_db.xlf:tx_gbevents_domain_model_event.recurring_days',
      'config' => array(
        'type' => 'check',
        'items' => array(
          array(
            'LLL:EXT:gb_events/Resources/Private/Language/locallang_db.xlf:tx_gbevents_domain_model_event.recurring_days.0',
            ''
          ) ,
          array(
            'LLL:EXT:gb_events/Resources/Private/Language/locallang_db.xlf:tx_gbevents_domain_model_event.recurring_days.1',
            ''
          ) ,
          array(
            'LLL:EXT:gb_events/Resources/Private/Language/locallang_db.xlf:tx_gbevents_domain_model_event.recurring_days.2',
            ''
          ) ,
          array(
            'LLL:EXT:gb_events/Resources/Private/Language/locallang_db.xlf:tx_gbevents_domain_model_event.recurring_days.3',
            ''
          ) ,
          array(
            'LLL:EXT:gb_events/Resources/Private/Language/locallang_db.xlf:tx_gbevents_domain_model_event.recurring_days.4',
            ''
          ) ,
          array(
            'LLL:EXT:gb_events/Resources/Private/Language/locallang_db.xlf:tx_gbevents_domain_model_event.recurring_days.5',
            ''
          ) ,
          array(
            'LLL:EXT:gb_events/Resources/Private/Language/locallang_db.xlf:tx_gbevents_domain_model_event.recurring_days.6',
            ''
          ) ,
        ) ,
        'suppress_icons' => 1,
      ) ,
    ) ,
    'recurring_stop' => array(
      'exclude' => 0,
      'label' => 'LLL:EXT:gb_events/Resources/Private/Language/locallang_db.xlf:tx_gbevents_domain_model_event.recurring_stop',
      'config' => array(
        'type' => 'input',
        'size' => 12,
        'max' => 20,
        'eval' => 'datetime',
        'checkbox' => 1,
      ) ,
    ) ,
    'recurring_exclude_holidays' => array(
      'exclude' => 0,
      'label' => 'LLL:EXT:gb_events/Resources/Private/Language/locallang_db.xlf:tx_gbevents_domain_model_event.recurring_exclude_holidays',
      'config' => array(
        'type' => 'check',
        'default' => 0,
      ) ,
    ) ,
    'recurring_exclude_dates' => array(
      'exclude' => 0,
      'label' => 'LLL:EXT:gb_events/Resources/Private/Language/locallang_db.xlf:tx_gbevents_domain_model_event.recurring_exclude_dates',
      'config' => array(
        'type' => 'text',
        'cols' => 40,
        'rows' => 15,
        'eval' => 'trim'
      ) ,
    ) ,
    'images' => array(
      'exclude' => 0,
      'label' => 'LLL:EXT:gb_events/Resources/Private/Language/locallang_db.xlf:tx_gbevents_domain_model_event.images',
      'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig("images")
    ),
    'downloads' => array(
      'exclude' => 0,
      'label' => 'LLL:EXT:gb_events/Resources/Private/Language/locallang_db.xlf:tx_gbevents_domain_model_event.downloads',
      'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig("downloads")
    ) ,
  ) ,
);
