# PHP-CookieHandler 2.0.0

PHP-CookieHandler is a simple helper class for easier access to PHP's cookie functionality.

The class provides easy to use write and read methods, helps with calculations of cookie-lifetime and generates processible outputs/results.

## Documentation

Visit https://www.simatex.de/php-cookiehandler for detailed information.

## Example

    // Include class to project
    require_once 'CookieHandler/CookieHandler.php';
    
    // Create CookieHandler object
    $Cookie = new CookieHandler("Supercookie")
    
    // Set a cookie with a 7 days lifetime
    $Cookie->WriteTextContent("Cookie content", 7);
    
    // Read cookie content from current session
    $CookieContent = $Cookie->ReadTextContent();
    
    // Delete cookie
    $Cookie->DeleteCookie();

## History

**Version 2.0.0 - 2019-02-05**
* Replacement of not meaningful return values with better exceptions and error codes
* New exception class CookieHandlerException
* Several code restructuring

**Version 1.3.0 - 2016-09-26**
* Prevent multi declaration of CookieHandler class when used in the same project
* New methods GetName(), GetPath(), GetDomain() and GetValidity() to read set information
* Various code cleanup

**Version 1.2.0 - 2015-04-19**
* New methods WriteObjectContent() and ReadObjectContent() to read or write cookie data directly from/to a PHP object instead of a string
* Correction in calculation of validity period. When validity 0 is set, the cookie lifetime is now the end of the current browser session instead of immediately

**Version 1.1.0 - 2014-05-02**
* New method DeleteCookie() to remove the complete cookie from the current session

**Version 1.0.0 - 2013-07-18**
* Initial version
