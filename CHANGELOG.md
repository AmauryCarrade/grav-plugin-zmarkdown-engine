# v0.1.0
##  06/18/2018

1. [](#new)
    * First version: fully functionnal, but the engine cannot be disabled if the plugin is enabled.

# v1.0.0
## 06/19/2018

1. [New](#new)
    * The ZMarkdown engine can now be enabled per-page or globally (either through frontmatter/`system.yaml`, or via the admin plugin), as a `process` option has been added for it alongside `markdown` and `twig`.

# v1.0.1
## 06/19/2018

1. [Fixes](#fixes)
    * Fixed error if the page's content is empty.

# v1.1.0
## 07/06/2019

1. [New](#new)
    * Links are now processed correctly according to [Grav rules](https://learn.getgrav.org/16/content/linking).

1. [Fixes](#fixes)
    * Fixed encoding errors in images ALTs due to Grav's `Excerpts::getExcerptFromHtml` not handling encoding very well.
