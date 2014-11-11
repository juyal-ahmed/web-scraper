<?php
/**
 * Example of Web Scraper.
 * A straight example use of the scraper without any form post. Just requesting the listing page then getting the whole content and processing it.
 *
 * @author Jewel Ahmed<tojibon@gmail.com>
 * @author web http://codeatomic.com
 * @link https://github.com/tojibon/web-scraper
 */
 
include('Scraper.php');

class DemoS3 extends Scraper {
    var $useCookie = false;														#necessary when only language support or authentication support with a form post call.
    var $baseDomain = 'http://www.pvlejedemobolig.dk';							#location domain name only.
    var $listingUrl = 'http://www.pvlejedemobolig.dk/erhverdemovslejemal.html'; #extact page location.
    
    function __construct() {                                                       
        $this->scrape();    
    }

    // Scrape the list page and generate a data array
    function scrape() {
        $items = array();
        //Getting lisitng contents    
        $content = $this->curl($this->listingUrl);
        if(strpos($content, 'Internal Server Error')) {
            echo $content;
            exit;
        }        
            
        $listing_html = $this->getValueByTagName($content, "<div id='redshopcomponent' class='redshop'>", '<div class="art-footer">');
        $listing_html_Arr = explode('<div class="category_box_inside">', $listing_html);
        array_shift($listing_html_Arr);
        
        foreach ($listing_html_Arr as $key => $rowValue) {
            $rowArr = explode('<tr>', $rowValue);                                                                                    
            
            $id = $this->getValueByTagName($rowArr[3], 'id="produkt_kasse_hoejre_pris_indre', '"');
            $headline2 = $this->getValueByTagName($rowArr[2], "'>", '</a>');
            $facts2 = $this->getValueByTagName($rowArr[3], '<div class="category_product_price"><p>', '</p></div>');
            $husleje2tmp = $this->getValueByTagName($rowArr[3], '<div class="category_product_price"><span', '</div>');
            $husleje2 = $this->getValueByTagName($husleje2tmp, '">', '</span>');
            $details_url = $this->baseDomain . $this->getValueByTagName($rowArr[1], "href='", "'");
            
            $items[$id]['ID'] = $id;
            $items[$id]['headline2'] = trim($headline2);
            $items[$id]['facts2'] = trim($facts2);
            $items[$id]['husleje2'] = trim($husleje2);
            $items[$id]['details_url'] = $details_url;                                       
        }

        // Loop through all the items in $items to get details for each
        foreach ($items as $key => $value) {
            $items[$key]['details'] = $this->scrape_detail($value['details_url']);
        }

        if ( empty( $items ) ) {
			echo 'Please check your configuration variables if it is valid or not.';
		} else {
			print_r($items);
		}
        exit;
    }
    
    // Scrape a detail page based on the details url
    // extract all data from the page
    // and return an array
    function scrape_detail($url=null) {
        //Getting details page scraped content
        
        $content = $this->curl($url);
        $details_page_content = $this->getValueByTagName($content, "<div id='redshopcomponent' class='redshop'>", '</body>');
    
        
        $headline = $this->getValueByTagName($details_page_content, '<h2>', '</h2>');
        $huslejetmp = $this->getValueByTagName($details_page_content, '<span id="produkt_kasse_hoejre_pris_indre', '</b>');
        $husleje = $this->getValueByTagName($huslejetmp, '">', '</span>');
        $factsanddescription = '<p>' . $this->getValueByTagName($details_page_content, '<p><p>', '</p></p>');
        
		$pos = strpos($factsanddescription, '<p>Se beliggenhed');
        if ($pos === false) {
        } else {
            $factsanddescription = '<start>' . $factsanddescription;
            $factsanddescription = '<p>' . $this->getValueByTagName($factsanddescription, '<start>', '<p>Se beliggenhed');    
        }

        
        $facts = $this->getValueByTagName($factsanddescription, '<p>', '</p>').'</p>';
        $facts = $this->getValueByTagName($facts, '<p>', '</p>');
        $factsanddescription.='</end>';
        $description = '<p>' . $this->getValueByTagName($factsanddescription, '<br /><p>', '</end>');
        
        
        $location = $this->getValueByTagName($details_page_content, '/maps?q=', '&amp;');
        
        $data['headline'] = trim($headline);
        $data['husleje'] = trim($husleje);
        $data['facts'] = trim($facts);
        $data['description'] = trim($description);
        $data['location'] = trim($location);
        
        //Processing images
        $main_image = $this->getValueByTagName($details_page_content, "<div class='productImageWrap'", "</div>");
        $images_array = array();
        if(!empty($main_image)){
            $images_array[] = $this->getValueByTagName($main_image, "href='", "'");
        }
        //Additional Images
        $additional_images_exp_arr = explode("<div class='additional_image'", $details_page_content);
        array_shift($additional_images_exp_arr);
        foreach($additional_images_exp_arr as $key=>$value){
            $src = $this->getValueByTagName($value, "onmouseover='display_image(\"", '"');
            if(!empty($src)){
                $images_array[] = $src; 
            }
        }
        $data['images'] = $images_array;
        return $data;
    }
}

$DemoS3 = new DemoS3();

