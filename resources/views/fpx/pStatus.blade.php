<?php
error_reporting(E_ALL);
/// Summary description for Controller
///  ErrorCode  : Description
///  00         : Your signature has been verified successfully.  
///  06         : No Certificate found 
///  07         : One Certificate Found and Expired
///  08         : Both Certificates Expired
///  09         : Your Data cannot be verified against the Signature.
function hextobin($hexstr) 
{ 
	$n = strlen($hexstr); 
	$sbin="";   
	$i=0; 
	while($i<$n) 
	{       
		$a =substr($hexstr,$i,2);           
		$c = pack("H*",$a); 
		if ($i==0){$sbin=$c;} 
		else {$sbin.=$c;} 
		$i+=2; 
	} 
	return $sbin; 
}


function validateCertificate($path,$sign, $toSign)
{
	global  $ErrorCode;

	$d_ate=date("Y");
	//validating Last Three Certificates 
	$fpxcert=array($path."EX00012323.cer",$path."fpxuat.cer");
	$certs=checkCertExpiry($fpxcert);
	// echo count($certs) ;
	    	$signdata = hextobin($sign);
			
	
	if(count($certs)==1)
	{

	   $pkeyid =openssl_pkey_get_public($certs[0]);
   	   $ret = openssl_verify($toSign, $signdata, $pkeyid);	
	      if($ret!=1) 
	      {
	  	   $ErrorCode=" Your Data cannot be verified against the Signature. "." ErrorCode :[09]";
	  	   return "09";	  
	      }
    }
	 elseif(count($certs)==2)
	{
	 
	 $pkeyid =openssl_pkey_get_public($certs[0]);
   	 $ret = openssl_verify($toSign, $signdata, $pkeyid);	
	   if($ret!=1)
	   {
       
	    $pkeyid =openssl_pkey_get_public($certs[1]);
   	    $ret = openssl_verify($toSign, $signdata, $pkeyid);	
         if($ret!=1) 
	     {
		  $ErrorCode=" Your Data cannot be verified against the Signature. "." ErrorCode :[09]";
		  return "09";	  
	      }
	    }
		
	}
	 if($ret==1)
	 {

        $ErrorCode=" Your signature has been verified successfully. "." ErrorCode :[00]";
        return "00";	  
 	 }
		 
		 
	return $ErrorCode;

		 

}
function verifySign_fpx($sign,$toSign) 
{
   error_reporting(0);

return validateCertificate('https://prim.my/fpx/',$sign, $toSign);
}

function checkCertExpiry($path)
{
		global  $ErrorCode;

      $stack = array();
    $t_ime= time();
    $curr_date=date("Ymd",$t_ime);
     for($x=0;$x<2;$x++)
	 {
		   error_reporting(0);
          $key_id = file_get_contents($path[$x]);
	       if($key_id==null)
	       {
			   $cert_exists++;
	      	 continue;
	       }	 
	       $certinfo = openssl_x509_parse($key_id);
           $s= $certinfo['validTo_time_t']; 
           $crtexpirydate=date("Ymd",$s-86400);
    	  if($crtexpirydate > $curr_date)
	       {
			    if ($x > 0)
				{
				 if(certRollOver($path[$x], $path[$x-1])=="true")
					 {  array_push($stack,$key_id);
						return $stack;
                      }
				}	
                array_push($stack,$key_id);
	      	  return $stack;
           }
	       elseif($crtexpirydate == $curr_date)
	       {
			     if ($x > 0 && (file_exists($path[$x-1])!=1))  
				 {	   
                       if(certRollOver($path[$x], $path[$x-1])=="true")
					   {  array_push($stack,$key_id);
						  return $stack;
					 }
				 }
				 else if(file_exists($path[$x+1])!=1)
				 {
                     array_push($stack,file_get_contents($path[$x]),$key_id);
                     return $stack;
				 }
            
			   
	      	    array_push($stack,file_get_contents($path[$x+1]),$key_id);
          
	      		return $stack;
	      	}
	   			
	 }
          if ($cert_exists == 2)
                $ErrorCode="Invalid Certificates.  " . " ErrorCode : [06]";  //No Certificate (or) All Certificate are Expired 
            else if ($stack.Count == 0 && $cert_exists == 1)
                $ErrorCode="One Certificate Found and Expired " . "ErrorCode : [07]";  
            else if ($stack.Count == 0 && $cert_exists == 0)
               $ErrorCode="Both Certificates Expired " . "ErrorCode : [08]";  
            return $stack;

	 
}
function certRollOver($old_crt,$new_crt)
{  

        if (file_exists($new_crt)==1)
        {
            
                rename($new_crt,$new_crt."_".date("YmdHis", time()));//FPXOLD.cer to FPX_CURRENT.cer_<CURRENT TIMESTAMP>

        }
		if ((file_exists($new_crt)!=1) && (file_exists($old_crt)==1))
        {
            rename($old_crt,$new_crt);                                 //FPX.cer to FPX_CURRENT.cer
        }

		
		return "true";
}

$fpx_buyerBankBranch=$request->fpx_buyerBankBranch;
$fpx_buyerBankId=$request->fpx_buyerBankId;
$fpx_buyerIban=$request->fpx_buyerIban;
$fpx_buyerId=$request->fpx_buyerId;
$fpx_buyerName=$request->fpx_buyerName;
$fpx_creditAuthCode=$request->fpx_creditAuthCode;
$fpx_creditAuthNo=$request->fpx_creditAuthNo;
$fpx_debitAuthCode=$request->fpx_debitAuthCode;
$fpx_debitAuthNo=$request->fpx_debitAuthNo;
$fpx_fpxTxnId=$request->fpx_fpxTxnId;
$fpx_fpxTxnTime=$request->fpx_fpxTxnTime;
$fpx_makerName=$request->fpx_makerName;
$fpx_msgToken=$request->fpx_msgToken;
$fpx_msgType=$request->fpx_msgType;
$fpx_sellerExId=$request->fpx_sellerExId;
$fpx_sellerExOrderNo=$request->fpx_sellerExOrderNo;
$fpx_sellerId=$request->fpx_sellerId;
$fpx_sellerOrderNo=$request->fpx_sellerOrderNo;
$fpx_sellerTxnTime=$request->fpx_sellerTxnTime;
$fpx_txnAmount=$request->fpx_txnAmount;
$fpx_txnCurrency=$request->fpx_txnCurrency;
$fpx_checkSum=$request->fpx_checkSum;

$data=$fpx_buyerBankBranch."|".$fpx_buyerBankId."|".$fpx_buyerIban."|".$fpx_buyerId."|".$fpx_buyerName."|".$fpx_creditAuthCode."|".$fpx_creditAuthNo."|".$fpx_debitAuthCode."|".$fpx_debitAuthNo."|".$fpx_fpxTxnId."|".$fpx_fpxTxnTime."|".$fpx_makerName."|".$fpx_msgToken."|".$fpx_msgType."|".$fpx_sellerExId."|".$fpx_sellerExOrderNo."|".$fpx_sellerId."|".$fpx_sellerOrderNo."|".$fpx_sellerTxnTime."|".$fpx_txnAmount."|".$fpx_txnCurrency;

// $val=verifySign_fpx($fpx_checkSum, $data);



// reponse OK to FPX.
echo "OK";
?>