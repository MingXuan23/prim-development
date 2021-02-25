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
	$fpxcert=array($path."fpxuat_current.cer",$path."fpxuat.cer");
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

$fpx_buyerBankBranch=$_POST['fpx_buyerBankBranch'];
$fpx_buyerBankId=$_POST['fpx_buyerBankId'];
$fpx_buyerIban=$_POST['fpx_buyerIban'];
$fpx_buyerId=$_POST['fpx_buyerId'];
$fpx_buyerName=$_POST['fpx_buyerName'];
$fpx_creditAuthCode=$_POST['fpx_creditAuthCode'];
$fpx_creditAuthNo=$_POST['fpx_creditAuthNo'];
$fpx_debitAuthCode=$_POST['fpx_debitAuthCode'];
$fpx_debitAuthNo=$_POST['fpx_debitAuthNo'];
$fpx_fpxTxnId=$_POST['fpx_fpxTxnId'];
$fpx_fpxTxnTime=$_POST['fpx_fpxTxnTime'];
$fpx_makerName=$_POST['fpx_makerName'];
$fpx_msgToken=$_POST['fpx_msgToken'];
$fpx_msgType=$_POST['fpx_msgType'];
$fpx_sellerExId=$_POST['fpx_sellerExId'];
$fpx_sellerExOrderNo=$_POST['fpx_sellerExOrderNo'];
$fpx_sellerId=$_POST['fpx_sellerId'];
$fpx_sellerOrderNo=$_POST['fpx_sellerOrderNo'];
$fpx_sellerTxnTime=$_POST['fpx_sellerTxnTime'];
$fpx_txnAmount=$_POST['fpx_txnAmount'];
$fpx_txnCurrency=$_POST['fpx_txnCurrency'];
$fpx_checkSum=$_POST['fpx_checkSum'];

$data=$fpx_buyerBankBranch."|".$fpx_buyerBankId."|".$fpx_buyerIban."|".$fpx_buyerId."|".$fpx_buyerName."|".$fpx_creditAuthCode."|".$fpx_creditAuthNo."|".$fpx_debitAuthCode."|".$fpx_debitAuthNo."|".$fpx_fpxTxnId."|".$fpx_fpxTxnTime."|".$fpx_makerName."|".$fpx_msgToken."|".$fpx_msgType."|".$fpx_sellerExId."|".$fpx_sellerExOrderNo."|".$fpx_sellerId."|".$fpx_sellerOrderNo."|".$fpx_sellerTxnTime."|".$fpx_txnAmount."|".$fpx_txnCurrency;

$val=verifySign_fpx($fpx_checkSum, $data);



// reponse OK to FPX.
echo "OK";
?>