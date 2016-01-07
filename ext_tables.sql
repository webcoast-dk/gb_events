#
# Table structure for table 'tx_gbevents_domain_model_event'
#
CREATE TABLE tx_gbevents_domain_model_event (
  uid                        int(11) unsigned default '0'    NOT NULL auto_increment,
  pid                        int(11) default '0'             NOT NULL,

  title                      varchar(255) default ''         NOT NULL,
  teaser                     text                            NOT NULL,
  description                text                            NOT NULL,
  location                   varchar(255) default ''         NOT NULL,
  event_date                 int(11) default '0'             NOT NULL,
  event_time                 varchar(255) default ''         NOT NULL,
  images                     text,
  downloads                  text,
  recurring_weeks            int(11) default '0'             NOT NULL,
  recurring_days             int(11) default '0'             NOT NULL,
  recurring_stop             int(11) default '0'             NOT NULL,
  recurring_exclude_holidays tinyint(4) default '0'          NOT NULL,
  recurring_exclude_dates    text,

  event_stop_date            int(11) default '0'             NOT NULL,

  tstamp                     int(11) unsigned default '0'    NOT NULL,
  crdate                     int(11) unsigned default '0'    NOT NULL,
  deleted                    tinyint(4) unsigned default '0' NOT NULL,
  hidden                     tinyint(4) unsigned default '0' NOT NULL,
  starttime                  int(11) unsigned default '0'    NOT NULL,
  endtime                    int(11) unsigned default '0'    NOT NULL,

  t3ver_oid                  int(11) default '0'             NOT NULL,
  t3ver_id                   int(11) default '0'             NOT NULL,
  t3ver_wsid                 int(11) default '0'             NOT NULL,
  t3ver_label                varchar(30) default ''          NOT NULL,
  t3ver_state                tinyint(4) default '0'          NOT NULL,
  t3ver_stage                tinyint(4) default '0'          NOT NULL,
  t3ver_count                int(11) default '0'             NOT NULL,
  t3ver_tstamp               int(11) default '0'             NOT NULL,
  t3_origuid                 int(11) default '0'             NOT NULL,

  sys_language_uid           int(11) default '0'             NOT NULL,
  l10n_parent                int(11) default '0'             NOT NULL,
  l10n_diffsource            mediumblob                      NOT NULL,

  PRIMARY KEY (uid),
  KEY parent (pid),
  KEY t3ver_oid (t3ver_oid,t3ver_wsid)
);
