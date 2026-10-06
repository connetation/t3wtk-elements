--
-- Connetation Elements – Database Schema
-- TYPO3 LTS 14
--

CREATE TABLE tt_content (
    tx_conn_t3wtk_elements_accordion_items      int(11) DEFAULT '0' NOT NULL,
    tx_conn_t3wtk_elements_accordion_settings   mediumtext,
    tx_conn_t3wtk_elements_card_group_items     int(11) DEFAULT '0' NOT NULL,
    tx_conn_t3wtk_elements_card_group_settings  mediumtext,
    tx_conn_t3wtk_elements_subpage_cards_source   varchar(20) DEFAULT '' NOT NULL,
    tx_conn_t3wtk_elements_subpage_cards_settings mediumtext,
    tx_conn_t3wtk_elements_hero_slider_items    int(11) DEFAULT '0' NOT NULL,
    tx_conn_t3wtk_elements_hero_slider_settings mediumtext,
    tx_conn_t3wtk_elements_slider_items              int(11) DEFAULT '0' NOT NULL,
    tx_conn_t3wtk_elements_slider_settings           mediumtext,
    tx_conn_t3wtk_elements_thumbs_slider_items       int(11) DEFAULT '0' NOT NULL,
    tx_conn_t3wtk_elements_thumbs_slider_settings    mediumtext,
    tx_conn_t3wtk_elements_parallax_slider_items     int(11) DEFAULT '0' NOT NULL,
    tx_conn_t3wtk_elements_parallax_slider_settings  mediumtext,
    tx_conn_t3wtk_elements_parallax_slider_bg_image  int(11) DEFAULT '0' NOT NULL,
    tx_conn_t3wtk_elements_text_animation            varchar(30) DEFAULT '' NOT NULL,
    tx_conn_t3wtk_elements_card_stack_items          int(11) DEFAULT '0' NOT NULL,
    tx_conn_t3wtk_elements_card_stack_settings       mediumtext,
    tx_conn_t3wtk_elements_shift_carousel_items       int(11) DEFAULT '0' NOT NULL,
    tx_conn_t3wtk_elements_shift_carousel_settings    mediumtext,
    tx_conn_t3wtk_elements_lightbox_gallery_items          int(11) DEFAULT '0' NOT NULL,
    tx_conn_t3wtk_elements_lightbox_gallery_settings       mediumtext,
    tx_conn_t3wtk_elements_mosaic_gallery_items       int(11) DEFAULT '0' NOT NULL,
    tx_conn_t3wtk_elements_mosaic_gallery_settings    mediumtext,
    tx_conn_t3wtk_elements_carousel_items    int(11) DEFAULT '0' NOT NULL,
    tx_conn_t3wtk_elements_carousel_settings mediumtext,
    tx_conn_t3wtk_elements_timeline_items          int(11) DEFAULT '0' NOT NULL,
    tx_conn_t3wtk_elements_timeline_settings       mediumtext,
    tx_conn_t3wtk_elements_stats_items             int(11) DEFAULT '0' NOT NULL,
    tx_conn_t3wtk_elements_stats_settings          mediumtext,
    tx_conn_t3wtk_elements_logo_wall_items         int(11) DEFAULT '0' NOT NULL,
    tx_conn_t3wtk_elements_logo_wall_settings      mediumtext,
    tx_conn_t3wtk_elements_testimonials_items      int(11) DEFAULT '0' NOT NULL,
    tx_conn_t3wtk_elements_testimonials_settings   mediumtext,
    tx_conn_t3wtk_elements_pricing_tiers           int(11) DEFAULT '0' NOT NULL,
    tx_conn_t3wtk_elements_pricing_settings        mediumtext,
    tx_conn_t3wtk_elements_feature_grid_items      int(11) DEFAULT '0' NOT NULL,
    tx_conn_t3wtk_elements_feature_grid_settings   mediumtext,
    tx_conn_t3wtk_elements_people_settings         mediumtext,
    tx_conn_t3wtk_elements_text_icon_library       varchar(30) DEFAULT 'bootstrap-icons' NOT NULL,
    tx_conn_t3wtk_elements_text_icon_style         varchar(20) DEFAULT 'solid' NOT NULL,
    tx_conn_t3wtk_elements_text_icon_name          varchar(100) DEFAULT '' NOT NULL,
    tx_conn_t3wtk_elements_text_icon_position      varchar(20) DEFAULT 'top' NOT NULL,
    tx_conn_t3wtk_elements_text_icon_shape         varchar(20) DEFAULT 'circle' NOT NULL,
    tx_conn_t3wtk_elements_text_icon_size          varchar(20) DEFAULT 'medium' NOT NULL,
    tx_conn_t3wtk_elements_text_icon_color         varchar(20) DEFAULT '' NOT NULL,
    tx_conn_t3wtk_elements_text_icon_bg            varchar(20) DEFAULT '' NOT NULL,
    tx_conn_t3wtk_elements_tabs_items              int(11) DEFAULT '0' NOT NULL,
    tx_conn_t3wtk_elements_tabs_settings           mediumtext,
    tx_conn_t3wtk_elements_process_items           int(11) DEFAULT '0' NOT NULL,
    tx_conn_t3wtk_elements_process_settings        mediumtext,
    tx_conn_t3wtk_elements_before_after_before          int(11) DEFAULT '0' NOT NULL,
    tx_conn_t3wtk_elements_before_after_after           int(11) DEFAULT '0' NOT NULL,
    tx_conn_t3wtk_elements_before_after_before_label    varchar(100) DEFAULT '' NOT NULL,
    tx_conn_t3wtk_elements_before_after_after_label     varchar(100) DEFAULT '' NOT NULL,
    tx_conn_t3wtk_elements_before_after_start           int(11) DEFAULT '50' NOT NULL,
    tx_conn_t3wtk_elements_before_after_direction       varchar(20) DEFAULT 'left-right' NOT NULL,
    tx_conn_t3wtk_elements_before_after_handle_icon     int(11) DEFAULT '0' NOT NULL,
    tx_conn_t3wtk_elements_before_after_handle_color        varchar(9) DEFAULT '' NOT NULL,
    tx_conn_t3wtk_elements_before_after_handle_border_color varchar(9) DEFAULT '' NOT NULL,
    tx_conn_t3wtk_elements_quote_author            varchar(255) DEFAULT '' NOT NULL,
    tx_conn_t3wtk_elements_quote_role              varchar(255) DEFAULT '' NOT NULL,
    tx_conn_t3wtk_elements_quote_image             int(11) DEFAULT '0' NOT NULL,
    tx_conn_t3wtk_elements_quote_settings          mediumtext,
    tx_conn_t3wtk_elements_alert_settings          mediumtext,
    tx_conn_t3wtk_elements_alert_custom_icon_name  varchar(100) DEFAULT '' NOT NULL,
    tx_conn_t3wtk_elements_alert_custom_icon_image int(11) DEFAULT '0' NOT NULL,
    tx_conn_t3wtk_elements_video_hero_media        int(11) DEFAULT '0' NOT NULL,
    tx_conn_t3wtk_elements_video_hero_poster       int(11) DEFAULT '0' NOT NULL,
    tx_conn_t3wtk_elements_video_hero_settings     mediumtext,
    tx_conn_t3wtk_elements_map_illustration_image  int(11) DEFAULT '0' NOT NULL,
    tx_conn_t3wtk_elements_map_settings            mediumtext,
    tx_conn_t3wtk_elements_flip_card_items         int(11) DEFAULT '0' NOT NULL,
    tx_conn_t3wtk_elements_flip_card_settings      mediumtext
);

