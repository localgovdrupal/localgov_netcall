<?php

namespace Drupal\localgov_netcall\EventSubscriber;

use Drupal\Core\Render\HtmlResponse;
use Drupal\localgov_netcall\HeaderFooterExtraction;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Responds with partial page content.
 *
 * - Converts all relative URLs in the page to absolute URLs.
 * - Returns the `header` tag and its children when the `header` HTTP query
 *   parameter is present.  Preceeded by div containers whose children are
 *   `script` tags that appear above the `header` tag in the page markup.
 * - Returns the `footer` tag and its children when the `footer` HTTP query
 *   parameter is present.  Followed by a div container whose children are
 *   `script` tags that appear below the `footer` tag in the page markup.
 * - Returns URLs of all CSS files as a plain text list when the 'stylesheets'
 *   HTTP query parameter is present.
 *
 * @todo Both the `header` and `footer` can happen more than once in a page.  At
 *       the moment we only process the very first of these.  This needs fixing.
 */
class HtmlResponseSubscriber implements EventSubscriberInterface {

  /**
   * Alters browser-bound page content.
   *
   * Converts all relative URLs to absolute.  Then a few more alterations as
   * mentioned in the class comment above.
   */
  public function onRespond(ResponseEvent $event) {

    $request = $event->getRequest();
    $has_header_req = !is_null($request->get('header'));
    $has_footer_req = !is_null($request->get('footer'));
    $has_stylesheet_req = !is_null($request->get('stylesheets'));
    $route_name = $request->get('_route');
    $response = $event->getResponse();

    if (!$response instanceof HtmlResponse || $route_name !== 'localgov_netcall.page_tpl') {
      return;
    }

    $html = $response->getContent();
    $html_dom = self::toDom($html);

    $request_scheme_and_host = $request->getSchemeAndHttpHost();
    $html_dom_with_absolute_urls = self::transformRootRelativeUrlsToAbsolute($html_dom, $request_scheme_and_host);

    if ($has_header_req) {
      $result = HeaderFooterExtraction::prepareHeader($html_dom_with_absolute_urls);
    }
    elseif ($has_footer_req) {
      $result = HeaderFooterExtraction::prepareFooter($html_dom_with_absolute_urls);
    }
    elseif ($has_stylesheet_req) {
      $result = HeaderFooterExtraction::prepareStylesheets($html_dom_with_absolute_urls);
      $response->headers->set('Content-Type', 'text/plain');
    }
    else {
      $result = '';
    }

    $response->setContent($result);
  }

  /**
   * Converts all root-relative URLs to absolute URLs.
   *
   * Based on
   * Drupal\Component\Utility\Html::transformRootRelativeUrlsToAbsolute()
   * which processes Html *body* content only.  Whereas here, we process the
   * entire HTML document.
   *
   * @param \DOMDocument $html_dom
   *   The partial (X)HTML snippet to load. Invalid markup will be corrected on
   *   import.
   * @param string $scheme_and_host
   *   The root URL, which has a URI scheme, host and optional port.
   *
   * @return \DOMDocument
   *   The updated (X)HTML snippet.
   *
   * @see Drupal\Component\Utility\Html::transformRootRelativeUrlsToAbsolute()
   */
  public static function transformRootRelativeUrlsToAbsolute(\DOMDocument $html_dom, $scheme_and_host): \DOMDocument {

    $xpath = new \DOMXpath($html_dom);

    $uriAttributes = [
      'href', 'poster', 'src', 'cite', 'data',
      'action', 'formaction', 'srcset', 'about',
    ];

    // Update all root-relative URLs to absolute URLs in the given HTML.
    foreach ($uriAttributes as $attr) {
      foreach ($xpath->query("//*[starts-with(@$attr, '/') and not(starts-with(@$attr, '//'))]") as $node) {
        $node->setAttribute($attr, $scheme_and_host . $node->getAttribute($attr));
      }
      foreach ($xpath->query("//*[@srcset]") as $node) {
        // @see https://html.spec.whatwg.org/multipage/embedded-content.html#attr-img-srcset
        // @see https://html.spec.whatwg.org/multipage/embedded-content.html#image-candidate-string
        $image_candidate_strings = explode(',', $node->getAttribute('srcset'));
        $image_candidate_strings = array_map('trim', $image_candidate_strings);
        for ($i = 0; $i < count($image_candidate_strings); $i++) {
          $image_candidate_string = $image_candidate_strings[$i];
          if ($image_candidate_string[0] === '/' && $image_candidate_string[1] !== '/') {
            $image_candidate_strings[$i] = $scheme_and_host . $image_candidate_string;
          }
        }
        $node->setAttribute('srcset', implode(', ', $image_candidate_strings));
      }
    }

    return $html_dom;
  }

  /**
   * HTML string to DOM.
   */
  public static function toDom(string $html): \DOMDocument {

    $html_dom = new \DOMDocument();
    // Ignore warnings during HTML soup loading.
    @$html_dom->loadHTML($html);

    return $html_dom;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {

    // This event subscriber needs to know about the route so it needs to have
    // a priority of 31 or less. To modify headers a priority of 0 or less is
    // needed.
    $events[KernelEvents::RESPONSE][] = ['onRespond', -10];

    return $events;
  }

}
