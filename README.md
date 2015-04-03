# SymfonyExtra
Provides common additions to Symfony.

# Installation

```bash
composer require mpom/symfony-extra-bundle
```

Add to your AppKernel.php:
```php
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new SymfonyExtraBundle\SymfonyExtraBundle,
            // ...
        )
    }
```

# Configuration

In your parameters.yml file add 2 new options:
```yml
locale: en # default locale
locales: [en, de, it, fr] # all supported locales
```

# What's inside?
### Locale autodetector
Detects user's locale using different strategies (in this order):  
* Query  
* Cookie  
* Accept Header  

Provides LocaleManger service (@se_locale_manager)

### Twig
#### Pagination function
Provides pagination function for rendering paginations ;D

```twig
{{ pagination(totalItems, itemsPerPage, route, currentPage = 1, template ='SymfonyExtraBundle::pagination.html.twig') }}
```
#### Money filter
Formats string as money appending currency symbol (locale-based).
```twig
{{ value|money }} # CHF 1'200.00
```

### SwiftMailer
New transports:  
* Mandrill (requires installation of "hipaway-travel/mandrill-bundle")  
* File  

####Plugins:
#####CssToInline
Extracts styles external css file into inline styles for every html message.
 requires "tijsverkoyen/css-to-inline-styles".  
 **Configuration**
 Set option "email_css_file" of section "symfony_extra" of  "config.yml" to valid *.less file.


