-- -----------------------------------------------------
-- Table tx_fecookies_domain_model_block
-- -----------------------------------------------------
CREATE TABLE tx_fecookies_domain_model_block (
    uid int(11) unsigned NOT NULL auto_increment,
    pid int(11) DEFAULT '0' NOT NULL,
    crdate int(11) unsigned DEFAULT '0' NOT NULL,
    cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
    tstamp int(11) unsigned DEFAULT '0' NOT NULL,
    deleted smallint(5) unsigned DEFAULT '0' NOT NULL,
    editlock tinyint(4) unsigned DEFAULT '0' NOT NULL,
    sorting int(11) unsigned DEFAULT '0' NOT NULL,

    hidden smallint(5) unsigned DEFAULT '0' NOT NULL,
    starttime int(11) unsigned DEFAULT '0' NOT NULL,
    endtime int(11) unsigned DEFAULT '0' NOT NULL,
    fe_group varchar(100) DEFAULT '0' NOT NULL,

    sys_language_uid int(11) DEFAULT '0' NOT NULL,
    l18n_parent int(11) DEFAULT '0' NOT NULL,
    l18n_diffsource mediumblob,
    l10n_source int(11) DEFAULT '0' NOT NULL,

    type varchar(255) NOT NULL DEFAULT '',
    title varchar(255) NOT NULL DEFAULT '',
    bodytext mediumtext,

    PRIMARY KEY (uid),
    KEY parent (pid,sorting),
    KEY language (l18n_parent,sys_language_uid)
);
