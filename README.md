## PHP Web Scraping Class

1. A very simple single page PHP web scraper class that utilizes the cURL library to scrape web page content. Scrape web pages using GET or POST methods. Also scrape web page content from asp.net based websites using form POST methods.
2. Support for:
    1. GET Method
    2. POST Method
    3. ASP Calls
    4. Retrieve Page Contents by Markup Tag Names
    5. Retrieve Values from Form Fields

## Installation
```
composer require juyal-ahmed/web-scraper
```

### Getting a full webpage content:
<pre>
&lt;?php
require 'vendor/autoload.php';

// Create a Scraper instance with only the URL specified
$scraper = new \PhpFarmer\WebScraper\Scraper('https://example.com');
$pageHtmlContent = $scraper->getPageContent('https://example.com/page.html');
?&gt;
</pre>

### Getting a full webpage content:
<pre>
&lt;?php
require 'vendor/autoload.php';

// Create a Scraper instance with custom cache settings
$scraperWithCache = new Scraper('https://example.com', true, './custom_cache/', 600);
$pageHtmlContent = $scraper->getPageContent('https://example.com/page.html');
?&gt;
</pre>

### Getting a full webpage content with Using Proxy IP:
<pre>
&lt;?php
require 'vendor/autoload.php';

// Create a Scraper instance with only the URL specified
$scraper = new \PhpFarmer\WebScraper\Scraper('https://example.com');
$pageHtmlContent = $scraper->curl('https://example.com/page.html', "93.118.xx.141:8800", "6USERR:8PASS1");
?&gt;
</pre>

### Parsing a page html content:
<pre>
&lt;?php
$subHtmlContent =  $scraper->getHtmlContentBetweenTags($pageHtmlContent, '<div class="entry-content">', '</div><!-- .entry-content -->');
?&gt;
</pre>

### How It Works:
1. Include The Class scraper.php in your Working page header.
2. Set some default settings.
3. Get the page content by its existing methods.
4. Split your content by getHtmlContentBetweenTags methods if single content you are searching for.
5. If grid data needed, split the content with a needle Ex: explode()
6. Then loop it whole and get the content by getHtmlContentBetweenTags again to make the final array of grid data.
7. That's' all

Thanks
