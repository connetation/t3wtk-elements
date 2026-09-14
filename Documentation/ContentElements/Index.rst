.. include:: /Includes.rst.txt

.. _content-elements-catalog:

=========================
Content Elements Catalog
=========================

Every content element shipped by ``conn/elements``, with what it's for,
what it's technically built on, and its key configurable settings
(available as FlexForm fields in the backend "Display" tab of each
element, unless noted otherwise).

----

Text & Content
================

.. _ce-text-column:

Text (Columns)
-----------------

:CType: ``conn_text_column``

Plain text split into multiple CSS columns from tablet width up — a
lightweight alternative to a single-column text block for longer copy.
Built with plain CSS multi-column layout (``column-count``), no
JavaScript. No FlexForm — just uses the standard header and bodytext
(RTE) fields.

.. _ce-text-icon:

Text Icon
-----------

:CType: ``conn_text_icon``

Header, subheader and body text paired with an icon from Bootstrap Icons
or Font Awesome — a compact building block for feature callouts that
don't need a full grid. Icon selection is a dedicated backend field
(``Classes/Form/Element/IconPreviewSelectElement.php``) with a live icon
preview, not a plain text input. Key settings: icon library
(bootstrap-icons/font-awesome/ionicons), icon position (top/left/right),
shape (circle/square/hexagon/none), size, colors.

.. _ce-quote:

Quote
-------

:CType: ``conn_quote``

Standalone pull-quote with optional author attribution — author name and
role are direct fields, the quote text itself is the bodytext field. Pure
CSS styling (no JS). Key settings: style (simple/mark/sketch/minimal),
text size, alignment, accent/text colors.

.. _ce-alert:

Alert / Notice Banner
------------------------

:CType: ``conn_alert``

Dismissible Bootstrap alert banner for announcements — can render inline
in the page flow or as a fixed/edge banner. Built directly on Bootstrap
5's alert component. Key settings: style (info/success/warning/danger/
custom), position (static/fixed-top/fixed-bottom/edge-left/edge-right),
dismissible + remember-dismiss (via ``localStorage``), icon, button
shape/color.

----

Bootstrap Components
=======================

.. _ce-bs-accordion:

Bootstrap Accordion
-----------------------

:CType: ``conn_bs_accordion``

Collapsible content panels using Bootstrap 5's accordion component —
each panel is an inline child record (title + rich-text body), reorderable
in the backend. Key settings: always-open (multiple panels expandable at
once) vs. classic single-open accordion, flush style, "use as FAQ" (adds
FAQPage structured data for SEO).

.. _ce-bs-tabs:

Bootstrap Tabs
-----------------

:CType: ``conn_bs_tabs``

Switchable content panels using Bootstrap 5's tabs component, each tab an
inline child record. Key settings: style (tabs/pills), justified
(equal-width tabs), fade transition, rounded corners.

.. _ce-bs-card-group:

Bootstrap Card Group
------------------------

:CType: ``conn_bs_card_group``

Responsive grid of Bootstrap cards with optional images, badges and
links — a general-purpose "services/features as cards" element. Key
settings: columns (1-4), style (default/flat/shadow), accent bar.

.. _ce-bs-carousel:

Bootstrap Carousel
----------------------

:CType: ``conn_bs_carousel``

Bootstrap 5 image carousel with optional captions, controls and
autoplay — the "native Bootstrap" alternative to the Swiper-based
sliders below, useful when you specifically want Bootstrap's own
JS/markup rather than pulling in Swiper. Key settings: aspect ratio,
autoplay + interval, fade vs. slide transition, indicators, lightbox
on click, dark variant.

.. _ce-bs-feature-grid:

Feature Grid
---------------

:CType: ``conn_bs_feature_grid``

Icon-and-text feature grid, also supporting a numbered-steps variant and
a horizontal layout — each item is a child record with icon, title, body
and optional link. Key settings: columns (2-4), variant
(default/horizontal/steps), icon style (soft/solid/none), accent bar.

.. _ce-bs-process:

Process / Steps
-------------------

:CType: ``conn_bs_process``

Numbered step-by-step process (e.g. "How we work"), connected by a
configurable connector line. Key settings: numbered on/off, connector
style (solid/dashed/dotted/none) and color, marker colors, max items
per row before wrapping.

