--
-- Connetation Elements – Database Schema
-- TYPO3 LTS 14
--

CREATE TABLE tt_content (
    tx_conn_elements_dashboard_projects   int(11) DEFAULT '0' NOT NULL,
    tx_conn_elements_dashboard_categories int(11) DEFAULT '0' NOT NULL,
    tx_conn_elements_dashboard_settings   mediumtext,
    tx_conn_elements_wiki_id              int(11) DEFAULT '0' NOT NULL,
    tx_conn_elements_wiki_settings        mediumtext
);

-- ─── Dashboard ────────────────────────────────────────────────────────────────

CREATE TABLE tx_conn_elements_domain_model_project (
    uid             int(11) NOT NULL auto_increment,
    pid             int(11) DEFAULT '0' NOT NULL,
    tstamp          int(11) DEFAULT '0' NOT NULL,
    crdate          int(11) DEFAULT '0' NOT NULL,
    deleted         tinyint(4) DEFAULT '0' NOT NULL,
    hidden          tinyint(4) DEFAULT '0' NOT NULL,
    sorting         int(11) DEFAULT '0' NOT NULL,
    content_element int(11) DEFAULT '0' NOT NULL,

    title           varchar(255) DEFAULT '' NOT NULL,
    description     text,
    status          varchar(50) DEFAULT 'active' NOT NULL,
    category        int(11) DEFAULT '0' NOT NULL,
    url             varchar(1024) DEFAULT '' NOT NULL,
    tags            varchar(255) DEFAULT '' NOT NULL,
    image           int(11) DEFAULT '0' NOT NULL,

    PRIMARY KEY (uid),
    KEY parent (pid)
);

CREATE TABLE tx_conn_elements_domain_model_projectcategory (
    uid             int(11) NOT NULL auto_increment,
    pid             int(11) DEFAULT '0' NOT NULL,
    tstamp          int(11) DEFAULT '0' NOT NULL,
    crdate          int(11) DEFAULT '0' NOT NULL,
    deleted         tinyint(4) DEFAULT '0' NOT NULL,
    hidden          tinyint(4) DEFAULT '0' NOT NULL,
    sorting         int(11) DEFAULT '0' NOT NULL,
    content_element int(11) DEFAULT '0' NOT NULL,

    title           varchar(255) DEFAULT '' NOT NULL,

    PRIMARY KEY (uid),
    KEY parent (pid)
);

-- ─── Wiki ─────────────────────────────────────────────────────────────────────

CREATE TABLE tx_conn_elements_domain_model_wiki (
    uid             int(11) NOT NULL auto_increment,
    pid             int(11) DEFAULT '0' NOT NULL,
    tstamp          int(11) DEFAULT '0' NOT NULL,
    crdate          int(11) DEFAULT '0' NOT NULL,
    deleted         tinyint(4) DEFAULT '0' NOT NULL,
    hidden          tinyint(4) DEFAULT '0' NOT NULL,
    sorting         int(11) DEFAULT '0' NOT NULL,

    title           varchar(255) DEFAULT '' NOT NULL,
    description     text,

    PRIMARY KEY (uid),
    KEY parent (pid)
);

CREATE TABLE tx_conn_elements_domain_model_wikipage (
    uid             int(11) NOT NULL auto_increment,
    pid             int(11) DEFAULT '0' NOT NULL,
    tstamp          int(11) DEFAULT '0' NOT NULL,
    crdate          int(11) DEFAULT '0' NOT NULL,
    deleted         tinyint(4) DEFAULT '0' NOT NULL,
    hidden          tinyint(4) DEFAULT '0' NOT NULL,
    sorting         int(11) DEFAULT '0' NOT NULL,
    wiki            int(11) DEFAULT '0' NOT NULL,

    title           varchar(255) DEFAULT '' NOT NULL,
    intro           text,
    category        int(11) DEFAULT '0' NOT NULL,
    tags            varchar(255) DEFAULT '' NOT NULL,
    blocks          int(11) DEFAULT '0' NOT NULL,

    PRIMARY KEY (uid),
    KEY parent (pid),
    KEY wiki (wiki)
);

CREATE TABLE tx_conn_elements_domain_model_wikipage_block (
    uid             int(11) NOT NULL auto_increment,
    pid             int(11) DEFAULT '0' NOT NULL,
    tstamp          int(11) DEFAULT '0' NOT NULL,
    crdate          int(11) DEFAULT '0' NOT NULL,
    deleted         tinyint(4) DEFAULT '0' NOT NULL,
    hidden          tinyint(4) DEFAULT '0' NOT NULL,
    sorting         int(11) DEFAULT '0' NOT NULL,
    page            int(11) DEFAULT '0' NOT NULL,

    type            varchar(20) DEFAULT 'text' NOT NULL,
    heading         varchar(255) DEFAULT '' NOT NULL,
    content         mediumtext,
    code_content    mediumtext,
    code_language   varchar(30) DEFAULT 'plaintext' NOT NULL,

    PRIMARY KEY (uid),
    KEY parent (pid),
    KEY page (page)
);

CREATE TABLE tx_conn_elements_domain_model_wikicategory (
    uid             int(11) NOT NULL auto_increment,
    pid             int(11) DEFAULT '0' NOT NULL,
    tstamp          int(11) DEFAULT '0' NOT NULL,
    crdate          int(11) DEFAULT '0' NOT NULL,
    deleted         tinyint(4) DEFAULT '0' NOT NULL,
    hidden          tinyint(4) DEFAULT '0' NOT NULL,
    sorting         int(11) DEFAULT '0' NOT NULL,
    wiki            int(11) DEFAULT '0' NOT NULL,

    title           varchar(255) DEFAULT '' NOT NULL,

    PRIMARY KEY (uid),
    KEY parent (pid)
);

CREATE TABLE tx_conn_elements_domain_model_wikitag (
    uid             int(11) NOT NULL auto_increment,
    pid             int(11) DEFAULT '0' NOT NULL,
    tstamp          int(11) DEFAULT '0' NOT NULL,
    crdate          int(11) DEFAULT '0' NOT NULL,
    deleted         tinyint(4) DEFAULT '0' NOT NULL,
    hidden          tinyint(4) DEFAULT '0' NOT NULL,
    sorting         int(11) DEFAULT '0' NOT NULL,
    wiki            int(11) DEFAULT '0' NOT NULL,

    title           varchar(255) DEFAULT '' NOT NULL,

    PRIMARY KEY (uid),
    KEY parent (pid)
);

CREATE TABLE tx_conn_elements_domain_model_wikipage_tag_mm (
    uid_local   int(11) DEFAULT '0' NOT NULL,
    uid_foreign int(11) DEFAULT '0' NOT NULL,
    sorting     int(11) DEFAULT '0' NOT NULL,

    KEY uid_local (uid_local),
    KEY uid_foreign (uid_foreign)
);