-- ─── Video Hero: per-media "Muted" (core sys_file_reference) ────────────────
-- Adds the column backing Configuration/TCA/Overrides/sys_file_reference.php's
-- "muted" field, shown next to core's own "autoplay" in the video overlay
-- palette. Read directly off the Media file reference by Video Hero's
-- Background loop mode.

CREATE TABLE sys_file_reference (
    muted tinyint(1) unsigned DEFAULT '1' NOT NULL
);

-- ─── Mosaic Gallery ──────────────────────────────────────────────────────────

CREATE TABLE tx_conn_t3wtk_elements_domain_model_mosaic_item (
    uid             int(11) NOT NULL auto_increment,
    pid             int(11) DEFAULT '0' NOT NULL,
    tstamp          int(11) DEFAULT '0' NOT NULL,
    crdate          int(11) DEFAULT '0' NOT NULL,
    deleted         tinyint(4) DEFAULT '0' NOT NULL,
    hidden          tinyint(4) DEFAULT '0' NOT NULL,
    sorting         int(11) DEFAULT '0' NOT NULL,
    content_element int(11) DEFAULT '0' NOT NULL,

    title           varchar(255) DEFAULT '' NOT NULL,
    caption         text,
    image           int(11) DEFAULT '0' NOT NULL,

    PRIMARY KEY (uid),
    KEY parent (pid),
    KEY content_element (content_element)
);

-- ─── Bootstrap Accordion ──────────────────────────────────────────────────────

