# v0.1.0
##  18-06-2018

1. [](#new)
    * First version: fully functional, but the engine cannot be disabled if the plugin is enabled.

# v1.0.0
## 19-06-2018

1. [](#new)
    * The ZMarkdown engine can now be enabled per-page or globally (either through frontmatter/`system.yaml`, or via the admin plugin), as a `process` option has been added for it alongside `markdown` and `twig`.

# v1.0.1
## 19-06-2018

1. [](#bugfix)
    * Fixed error if the page's content is empty.

# v1.1.0
## 06-07-2019

1. [](#new)
    * Links are now processed correctly according to [Grav rules](https://learn.getgrav.org/16/content/linking).

2. [](#bugfix)
    * Fixed encoding errors in images ALTs due to Grav's `Excerpts::getExcerptFromHtml` not handling encoding very well.

# v1.2.0
## 21-11-2022

1. [](#new)
   * Now compatible with Grav 1.7+.

2. [](#bugfix)
   * Fixed HTML inside links being stripped by links post-processing, by moving back to Grav's `Excerpts::getExcerptFromHtml`, now fixed.
