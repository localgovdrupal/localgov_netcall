<?php

declare(strict_types=1);

namespace Drupal\localgov_netcall;

/**
 * Grabs HTML header and footer markup.
 *
 * Extracts HTML markup for header, footer and associated scripts and styles.
 */
class HeaderFooterExtraction {

  /**
   * Header markup.
   *
   * Includes header and all scripts.
   *
   * Example output:
   * ```
   * <div class="scripts">
   *   <script src="https://example.net/script0.js"></script>
   *   <script src="https://example.net/script1.js"></script>
   * </div>
   *
   * <div class="pre-header-body-scripts">
   *   <script>alert('foo')</script>
   * </div>
   *
   * <header>
   *   ...
   * </header>
   * ```
   */
  public static function prepareHeader(\DOMDocument $html_dom): string {

    $header_list = $html_dom->getElementsByTagName('header');
    if (!$header_list->count()) {
      return '';
    }
    $header = self::toHtml($html_dom, $header_list->item(0));

    $xpath         = new \DOMXpath($html_dom);
    $head_scripts  = self::extractHeadScripts($html_dom, $xpath);
    $other_scripts = self::extractPreHeaderScripts($html_dom, $xpath);

    $result = $head_scripts . PHP_EOL . $other_scripts . PHP_EOL . $header;
    $trimmed_result = trim($result);
    return $trimmed_result;
  }

  /**
   * Footer markup.
   *
   * Includes footer and any script tags that follow the footer tag.
   *
   * Sample output:
   * ```
   * <footer>
   * ...
   * </footer>
   *
   * <div class="post-footer-body-scripts">
   *   <script src="https://example.net/script2.js"></script>
   *   <script src="https://example.net/script3.js"></script>
   * </div>
   * ```
   */
  public static function prepareFooter(\DOMDocument $html_dom): string {

    $footer_list = $html_dom->getElementsByTagName('footer');
    if (!$footer_list->count()) {
      return '';
    }

    $footer = self::toHtml($html_dom, $footer_list->item(0));

    $xpath = new \DOMXpath($html_dom);
    $other_scripts = self::extractPostFooterScripts($html_dom, $xpath);

    $result = $footer . PHP_EOL . $other_scripts;
    $trimmed_result = trim($result);
    return $trimmed_result;
  }

  /**
   * Plain text list of stylesheets.
   *
   * Sample output:
   * ```
   * https://example.net/stylesheet1.css
   * https://example.net/stylesheet2.css
   * ...
   * ```
   */
  public static function prepareStylesheets(\DOMDocument $html_dom): string {

    $stylesheet_urls = [];

    $xpath = new \DOMXpath($html_dom);
    $tags = $xpath->query('/html/head/link[@rel="stylesheet"]');
    foreach ($tags as $dom_node) {
      $stylesheet_urls[] = $dom_node->getAttribute('href');
    }
    $nonempty_stylesheet_urls = array_filter($stylesheet_urls);

    $stylesheet_url_list = implode(PHP_EOL, $nonempty_stylesheet_urls);
    return $stylesheet_url_list;
  }

  /**
   * Script tags from the head tag.
   */
  public static function extractHeadScripts(\DOMDocument $html_dom, \DOMXpath $xpath): string {

    return self::extractMarkup($html_dom, $xpath, '/html/head/script', 'scripts');
  }

  /**
   * Script tags preceeding the header tag.
   */
  public static function extractPreHeaderScripts(\DOMDocument $html_dom, \DOMXpath $xpath): string {

    return self::extractMarkup($html_dom, $xpath, '/html/body//script[following::header]', 'pre-header-body-scripts');
  }

  /**
   * Script tags following the footer tag.
   */
  public static function extractPostFooterScripts(\DOMDocument $html_dom, \DOMXpath $xpath): string {

    return self::extractMarkup($html_dom, $xpath, '/html/body//script[preceding::footer]', 'post-footer-body-scripts');
  }

  /**
   * Extracts a chunk of a page.
   *
   * Extracts everything based on the given XPath query and return as an HTML
   * string wrapped in a wrapper div.
   */
  public static function extractMarkup(\DOMDocument $html_dom, \DOMXpath $xpath, $xpath_query, $wrapper_class): string {

    $tags = $xpath->query($xpath_query);
    if (!$tags->count()) {
      return '';
    }

    $wrapper_element = self::createEmptyDiv($html_dom, $wrapper_class);
    foreach ($tags as $dom_node) {
      $wrapper_element->append($dom_node);
    }

    return self::toHtml($html_dom, $wrapper_element);
  }

  /**
   * Creates a wrapper div.
   */
  public static function createEmptyDiv(\DOMDocument $html_dom, string $classname = ''): \DOMNode {

    $empty_div = $html_dom->createElement('div');
    if ($classname) {
      $empty_div->setAttribute('class', $classname);
    }

    return $empty_div;
  }

  /**
   * HTML DOM to string.
   */
  public static function toHtml(\DOMDocument $html_dom, ?\DOMNode $node = NULL): string {

    $html = $html_dom->saveHtml($node);
    return $html;
  }

}
