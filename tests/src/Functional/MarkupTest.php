<?php

declare(strict_types=1);

namespace Drupal\Tests\localgov_netcall\Functional;

use Drupal\Tests\BrowserTestBase;
use Drupal\user\Entity\Role;

/**
 * HTML markup integration test.
 */
class MarkupTest extends BrowserTestBase {

  /**
   * Validates markup.
   *
   * Things we are validating:
   * - Presence of header or footer markup.
   * - Absolute asset URLs.
   * - Stylesheet list.
   */
  public function testMarkup() {

    // Header section.
    $this->drupalGet('localgov-page-section', ['query' => ['header' => '']]);
    $this->assertSession()->statusCodeEquals(200);

    $header_element_list = $this->cssSelect('header');
    $this->assertCount(1, $header_element_list, 'Header tag found.');

    // Footer section.
    $this->drupalGet('localgov-page-section', ['query' => ['footer' => '']]);
    $this->assertSession()->statusCodeEquals(200);

    $footer_element_list = $this->cssSelect('footer');
    $this->assertCount(1, $footer_element_list, 'Footer tag found.');

    // Validate absolute asset URLs.  We are sampling the logo URL only.  The
    // logo is embedded within the footer region.
    $logo_img_element_list = $this->cssSelect('img[alt="Home"]');
    $logo_img_element = current($logo_img_element_list);

    $logo_url = $logo_img_element->getAttribute('src');
    $this->assertNotNull($logo_url);

    $logo_url_parts = parse_url($logo_url);
    $logo_url_has_hostname = array_key_exists('host', $logo_url_parts);
    $logo_url_is_absolute  = $logo_url_has_hostname;
    $this->assertTrue($logo_url_is_absolute);

    // List of Stylesheets.
    $this->drupalGet('localgov-page-section', ['query' => ['stylesheets' => '']]);
    $this->assertSession()->statusCodeEquals(200);

    $this->assertSession()->pageTextContains('.css');
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {

    parent::setUp();

    $anonymous_role = Role::load('anonymous');
    $anonymous_role->grantPermission('access content');
    $anonymous_role->save();

    $this->placeBlock('system_branding_block', ['region' => 'footer']);
  }

  /**
   * Theme used during testing.
   *
   * @var string
   */
  protected $defaultTheme = 'stark';

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = [
    'block',
    'localgov_netcall',
  ];

}
