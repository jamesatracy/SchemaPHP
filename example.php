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