.. _ce-bs-stats:

Stats / Counters
--------------------

:CType: ``conn_bs_stats``

Animated counter grid for key metrics (uses a count-up-on-scroll JS
animation). Each stat is a child record with value, optional
prefix/suffix, label and description. Key settings: columns, layout
(default/row), animation duration, accent bar.

.. _ce-bs-logo-wall:

Logo Wall
------------

:CType: ``conn_bs_logo_wall``

Grid or continuously-scrolling marquee strip of partner/client logos —
entirely image-driven (each item is just alt text, link and an image).
Key settings: layout (grid/marquee), marquee direction and speed,
grayscale filter, columns.

.. _ce-bs-testimonials:

Testimonials
----------------

:CType: ``conn_bs_testimonials``

Customer reviews in grid or carousel (Swiper-powered) layout with star
ratings. Each testimonial is a child record: quote, author name/role,
rating (1-5), optional photo. Key settings: layout (grid/carousel),
columns, autoplay + delay, show rating stars, accent bar.

.. _ce-bs-pricing:

Pricing Table
-----------------

:CType: ``conn_bs_pricing``

Pricing tiers with feature lists and CTA buttons, with a "featured"
highlight option for the tier you want to draw attention to. Each tier
is a child record: name, tagline, currency, amount, period, feature
list (newline-separated), CTA label/link, optional badge text. Key
settings: columns (2-4), style (default/flat/shadow), accent bar.

.. _ce-bs-timeline:

Timeline
-----------

:CType: ``conn_bs_timeline``

A vertical (or horizontal, depending on layout) dated timeline for
company history, roadmaps, or changelogs — each entry is a child record
with a date, title and body. Key settings: layout, line style
(solid/dashed/dotted), track alignment (left/center/right), sort order
(date ascending/descending), whether to show the item date, and a
"changelog" position preset.

.. _ce-bs-before-after:

Before / After Image Slider
-------------------------------

:CType: ``conn_bs_before_after``

Drag-to-compare image slider — for redesign or renovation showcases.
Entirely image-driven (before image, after image, draggable handle).
Direct tt_content fields (not FlexForm): before/after labels, starting
handle position (0-100), direction (left-right/top-bottom), handle icon
and colors.

----

Sliders (Swiper.js)
======================

Four elements share the same underlying slide/child-record table
(``tx_conn_elements_domain_model_slide_item``) and are all built on the
`Swiper <https://swiperjs.com/>`__ JavaScript library — they differ in
layout/navigation style, not in their underlying data model.

.. _ce-swiper-hero-slider:

Hero Slider
--------------

:CType: ``conn_swiper_hero_slider``

Full-width image slider with text overlay and CTA button — the classic
"hero banner slider." Each slide: title, subtitle, body, image, link,
plus optional per-slide text animation and positioning. Key settings:
autoplay + delay, fade vs. slide effect, height, navigation/pagination
visibility.

.. _ce-swiper-slider:

Card Slider
--------------

:CType: ``conn_swiper_slider``

Responsive card slider with a configurable number of slides visible per
view — for showcasing multiple items side by side rather than one
full-width hero at a time. Key settings: slides per view, loop, autoplay
+ delay, navigation/pagination visibility.

.. _ce-swiper-parallax-slider:

Parallax Slider
-------------------

:CType: ``conn_swiper_parallax_slider``

