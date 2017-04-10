<?php
$flage = true;
$start = 28203;
$result = [];
while($flage) {
	$start++;
	if (strpos(strval($start),'4') === false){
		$result[] = $start;
	}
	if(count($result) >= 7000) {
		break;
	}
}
file_put_contents('./yunjie_code',join("\n",$result));
