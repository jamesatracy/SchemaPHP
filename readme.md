SchemaPHP
=========

SchemaPHP provides a foundation for validating data against a model or database schema. Schema's can be loaded externally or generated from a MySQL table schema. SchemaPHP currently supports the following attributes:

* 'primary' - True if the field is the primary key

* 'default' - The default value of the field

* 'unsigned' - True if unsigned, for integers

* 'size' - For integers, can be: int, tinyint, smallint, mediumint, or bigint. For floats it is the max size of the float.

* 'length' - For strings, the max length

* 'acceptsNull' - True if the field can be set to NULL

SchemaPHP currently supports the following MySQL data types:

* INT, TINYINT, SMALLINT, MEDIUMINT, BIGINT ('integer')

* FLOAT, DOUBLE ('float')

* VARCHAR, TEXT, LONGTEXT ('string')

* CHAR ('char')

* DATETIME, TIMESTAMP ('datetime', 'timestamp') Supports CURRENT_TIMESTAMP as a default value.

You can also add additional validation rules to any field in the schema as defined in SchemaRules or add your own custom rules. Built-in rules include:

* "required" => true: field is required (cannot be empty)

* "numeric" => true: field must be numeric (0-9 chars only)

* "email" => true: validate email address

* "url" => true: validate url (with path)

* "min" => value: require a minium value for a field

* "max" => value: require a maximum value for a field

* "minlength" => value: require a minimum length for a (string)

* "maxlength" => value: require a maximum length for a (string)

* "enum" => array(): value must in the given array

* "binary" => true: value is either 0 or 1

A schema is initialized on a table:

	$schema = new Schema($mysql_connection);
	$schema->initialize("table_name");
	
From there, you can get the value of the primary key:

	$pri = $schema->getID();
	
Check for the existence of a field:

	$schema->hasField("field_name");
	
Get an associated array of all the schema's fields initialized to their default values, if any:

	$defaults = $schema->defaultValues();
	
Validate one or more field values against the schema (checking type, length, etc):

	$values = array("field1" => 1, "field2" => 2.05, "field3" => "abcdef", ...);
	$result = $schema->validate($values);

Add additional validation rules to any field:

		$schema->rules(
			array(
				"email" => array("required" => true, "email" => true),
				"name" => array("required" => true, "maxlength" => 20,
				"age" => array("numeric" => true),
				"gender" => array("enum" => array("Male", "Female"))
			)
		);
	
Define your own custom rules:

	function genderRule($name, $value, $args)
	{
		if(!empty($value))
		{
			if(!in_array($value, array("Male", "Female"))
			{
				self::$last_error = "Error `".$name."`: Expected 'Male' or 'Female' but found ".$value;
				return false;
			}
		}
		return true;
	}
	
	SchemaRules::addRule("gender", "genderRule");

Export the schema as a JSON encoded string:

	$schema->export($filepath);
	
Initialize the schema from a file generated by the export method:

	$schema->schemaFile = $filepath;
	$schema->initialize("table_name");
	
By default, a schema is cached by the Schema class for subsequent uses to minimize the number of DESCRIBE queries or file imports it has to make.