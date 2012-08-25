SchemaPHP
=========

SchemaPHP provides a foundation for validating data against a model or database schema. Schema's can be loaded externally or generated from a MySQL table schema.

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

	$values = array("field1" => 1, "field2" => 2, ...);
	$result = $schema->validate($values);

Export the schema as a JSON encoded string:

	$schema->export($filepath);
	
Initialize the schema from a file generated by the export method:

	$schema->schemaFile = $filepath;
	$schema->initialize("table_name");
	
By default, a schema is cached by the Schema class for subsequent uses to minimize the number of DESCRIBE queries or file imports it has to make.