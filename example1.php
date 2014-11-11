<?php
/**
 * Example of Web Scraper.
 * This example has a optional a search form submission to get data of search result.
 * So it is first make a call to create the resulting page with data. Then get the data and being processed.
 *
 * @author Jewel Ahmed<tojibon@gmail.com>
 * @author web http://codeatomic.com
 * @link https://github.com/tojibon/web-scraper
 */
 
include('Scraper.php');

class DemoS1 extends Scraper {
    var $useCookie = false;																					#necessary when only language support or authentication support with a form post call.
    var $useFormPost = true;																				#form post request call
    var $baseDomain = 'http://lejlighederfdemorederikshavn.dkdemo';											#location domain name only.
    var $listingFormPostUrl = 'http://lejlighederfdemorederikshavn.dk/lejlighederdemo.aspx';				#form post location.
    var $listingUrl = 'http://lejlighederfdemorederikshavn.dk/lejlighederdemo/soendergadedemo-227.aspx';	#existing listing page location.
    
    function __construct() {                                                       
        $this->scrape();  
    }

    // Scrape the list page and generate a data array
    function scrape() {
        $items = array();
        //Getting lisitng contents    
        if($this->useFormPost){
            $data = array(
                'ctl00$ctl00$PageContent$PageContent$ctl00$Default1$ctl00$chkTypes$0'=>'true',
                'ctl00$ctl00$PageContent$PageContent$ctl00$Default1$ctl00$chkTypes$1'=>'true',
                'ctl00$ctl00$PageContent$PageContent$ctl00$Default1$ctl00$chkTypes$2'=>'true',
                'ctl00$ctl00$PageContent$PageContent$ctl00$Default1$ctl00$chkTypes$3'=>'true',
                'ctl00$ctl00$PageContent$PageContent$ctl00$Default1$ctl00$chkTypes$4'=>'true',
                'ctl00$ctl00$PageContent$PageContent$ctl00$Default1$ctl00$btnSubmit'=>'Søg bolig'
            );
            $content = $this->aspFormPost($this->listingFormPostUrl, $data);
        } else {
            $content = $this->curl($this->listingUrl);    
        }
        

        $listing_html = $this->getValueByTagName($content, '<div id="estate_overview">', '</body>');
        $listing_html_Arr = explode('<div class="each_estate clearfix">', $listing_html);
        array_shift($listing_html_Arr);
        $i=0;
        foreach ($listing_html_Arr as $key => $rowValue) {
            $id = $i;
            
            $details_url = $this->baseDomain . $this->getValueByTagName($rowValue, 'href="', '"');
            $thumbnail = $this->baseDomain . $this->getValueByTagName($rowValue, 'src="', '"');
            $address = strip_tags($this->getValueByTagName($rowValue, '<h3 class="title">', '</h3>'));
            $description = strip_tags($this->getValueByTagName($rowValue, '</h3>', '</div>'));
            
            $items[$id]['thumbnail'] = trim($thumbnail);
            $items[$id]['address'] = trim($address);
            $items[$id]['description'] = trim($description);
            
            $details_html = $this->getValueByTagName($rowValue, '<table cellpadding="0" cellspacing="0" class="details_overview">', '</table>');
            $details_html_arr = explode('<tr>', $details_html);
            foreach($details_html_arr as $key=>$value){
                $label = $this->getValueByTagName($value, '<th>', '</th>');  
                $lblvalue = $this->getValueByTagName($value, '<td>', '</td>');  
                if(!empty($label) && !empty($lblvalue)) {
                    $items[$id][$label] = trim($lblvalue);             
                }
            }
            $items[$id]['details_url'] = $details_url;                                       
            $i++;
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
        $details_page_content = $this->getValueByTagName($content, '<div id="estatesystem">', '</body>');
        
        $address2 = $this->getValueByTagName($details_page_content, '<h1>', '</h1>');
        $data['address2'] = trim($address2);
        
        $details_html = $this->getValueByTagName($details_page_content, '<div class="right_side">', '</div>');
        $details_html_arr = explode('<tr>', $details_html);
        array_shift($details_html_arr);
        foreach($details_html_arr as $key=>$value){
            $headingstring = '<th class="header" colspan="2">';
            $pos = strpos($value, $headingstring);
            if ($pos === false) {
                $label = $this->getValueByTagName($value, '<th>', '</th>');  
                $lblvalue = $this->getValueByTagName($value, '<td>', '</td>');  
                if(!empty($label) && !empty($lblvalue)) {
                    $data[$label . ' 2'] = trim($lblvalue);             
                }
            }   
        }
        
        //Processing images
        $images_array = array();
        $image_ul_html = $this->getValueByTagName($details_page_content, '<div class="thumbnails">', "</div>");
        $images_arr = explode("href=", $image_ul_html);
        array_shift($images_arr);
        foreach($images_arr as $key=>$value){
            $src = $this->getValueByTagName($value, '"', '"');
            if(!empty($src)){
                $images_array[] = $this->baseDomain . $src; 
            }
        }
        $data['images'] = $images_array;   
        
        return $data;
    }
}

$DemoS1 = new DemoS1();