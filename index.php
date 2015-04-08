<?php
// Author: Antonio Sejas
//Parse credentials

$app_id = '';
$rest_key = '';
$master_key = '';


//Conf Files
$nombre_clase = "Palabras";
$csv_file = "vocabulario.csv";

date_default_timezone_set('Europe/Madrid');
set_time_limit(0);
require 'vendor/autoload.php'; 
use Parse\ParseClient; 
use Parse\ParseObject;
ParseClient::initialize($app_id, $rest_key, $master_key);

/**
*
* Input $parameters type Array
**/
function saveObject ($parameters)
{
	$testObject = ParseObject::create($nombre_clase);
	foreach ($parameters as $key => $value ) { 
		$testObject->set($key, $value);
	}
	$testObject->save();
	return $testObject->getObjectId();
}
$header = Array();
$row = 1;
$saved = 0;
$error = 0;
$from = file_get_contents('current_row.txt', $row);
if (($gestor = fopen($csv_file, "r")) !== FALSE) {
    while (($datos = fgetcsv($gestor, 1000, ",")) !== FALSE) {
    	if (1 == $row) {
    		//header
    		foreach ($datos as $aKey) {
    			$header[] = strtr(strtolower(trim($aKey)),array(" "=>"_"));
    		}
    		var_dump($header);//Echo
    	}else{
    		if ($row > $from) {
				foreach ($datos as $i => $aValue) {
					$aValue = trim($aValue);
					if (is_numeric($aValue)&&false) {
						$temp[$header[$i]] = intval($aValue);
					}else{
						$temp[$header[$i]] = $aValue;    				
					}
				}
				if(saveObject($temp)){
					file_put_contents('current_row.txt', $row);
					$saved++;
					echo "ROW: $row OK<br>\n";
				}else{
					$error++;
					echo "ROW: $row ERROR<br>\n";
				}    			
    		}
    	}
        $row++;
    }
    fclose($gestor);
}
echo "'$saved' rows saved. '$error' errors in Parse";

 ?>
