<?php
// Example usages and test cases for Schema

require("Schema.class.php");

$mysql = mysql_connect("localhost", "root", "");
$schema = new Schema($mysql);
$schema->initialize("`schema`.`example`");
// print_r($schema->schemaToJSON());
// echo "<br/>";
// echo "<br/>";

function assertTest($condition, $message)
{
	if(!$condition) echo "<span style='color:red'>ERROR: ".$message;
	else echo "<span>".$message;
	echo "</span><br/>";
}

$data = $schema->schemaToJSON();
assertTest($data['ID']['primary'] == '1', "ID is the primary key");
assertTest($data['ID']['type'] == "integer", "ID is an integer");
assertTest($data['ID']['size'] == "int", "ID size is int");

assertTest($data['signed_integer_no_default']['type'] == "integer", "signed_integer_no_default is an integer");
assertTest($data['signed_integer_no_default']['unsigned'] == "0", "signed_integer_no_default is signed");
assertTest($data['signed_integer_no_default']['default'] == "0", "signed_integer_no_default has no default");
assertTest($data['signed_integer_no_default']['size'] == "int", "signed_integer_no_default size is int");

assertTest($data['signed_integer_default']['type'] == "integer", "signed_integer_default is an integer");
assertTest($data['signed_integer_default']['unsigned'] == "0", "signed_integer_default is signed");
assertTest($data['signed_integer_default']['default'] == "1", "signed_integer_default default is 1");
assertTest($data['signed_integer_default']['size'] == "int", "signed_integer_default size is int");

assertTest($data['unsigned_integer_no_default']['type'] == "integer", "unsigned_integer_no_default is an integer");
assertTest($data['unsigned_integer_no_default']['unsigned'] == "1", "unsigned_integer_no_default is unsigned");
assertTest($data['unsigned_integer_no_default']['default'] == "0", "unsigned_integer_no_default has no default");
assertTest($data['unsigned_integer_no_default']['size'] == "int", "unsigned_integer_no_default size is int");

assertTest($data['unsigned_integer_default']['type'] == "integer", "unsigned_integer_default is an integer");
assertTest($data['unsigned_integer_default']['unsigned'] == "1", "unsigned_integer_default is unsigned");
assertTest($data['unsigned_integer_default']['default'] == "1", "unsigned_integer_default default is 1");
assertTest($data['unsigned_integer_default']['size'] == "int", "unsigned_integer_default size is int");

assertTest($data['signed_tinyint_no_default']['type'] == "integer", "signed_tinyint_no_default is an integer");
assertTest($data['signed_tinyint_no_default']['unsigned'] == "0", "signed_tinyint_no_default is signed");
assertTest($data['signed_tinyint_no_default']['default'] == "0", "signed_tinyint_no_default has no default");
assertTest($data['signed_tinyint_no_default']['size'] == "tinyint", "signed_tinyint_no_default size is tinyint");

assertTest($data['signed_smallint_no_default']['type'] == "integer", "signed_smallint_no_default is an integer");
assertTest($data['signed_smallint_no_default']['unsigned'] == "0", "signed_smallint_no_default is signed");
assertTest($data['signed_smallint_no_default']['default'] == "0", "signed_smallint_no_default has no default");
assertTest($data['signed_smallint_no_default']['size'] == "smallint", "signed_smallint_no_default size is smallint");

assertTest($data['signed_mediumint_no_default']['type'] == "integer", "signed_mediumint_no_default is an integer");
assertTest($data['signed_mediumint_no_default']['unsigned'] == "0", "signed_mediumint_no_default is signed");
assertTest($data['signed_mediumint_no_default']['default'] == "0", "signed_mediumint_no_default has no default");
assertTest($data['signed_mediumint_no_default']['size'] == "mediumint", "signed_mediumint_no_default size is mediumint");

