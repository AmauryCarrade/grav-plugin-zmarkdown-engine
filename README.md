# ZMarkdown Engine Plugin

The **ZMarkdown Engine** Plugin is for [Grav CMS](http://github.com/getgrav/grav). Allows to use the [ZMarkdown engine](https://github.com/zestedesavoir/zmarkdown) to parse the markdown.

**Warning**: this plugin does not includes ZMarkdown: it must be installed separatly.

## Installation

Installing the ZMarkdown Engine plugin can be done in one of two ways. The GPM (Grav Package Manager) installation method enables you to quickly and easily install the plugin with a simple terminal command, while the manual method enables you to do so via a zip file.

### GPM Installation (Preferred)

The simplest way to install this plugin is via the [Grav Package Manager (GPM)](http://learn.getgrav.org/advanced/grav-gpm) through your system's terminal (also called the command line).  From the root of your Grav install type:

    bin/gpm install zmarkdown-engine

This will install the ZMarkdown Engine plugin into your `/user/plugins` directory within Grav. Its files can be found under `/your/site/grav/user/plugins/zmarkdown-engine`.

### Manual Installation

To install this plugin, just download the zip version of this repository and unzip it under `/your/site/grav/user/plugins`. Then, rename the folder to `zmarkdown-engine`. You can find these files on [GitHub](https://github.com/Nebulius/grav-plugin-zmarkdown-engine) or via [GetGrav.org](http://getgrav.org/downloads/plugins#extras).

You should now have all the plugin files under

    /your/site/grav/user/plugins/zmarkdown-engine
	
> NOTE: This plugin is a modular component for Grav which requires [Grav](http://github.com/getgrav/grav) and the [Error](https://github.com/getgrav/grav-plugin-error) and [Problems](https://github.com/getgrav/grav-plugin-problems) to operate.

### Admin Plugin

If you use the admin plugin, you can install directly through the admin plugin by browsing the `Plugins` tab and clicking on the `Add` button.

## Configuration

Before configuring this plugin, you should copy the `user/plugins/zmarkdown-engine/zmarkdown-engine.yaml` to `user/config/plugins/zmarkdown-engine.yaml` and only edit that copy.

Here is the default configuration and an explanation of available options:

```yaml
enabled: true
zmd_server: http://127.0.0.1:27272
```

The `zmd_server` option must contains a link to the root of the zmarkdown engine server, with the port and without trailing slash. Hint: the default port is 27272.

Note that if you use the admin plugin, a file with your configuration, and named zmarkdown-engine.yaml will be saved in the `user/config/plugins/` folder once the configuration is saved in the admin.

## Usage

For this plugin to work, you must have a ZMarkdown server running and accessible from Grav's backend. Then, fill the `zmd_server` configuration option (using the Admin plugin or the yml file). Now, the engine is ready to run.

The last thing to do is to tell Grav (and this plugin) to actually use this engine. To do so, you can either enable it globally, by disabling the `markdown` processor and adding a new `zmarkdown` set to `true`, in the `system.yaml` file, as below:

```yaml
pages:
  ...
  process:
    markdown: false
    twig: false
    zmarkdown: true
```

…or configure it per page by adding something like this in the page's frontmatter.

```yaml
process:
    zmarkdown: true
    markdown: false
    twig: false
```

If you use the admin plugin, you'll be able to change that using the page or system configuration forms—a “ZMarkdown” option is added to the _Process_ fields in both system config and page forms.

## Credits

This plugin uses (obviously) the [ZMarkdown engine](https://github.com/zestedesavoir/zmarkdown), initially developped for Zeste de Savoir. Also, thanks to the developers of [SimpleHTMLDOM](http://simplehtmldom.sourceforge.net/), used to backport the images processing to the Markdown parsed by ZMD.

## To Do

- [ ] Currently, images URL are supported, but not links, as the functions into `Excerpts` does not really support anything but images to convert HTML tags to Parsedown excerpts. So special links (relative, by folder, or special attributes) will not work. We should re-write the HTML-to-excerpt converter to support tags with content and sub-tags (or use Parsedown for that, if possible).
