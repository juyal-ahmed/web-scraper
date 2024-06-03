<?php
/**
 * @author      Juyal Ahmed <tojibon@gmail.com>
 * @copyright   Copyright (c) Juyal Ahmed
 * @license     http://mit-license.org/
 *
 * @link        https://github.com/tojibon/web-scraper
 */

namespace PhpFarmer\WebScraper\Traits;

trait AspFormPostTrait
{
    /*
    *
    * @param string $url as 'http://example-web-page.com/about.html' Page URL location which you want to fetch
    * @param array  $data
    * @return HTML content of given URL
    * */
    public function aspFormPost($url, $curlPostFieldData = array()){
        $regexViewState = '/__VIEWSTATE\" value=\"(.*)\"/i';
        $regexEventVal  = '/__EVENTVALIDATION\" value=\"(.*)\"/i';

        $regs = array();
        $this->cookieFile = tempnam ("/tmp", "CURLCOOKIE");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $content=curl_exec($ch);
        $curlPostFieldData['__VIEWSTATE'] = $this->regexExtract($content,$regexViewState,$regs,1);
        $curlPostFieldData['__EVENTVALIDATION'] = $this->regexExtract($content, $regexEventVal,$regs,1);

        curl_setOpt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPostFieldData);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookieFile);

        $content = curl_exec($ch);
        if($content === false) {
            $content = curl_error($ch);
        }
        curl_close($ch);

        return $content;
    }

    /*
    *
    * @param string $text Full html content which you want to parse
    * @param string $regex rgx
    * @param string $regs will be set to an array of all group values (assuming a match)
    * @param string $nthValue nth number value example where n= 1,2,3,4
    * @return a value of matched string
    * */
    private function regexExtract($text, $regex, $regs, $nthValue) {
        $result = "";

        if (preg_match($regex, $text, $regs)) {
            $result = $regs[$nthValue];
        }

        return $result;
    }
}