Full-height slider with a depth parallax effect on images and text as
slides transition (Swiper's parallax module). Key settings: height
(medium/large/fullscreen), autoplay + delay, navigation/pagination
visibility.

.. _ce-swiper-thumbs-slider:

Thumbnails Slider
---------------------

:CType: ``conn_swiper_thumbs_slider``

Full-height image slider with an interactive thumbnail strip for direct
navigation (Swiper's thumbs module, two synced Swiper instances). Key
settings: height, thumbnails per view, autoplay + delay, lightbox on
click.

----

GSAP-Animated
================

Three elements are explicitly built around `GSAP <https://gsap.com/>`__'s
Flip plugin (smoothly animating layout changes) rather than Swiper —
used where the effect is a transform/morph rather than a sliding
carousel.

.. _ce-card-stack:

Card Stack
-------------

:CType: ``conn_card_stack``

GSAP-animated stacked deck of cards with prev/next navigation — cards
animate in a stacked, layered transition rather than sliding. Key
settings: autoplay + delay, nav position, text alignment/position.

.. _ce-flip-carousel:

Flip Carousel
----------------

:CType: ``conn_flip_carousel``

Filterable grid of cards (each with title, subtitle, category, body,
link) that re-flows with a GSAP Flip animation when a category filter is
applied — the layout change itself is animated, not just the content.
Key settings: columns, autoplay + delay, nav position, text
alignment/position.

.. _ce-flip-gallery:

Flip Gallery
---------------

:CType: ``conn_flip_gallery``

Image grid with a GSAP Flip animation that expands a clicked image into
a lightbox view — entirely image-driven. Key settings: columns, gap,
image fit (cover/contain), border style/radius, caption position,
"caption in lightbox only."

----

Galleries & Media
====================

.. _ce-mosaic-gallery:

Mosaic Gallery
------------------

:CType: ``conn_mosaic_gallery``

Image gallery with masonry, grid or "metro" mosaic layouts, lightbox,
hover effects and configurable columns — entirely image-driven. Key
settings: layout type (grid/masonry/metro/justified), columns, gallery
height, hover effect (zoom/dim/none), caption style and position,
lightbox on click.

.. _ce-video-hero:

Video Hero
-------------

:CType: ``conn_video_hero``

Full-width video hero — self-hosted, YouTube or Vimeo — as either a
muted background loop or a click-to-play video. Media and poster are FAL
image/file fields. Key settings: mode (background loop/click-to-play),
object-fit (cover/contain), overlay darkness, height, CTA
caption/label/link.

.. _ce-map:

Map & Location
------------------

:CType: ``conn_map``

An address/phone/email panel next to a map — illustrative-only by
default (a decorative grid pattern, or a static illustration image), or
a real keyless Google Maps iframe embed. The distinctive part: **this
element doesn't use its own header/bodytext fields for the address at
all** — it pulls ``settings.contact.address.*`` from site-wide TypoScript
settings, so the same address renders consistently everywhere the
element is used across a site. Key settings: provider (static/google),
zoom, panel position, grid color, privacy mode (loads the Google iframe
only after consent).

----

Specialty
============

These two elements are closer to small bespoke applications than simple
display components — they bring their own child-record data model, list
UI (search/filter), and considerably more Fluid/JS than the rest of the
package.

.. _ce-people:

People
---------

:CType: ``conn_people``

Renders standalone Person records (a project-wide address-book table,
not tied to a specific content element) — either hand-picked by UID or
an entire page's worth of records — as team cards, contact cards,
flippable cards, "reveal" cards, or a single visit card. Because Person
selection is itself a FlexForm field (not a database relation), an
instance with nothing selected renders empty by default — at least one
person must be picked, or a folder selected, for anything to show. Key
settings: source (manual selection/whole folder), style (7 card
variants), columns, image side, show role/bio/border/shadow, per-style
accent colors.

.. _ce-dashboard:

Service Dashboard
---------------------

:CType: ``conn_dashboard``

Interactive project/service overview: search box, status filter,
category filter, grid/compact/list view toggle, and status/tag badges
per item — each project is a child record with title, description,
status, category, tags, URL and image. Categories are a separate child
table, filterable independently. Key settings: default view
(grid/compact/list), columns, card variant, which controls to show
(search/filter/stats), stats and tags display style.

.. _ce-wiki:

Wiki
-------

:CType: ``conn_wiki``

A small knowledge base embedded as a content element: a sidebar of
pages grouped by category, full-text search, tag filtering, and each
page built from ordered content "blocks" (text or code, with syntax
language). Unlike every other element here, the actual content (pages,
categories, tags, blocks) lives in its own dedicated tables pointed to
by a single ``tx_conn_elements_wiki_id`` field on the content element —
not inline child records directly on ``tt_content`` — so one wiki
"directory" record can in principle be reused, and the backend also
ships a dedicated Wiki administration module
(``Classes/Controller/WikiModuleController.php``) for managing pages
outside of this content element's own record. Key settings: show
search, show category filter, show tag filter.
