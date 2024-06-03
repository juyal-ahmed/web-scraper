<?php
/**
 * @author      Juyal Ahmed <tojibon@gmail.com>
 * @copyright   Copyright (c) Juyal Ahmed
 * @license     http://mit-license.org/
 *
 * @link        https://github.com/tojibon/web-scraper
 */

require 'vendor/autoload.php';

use PhpFarmer\WebScraper\Scraper;

class ScrappingPHPFarmer extends Scraper {															#form post request call
    public string $baseDomain = 'http://phpfarmer.com';											#location domain name only.

    function __construct() {
        // Create a Scraper instance with only the URL specified so, no cache and cookie enabled
        parent::__construct($this->baseDomain);
    }

    public function scrapPostContentByURL(string $url): ?string
    {
        $content = $this->getPageContent($url);
        return $this->getHtmlContentBetweenTags($content, '<div class="entry-content">', '</div><!-- .entry-content -->');
    }
}

$scraper = new ScrappingPHPFarmer();

$url = 'https://phpfarmer.com/2020/01/10/running-docker-containerized-app-from-local-image/';
echo $scraper->scrapPostContentByURL($url);