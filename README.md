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
&lt;?php
include_once( './scraper.php' );
$scraper = new Scraper();
$pageUrl = 'http://maps.google.com';
$pageHtmlContent = $scraper->getPage($pageUrl);
?&gt;
</pre>

### Parsing a page html content:
<pre>
&lt;?php
$subHtmlContent =  $scraper->getValueByTagName($pageHtmlContent, '&lt;div class="itemlist"&gt;', '&lt;/div&gt;');
?&gt;
</pre>

### How It Works:
1. Include The Class scraper.php in your Working page header.
2. Set some default settings.
3. Get the page content by it's existing methods.
4. Split your content by getValueByTagName methods if single content you are searching for.
5. If grid data needed, split the content with a needle Ex: explode()
6. Then loop it whole and get the content by getValueByTagName again to make the filnal array of grid data.
7. Thats' all

Thanks