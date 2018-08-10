<?php

namespace App\Http\Controllers\Ebay;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EbayController extends Controller

{
	public function __contruct(){

		include(app_path() . '\config\config.php');

	}
    public function uploadCSV(){
    	$item=$this->getItem(192281326005);
    	$callName = 'ReviseFixedPriceItem';
    	$version = '845';
    	$url = EBAY_HOST . "wsapi?callname=${callName}&siteid=" . SITE_ID . "&appid=" . APP_ID . "&version=${version}&routing=default";
    	$title='Parts Unlimited R09-774X Ring Set 64.50mm.';
    	$titleNode = '<Title><![CDATA[' . trim($title) . ']]></Title>';

    	//----
    	$oldCondition ='New';
    	$newDescription ='newDescription';
    	$partNumbers = [];
    	$title = 'title';
    	$brand= 'brand';
    	$oldCondition ='oldCondition';
    	$notes ='notes';
    	$ranges ='ranges';
    	//----

    	if (!empty($item['mpn'])) {
            $mpnNode = '<NameValueList><Name>Manufacturer Part Number</Name><Value><![CDATA[' . trim($item['mpn']) . ']]></Value></NameValueList>';
            $pns = explode('/', $item['mpn']);
            foreach ($pns as $pn) $partNumbers[] = trim($pn);
        }
        if (!empty($item['brand']))
            $brandNode = '<NameValueList><Name>Brand</Name><Value><![CDATA[' . trim($item['brand']) . ']]></Value></NameValueList>';

    	$ch = curl_init('http://integra2.eocenterprise.com/api/ebay/raw_preview_v2');
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'title' => $title,
            'desc' => $newDescription,
            'brand' => trim($brand),
            'condition' => $oldCondition,
            'partNumbers' => implode("\n", $partNumbers),
            'notes' => $notes,
            'ranges' => $ranges
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $descNode = curl_exec($ch);
        curl_close($ch);
    	$ebayToken = EBAY_TOKEN;
    	$itemId = 192281326005;



    	        $data = <<< EOD
<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">
    <s:Header>
        <h:RequesterCredentials xmlns:h="urn:ebay:apis:eBLBaseComponents" xmlns="urn:ebay:apis:eBLBaseComponents" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema"><eBayAuthToken>${ebayToken}</eBayAuthToken></h:RequesterCredentials>
    </s:Header>
    <s:Body xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">
    <${callName}Request xmlns="urn:ebay:apis:eBLBaseComponents">
        <Version>${version}</Version>
        <DeletedField>Item.ProductListingDetails</DeletedField>
        <Item>
            <ItemID>${itemId}</ItemID>
            <ItemSpecifics>${mpnNode}${brandNode}</ItemSpecifics>
            ${titleNode}
            <ProductListingDetails>
                <UPC><![CDATA[N/A]]></UPC>
            </ProductListingDetails>
        </Item>
    </${callName}Request>
    </s:Body>
</s:Envelope>
EOD;
$headers = array
        (
            'Content-Type: text/xml',
            'SOAPAction: ""'
        );
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($ch);
        curl_close($ch);
        dd($res);
	    	dd($data);

    }
    public function getCSV(){
    	return view('ebay.getcsv');
    }
    public function postCSV(Request $request){
    	$input = $request->all();
    	if($request->hasFile('file')){
    		dd($input);
    	}
    	dd('asdsd');
    	$array = ['title','description'];
    	$contents =['Parts Unlimited R09-774X Ring Set 64.50mm','Description'];

		$file = fopen("products.csv","w");
		fputcsv($file,$array);
		fclose($file);
    	dd($input);
    }
    public function getItem($itemId){
    	$res = file_get_contents("http://open.api.ebay.com/shopping?callname=GetSingleItem&responseencoding=XML&appid=" . APP_ID . "&siteid=0&version=847&ItemID=${itemId}&IncludeSelector=Details,Compatibility,ItemSpecifics,Description");
        $xml = simplexml_load_string($res);
        if ($xml->Ack != 'Success' && $xml->Ack != 'Warning')
            return false;
        $item['id'] = (string)$xml->Item->ItemID;

        $item['title'] = (string)$xml->Item->Title;
        $item['description'] = (string)$xml->Item->Description;
        $item['category'] = (string)$xml->Item->PrimaryCategoryID;
        $allCat = (string)$xml->Item->PrimaryCategoryName;
        $cats = explode(':', $allCat);
        $catName = $allCat;
        if (count($cats) > 1)
            $catName = $cats[count($cats) - 1];
        $item['category_name'] = $catName;
        $item['num_avail'] = (string)$xml->Item->Quantity;
        $item['price'] = (string)$xml->Item->CurrentPrice;
        $item['num_sold'] = (string)$xml->Item->QuantitySold;
        $hits = (string)$xml->Item->HitCount;
        settype($hits, 'integer');
        $item['num_hit'] = $hits;
        $item['condition'] = (string)$xml->Item->ConditionDisplayName;
        $item['sku'] = (string)$xml->Item->SKU;
        $item['seller_id'] = (string)$xml->Item->Seller->UserID;
        $item['seller_score'] = (string)$xml->Item->Seller->FeedbackScore;
        $item['seller_rating'] = (string)$xml->Item->Seller->PositiveFeedbackPercent;
        $item['seller_top'] = ((string)$xml->Item->Seller->TopRatedSeller == 'true' ? 1 : 0);
        $item['picture_small'] = (string)$xml->Item->GalleryURL;
        $item['picture_big'] = (string)$xml->Item->PictureURL;
        $item['shipping_cost'] = (string)$xml->Item->ShippingCostSummary->ShippingServiceCost;
        $item['shipping_type'] = (string)$xml->Item->ShippingCostSummary->ShippingType;
        if ($item['shipping_type'] == 'Calculated')
        {
            $res = file_get_contents("http://www.ebay.com/itm/getrates?item=${itemId}&quantity=1&country=1&zipCode=77057&co=0&cb=j");
            unset($match);
            preg_match('/US \$(?P<shipping>[^<]+)/i', $res, $match);
            if (isset($match) && array_key_exists('shipping', $match))
                $item['shipping_cost'] = $match['shipping'];
        }
        $item['num_compat'] = (string)$xml->Item->ItemCompatibilityCount;
        if (empty($item['num_compat']))
            $item['num_compat'] = 0;
        $item['compatibility'] = str_replace('<NameValueList/>', '', (string)$xml->Item->ItemCompatibilityList->asXML());
        if (!empty($item['compatibility']))
        {
            $xml2 = simplexml_load_string($item['compatibility']);
            if (!empty($xml2->Compatibility))
            {
                foreach ($xml2->Compatibility as $c)
                {
                    $fit['make'] = '';
                    $fit['model'] = '';
                    $fit['year'] = '';
                    $fit['trim'] = '';
                    $fit['engine'] = '';
                    $fit['notes'] = '';
                    foreach ($c->NameValueList as $n)
                    {
                        if ($n->Name == 'Year') $fit['year'] = trim($n->Value);
                        else if ($n->Name == 'Make') $fit['make'] = trim($n->Value);
                        else if ($n->Name == 'Model') $fit['model'] = trim($n->Value);
                        else if ($n->Name == 'Trim') $fit['trim'] = trim($n->Value);
                        else if ($n->Name == 'Engine') $fit['engine'] = trim($n->Value);
                    }
                    $fit['notes'] = trim($c->CompatibilityNotes);
                    $item['fitment'][] = $fit;
                }
                self::SaveFitment($item['id'], $item['fitment']);
            }
        }
        $item['mpn'] = '';
        $item['ipn'] = '';
        $item['opn'] = '';
        $item['placement'] = '';
        $item['brand'] = '';
        $item['comp_mpn'] = '';
        $item['comp_brand'] = '';
        $item['comp_name'] = '';
        $item['part_notes'] = '';
        $item['comp_weight'] = '';
        if (!empty($xml->Item->ItemSpecifics) && !empty($xml->Item->ItemSpecifics->NameValueList))
        {
            $placements = array();
            $others = array();
            foreach ($xml->Item->ItemSpecifics->NameValueList as $pair)
            {
                if ($pair->Name == 'Manufacturer Part Number')
                    $item['mpn'] = (string)$pair->Value;
                else if ($pair->Name == 'Interchange Part Number')
                    $item['ipn'] = (string)$pair->Value;
                else if ($pair->Name == 'Other Part Number')
                    $item['opn'] = (string)$pair->Value;
                else if ($pair->Name == 'Placement on Vehicle')
                {
                    foreach ($pair->Value as $v)
                        $placements[] = $v;
                }
                else if ($pair->Name == 'Part Brand')
                    $item['brand'] = (string)$pair->Value;
                else if ($pair->Name == 'Brand')
                    $item['brand'] = (string)$pair->Value;
                else if ($pair->Name == 'Surface Finish')
                    $item['surface_finish'] = (string)$pair->Value;
                else if ($pair->Name == 'Warranty')
                    $item['warranty'] = (string)$pair->Value;
                else

                    $others[] = array((string)$pair->Name, (string)$pair->Value);
            }
            $item['placement'] = implode(', ', $placements);
            $item['other_attribs'] = $others;
        }
        return $item;
    }
}
