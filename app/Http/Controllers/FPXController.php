<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

class FPXController extends AppBaseController
{
    public function getBankList()
    {
        //Merchant will need to edit the below parameter to match their environment.
        error_reporting(E_ALL);

        /* Generating String to send to fpx */
        /*For B2C, message.token = 01
        For B2B1, message.token = 02 */

        $fpx_msgToken="01";
        $fpx_msgType="BE";
        $fpx_sellerExId="EX00012323";
        $fpx_version="6.0";
        /* Generating signing String */
        $data=$fpx_msgToken."|".$fpx_msgType."|".$fpx_sellerExId."|".$fpx_version;
        /* Reading key */
        $priv_key = getenv('FPX_KEY');
        // $priv_key = file_get_contents('C:\\pki-keys\\DevExchange\\EX00012323.key');
        $pkeyid = openssl_get_privatekey($priv_key, null);
        openssl_sign($data, $binary_signature, $pkeyid, OPENSSL_ALGO_SHA1);
        $fpx_checkSum = strtoupper(bin2hex($binary_signature));


        //extract data from the post

        extract($_POST);
        $fields_string="";

        //set POST variables
        $url ='https://uat.mepsfpx.com.my/FPXMain/RetrieveBankList';

        $fields = array(
                    'fpx_msgToken' => urlencode($fpx_msgToken),
                    'fpx_msgType' => urlencode($fpx_msgType),
                    'fpx_sellerExId' => urlencode($fpx_sellerExId),
                    'fpx_checkSum' => urlencode($fpx_checkSum),
                    'fpx_version' => urlencode($fpx_version)
                );
        $response_value=array();
        $bank_list=array();

        try {
            //url-ify the data for the POST
            foreach ($fields as $key=>$value) {
                $fields_string .= $key.'='.$value.'&';
            }
            rtrim($fields_string, '&');

            //open connection
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

            //set the url, number of POST vars, POST data
            curl_setopt($ch, CURLOPT_URL, $url);

            curl_setopt($ch, CURLOPT_POST, count($fields));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);

            // receive server response ...
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
           
            //execute post
            $result = curl_exec($ch);

            //close connection
            curl_close($ch);

            $token = strtok($result, "&");
            while ($token !== false) {
                list($key1, $value1)=explode("=", $token);
                $value1=urldecode($value1);
                $response_value[$key1]=$value1;
                $token = strtok("&");
            }

            $fpx_msgToken=reset($response_value);

            //Response Checksum Calculation String
            $data=$response_value['fpx_bankList']."|".$response_value['fpx_msgToken']."|".$response_value['fpx_msgType']."|".$response_value['fpx_sellerExId'];

            // val == 00 verification success
            $token = strtok($response_value['fpx_bankList'], ",");

            $count = -1;

            while ($token !== false) {
                list($key1, $value1)=explode("~", $token);
                $count = $count + 1;
                $value1=urldecode($value1);
                $bank_list[$count]=['code' => $key1 , 'value' => $value1 , 'nama' => '' ];
                
                $token = strtok(",");
            }

            $bank_list[0]['nama']  = $bank_list[0]['value']  == "A" ? "Affin B2C - Test ID" : "Affin B2C - Test ID (OFFLINE)";
            $bank_list[1]['nama']  = $bank_list[1]['value']  == "A" ? "Affin Bank" : "Affin Bank (OFFLINE)";
            $bank_list[2]['nama']  = $bank_list[2]['value']  == "A" ? "Alliance Bank (Personal)" : "Alliance Bank (OFFLINE)";
            $bank_list[3]['nama']  = $bank_list[3]['value']  == "A" ? "AGRONet" : "AGRONet (OFFLINE)";
            $bank_list[4]['nama']  = $bank_list[4]['value']  == "A" ? "AmBank" : "AmBank (OFFLINE)";
            $bank_list[5]['nama']  = $bank_list[5]['value']  == "A" ? "Bank Islam Sdn Bhd" : "Bank Islam Sdn Bhd (OFFLINE)";
            $bank_list[6]['nama']  = $bank_list[6]['value']  == "A" ? "Bank Muamalat" : "Bank Muamalat (OFFLINE)";
            $bank_list[7]['nama']  = $bank_list[7]['value']  == "A" ? "Bank Rakyat" : "Bank Rakyat (OFFLINE)";
            $bank_list[8]['nama']  = $bank_list[8]['value']  == "A" ? "BSN" : "BSN (OFFLINE)";
            $bank_list[9]['nama']  = $bank_list[9]['value']  == "A" ? "CIMB Clicks" : "CIMB Clicks (OFFLINE)";
            $bank_list[10]['nama'] = $bank_list[10]['value'] == "A" ? "Citibank" : "Citibank (OFFLINE)";
            $bank_list[11]['nama'] = $bank_list[11]['value'] == "A" ? "Hong Leong Bank" : "Hong Leong Bank (OFFLINE)";
            $bank_list[12]['nama'] = $bank_list[12]['value'] == "A" ? "HSBC Bank" : "HSBC Bank (OFFLINE)";
            $bank_list[13]['nama'] = $bank_list[13]['value'] == "A" ? "KFH" : "KFH (OFFLINE)";
            $bank_list[14]['nama'] = $bank_list[14]['value'] == "A" ? "Maybank2E" : "Maybank2E (OFFLINE)";
            $bank_list[15]['nama'] = $bank_list[15]['value'] == "A" ? "Maybank2U" : "Maybank2U (OFFLINE)";
            $bank_list[16]['nama'] = $bank_list[16]['value'] == "A" ? "OCBC Bank" : "OCBC Bank (OFFLINE)";
            $bank_list[17]['nama'] = $bank_list[17]['value'] == "A" ? "Public Bank" : "Public Bank (OFFLINE)";
            $bank_list[18]['nama'] = $bank_list[18]['value'] == "A" ? "RHB Bank" : "RHB Bank (OFFLINE)";
            $bank_list[19]['nama'] = $bank_list[19]['value'] == "A" ? "SBI Bank A" : "SBI Bank A (OFFLINE)";
            $bank_list[20]['nama'] = $bank_list[20]['value'] == "A" ? "SBI Bank B" : "SBI Bank B (OFFLINE)";
            $bank_list[21]['nama'] = $bank_list[21]['value'] == "A" ? "SBI Bank C" : "SBI Bank C (OFFLINE)";
            $bank_list[22]['nama'] = $bank_list[22]['value'] == "A" ? "Standard Chartered" : "Standard Chartered (OFFLINE)";
            $bank_list[23]['nama'] = $bank_list[23]['value'] == "A" ? "UOB Bank" : "UOB Bank (OFFLINE)";
            $bank_list[24]['nama'] = $bank_list[24]['value'] == "A" ? "UOB Bank - Test ID" : "UOB Bank - Test ID (OFFLINE)";
            // asort($bank_list);

            function compareByName($a, $b)
            {
                return strcasecmp($a["nama"], $b["nama"]);
            }

            // usort($bank_list, 'compareByName');

            return $this->sendResponse($bank_list, "Success");
        } catch (\Throwable $th) {
            return $this->sendError($th->getMessage(), 500);
        }
    }
}