CREATE TABLE tx_conn_t3wtk_elements_domain_model_accordion_item (
    uid             int(11) NOT NULL auto_increment,
    pid             int(11) DEFAULT '0' NOT NULL,
    tstamp          int(11) DEFAULT '0' NOT NULL,
    crdate          int(11) DEFAULT '0' NOT NULL,
    deleted         tinyint(4) DEFAULT '0' NOT NULL,
    hidden          tinyint(4) DEFAULT '0' NOT NULL,
    sorting         int(11) DEFAULT '0' NOT NULL,
    content_element int(11) DEFAULT '0' NOT NULL,

    title           varchar(255) DEFAULT '' NOT NULL,
    title_layout    int(11) DEFAULT '0' NOT NULL,
    title_class     varchar(30) DEFAULT '' NOT NULL,
    subtitle        varchar(255) DEFAULT '' NOT NULL,
    image           int(11) unsigned DEFAULT '0' NOT NULL,
    back_title      varchar(255) DEFAULT '' NOT NULL,
    back_title_layout int(11) DEFAULT '0' NOT NULL,
    body            mediumtext,
    link            varchar(2048) DEFAULT '' NOT NULL,
    link_label      varchar(100) DEFAULT '' NOT NULL,

    PRIMARY KEY (uid),
    KEY parent (pid),
    KEY content_element (content_element)
);

-- ─── Bootstrap Card Group ────────────────────────────────────────────────────

