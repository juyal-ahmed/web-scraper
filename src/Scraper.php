<?php
/**
 * @author      Juyal Ahmed <tojibon@gmail.com>
 * @copyright   Copyright (c) Juyal Ahmed
 * @license     http://mit-license.org/
 *
 * @link        https://github.com/tojibon/web-scraper
 */

namespace PhpFarmer\WebScraper;

use PhpFarmer\WebScraper\Traits\AspFormPostTrait;

class Scraper {
    use AspFormPostTrait;

    /**
     * @var string Base URL of the page to be scraped
     */
    public string $url;

    /**
     * @var bool Set to true to enable caching feature
     */
    public bool $enableCache;

    /*
     * @var string Directory/path of cached contents to be stored
     */
    public string $cacheLocation;

    /**
     * @var int Cache Time To Live in seconds
     */
    public int $cacheTTL;

    /*
     * @var bool Set to true if you need to use cookies for language or authentication support
     */
    public bool $enableCookie;

    /**
     * @var string File/path of the cookie file to store cookies
     */
    public string $cookieFile;

    /**
     * Constructor for Scraper class.
     *
     * @param string $url The base URL of the page to be scraped.
     * @param bool $enableCache Set to true to enable caching feature.
     * @param string $cacheLocation Directory/path of cached contents to be stored.
     * @param int $cacheTTL Cache Time To Live in seconds.
     * @param bool $enableCookie Set to true if you need to use cookies for language or authentication support.
     * @param string $cookieFile File/path of the cookie file to store cookies.
     */
    public function __construct(
        string $url,
        bool $enableCache = false,
        string $cacheLocation = "./cache/",
        int $cacheTTL = 300,
        bool $enableCookie = false,
        string $cookieFile = './cookies.txt'
    ) {
        $this->url = $url;
        $this->enableCache = $enableCache;
        $this->cacheLocation = $cacheLocation;
        $this->cacheTTL = $cacheTTL;
        $this->enableCookie = $enableCookie;
        $this->cookieFile = $cookieFile;
    }
        
    /*
    *
    * @param string $url as 'http://phpfarmer.com'; Page URL which you want to fetch
    * @param array  $data
    * @param string $proxy [optional] as '[proxy IP]:[port]'; Proxy address and port number which you want to use
    * @param string $userpass [optional] as '[username]:[password]'; Proxy authentication username and password
    * @return a url page HTML content
     *
    * */
    public function getPageSubmitContent(string $url, array $data = [], string $proxy = '', string $userNameAndPassword = '', array $header = []): string
    {
        // Initialize cURL session
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        if(!empty($header)){
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header );
            curl_setopt($ch,CURLOPT_ENCODING , "gzip");
        }

        // Set the CURLOPT_COOKIEJAR option to store cookies in a file
        if($this->enableCookie)
            curl_setopt ($ch, CURLOPT_COOKIEJAR, $this->cookieFile);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

        if(!empty($proxy))
            curl_setopt($ch, CURLOPT_PROXY, $proxy);

        if(!empty($userNameAndPassword))
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, $userNameAndPassword);

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $result = curl_exec($ch);
        if($result === false) {
            $result = curl_error($ch);
        }

        curl_close($ch);
        return $result;
    }

    /*
    *
    * @param string $url as 'http://phpfarmer.com'; Page url location which you want to fetch
    * @param string $proxy [optional] as '[proxy IP]:[port]'; Proxy address and port number which you want to use
    * @param string $userpass [optional] as '[username]:[password]'; Proxy authentication username and password
    * @return a url page HTML content
     *
    * */
    public function getPageContent(string $url, string $proxy = '', string $userNameAndPassword = ''): string
    {
        // Initialize cURL session
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        // Set the CURLOPT_COOKIEFILE option to load cookies from the file
        if($this->enableCookie)
            curl_setopt ($ch, CURLOPT_COOKIEFILE, $this->cookieFile);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        if(!empty($proxy))
            curl_setopt($ch, CURLOPT_PROXY, $proxy);

        if(!empty($userNameAndPassword))
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, $userNameAndPassword);

        $result = curl_exec($ch);
        if($result === false) {
            $result = curl_error($ch);
        }

        curl_close($ch);
        return $result;
    }

    /**
     * Get the middle HTML content between the specified start and end tags in the provided HTML data.
     *
     * @param string $html Full HTML content to parse
     * @param string $startTag Start tag to search for in the HTML
     * @param string $endTag End tag to search for in the HTML
     * @return string Middle HTML content between the start and end tags, or an empty string if not found
     */
    public function getHtmlContentBetweenTags(string $html, string $startTag, string $endTag): string
    {
        $startPos = strpos($html, $startTag);
        if ($startPos === false) {
            return '';
        }

        $startPos += strlen($startTag);
        $endPos = strpos($html, $endTag, $startPos);

        if ($endPos === false) {
            return '';
        }

        return substr($html, $startPos, $endPos - $startPos);
    }

    /*
    *
    * Checking if cache file exist
    * @ param string $name cache file name
    *
    */
    private function checkIfCacheAvailable($name): bool
    {
        if(!$this->isCacheEnabled()) return false;

        $cacheFile = $this->cacheLocation . $name;
        $cacheTime = $this->cacheTTL;

        return file_exists($cacheTime) && (time() - $cacheTime < filemtime($cacheFile));
    }

    /*
    *
    * Reading cache file
     *
    * @ param string $name cache file name
    */
    private function readCache($name){
        if(!$this->isCacheEnabled()) return false;

        $cacheFile = $this->cacheLocation . $name;
        return file_get_contents($cacheFile, FILE_USE_INCLUDE_PATH);
    }

    /*
    *
    * Writing cache file with contents
     *
    * @param string $name cache file name
    * @param string $content cache content to write on cache file
    */
    private function writeCache($name, $content){
        if(!$this->isCacheEnabled()) return false;

        $cacheFile = $this->cacheLocation . $name;
        $fp = fopen($cacheFile, 'w');
        fwrite($fp, $content);
        fclose($fp);

        return $this;
    }

    /**
     *
     * Check if Cache enabled and cache location set to a valid directory
     *
     * @return bool
     */
    private function isCacheEnabled(): bool
    {
        return ($this->enableCache && is_dir($this->cacheLocation));
    }
}           
    