assertTest($data['signed_bigint_no_default']['type'] == "integer", "signed_bigint_no_default is an integer");
assertTest($data['signed_bigint_no_default']['unsigned'] == "0", "signed_bigint_no_default is signed");
assertTest($data['signed_bigint_no_default']['default'] == "0", "signed_bigint_no_default has no default");
assertTest($data['signed_bigint_no_default']['size'] == "bigint", "signed_bigint_no_default size is bigint");

assertTest($data['char_length1_no_default']['type'] == "char", "char_length1_no_default is a char");
assertTest($data['char_length1_no_default']['length'] == "1", "char_length1_no_default length is 1");
assertTest($data['char_length1_no_default']['default'] == "", "char_length1_no_default has no default");

assertTest($data['char_length1_default']['type'] == "char", "char_length1_default is a char");
assertTest($data['char_length1_default']['length'] == "1", "char_length1_default length is 1");
assertTest($data['char_length1_default']['default'] == "A", "char_length1_default has default 'A'");

assertTest($data['char_length5_no_default']['type'] == "char", "char_length5_no_default is a char");
assertTest($data['char_length5_no_default']['length'] == "5", "char_length5_no_default length is 5");
assertTest($data['char_length5_no_default']['default'] == "", "char_length5_no_default has no default");

assertTest($data['char_length5_default']['type'] == "char", "char_length5_default is a char");
assertTest($data['char_length5_default']['length'] == "5", "char_length5_default length is 5");
assertTest($data['char_length5_default']['default'] == "abcde", "char_length5_default has default 'abcde'");

assertTest($data['varchar_length20_no_default']['type'] == "string", "varchar_length20_no_default is a string");
assertTest($data['varchar_length20_no_default']['length'] == "20", "varchar_length20_no_default length is 20");
assertTest($data['varchar_length20_no_default']['default'] == "", "varchar_length20_no_default has no default");

assertTest($data['varchar_length20_default']['type'] == "string", "varchar_length20_default is a string");
assertTest($data['varchar_length20_default']['length'] == "20", "varchar_length20_default length is 20");
assertTest($data['varchar_length20_default']['default'] == "one two three", "varchar_length20_default has default 'one two three'");

assertTest($data['text_no_default']['type'] == "string", "text_no_default is a string");
assertTest($data['text_no_default']['length'] == "", "text_no_default length is not defined");
assertTest($data['text_no_default']['default'] == "", "text_no_default has no default");

assertTest($data['longtext_no_default']['type'] == "string", "longtext_no_default is a string");
assertTest($data['longtext_no_default']['length'] == "", "longtext_no_default length is not defined");
assertTest($data['longtext_no_default']['default'] == "", "longtext_no_default has no default");

assertTest($data['float_no_default']['type'] == "float", "float_no_default is a float");
assertTest($data['float_no_default']['default'] == "", "float_no_default has no default");

assertTest($data['float_default']['type'] == "float", "float_default is a float");
assertTest($data['float_default']['default'] == "5.0", "float_default has default '5.0'");

assertTest($data['double_no_default']['type'] == "float", "double_no_default is a float");
assertTest($data['double_no_default']['default'] == "", "double_no_default has no default");

assertTest($data['double_default']['type'] == "float", "double_default is a float");
assertTest($data['double_default']['default'] == "5.0", "double_default has default '5.0'");

assertTest($data['datetime_no_default']['type'] == "datetime", "datetime_no_default is a datetime");
assertTest($data['datetime_no_default']['default'] == "", "datetime_no_default has no default");

assertTest($data['datetime_default']['type'] == "datetime", "datetime_default is a datetime");
assertTest($data['datetime_default']['default'] == "0000-00-00 00:00:00", "datetime_default has default '0000-00-00 00:00:00'");

assertTest($data['timestamp_default']['type'] == "timestamp", "timestamp_default is a timestamp");
assertTest($data['timestamp_default']['default'] == "CURRENT_TIMESTAMP", "timestamp_default has default 'CURRENT_TIMESTAMP'");