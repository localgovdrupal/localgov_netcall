Drupal module for [Netcall Low-code platform](https://www.netcall.com/) integration.

At the moment, the module functionality is limited to providing partial page markup (e.g. header) only.  This is useful for sharing Drupal's page sections with Netcall platforms.  More functionalities are expected at later stages.

## Features
### Page markup
- Header: Provides HTML markup of the `header` tag.  Preceeded by Javascript tags that appear before the `header` tag.  Request path: `/localgov-page-section?header`.
- Footer: provides HTML makrup of the `footer` tag.  Followed by Javascript tags that appear after the `footer` tag.  Request path: `/localgov-page-section?footer`.

### Stylesheet list
- Provides a plain text list of all stylesheets mentioned within the `html > head` tag.  Request path: `/localgov-page-section?stylesheets`.

### Note
- Part of the functionality of this module has been taken from the [localgov_moderngov](https://github.com/localgovdrupal/localgov_moderngov) module.  If you want to use Drupal's page markup on any Netcall platform, please use this module instead.
