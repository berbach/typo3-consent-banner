#
# Table structure for table 'tt_content'
#
CREATE TABLE tt_content
(
    ce_consent_component varchar(255) DEFAULT '' NOT NULL
);
#
# Table structure for table 'tx_consentbanner_domain_model_banner'
#
CREATE TABLE tx_consentbanner_domain_model_banner
(
    banner_id                       varchar(30)       DEFAULT ''          NOT NULL,
    banner_hash                     varchar(30)       DEFAULT ''          NOT NULL,

    banner_title                    varchar(255)      DEFAULT ''          NOT NULL,
    banner_description              text,
    banner_layout                   varchar(20)       DEFAULT 'cb-bottom' NOT NULL,
    banner_navigation               longtext          DEFAULT NULL,
    banner_version                  int(11)           DEFAULT '1'         NOT NULL,

    gtm_container_id                varchar(30)       DEFAULT ''          NOT NULL,
    matomo_url                      varchar(255)      DEFAULT ''          NOT NULL,
    matomo_site_id                  varchar(10)       DEFAULT ''          NOT NULL,
    matomo_mtm_url                  varchar(255)      DEFAULT ''          NOT NULL,

    user_identification_text        varchar(30)       DEFAULT ''          NOT NULL,
    provider_description_text       varchar(30)       DEFAULT ''          NOT NULL,

    accept_all_text                 varchar(30)       DEFAULT ''          NOT NULL,
    confirm_selection_text          varchar(30)       DEFAULT ''          NOT NULL,
    save_and_close_text             varchar(30)       DEFAULT ''          NOT NULL,
    advanced_settings_text          varchar(30)       DEFAULT ''          NOT NULL,
    accept_essential_text           varchar(30)       DEFAULT ''          NOT NULL,

    cookie_infos_show_text          varchar(30)       DEFAULT ''          NOT NULL,
    cookie_infos_close_text         varchar(30)       DEFAULT ''          NOT NULL,

    cookie_name_text                varchar(30)       DEFAULT ''          NOT NULL,
    cookie_lifetime_text            varchar(30)       DEFAULT ''          NOT NULL,
    cookie_provider_text            varchar(30)       DEFAULT ''          NOT NULL,
    cookie_purpose_text             varchar(30)       DEFAULT ''          NOT NULL,
    cookie_description_text         varchar(30)       DEFAULT ''          NOT NULL,

    essential_group_id              varchar(30)       DEFAULT ''          NOT NULL,
    essential_group_hash            varchar(30)       DEFAULT ''          NOT NULL,

    essential_title                 varchar(150)      DEFAULT ''          NOT NULL,
    essential_description           text,
    essential_components            int(11) unsigned  DEFAULT '0'         NOT NULL,

    consent_other_groups            int(11) unsigned  DEFAULT '0'         NOT NULL,

    privacy_settings_variant        int(11) unsigned  DEFAULT '10'        NOT NULL,
    button_widget_position          varchar(30)       DEFAULT 'left'      NOT NULL,
    button_widget_text              varchar(30)       DEFAULT ''          NOT NULL,
    text_link_position              varchar(30)       DEFAULT 'last'      NOT NULL,
    text_link_text                  varchar(30)       DEFAULT ''          NOT NULL,
    target_footer_navigation        varchar(150)      DEFAULT ''          NOT NULL,

    lifetime_banner                 int(11) unsigned  DEFAULT '14'        NOT NULL,
    lifetime_user_consent           int(11) unsigned  DEFAULT '1095'      NOT NULL,

    deleted                         smallint unsigned DEFAULT '0'         NOT NULL,
    hidden                          smallint unsigned DEFAULT '0'         NOT NULL

);

