<?php
namespace Grav\Plugin;

use DOMDocument;
use Grav\Common\Page\Page;
use Grav\Common\Plugin;
use Grav\Common\Helpers\Excerpts;
use RocketTheme\Toolbox\Event\Event;

/**
 * Class ZMarkdownEnginePlugin
 * @package Grav\Plugin
 */
class ZMarkdownEnginePlugin extends Plugin
{
    // Enables blueprints for the system & page admins.
    public $features = [
        'blueprints' => 1000,
    ];

    /**
     * @return array
     *
     * The getSubscribedEvents() gives the core a list of events
     *     that the plugin wants to listen to. The key of each
     *     array section is the event that the plugin listens to
     *     and the value (in the form of an array) contains the
     *     callable (or function) as well as the priority. The
     *     higher the number the higher the priority.
     */
    public static function getSubscribedEvents()
    {
        return [
            'onPluginsInitialized' => ['onPluginsInitialized', 0],
        ];
    }

    /**
     * Initialize the plugin
     */
    public function onPluginsInitialized()
    {
        if ($this->isAdmin()) return;

        $this->enable([
            'onPageContentProcessed' => ['onPageContentProcessed', 101010],
        ]);
    }

    /**
     * When the page is processed, if Markdown rendering is disabled,
     * renders with ZMD.
     *
     * @param Event $e
     */
    public function onPageContentProcessed(Event $e)
    {
        /** @var Page $page */
        $page = $e['page'];
        $config = $this->mergeConfig($page);

        $this->active = $config->get('active', true);

        // If the plugin is not active (either global or on page), exit.
        if (!$this->active) return;

        // We now check if we should render the content using ZMD.
        $header = $page->header();
        $should_process_zmarkdown = isset($header->process) && isset($header->process['zmarkdown']) ? (bool) $header->process['zmarkdown'] : null;

        if ($should_process_zmarkdown === null)
        {
            $should_process_zmarkdown = $this->grav['config']->get('system.pages.process.zmarkdown');

            if ($should_process_zmarkdown === null)
            {
                $should_process_zmarkdown = false;
            }
        }

        if (!$should_process_zmarkdown) return;

        // Updates the content with the rendered ZMD.
        $page->setRawContent($this->renderZMarkdown($page));
    }

    private function renderZMarkdown(Page $page)
    {
        require_once(__DIR__ . '/libs/simple_html_dom.php');

        $content = $page->getRawContent();

        // First, we ask nicely the ZMD server to parse the markdown string.

        $zmd_server = $this->grav['config']->get('plugins.zmarkdown-engine.zmd_server');
        $zmd_request = ['md' => $content];

        $zmd_request_str = json_encode($zmd_request);

        $ch = curl_init($zmd_server . '/html');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $zmd_request_str);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($zmd_request_str)
        ]);

        $result = curl_exec($ch);

        if (!$result && !empty($content))
        {
            return '<div class="custom-block custom-block-error"><div class="custom-block-heading">Unable to parse Markdown</div><div class="custom-block-content">Please check that the zmarkdown server is reachable and does work.</div></div>' . "\n\n" . $content;
        }

        $html = json_decode($result)[0];

        // Then, we have some post-processing to do.
        // Grav allows to pass options to process images, and links, in Markdown. But these are processed using
        // Parsedown, and we removed it entirely. So we parse the generated HTML to find all images and links
        // to process them manually.

        // Arguments: html, lowercase, forceTagsClosed, charset (default = UTF-8), ignore line breaks.
        // We want to switch the last one as it breaks the code blocks.
        $html_tree = str_get_html($html, true, true, DEFAULT_TARGET_CHARSET, false);

        // If we can't parse it, we don't parse it. This may happen if the page is empty,
        // or if ZMD returns bad HTML (but it will very likely be the first).
        if (!$html_tree) return $html;

        // The DOMDocument does not likes the HTML5 or MathML tags. This silents
        // its errors. We don't use it directly, but Excerpts::getExcerptFromHtml do.
        libxml_use_internal_errors(true);

	    // Just in case: only images with src attributes.
        foreach ($html_tree->find('img[src]') as $element)
        {
            $element->outertext = $this->processImageHTML($element->outertext, $page);
        }

        // We process links too. Only problem: Grav's getExcerptFromHtml actually does not
        // support tags with content (content not saved, so the re-constructed tag is always
        // empty). So, we build the excerpt manually.
        foreach ($html_tree->find('a[!aria-hidden]') as $element)
        {
            // Skips footnotes
            if (strpos($element->class, 'footnote-ref') !== false) continue;
            if (strpos($element->class, 'footnote-backref') !== false) continue;

            $element->outertext = $this->processLinkHTML($element->outertext, $page);
        }

        $html = $html_tree->save();
        $html_tree->clear();

        return $html;
    }

    private function processImageHTML($html, $page)
    {
        $excerpt = Excerpts::getExcerptFromHtml($html, 'img');

        $original_src = $excerpt['element']['attributes']['src'];
        $excerpt['element']['attributes']['href'] = $original_src;

        $excerpt = Excerpts::processLinkExcerpt($excerpt, $page, 'image');

        $excerpt['element']['attributes']['src'] = $excerpt['element']['attributes']['href'];
        unset($excerpt['element']['attributes']['href']);

        $excerpt = Excerpts::processImageExcerpt($excerpt, $page);

        $excerpt['element']['attributes']['data-src'] = $original_src;

        return Excerpts::getHtmlFromExcerpt($excerpt);
    }

    private function processLinkHTML($html, $page)
    {
        $excerpt = Excerpts::getExcerptFromHtml($html, 'a');

        if ($excerpt != null)
        {
            return Excerpts::getHtmlFromExcerpt(Excerpts::processLinkExcerpt($excerpt, $page, 'link'));
        }
        else return $html;
    }
}
