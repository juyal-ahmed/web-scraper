## PHP Web Scraping Class

1. A PHP web scraper class that utilizes the cURL library to scrape web page content. Scrape web pages using GET or POST methods. Also scrape web page content from asp.net based websites using form POST methods.
2. Support for:
    1. Get Mathod
    2. POST Method
    3. ASP Calls
    4. Retrieve Page Contents by Markup Tag Names
    5. Retrieve Values from Form Fields

### Getting a full webpage content:
<pre>
<?php
include_once( './scraper.php' );
$scraper = new Scraper();
$pageUrl = 'http://maps.google.com';
$pageHtmlContent = $scraper->getPage($pageUrl);
?>
</pre>

### Parsing a page html content:
<pre>
<?php
$subHtmlContent =  $scraper->getValueByTagName($pageHtmlContent, '<div class="itemlist">', '</div>');
?>
</pre>

Conclusion: Some example files are coming very soon for PHP and ASP web scraping. However, for some quick other examples you can follow here http://codeatomic.com/page-scraping-on-php/

Thanks
