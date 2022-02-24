<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

ExtensionManagementUtility::makeCategorizable(
    'gb_events',
    'tx_gbevents_domain_model_event',
    'categories',
    []
);

return [
    'ctrl' => [
        'title' => 'LLL:EXT:gb_events/Resources/Private/Language/locallang_db.xlf:tx_gbevents_domain_model_event',
        'label' => 'title',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'dividers2tabs' => true,
        'versioningWS' => true,
        'origUid' => 't3_origuid',
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'iconfile' => 'EXT:gb_events/Resources/Public/Icons/tx_gbevents_domain_model_event.gif',
        'searchFields' => 'title,teaser,description,location'
    ],
    'interface' => [
        'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, title, teaser, description, location, event_date, event_time, event_stop_date, images, downloads, recurring_weeks, recurring_days, recurring_stop, recurring_exclude_holidays, recurring_exclude_dates',
    ],
    'types' => [
        '1' => [
            'showitem' => 'title, url_segment,teaser,description,location,event_date,event_time,event_stop_date,images,downloads,--div--;LLL:EXT:gb_events/Resources/Private/Language/locallang_db.xlf:tx_gbevents_domain_model_event.recurring,recurring_weeks,recurring_days,recurring_stop,recurring_exclude_holidays,recurring_exclude_dates,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language, sys_language_uid,--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xml:tabs.access, hidden, --palette--;;access_times',
        ],
    ],
    'palettes' => [
        'access_times' => [
            'showitem' => 'starttime, endtime',
        ],
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'special' => 'languages',
            ],
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude' => 1,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'tx_gbevents_domain_model_event',
                'foreign_table_where' => 'AND tx_gbevents_domain_model_event.pid=###CURRENT_PID### AND tx_gbevents_domain_model_event.sys_language_uid IN (-1,0)',
                'default' => 0,
            ],
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'hidden' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle'
            ],
        ],
        'starttime' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'size' => '10',
                'eval' => 'datetime',
                'checkbox' => '0',
                'default' => '0',
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ],
        ],
        'endtime' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'size' => '8',
                'eval' => 'datetime',
                'checkbox' => '0',
                'default' => '0',
                'range' => [
                    'upper' => mktime(0, 0, 0, 12, 31, date('Y') + 10),
                    'lower' => mktime(0, 0, 0, date('m') - 1, date('d'), date('Y')),
                ],
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ],
        ],
        'title' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:gb_events/Resources/Private/Language/locallang_db.xlf:tx_gbevents_domain_model_event.title',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required',
            ],
        ],
        'teaser' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:gb_events/Resources/Private/Language/locallang_db.xlf:tx_gbevents_domain_model_event.teaser',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim',
            ],
        ],
        'description' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:gb_events/Resources/Private/Language/locallang_db.xlf:tx_gbevents_domain_model_event.description',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim,required',
                'enableRichtext' => true,
            ],
        ],
        'location' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:gb_events/Resources/Private/Language/locallang_db.xlf:tx_gbevents_domain_model_event.location',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'event_date' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:gb_events/Resources/Private/Language/locallang_db.xlf:tx_gbevents_domain_model_event.event_date',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'size' => 12,
                'eval' => 'date,required',
                'checkbox' => 1,
            ],
        ],
        'event_time' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:gb_events/Resources/Private/Language/locallang_db.xlf:tx_gbevents_domain_model_event.event_time',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'event_stop_date' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:gb_events/Resources/Private/Language/locallang_db.xlf:tx_gbevents_domain_model_event.event_stop_date',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'size' => 12,
                'eval' => 'date,int',
                'checkbox' => 1,
            ],
        ],
        'recurring_weeks' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:gb_events/Resources/Private/Language/locallang_db.xlf:tx_gbevents_domain_model_event.recurring_weeks',
            'config' => [
                'type' => 'check',
                'items' => [
                    [
                        'LLL:EXT:gb_events/Resources/Private/Language/locallang_db.xlf:tx_gbevents_domain_model_event.recurring_weeks.0',
                        '',
                    ],
                    [
                        'LLL:EXT:gb_events/Resources/Private/Language/locallang_db.xlf:tx_gbevents_domain_model_event.recurring_weeks.1',
                        '',
                    ],
                    [
                        'LLL:EXT:gb_events/Resources/Private/Language/locallang_db.xlf:tx_gbevents_domain_model_event.recurring_weeks.2',
                        '',
                    ],
                    [
                        'LLL:EXT:gb_events/Resources/Private/Language/locallang_db.xlf:tx_gbevents_domain_model_event.recurring_weeks.3',
                        '',
                    ],
                    [
                        'LLL:EXT:gb_events/Resources/Private/Language/locallang_db.xlf:tx_gbevents_domain_model_event.recurring_weeks.4',
                        '',
                    ],
                    [
                        'LLL:EXT:gb_events/Resources/Private/Language/locallang_db.xlf:tx_gbevents_domain_model_event.recurring_weeks.5',
                        '',
                    ],
                ],
                'suppress_icons' => 1,
            ],
        ],
        'recurring_days' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:gb_events/Resources/Private/Language/locallang_db.xlf:tx_gbevents_domain_model_event.recurring_days',
            'config' => [
                'type' => 'check',
                'items' => [
                    [
                        'LLL:EXT:gb_events/Resources/Private/Language/locallang_db.xlf:tx_gbevents_domain_model_event.recurring_days.0',
                        '',
                    ],
                    [
                        'LLL:EXT:gb_events/Resources/Private/Language/locallang_db.xlf:tx_gbevents_domain_model_event.recurring_days.1',
                        '',
                    ],
                    [
                        'LLL:EXT:gb_events/Resources/Private/Language/locallang_db.xlf:tx_gbevents_domain_model_event.recurring_days.2',
                        '',
                    ],
                    [
                        'LLL:EXT:gb_events/Resources/Private/Language/locallang_db.xlf:tx_gbevents_domain_model_event.recurring_days.3',
                        '',
                    ],
                    [
                        'LLL:EXT:gb_events/Resources/Private/Language/locallang_db.xlf:tx_gbevents_domain_model_event.recurring_days.4',
                        '',
                    ],
                    [
                        'LLL:EXT:gb_events/Resources/Private/Language/locallang_db.xlf:tx_gbevents_domain_model_event.recurring_days.5',
                        '',
                    ],
                    [
                        'LLL:EXT:gb_events/Resources/Private/Language/locallang_db.xlf:tx_gbevents_domain_model_event.recurring_days.6',
                        '',
                    ],
                ],
                'suppress_icons' => 1,
            ],
        ],
        'recurring_stop' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:gb_events/Resources/Private/Language/locallang_db.xlf:tx_gbevents_domain_model_event.recurring_stop',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'size' => 12,
                'eval' => 'datetime,int',
                'checkbox' => 1,
            ],
        ],
        'recurring_exclude_holidays' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:gb_events/Resources/Private/Language/locallang_db.xlf:tx_gbevents_domain_model_event.recurring_exclude_holidays',
            'config' => [
                'type' => 'check',
                'default' => 0,
            ],
        ],
        'recurring_exclude_dates' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:gb_events/Resources/Private/Language/locallang_db.xlf:tx_gbevents_domain_model_event.recurring_exclude_dates',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim',
            ],
        ],
        'images' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:gb_events/Resources/Private/Language/locallang_db.xlf:tx_gbevents_domain_model_event.images',
            'config' => ExtensionManagementUtility::getFileFieldTCAConfig("images"),
        ],
        'downloads' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:gb_events/Resources/Private/Language/locallang_db.xlf:tx_gbevents_domain_model_event.downloads',
            'config' => ExtensionManagementUtility::getFileFieldTCAConfig("downloads"),
        ],
        'url_segment' => [
            'label' => 'LLL:EXT:gb_events/Resources/Private/Language/locallang_db.xlf:tx_gbevents_domain_model_event.url_segment',
            'displayCond' => 'VERSION:IS:false',
            'config' => [
                'type' => 'slug',
                'size' => 50,
                'generatorOptions' => [
                    'fields' => ['title'],
                    'replacements' => [
                        '/' => '-'
                    ],
                ],
                'fallbackCharacter' => '-',
                'eval' => 'uniqueInSite',
                'default' => ''
            ]
        ],
    ],
];
