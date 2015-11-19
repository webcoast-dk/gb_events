<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

return [
    'ctrl' => [
        'title' => 'LLL:EXT:gb_events/Resources/Private/Language/locallang_db.xlf:tx_gbevents_domain_model_event',
        'label' => 'title',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'dividers2tabs' => true,
        'versioningWS' => 2,
        'versioning_followPages' => true,
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
        'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('gb_events') . 'Resources/Public/Icons/tx_gbevents_domain_model_event.gif',
    ],
    'interface' => [
        'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, title, teaser, description, location, event_date, event_time, event_stop_date, images, downloads, recurring_weeks, recurring_days, recurring_stop, recurring_exclude_holidays, recurring_exclude_dates',
    ],
    'types' => [
        '1' => [
            'showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, title, teaser, description;;;richtext[]:rte_transform[mode=ts_css|imgpath=uploads/tx_gbevents/rte/], location, event_date, event_time, event_stop_date, images, downloads,--div--;LLL:EXT:gb_events/Resources/Private/Language/locallang_db.xlf:tx_gbevents_domain_model_event.recurring, recurring_weeks, recurring_days, recurring_stop, recurring_exclude_holidays, recurring_exclude_dates,--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.access,starttime, endtime',
        ],
    ],
    'palettes' => [
        '1' => [
            'showitem' => '',
        ],
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.php:LGL.language',
            'config' => [
                'type' => 'select',
                'foreign_table' => 'sys_language',
                'foreign_table_where' => 'ORDER BY sys_language.title',
                'items' => [
                    [
                        'LLL:EXT:lang/locallang_general.php:LGL.allLanguages',
                        -1,
                    ],
                    [
                        'LLL:EXT:lang/locallang_general.php:LGL.default_value',
                        0,
                    ],
                ],
            ],
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.php:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'items' => [
                    [
                        '',
                        0,
                    ],
                ],
                'foreign_table' => 'tx_gbevents_domain_model_event',
                'foreign_table_where' => 'AND tx_gbevents_domain_model_event.pid=###CURRENT_PID### AND tx_gbevents_domain_model_event.sys_language_uid IN (-1,0)',
            ],
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        't3ver_label' => [
            'displayCond' => 'FIELD:t3ver_label:REQ:true',
            'label' => 'LLL:EXT:lang/locallang_general.php:LGL.versionLabel',
            'config' => [
                'type' => 'none',
                'cols' => 27,
            ],
        ],
        'hidden' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
            'config' => [
                'type' => 'check',
            ],
        ],
        'starttime' => [
            'exclude' => 1,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:lang/locallang_general.php:LGL.starttime',
            'config' => [
                'type' => 'input',
                'size' => '10',
                'max' => '20',
                'eval' => 'datetime',
                'checkbox' => '0',
                'default' => '0',
            ],
        ],
        'endtime' => [
            'exclude' => 1,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:lang/locallang_general.php:LGL.endtime',
            'config' => [
                'type' => 'input',
                'size' => '8',
                'max' => '20',
                'eval' => 'datetime',
                'checkbox' => '0',
                'default' => '0',
                'range' => [
                    'upper' => mktime(0, 0, 0, 12, 31, date('Y') + 10),
                    'lower' => mktime(0, 0, 0, date('m') - 1, date('d'), date('Y')),
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
                'size' => 12,
                'max' => 20,
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
                'size' => 12,
                'max' => 20,
                'eval' => 'date',
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
                'size' => 12,
                'max' => 20,
                'eval' => 'datetime',
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
            'config' => [
                'type' => 'group',
                'internal_type' => 'file',
                'allowed' => $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'],
                'max_size' => $GLOBALS['TYPO3_CONF_VARS']['BE']['maxFileSize'],
                'uploadfolder' => 'uploads/tx_gbevents',
                'show_thumbs' => 1,
                'size' => 5,
                'minitems' => 0,
                'maxitems' => 5,
            ],
        ],
        'downloads' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:gb_events/Resources/Private/Language/locallang_db.xlf:tx_gbevents_domain_model_event.downloads',
            'config' => [
                'type' => 'group',
                'internal_type' => 'file',
                'allowed' => '',
                'disallowed' => 'php,php3,php4,php5,phps',
                'max_size' => $GLOBALS['TYPO3_CONF_VARS']['BE']['maxFileSize'],
                'uploadfolder' => 'uploads/tx_gbevents',
                'show_thumbs' => 0,
                'size' => 5,
                'minitems' => 0,
                'maxitems' => 5,
            ],
        ],
    ],
];