CREATE TABLE tx_conn_t3wtk_elements_domain_model_card_item (
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

CREATE TABLE tx_conn_t3wtk_elements_domain_model_slide_item (
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
    text_animation      varchar(30) DEFAULT '' NOT NULL,
    animation_duration  int(11) DEFAULT '0' NOT NULL,
    text_pos_v          varchar(10) DEFAULT '' NOT NULL,
    text_pos_h          varchar(10) DEFAULT '' NOT NULL,

    PRIMARY KEY (uid),
    KEY parent (pid),
    KEY content_element (content_element)
);

-- ─── GSAP Card Stack ──────────────────────────────────────────────────────────

CREATE TABLE tx_conn_t3wtk_elements_domain_model_stack_card (
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

-- ─── GSAP Shift Carousel ───────────────────────────────────────────────────────

CREATE TABLE tx_conn_t3wtk_elements_domain_model_shift_carousel_item (
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

CREATE TABLE tx_conn_t3wtk_elements_domain_model_carousel_slide (
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

-- ─── GSAP Lightbox Gallery ────────────────────────────────────────────────────────

CREATE TABLE tx_conn_t3wtk_elements_domain_model_gallery_item (
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

-- ─── Stats / Counter ─────────────────────────────────────────────────────────

CREATE TABLE tx_conn_t3wtk_elements_domain_model_stat_item (
    uid             int(11) NOT NULL auto_increment,
    pid             int(11) DEFAULT '0' NOT NULL,
    tstamp          int(11) DEFAULT '0' NOT NULL,
    crdate          int(11) DEFAULT '0' NOT NULL,
    deleted         tinyint(4) DEFAULT '0' NOT NULL,
    hidden          tinyint(4) DEFAULT '0' NOT NULL,
    sorting         int(11) DEFAULT '0' NOT NULL,
    content_element int(11) DEFAULT '0' NOT NULL,

    value           varchar(50) DEFAULT '' NOT NULL,
    prefix          varchar(20) DEFAULT '' NOT NULL,
    suffix          varchar(20) DEFAULT '' NOT NULL,
    label           varchar(255) DEFAULT '' NOT NULL,
    description     varchar(255) DEFAULT '' NOT NULL,

    PRIMARY KEY (uid),
    KEY parent (pid),
    KEY content_element (content_element)
);

-- ─── Logo Wall ────────────────────────────────────────────────────────────────

CREATE TABLE tx_conn_t3wtk_elements_domain_model_logo_item (
    uid             int(11) NOT NULL auto_increment,
    pid             int(11) DEFAULT '0' NOT NULL,
    tstamp          int(11) DEFAULT '0' NOT NULL,
    crdate          int(11) DEFAULT '0' NOT NULL,
    deleted         tinyint(4) DEFAULT '0' NOT NULL,
    hidden          tinyint(4) DEFAULT '0' NOT NULL,
    sorting         int(11) DEFAULT '0' NOT NULL,
    content_element int(11) DEFAULT '0' NOT NULL,

    alt             varchar(255) DEFAULT '' NOT NULL,
    link            varchar(1024) DEFAULT '' NOT NULL,
    image           int(11) DEFAULT '0' NOT NULL,

    PRIMARY KEY (uid),
    KEY parent (pid),
    KEY content_element (content_element)
);

-- ─── Testimonials ─────────────────────────────────────────────────────────────

CREATE TABLE tx_conn_t3wtk_elements_domain_model_testimonial_item (
    uid             int(11) NOT NULL auto_increment,
    pid             int(11) DEFAULT '0' NOT NULL,
    tstamp          int(11) DEFAULT '0' NOT NULL,
    crdate          int(11) DEFAULT '0' NOT NULL,
    deleted         tinyint(4) DEFAULT '0' NOT NULL,
    hidden          tinyint(4) DEFAULT '0' NOT NULL,
    sorting         int(11) DEFAULT '0' NOT NULL,
    content_element int(11) DEFAULT '0' NOT NULL,

    quote           mediumtext,
    author_name     varchar(255) DEFAULT '' NOT NULL,
    author_role     varchar(255) DEFAULT '' NOT NULL,
    rating          tinyint(4) DEFAULT '5' NOT NULL,
    image           int(11) DEFAULT '0' NOT NULL,

    PRIMARY KEY (uid),
    KEY parent (pid),
    KEY content_element (content_element)
);

-- ─── Pricing ──────────────────────────────────────────────────────────────────

CREATE TABLE tx_conn_t3wtk_elements_domain_model_pricing_tier (
    uid             int(11) NOT NULL auto_increment,
    pid             int(11) DEFAULT '0' NOT NULL,
    tstamp          int(11) DEFAULT '0' NOT NULL,
    crdate          int(11) DEFAULT '0' NOT NULL,
    deleted         tinyint(4) DEFAULT '0' NOT NULL,
    hidden          tinyint(4) DEFAULT '0' NOT NULL,
    sorting         int(11) DEFAULT '0' NOT NULL,
    content_element int(11) DEFAULT '0' NOT NULL,

    name            varchar(255) DEFAULT '' NOT NULL,
    tagline         varchar(255) DEFAULT '' NOT NULL,
    currency        varchar(10) DEFAULT '' NOT NULL,
    amount          varchar(30) DEFAULT '' NOT NULL,
    period          varchar(50) DEFAULT '' NOT NULL,
    price_note      varchar(255) DEFAULT '' NOT NULL,
    features        mediumtext,
    cta_label       varchar(100) DEFAULT '' NOT NULL,
    cta_link        varchar(1024) DEFAULT '' NOT NULL,
    featured        tinyint(4) DEFAULT '0' NOT NULL,
    badge_text      varchar(60) DEFAULT '' NOT NULL,

    PRIMARY KEY (uid),
    KEY parent (pid),
    KEY content_element (content_element)
);

-- ─── Feature Grid ─────────────────────────────────────────────────────────────

CREATE TABLE tx_conn_t3wtk_elements_domain_model_feature_item (
    uid             int(11) NOT NULL auto_increment,
    pid             int(11) DEFAULT '0' NOT NULL,
    tstamp          int(11) DEFAULT '0' NOT NULL,
    crdate          int(11) DEFAULT '0' NOT NULL,
    deleted         tinyint(4) DEFAULT '0' NOT NULL,
    hidden          tinyint(4) DEFAULT '0' NOT NULL,
    sorting         int(11) DEFAULT '0' NOT NULL,
    content_element int(11) DEFAULT '0' NOT NULL,

    icon            varchar(50) DEFAULT '' NOT NULL,
    image           int(11) DEFAULT '0' NOT NULL,
    title           varchar(255) DEFAULT '' NOT NULL,
    body            mediumtext,
    link            varchar(1024) DEFAULT '' NOT NULL,
    link_label      varchar(100) DEFAULT '' NOT NULL,

    PRIMARY KEY (uid),
    KEY parent (pid),
    KEY content_element (content_element)
);

-- ─── Timeline ─────────────────────────────────────────────────────────────────

CREATE TABLE tx_conn_t3wtk_elements_domain_model_timeline_item (
    uid             int(11) NOT NULL auto_increment,
    pid             int(11) DEFAULT '0' NOT NULL,
    tstamp          int(11) DEFAULT '0' NOT NULL,
    crdate          int(11) DEFAULT '0' NOT NULL,
    deleted         tinyint(4) DEFAULT '0' NOT NULL,
    hidden          tinyint(4) DEFAULT '0' NOT NULL,
    sorting         int(11) DEFAULT '0' NOT NULL,
    content_element int(11) DEFAULT '0' NOT NULL,

    date            int(11) DEFAULT NULL,
    date_precision  varchar(10) DEFAULT 'full' NOT NULL,
    title           varchar(255) DEFAULT '' NOT NULL,
    body            mediumtext,
    image           int(11) DEFAULT '0' NOT NULL,

    PRIMARY KEY (uid),
    KEY parent (pid),
    KEY content_element (content_element)
);

-- ─── Person (standalone record, not tied to a content element) ───────────────

CREATE TABLE tx_conn_t3wtk_elements_domain_model_person (
    uid        int(11) NOT NULL auto_increment,
    pid        int(11) DEFAULT '0' NOT NULL,
    tstamp     int(11) DEFAULT '0' NOT NULL,
    crdate     int(11) DEFAULT '0' NOT NULL,
    deleted    tinyint(4) DEFAULT '0' NOT NULL,
    hidden     tinyint(4) DEFAULT '0' NOT NULL,
    starttime  int(11) DEFAULT '0' NOT NULL,
    endtime    int(11) DEFAULT '0' NOT NULL,
    sorting    int(11) DEFAULT '0' NOT NULL,

    name       varchar(255) DEFAULT '' NOT NULL,
    role       varchar(255) DEFAULT '' NOT NULL,
    image      int(11) DEFAULT '0' NOT NULL,
    bio        mediumtext,
    email      varchar(255) DEFAULT '' NOT NULL,
    phone      varchar(60) DEFAULT '' NOT NULL,
    link       varchar(1024) DEFAULT '' NOT NULL,
    link_label varchar(100) DEFAULT '' NOT NULL,
    accent     varchar(20) DEFAULT 'auto' NOT NULL,
    image_side varchar(20) DEFAULT 'auto' NOT NULL,

    PRIMARY KEY (uid),
    KEY parent (pid)
);

-- ─── Bootstrap Tabs ──────────────────────────────────────────────────────────

CREATE TABLE tx_conn_t3wtk_elements_domain_model_tab_item (
    uid             int(11) NOT NULL auto_increment,
    pid             int(11) DEFAULT '0' NOT NULL,
    tstamp          int(11) DEFAULT '0' NOT NULL,
    crdate          int(11) DEFAULT '0' NOT NULL,
    deleted         tinyint(4) DEFAULT '0' NOT NULL,
    hidden          tinyint(4) DEFAULT '0' NOT NULL,
    sorting         int(11) DEFAULT '0' NOT NULL,
    content_element int(11) DEFAULT '0' NOT NULL,

    title           varchar(60) DEFAULT '' NOT NULL,
    body            mediumtext,

    PRIMARY KEY (uid),
    KEY parent (pid),
    KEY content_element (content_element)
);

-- ─── Process/Steps ───────────────────────────────────────────────────────────

CREATE TABLE tx_conn_t3wtk_elements_domain_model_process_step (
    uid             int(11) NOT NULL auto_increment,
    pid             int(11) DEFAULT '0' NOT NULL,
    tstamp          int(11) DEFAULT '0' NOT NULL,
    crdate          int(11) DEFAULT '0' NOT NULL,
    deleted         tinyint(4) DEFAULT '0' NOT NULL,
    hidden          tinyint(4) DEFAULT '0' NOT NULL,
    sorting         int(11) DEFAULT '0' NOT NULL,
    content_element int(11) DEFAULT '0' NOT NULL,

    title           varchar(100) DEFAULT '' NOT NULL,
    icon            varchar(60) DEFAULT '' NOT NULL,
    body            mediumtext,

    PRIMARY KEY (uid),
    KEY parent (pid),
    KEY content_element (content_element)
);

-- ─── Page card fields (Subpage Cards element) ───────────────────────────────

CREATE TABLE pages (
    tx_conn_t3wtk_elements_card_teaser text,
    tx_conn_t3wtk_elements_card_link   varchar(2048) DEFAULT '' NOT NULL,
    tx_conn_t3wtk_elements_card_media  int(11) unsigned DEFAULT '0' NOT NULL
);

-- ─── Flip Card ────────────────────────────────────────────────────────────────

CREATE TABLE tx_conn_t3wtk_elements_domain_model_flip_card_item (
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
    image           int(11) unsigned DEFAULT '0' NOT NULL,

    back_title      varchar(255) DEFAULT '' NOT NULL,
    back_subtitle   varchar(255) DEFAULT '' NOT NULL,
    back_body       mediumtext,
    back_image      int(11) unsigned DEFAULT '0' NOT NULL,
    back_link       varchar(2048) DEFAULT '' NOT NULL,
    back_link_label varchar(100) DEFAULT '' NOT NULL,

    PRIMARY KEY (uid),
    KEY parent (pid),
    KEY content_element (content_element)
);
