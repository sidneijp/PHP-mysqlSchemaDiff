<?php

$dumpsPath = dirname(__FILE__);
// DB configuration
$dbHost = "localhost";
//$dbPort = "3306";
$dbName = "intel";
$dbUser = "root";
$dbPass= "root";

$connect_db = mysql_connect($dbHost, $dbUser, $dbPass) or die(mysql_error());

mysql_select_db("INFORMATION_SCHEMA") or die(mysql_error());
mysql_set_charset('utf8', $connect_db);

$query = "SELECT CONSTRAINT_SCHEMA,
		CONSTRAINT_NAME,
		TABLE_NAME AS table,
		COLUMN_NAME AS colunm,
		ORDINAL_POSITION,
		POSITION_IN_UNIQUE_CONSTRAINT,
		REFERENCED_TABLE_NAME AS refence_table,
		REFERENCED_COLUMN_NAME AS refence_column,
	FROM KEY_COLUMN_USAGE
	WHERE TABLE_SCHEMA='$dbName'
		AND REFERENCED_TABLE_NAME IS NOT NULL";
$mysqlResource = mysql_query($query);
$informationSchema = array();
while ($row = mysql_fetch_assoc($mysqlResource)) {
	$informationSchema[$row->] = $row;
}

var_dump($informationSchema);
exit();
$headSchemaFile = $dumpsPath . "/head_schema.xml";
$currentSchemaFile = $dumpsPath . "/current_schema.xml";

// $mysqldumpShellCommand = "mysqldump --xml --skip-lock-tables --no-data -p%s -u %s %s > %s";
// $mysqldumpShellCommand = sprintf($mysqldumpShellCommand, $dbPass, $dbUser, $dbName, $currentSchemaFile);
// exec($mysqldumpShellCommand);

$headDom = new DOMDocument;
$headDom->preserveWhiteSpace = false;
$headDom->Load($headSchemaFile);
//$xpathHead = new DOMXPath($head);

$currentDom = new DOMDocument;
$currentDom->preserveWhiteSpace = false;
$currentDom->Load($currentSchemaFile);
//$xpathCurrent = new DOMXPath($current);

//$delta = new DOMDocument;
//$delta->preserveWhiteSpace = false;
//$delta->Load($headSchemaFile);
//$xpathDelta = new DOMXPath($delta);
// We starts from the root element
//$query = '//book/chapter/para/informaltable/tgroup/tbody/row/entry[. = "en"]';

// $added = array();
// $query = "//table_structure";
// $table_structure_list = $xpathCurrent->query($query);
// foreach ($table_structure_list as $table_structure) {
// 	$tableName = $table_structure->attributes->getNamedItem("name")->nodeValue;
// 	$q = "//table_structure[@name=\"$tableName\"]";
// 	$len = $xpathHead->query($q)->length;
// 	$tableExists = ($len > 0) ? true : false;
// 	if (!$tableExists) {
// 		$added[$tableName] = array("all_current_fields");
// 	} else{
// 		$fields = array();
// 		$field_list = $xpathCurrent->query("field", $table_structure);
// 		foreach ($field_list as $field) {
// 			$fieldName = $field->attributes->getNamedItem("Field")->nodeValue;
// 			$q = "//table_structure[@name=\"$tableName\"]/field[@Field=\"$fieldName\"]";
// 			$len = $xpathHead->query($q)->length;
// 			$fieldExists = ($len > 0) ? true : false;
// 			if (!$fieldExists) {
// 				$fields[] = $fieldName;
// 			} else {
// 				//check fields constraints
// 			}
// 		}
// 		if (!empty($fields))
// 			$added[$tableName][] = $fields;
// 	}
// }
// var_dump($added);


