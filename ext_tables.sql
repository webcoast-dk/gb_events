#
# Table structure for table 'tx_gbevents_domain_model_event'
#
CREATE TABLE tx_gbevents_domain_model_event (
	uid                        INT(11) UNSIGNED DEFAULT '0'    NOT NULL AUTO_INCREMENT,
	pid                        INT(11) DEFAULT '0'             NOT NULL,

	title                      VARCHAR(255) DEFAULT ''         NOT NULL,
	teaser                     TEXT                            NOT NULL,
	description                TEXT                            NOT NULL,
	location                   VARCHAR(255) DEFAULT ''         NOT NULL,
	event_date                 INT(11) DEFAULT '0'             NOT NULL,
	event_time                 VARCHAR(255) DEFAULT ''         NOT NULL,
	images                     TEXT,
	downloads                  TEXT,
	recurring_weeks            INT(11) DEFAULT '0'             NOT NULL,
	recurring_days             INT(11) DEFAULT '0'             NOT NULL,
	recurring_stop             INT(11) DEFAULT '0'             NOT NULL,
	recurring_exclude_holidays TINYINT(4) DEFAULT '0'          NOT NULL,
	recurring_exclude_dates    TEXT,

	event_stop_date            INT(11) DEFAULT '0'             NOT NULL,

	tstamp                     INT(11) UNSIGNED DEFAULT '0'    NOT NULL,
	crdate                     INT(11) UNSIGNED DEFAULT '0'    NOT NULL,
	deleted                    TINYINT(4) UNSIGNED DEFAULT '0' NOT NULL,
	hidden                     TINYINT(4) UNSIGNED DEFAULT '0' NOT NULL,
	starttime                  INT(11) UNSIGNED DEFAULT '0'    NOT NULL,
	endtime                    INT(11) UNSIGNED DEFAULT '0'    NOT NULL,

	t3ver_oid                  INT(11) DEFAULT '0'             NOT NULL,
	t3ver_id                   INT(11) DEFAULT '0'             NOT NULL,
	t3ver_wsid                 INT(11) DEFAULT '0'             NOT NULL,
	t3ver_label                VARCHAR(30) DEFAULT ''          NOT NULL,
	t3ver_state                TINYINT(4) DEFAULT '0'          NOT NULL,
	t3ver_stage                TINYINT(4) DEFAULT '0'          NOT NULL,
	t3ver_count                INT(11) DEFAULT '0'             NOT NULL,
	t3ver_tstamp               INT(11) DEFAULT '0'             NOT NULL,
	t3_origuid                 INT(11) DEFAULT '0'             NOT NULL,

	sys_language_uid           INT(11) DEFAULT '0'             NOT NULL,
	l10n_parent                INT(11) DEFAULT '0'             NOT NULL,
	l10n_diffsource            MEDIUMBLOB                      NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY t3ver_oid (t3ver_oid, t3ver_wsid)
);
