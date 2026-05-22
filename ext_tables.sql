--
-- Connetation Elements – Database Schema
-- TYPO3 LTS 14
--

CREATE TABLE tt_content (
    tx_conn_elements_dashboard_projects   int(11) DEFAULT '0' NOT NULL,
    tx_conn_elements_dashboard_categories int(11) DEFAULT '0' NOT NULL,
    tx_conn_elements_dashboard_settings   mediumtext,
    tx_conn_elements_wiki_id              int(11) DEFAULT '0' NOT NULL,
    tx_conn_elements_wiki_settings        mediumtext,
    tx_conn_elements_accordion_items      int(11) DEFAULT '0' NOT NULL,
    tx_conn_elements_accordion_settings   mediumtext,
    tx_conn_elements_card_group_items     int(11) DEFAULT '0' NOT NULL,
    tx_conn_elements_card_group_settings  mediumtext,
    tx_conn_elements_hero_slider_items    int(11) DEFAULT '0' NOT NULL,
    tx_conn_elements_hero_slider_settings mediumtext,
    tx_conn_elements_slider_items              int(11) DEFAULT '0' NOT NULL,
    tx_conn_elements_slider_settings           mediumtext,
    tx_conn_elements_thumbs_slider_items       int(11) DEFAULT '0' NOT NULL,
    tx_conn_elements_thumbs_slider_settings    mediumtext,
    tx_conn_elements_parallax_slider_items     int(11) DEFAULT '0' NOT NULL,
    tx_conn_elements_parallax_slider_settings  mediumtext,
    tx_conn_elements_parallax_slider_bg_image  int(11) DEFAULT '0' NOT NULL,
    tx_conn_elements_text_animation            varchar(30) DEFAULT '' NOT NULL,
    tx_conn_elements_card_stack_items          int(11) DEFAULT '0' NOT NULL,
    tx_conn_elements_card_stack_settings       mediumtext,
    tx_conn_elements_flip_carousel_items       int(11) DEFAULT '0' NOT NULL,
    tx_conn_elements_flip_carousel_settings    mediumtext,
    tx_conn_elements_flip_gallery_items        int(11) DEFAULT '0' NOT NULL,
    tx_conn_elements_flip_gallery_settings     mediumtext,
    tx_conn_elements_carousel_items    int(11) DEFAULT '0' NOT NULL,
    tx_conn_elements_carousel_settings mediumtext
);

-- ─── Dashboard ────────────────────────────────────────────────────────────────