function arraySchemaFromDom($dom) {
	$xpath = new DOMXPath($dom);
	$schema = array();
	$query = "//table_structure";
	$table_structure_list = $xpath->query($query);
	foreach ($table_structure_list as $table_structure) {
		$tableName = $table_structure->attributes->getNamedItem("name")->nodeValue;
		$schema[$tableName] = array("fields" => array(), "options" => array());
		$field_list = $xpath->query("field", $table_structure);
		foreach ($field_list as $field) {
			$fieldName = $field->attributes->getNamedItem("Field")->nodeValue;
			$schema[$tableName]["fields"][$fieldName] = array();
			$column_list = $field->attributes;
			foreach ($column_list as $column) {
				$columnName = $column->nodeName;
				$columnValue = $column->nodeValue;
				$schema[$tableName]["fields"][$fieldName][$columnName] = $columnValue;
			}
		}
		$key_list = $xpath->query("key", $table_structure);
		foreach ($key_list as $key) {
			$fieldName = $key->attributes->getNamedItem("Field")->nodeValue;
			$schema[$tableName]["fields"][$fieldName] = array();
			$column_list = $field->attributes;
			foreach ($column_list as $column) {
				$columnName = $column->nodeName;
				$columnValue = $column->nodeValue;
				$schema[$tableName]["fields"][$fieldName][$columnName] = $columnValue;
			}
		}
		$option_list = $xpath->query("options", $table_structure)->attributes;
		foreach ($option_list as $option) {
			$optionName = $option->nodeName;
			$optionValue = $option->nodeValue;
			$schema[$tableName]["options"]["options"][$optionName] = $optionValue;
		}
	}
	return $schema;
}

$headSchema = arraySchemaFromDom($headDom);
$currentSchema = arraySchemaFromDom($currentDom);
//var_dump($headSchema);
$a = array(1,2,3,4,5);
$b = array(3,4,5);
$a = array("tblA" => array("soup" => "maybe", "def" => "never", "same" => "si"), "tblB" => array("aff" => "no", "def" => "always", "same" => "si"));
$b = array("tbl" => array("soup" => "maybe", "def" => "never", "same" => "si"), "tblB" => array("aff" => "no", "def" => "always", "same" => "si"));
var_dump(array_diff_key($a, $b)); //removed
var_dump(array_diff_key($b, $a)); //added

function schemaDiff($schemaA, $schemaB) {
	$diff = array();
}
// $removed = array();
// $query = "//table_structure";
// $table_structure_list = $xpathHead->query($query);
// foreach ($table_structure_list as $table_structure) {
// 	$tableName = $table_structure->attributes->getNamedItem("name")->nodeValue;
// 	$q = "//table_structure[@name=\"$tableName\"]";
// 	$len = $xpathCurrent->query($q)->length;
// 	$tableExists = ($len > 0) ? true : false;
// 	if (!$tableExists) {
// 		$removed[$tableName] = array("all_current_fields");
// 	} else {
// 		$fields = array();
// 		$field_list = $xpathHead->query("field", $table_structure);
// 		foreach ($field_list as $field) {
// 			$fieldName = $field->attributes->getNamedItem("Field")->nodeValue;
// 			$q = "//table_structure[@name=\"$tableName\"]/field[@Field=\"$fieldName\"]";
// 			$len = $xpathCurrent->query($q)->length;
// 			$fieldExists = ($len > 0) ? true : false;
// 			if (!$fieldExists) {
// 				$fields[] = $fieldName;
// 			} else {
// 				$constraints = array();
// 				$constraint_list = $field->attributes;
// 				foreach ($constraint_list as $constraint) {
// 					$constraintName = $constraint->nodeName;
// 					$constraintValue = $constraint->nodeValue;
// 					$q = "//table_structure[@name=\"$tableName\"]/field[@Field=\"$fieldName\",@$constraintName=\"$constraintName\"]";
// 					$len = $xpathCurrent->query($q)->length;
// 					$constraintUnchanged = ($len > 0) ? true : false;
// 					if (!$constraintUnchanged) {
// 						$constraints[] = $fieldName;
// 				}
// 				if (!empty($fields))
// 					$removed[$tableName][] = $fields;
// 			}
// 		}
// 		if (!empty($fields))
// 			$removed[$tableName][] = $fields;
// 	}
// }
// var_dump($removed);
//$entries = $xpath->query($query);

// foreach ($entries as $entry) {
//     echo "Found {$entry->previousSibling->previousSibling->nodeValue}," .
//          " by {$entry->previousSibling->nodeValue}\n";
//}
