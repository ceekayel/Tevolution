<?php
/*
* fetch the count for google, facebook, pinterest share for particular post.
*/
  header('content-type: application/json');
  //Sharrre by Julien Hany
  $json = array('url'=>'','count'=>0);
  $json['url'] = $_GET['url'];
  $url = ($_GET['url']);
  $type = ($_GET['type']);
  
	if($type == 'googlePlus'){
		$content = file_get_contents("https://plusone.google.com/u/0/_/+1/fastbutton?url=".urlencode($_GET['url'])."&count=true");
		$doc = new DOMdocument();
		libxml_use_internal_errors(true);
		$doc->loadHTML($content);
		$doc->saveHTML();
		$num = $doc->getElementById('aggregateCount')->textContent;
		if($num){
			echo str_replace('\/','/',json_encode(array('url'=>'','count'=>$num)));
		}else{
			echo str_replace('\/','/',json_encode(array('url'=>'','count'=>0)));
		}
	}else if($type == 'pinterest'){
		$content = file_get_contents("http://api.pinterest.com/v1/urls/count.json?callback=receiveCount&url=".($_GET['url']));
		$json_string = preg_replace('/^receiveCount\((.*)\)$/', "\\1", $content);
		$json = json_decode($json_string, true);
		
		if($json['count']){
			echo str_replace('\/','/',json_encode(array('url'=>'','count'=>$json['count'])));
		}else{
			echo str_replace('\/','/',json_encode(array('url'=>'','count'=>0)));
		}
	}else if($type == 'facebook'){
		$content = file_get_contents("http://graph.facebook.com/?id=".urlencode($_GET['url']));
		$doc = new DOMdocument();
		libxml_use_internal_errors(true);
		$doc->loadHTML($content);
		$doc->saveHTML();
		$num = $doc->getElementById('aggregateCount')->textContent;
		if($num){
			echo str_replace('\/','/',json_encode(array('url'=>'','count'=>$num)));
		}else{
			echo str_replace('\/','/',json_encode(array('url'=>'','count'=>0)));
		}
	}
	
  
  function parse($encUrl){
    $options = array(
      CURLOPT_RETURNTRANSFER => true, // return web page
      CURLOPT_HEADER => false, // don't return headers
      CURLOPT_FOLLOWLOCATION => true, // follow redirects
      CURLOPT_ENCODING => "", // handle all encodings
      CURLOPT_USERAGENT => 'sharrre', // who am i
      CURLOPT_AUTOREFERER => true, // set referer on redirect
      CURLOPT_CONNECTTIMEOUT => 5, // timeout on connect
      CURLOPT_TIMEOUT => 10, // timeout on response
      CURLOPT_MAXREDIRS => 3, // stop after 10 redirects
      CURLOPT_SSL_VERIFYHOST => 0,
      CURLOPT_SSL_VERIFYPEER => false,
    );
    $ch = curl_init();
    
    $options[CURLOPT_URL] = $encUrl;  
    curl_setopt_array($ch, $options);
    
    $content = curl_exec($ch);
    $err = curl_errno($ch);
    $errmsg = curl_error($ch);
    
    curl_close($ch);
    
    if ($errmsg != '' || $err != '') {
      /*print_r($errmsg);
      print_r($errmsg);*/
    }
    return $content;
  }