<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class EbayUpdateListing implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    protected $ebay_host;
    protected $site_id;
    protected $app_id;
    protected $token;

    public function __construct()
    {
        $this->ebay_host = env('EBAY_HOST');
        $this->site_id = env('EBAY_SITE_ID');
        $this->app_id = env('EBAY_APP_ID');
        $this->token = env('EBAY_TOKEN');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $callName = 'ReviseFixedPriceItem';
        $version = '845';
        $url = $this->ebay_host . "wsapi?callname=${callName}&siteid=" . $this->site_id . "&appid=" . $this->app_id . "&version=${version}&routing=default";
        $pathPublic = public_path().'/files/products.csv';

        if(\File::exists($pathPublic)){
            $csv = $this->convertToArray('files/products.csv');
        }else{
            $csv =[];
        }

        foreach ($csv as $k_csv => $v_csv) {
            $item = $this->getItem($v_csv);
            //-------------
            $title=$v_csv['title'];
            //-------------
            $titleNode = '<Title><![CDATA[' . trim($title) . ']]></Title>';
            if (!empty($item['mpn'])) {
                $mpnNode = '<NameValueList><Name>Manufacturer Part Number</Name><Value><![CDATA[' . trim($item['mpn']) . ']]></Value></NameValueList>';
                $pns = explode('/', $item['mpn']);
                foreach ($pns as $pn) $partNumbers[] = trim($pn);
            }
            if (!empty($item['brand']))
                $brandNode = '<NameValueList><Name>Brand</Name><Value><![CDATA[' . trim($item['brand']) . ']]></Value></NameValueList>';

            $ebayToken = $this->token;
            $itemId = $v_csv['itemId'];
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
            dispatch(new \App\Jobs\SubEbayUpload($data,$headers,$url));
        }

    }
    public function getItem($attribute){
        $itemId = $attribute['itemId'];
        $res = file_get_contents("http://open.api.ebay.com/shopping?callname=GetSingleItem&responseencoding=XML&appid=" . $this->app_id . "&siteid=0&version=847&ItemID=${itemId}&IncludeSelector=Details,Compatibility,ItemSpecifics,Description");
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

    public function convertToArray($attribute){
        $csv = Array();
        $rowcount = 0;
        $file =  public_path($attribute);
        if (($handle = fopen($file, "r")) !== FALSE) {
            $max_line_length = defined('MAX_LINE_LENGTH') ? MAX_LINE_LENGTH : 10000;
            $header = fgetcsv($handle, $max_line_length);
            $header_colcount = count($header);
            while (($row = fgetcsv($handle, $max_line_length)) !== FALSE) {
                $row_colcount = count($row);
                if ($row_colcount == $header_colcount) {
                    $entry = array_combine($header, $row);
                    $csv[] = $entry;
                }
                else {
                    return null;
                }
                $rowcount++;
            }
            fclose($handle);
        }
        else {
            error_log("csvreader: Could not read CSV \"$csvfile\"");
            return null;
        }
        return $csv;
    }
}