#
# Table structure for table 'tx_consentbanner_domain_model_consent_groups'
#
CREATE TABLE tx_consentbanner_domain_model_consent_groups
(
    group_id              varchar(30)          DEFAULT ''  NOT NULL,
    group_hash            varchar(30)          DEFAULT ''  NOT NULL,

    group_title           varchar(255)         DEFAULT ''  NOT NULL,
    group_description     text,
    group_components      int(11) unsigned     DEFAULT '0' NOT NULL,

    sorting_foreign       int(11)              DEFAULT '0',
    banner_id             int(11) unsigned     DEFAULT NULL,

    deleted               smallint unsigned    DEFAULT '0' NOT NULL,
    hidden                smallint unsigned    DEFAULT '0' NOT NULL

);
#
# Table structure for table 'tx_consentbanner_domain_model_consent_components'
#
CREATE TABLE tx_consentbanner_domain_model_consent_components
(
    component_id                varchar(30)          DEFAULT ''  NOT NULL,
    component_hash              varchar(30)          DEFAULT ''  NOT NULL,

    component_title             varchar(255)         DEFAULT ''  NOT NULL,
    component_description       text,

    component_ce_target         varchar(255)         DEFAULT ''  NOT NULL,

    integration_type            varchar(30)          DEFAULT 'iframe' NOT NULL,
    consent_mode_signals        varchar(255)         DEFAULT ''  NOT NULL,

    service_name                varchar(255)         DEFAULT ''  NOT NULL,
    service_provider            varchar(255)         DEFAULT ''  NOT NULL,
    service_privacy_link        varchar(1024)        DEFAULT ''  NOT NULL,
    service_publisher           varchar(255)         DEFAULT ''  NOT NULL,
    service_data_collected      text,
    -- child count of the inline cookies relation; required so Extbase's
    -- DataMapper thaws the HAS_MANY relation (it skips columns absent from the row).
    service_cookies             int(11) unsigned     DEFAULT '0' NOT NULL,

    placeholder_title           varchar(255),
    placeholder_description     text,

    accepted_script             text,
    rejected_script             text,

    sorting_foreign             int(11)              DEFAULT '0',
    group_id                    int(11)  unsigned    DEFAULT NULL,
    group_parent                varchar(30)          DEFAULT '' NOT NULL,
    hidden                      smallint unsigned DEFAULT '0' NOT NULL,
    deleted                     smallint unsigned DEFAULT '0' NOT NULL
);

#
# Table structure for table 'tx_consentbanner_domain_model_cookie'
# -- Cookies belonging to a consent component (inline / IRRE child records)
#
CREATE TABLE tx_consentbanner_domain_model_cookie
(
    cookie_name                 varchar(255)         DEFAULT '' NOT NULL,
    cookie_type                 varchar(60)          DEFAULT '' NOT NULL,
    cookie_provider             varchar(255)         DEFAULT '' NOT NULL,
    cookie_description          text,
    cookie_purpose              text,
    cookie_lifetime             varchar(255)         DEFAULT '' NOT NULL,

    component                   int(11) unsigned     DEFAULT '0' NOT NULL,
    sorting_foreign             int(11)              DEFAULT '0' NOT NULL,
    hidden                      smallint unsigned    DEFAULT '0' NOT NULL,
    deleted                     smallint unsigned    DEFAULT '0' NOT NULL
);

#
# Table structure for table 'tx_consentbanner_domain_model_consent_log'
# -- Define table and fields since it has no TCA
CREATE TABLE tx_consentbanner_domain_model_consent_log
(
    identification_key          char(64) DEFAULT '' NOT NULL,
    root_page_id                int(11) unsigned DEFAULT '0' NOT NULL,
    banner_version              int(11) NOT NULL,
    consent_services            json,

    tstamp                      int(11) unsigned DEFAULT '0' NOT NULL,
    crdate                      int(11) DEFAULT '0' NOT NULL,
    PRIMARY KEY (identification_key, root_page_id)
);
-- #
-- # Table structure for table 'tx_consentbanner_module_categories_mm'
-- #
-- CREATE TABLE tx_consentbanner_module_categories_mm
-- (
--     uid_local       int(11) unsigned DEFAULT '0' NOT NULL,
--     uid_foreign     int(11) unsigned DEFAULT '0' NOT NULL,
--     sorting         int(11) unsigned DEFAULT '0' NOT NULL,
--     sorting_foreign int(11) unsigned DEFAULT '0' NOT NULL,
--
--     KEY uid_local (uid_local),
--     KEY uid_foreign (uid_foreign)
-- );
-- #
-- # Table structure for table 'tx_consentbanner_module_categories_mm'
-- #
-- CREATE TABLE tx_consentbanner_categories_banner_mm
-- (
--     uid_local       int(11) unsigned DEFAULT '0' NOT NULL,
--     uid_foreign     int(11) unsigned DEFAULT '0' NOT NULL,
--     sorting         int(11) unsigned DEFAULT '0' NOT NULL,
--     sorting_foreign int(11) unsigned DEFAULT '0' NOT NULL,
--
--     KEY uid_local (uid_local),
--     KEY uid_foreign (uid_foreign)
-- );
