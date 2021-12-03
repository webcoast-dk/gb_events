CREATE TABLE tx_gbevents_domain_model_event
(
    title                      VARCHAR(255) DEFAULT '' NOT NULL,
    teaser                     TEXT                    NOT NULL,
    description                TEXT                    NOT NULL,
    location                   VARCHAR(255) DEFAULT '' NOT NULL,
    event_date                 INT(11) DEFAULT '0' NOT NULL,
    event_time                 VARCHAR(255) DEFAULT '' NOT NULL,
    images                     TEXT,
    downloads                  TEXT,
    recurring_weeks            INT(11) DEFAULT '0' NOT NULL,
    recurring_days             INT(11) DEFAULT '0' NOT NULL,
    recurring_stop             INT(11) DEFAULT '0' NOT NULL,
    recurring_exclude_holidays TINYINT(4) DEFAULT '0' NOT NULL,
    recurring_exclude_dates    TEXT,

    event_stop_date            INT(11) DEFAULT '0' NOT NULL,
    url_segment                VARCHAR(255) DEFAULT '' NOT NULL
);
