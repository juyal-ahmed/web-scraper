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

class DemoS2 extends Scraper {
    var $useCookie = false;							#necessary when only language support or authentication support with a form post call.
    var $baseDomain = 'http://lykdemokebo.dk';		#location domain name only.
    var $listingUrl = 'http://lykdemokebo.dk/';		#extact page location.
    
    function __construct() {                                                       
        $this->scrape();  
    }

    // Scrape the list page and generate a data array
    function scrape() {
        $items = array();
        //Getting lisitng contents    
        $content = $this->curl($this->listingUrl);
        
        $listing_html = $this->getValueByTagName($content, '<!--webbot bot="Include" U-Include="06_0versigt_ledige/total_oversigt.htm" TAG="BODY" startspan -->', '<table border="1" cellpadding="0" cellspacing="0" style="border-width:0; border-collapse: collapse" bordercolor="#111111" width="465" align="left">');
        $listing_html_Arr = explode('<tr>', $listing_html);
        array_shift($listing_html_Arr);
        array_shift($listing_html_Arr);
        
        
        $i=0;
        foreach ($listing_html_Arr as $key => $rowValue) {
            $id = $i;
            $rowArr = explode('<td', $rowValue);                                                                                    
            $td0val = trim($rowArr[0]);
            if(empty($td0val)){
                array_shift($rowArr);         
            }
            
            $headingstring = '<b><font face="Verdana" size="2">Dato</font></b>';
            $pos = strpos($rowValue, $headingstring);
            if ($pos === false) {
                //Ligsint data
                if(!empty($rowArr[0]))
                $dato = $this->getValueByTagName($rowArr[0], '">', '</td>');       
                else
                $dato = '';
                
                if(!empty($rowArr[1]))
                $address = $this->getValueByTagName($rowArr[1], '">', '</td>');       
                else
                $address = '';
                
                if(!empty($rowArr[1])) {
                    $details_url = $this->getValueByTagName($rowArr[1], ' href="', '"');       
                    if(!empty($details_url))
                        $details_url = $this->baseDomain .'/'. $details_url;
                } else
                    $details_url = '';
                
                if(!empty($rowArr[2]))
                    $areal = $this->getValueByTagName($rowArr[2], '">', '</td>');       
                else
                    $areal = '';
                    
                if(!empty($rowArr[3]))
                    $prmd = $this->getValueByTagName($rowArr[3], '">', '</td>');       
                else
                    $prmd = '';
                    
                if(!empty($rowArr[4]))
                    $bem = $this->getValueByTagName($rowArr[4], '">', '</td>');      
                else
                    $bem = '';
                
                //$items[$id]['ID'] = $id;
                $items[$id][$lblDato] = trim($dato);
                $items[$id]['address'] = trim(strip_tags($address, '<span><br><strong><p><font>'));
                $items[$id][$lblAreal] = trim($areal);                                       
                $items[$id][$lblPrmd] = trim($prmd);                                       
                $items[$id][$lblBem] = trim($bem);                                       
                $items[$id]['details_url'] = trim($details_url);                                       
                $i++;      
            } else {
                //$address = $this->getValueByTagName($rowArr[1], '">', '</td>');  
                $lblDato = $this->getValueByTagName($rowArr[0], '">', '</td>');     
                $lblDato = trim(strip_tags($lblDato));
                
                $lblAreal = $this->getValueByTagName($rowArr[2], '">', '</td>');     
                $lblAreal = trim(strip_tags($lblAreal));
                
                $lblPrmd = $this->getValueByTagName($rowArr[3], '">', '</td>');     
                $lblPrmd = trim(strip_tags($lblPrmd));
                
                $lblBem = $this->getValueByTagName($rowArr[4], '">', '</td>');      
                $lblBem = trim(strip_tags($lblBem));          
            }              
        }

        // Loop through all the items in $items to get details for each
        foreach ($items as $key => $value) {
            if(!empty($value['details_url']))
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
        $details_page_content = $this->getValueByTagName($content, "</head>", '</html>');
        
        $address2 = $this->getValueByTagName($details_page_content, '<u>', '</u>');
        $data['address2'] = trim(strip_tags($address2));
        
        $ledig = $this->getValueByTagName($details_page_content, '<font face="Verdana" size="3">', '</font>');
        $data['ledig'] = trim($ledig);
        
        $teaser = $this->getValueByTagName($details_page_content, '<i>', '</i>');         
        $data['teaser'] = trim('<b><i>' . $teaser . '</i></b>');
        
        $remarks = $this->getValueByTagName($details_page_content, '<td style="border-left:1px solid #111111; border-right:1px solid #111111; border-top-style:solid; border-top-width:1; border-bottom-style:solid; border-bottom-width:1" width="559" align="center" colspan="2">', '</td>');         
        $data['remarks'] = trim($remarks);
        
        $details_table_html = $this->getValueByTagName($details_page_content, 'id="table1">', '</table>');         
        $details_table_arr = explode('<tr>', $details_table_html);
        array_shift($details_table_arr);
        foreach($details_table_arr as $key=>$value){
            $valarr = explode('<td', $value);    
            array_shift($valarr);
            
            $label = strip_tags($this->getValueByTagName($valarr[0], '">', '</td>'));         
            $value = $this->getValueByTagName($valarr[1], '">', '</td>');         
            
            $label = trim($label);
            if(!empty($label) && $label!='&nbsp;'){
                $data[$label] = trim($value);    
            }
        }
        
        //Processing images
        $baseImagePath = '';
        $baseDir = basename($url);
        $baseImagePath = str_replace($baseDir, '', $url);
        $this->baseImagePath = $baseImagePath;
        
        $image_html = $this->getValueByTagName($details_page_content, '<table border="1" bordercolor="#FFFFFF" width="594" cellspacing="1" style="border-width: 0" height="255">', '</table>');         
        $images_array = array();
        $images_exp_arr = explode("<tr>", $image_html);
        array_shift($images_exp_arr);
        foreach($images_exp_arr as $key=>$value){
            $valarr = explode('<td', $value);    
            array_shift($valarr);
            
            $src = $this->getValueByTagName($valarr[0], 'src="', '" width');
            if(!empty($src)){
                $images_array[] = $this->baseImagePath . $src; 
            } else {
                $description = trim($this->getValueByTagName($valarr[0], '">', '</td>'));       
                if(!empty($description) && $description!="&nbsp;"){
                    $data['description'] = trim($description);        
                }
            }
            
            if(!empty($valarr[1])){
                 $src = $this->getValueByTagName($valarr[1], 'src="', '" width');
                if(!empty($src)){
                    $images_array[] = $this->baseImagePath . $src; 
                } else {
                    $description = trim($this->getValueByTagName($valarr[1], '">', '</td>'));       
                    if(!empty($description) && $description!="&nbsp;"){
                        $data['description'] = trim($description);        
                    }
                }
            } 
        }
        
        $data['images'] = $images_array; 
        return $data;
    }
}

$DemoS2 = new DemoS2();