CREATE TABLE tx_conn_elements_domain_model_dashboard_project (
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

CREATE TABLE tx_conn_elements_domain_model_dashboard_projectcategory (
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
    code_text       mediumtext,
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

-- ─── Bootstrap Accordion ──────────────────────────────────────────────────────

CREATE TABLE tx_conn_elements_domain_model_accordion_item (
    uid             int(11) NOT NULL auto_increment,
    pid             int(11) DEFAULT '0' NOT NULL,
    tstamp          int(11) DEFAULT '0' NOT NULL,
    crdate          int(11) DEFAULT '0' NOT NULL,
    deleted         tinyint(4) DEFAULT '0' NOT NULL,
    hidden          tinyint(4) DEFAULT '0' NOT NULL,
    sorting         int(11) DEFAULT '0' NOT NULL,
    content_element int(11) DEFAULT '0' NOT NULL,

    title           varchar(255) DEFAULT '' NOT NULL,
    body            mediumtext,

    PRIMARY KEY (uid),
    KEY parent (pid),
    KEY content_element (content_element)
);

-- ─── Bootstrap Card Group ────────────────────────────────────────────────────

CREATE TABLE tx_conn_elements_domain_model_card_item (
    uid             int(11) NOT NULL auto_increment,
    pid             int(11) DEFAULT '0' NOT NULL,
    tstamp          int(11) DEFAULT '0' NOT NULL,
    crdate          int(11) DEFAULT '0' NOT NULL,
    deleted         tinyint(4) DEFAULT '0' NOT NULL,
    hidden          tinyint(4) DEFAULT '0' NOT NULL,
    sorting         int(11) DEFAULT '0' NOT NULL,
    content_element int(11) DEFAULT '0' NOT NULL,

    title           varchar(255) DEFAULT '' NOT NULL,
    subtitle        varchar(255) DEFAULT '' NOT NULL,
    badge           varchar(60) DEFAULT '' NOT NULL,
    body            mediumtext,
    image           int(11) DEFAULT '0' NOT NULL,
    link            varchar(1024) DEFAULT '' NOT NULL,
    link_label      varchar(100) DEFAULT '' NOT NULL,

    PRIMARY KEY (uid),
    KEY parent (pid),
    KEY content_element (content_element)
);

-- ─── Sliders ──────────────────────────────────────────────────────────────────

CREATE TABLE tx_conn_elements_domain_model_slide_item (
    uid             int(11) NOT NULL auto_increment,
    pid             int(11) DEFAULT '0' NOT NULL,
    tstamp          int(11) DEFAULT '0' NOT NULL,
    crdate          int(11) DEFAULT '0' NOT NULL,
    deleted         tinyint(4) DEFAULT '0' NOT NULL,
    hidden          tinyint(4) DEFAULT '0' NOT NULL,
    sorting         int(11) DEFAULT '0' NOT NULL,
    content_element int(11) DEFAULT '0' NOT NULL,

    title           varchar(255) DEFAULT '' NOT NULL,
    subtitle        varchar(255) DEFAULT '' NOT NULL,
    body            mediumtext,
    image           int(11) DEFAULT '0' NOT NULL,
    link            varchar(1024) DEFAULT '' NOT NULL,
    link_label      varchar(100) DEFAULT '' NOT NULL,

    PRIMARY KEY (uid),
    KEY parent (pid),
    KEY content_element (content_element)
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

-- ─── GSAP Card Stack ──────────────────────────────────────────────────────────

CREATE TABLE tx_conn_elements_domain_model_stack_card (
    uid             int(11) NOT NULL auto_increment,
    pid             int(11) DEFAULT '0' NOT NULL,
    tstamp          int(11) DEFAULT '0' NOT NULL,
    crdate          int(11) DEFAULT '0' NOT NULL,
    deleted         tinyint(4) DEFAULT '0' NOT NULL,
    hidden          tinyint(4) DEFAULT '0' NOT NULL,
    sorting         int(11) DEFAULT '0' NOT NULL,
    content_element int(11) DEFAULT '0' NOT NULL,

    title           varchar(255) DEFAULT '' NOT NULL,
    subtitle        varchar(100) DEFAULT '' NOT NULL,
    body            mediumtext,
    image           int(11) DEFAULT '0' NOT NULL,

    PRIMARY KEY (uid),
    KEY parent (pid),
    KEY content_element (content_element)
);

-- ─── GSAP Flip Carousel ───────────────────────────────────────────────────────

CREATE TABLE tx_conn_elements_domain_model_flip_carousel_item (
    uid             int(11) NOT NULL auto_increment,
    pid             int(11) DEFAULT '0' NOT NULL,
    tstamp          int(11) DEFAULT '0' NOT NULL,
    crdate          int(11) DEFAULT '0' NOT NULL,
    deleted         tinyint(4) DEFAULT '0' NOT NULL,
    hidden          tinyint(4) DEFAULT '0' NOT NULL,
    sorting         int(11) DEFAULT '0' NOT NULL,
    content_element int(11) DEFAULT '0' NOT NULL,

    title           varchar(255) DEFAULT '' NOT NULL,
    subtitle        varchar(100) DEFAULT '' NOT NULL,
    category        varchar(100) DEFAULT '' NOT NULL,
    body            mediumtext,
    image           int(11) DEFAULT '0' NOT NULL,
    link            varchar(1024) DEFAULT '' NOT NULL,
    link_label      varchar(100) DEFAULT '' NOT NULL,

    PRIMARY KEY (uid),
    KEY parent (pid),
    KEY content_element (content_element)
);

-- ─── Bootstrap Carousel ───────────────────────────────────────────────────────

CREATE TABLE tx_conn_elements_domain_model_carousel_slide (
    uid             int(11) NOT NULL auto_increment,
    pid             int(11) DEFAULT '0' NOT NULL,
    tstamp          int(11) DEFAULT '0' NOT NULL,
    crdate          int(11) DEFAULT '0' NOT NULL,
    deleted         tinyint(4) DEFAULT '0' NOT NULL,
    hidden          tinyint(4) DEFAULT '0' NOT NULL,
    sorting         int(11) DEFAULT '0' NOT NULL,
    content_element int(11) DEFAULT '0' NOT NULL,

    title           varchar(255) DEFAULT '' NOT NULL,
    caption         mediumtext,
    image           int(11) DEFAULT '0' NOT NULL,
    link            varchar(1024) DEFAULT '' NOT NULL,
    link_label      varchar(100) DEFAULT '' NOT NULL,

    PRIMARY KEY (uid),
    KEY parent (pid),
    KEY content_element (content_element)
);

-- ─── GSAP Flip Gallery ────────────────────────────────────────────────────────

CREATE TABLE tx_conn_elements_domain_model_gallery_item (
    uid             int(11) NOT NULL auto_increment,
    pid             int(11) DEFAULT '0' NOT NULL,
    tstamp          int(11) DEFAULT '0' NOT NULL,
    crdate          int(11) DEFAULT '0' NOT NULL,
    deleted         tinyint(4) DEFAULT '0' NOT NULL,
    hidden          tinyint(4) DEFAULT '0' NOT NULL,
    sorting         int(11) DEFAULT '0' NOT NULL,
    content_element int(11) DEFAULT '0' NOT NULL,

    title           varchar(255) DEFAULT '' NOT NULL,
    caption         mediumtext,
    image           int(11) DEFAULT '0' NOT NULL,

    PRIMARY KEY (uid),
    KEY parent (pid),
    KEY content_element (content_element)
);
