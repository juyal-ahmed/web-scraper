<?php
/**
 * @author      Juyal Ahmed <tojibon@gmail.com>
 * @copyright   Copyright (c) Juyal Ahmed
 * @license     http://mit-license.org/
 *
 * @link        https://github.com/tojibon/web-scraper
 */

namespace PhpFarmer\WebScraper\Traits;

trait TableKeyValuePairArray
{
    /**
     * Load the HTML content into a DOMDocument object and parse it to get key value pair of 2 TD based table content where first td is expected to be the name of the field and second td is expected to be the value.
     *
     * @param $content
     * @param bool $labelStripTag
     * @param bool $valueStripTag
     * @return array
     */
    function tableKeyValuePairArray($content, bool $labelStripTag = true, bool $valueStripTag = false): array
    {
        $resultDataArray = [];

        // Use DOMDocument to parse the HTML content
        $doc = new \DOMDocument();
        $doc->loadHTML($content);

        // Get all table rows
        $rows = $doc->getElementsByTagName('tr');

        foreach ($rows as $row) {
            $cols = $row->getElementsByTagName('td');
            $label = trim($cols->item(0)->nodeValue);
            $value = trim($cols->item(1)->nodeValue);

            if ($labelStripTag) {
                $label = strip_tags($label);
            }
            if ($valueStripTag) {
                $value = strip_tags($value);
            }

            if (!empty($label)) {
                $resultDataArray[$label] = $value;
            }
        }

        return $resultDataArray;
    }
}