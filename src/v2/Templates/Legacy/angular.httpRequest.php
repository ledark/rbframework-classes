<?php 

Required::logic("request_input");
Required::plugin("response");

$result = array();

$result[] = array(
	'success' => true
,	'exectime' => time()
);

response($result);