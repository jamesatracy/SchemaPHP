<?php
/*
Schema.class.php
Copyright (C) 2012 James Tracy

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/

require_once("SchemaRules.class.php");

class Schema
{
	/* Central cache for schema definitions */
	protected static $_schema_cache = array();
	
	/* Database connection */
	protected $_db = null;
	
	/* Optional pointer to schema file name */
	public $schemaFile = null;
	
	/* The schema */
	protected $_schema = array();
	
	/* The primary key */
	protected $_id = "";
	
	/* Holds the last error message(s) */
	protected $_errors = array();
	
	/*
	Constructor
	
	@param [string] $connection The mysql database connection.
	*/
	public function __construct($connection)
	{
		$this->_db = $connection;
		if(!$this->_db)
		{
			trigger_error("Error: Invalid connection suplied to Schema");
		}
	}
	
	/*
	Initialize the schema
	
	@param [string] $table The table name
	@param [boolean] $cacheable Whether or not to cache the schema. Default is true.
	@return [array] The schema object as an associated array
	*/
	public function initialize($table, $cacheable = true)
	{
		if($this->_db)
		{
			if($cacheable)
			{
				if(isset(Schema::$_schema_cache[$table]))
				{
					$cache = Schema::$_schema_cache[$table];
					$this->_schema = $cache['schema'];
					$this->_id = $cache['id'];
					return $this->_schema;
				}
			}
			if($this->schemaFile)
			{
				$cache = json_decode(file_get_contents($this->schemaFile), true);
				$this->_schema = $cache['schema'];
				$this->_id = $cache['id'];
			}
			else
				$this->_schema = $this->_loadSchema($table);
			if($cacheable)
			{
				Schema::$_schema_cache[$table] = array("id" => $this->_id, "schema" => $this->_schema);
			}
		}
	}
	
	/*
	Checks if the schema is initialized
	
	@return [boolean] True if it is initialized
	*/
	public function isInitialized()
	{
		return (!empty($this->_schema));
	}
	
	/*
	Add additional validation rules to a group of fields.
	
	@param [array] An array of key => value pairs, where the value corresponds to
		a rule or an array of rules to add to the field.
		
		Ex:
			$schema->rules(
				array("email" => array("email" => true))
			);
	*/
	public function rules($rules)
	{
		if($this->_schema && is_array($rules))
		{
			foreach($rules as $key => $values)
			{
				if(isset($this->_schema[$key]))
				{
					$this->_schema[$key]['rules'] = $values;
				}
			}
		}
	}
	
	/*
	Get the primary key
	
	@return [string] The field name of the primary key
	*/
	public function getID()
	{
		return $this->_id;
	}
	
	/*
	Checks for the existence of a field in the schema
	
	@param [string] $field The name of the field
	@return [boolean] True if the field exists
	*/
	public function hasField($field)
	{
		if(!$this->isInitialized())
			return false;
		return (isset($this->_schema[$field]));
	}
	
	/* 
	Convert the schema to a JSON (associative) array
	
	@return [array] The JSON like schema array
	*/
	public function schemaToJSON()
	{
		if(!$this->_schema)
			return "";
		return $this->_schema;
	}
	
	/*
	Export this schema defintion to a file
	
	@param [string] $file The absolute file path
	*/
	public function export($file)
	{
		file_put_contents($file, json_encode(array("id" => $this->getID(), "schema" => $this->schemaToJSON())));
	}
	
	/*
	Gets the last error message(s)
	
	@return [string] The error message(s)
	*/
	public function getErrors()
	{
		return $this->_errors;
	}
	
	/*
	Returns true if the last operation produced any error messages
	
	@return [boolean] True if the last operation produced any error messages
	*/
	public function hasErrors()
	{
		return (!empty($this->_errors));
	}
	
	/* 
	Validate one or more fields.
	Error messages can be retrieved through getErrors()
	
	@param [array] $fields An array of field => value pairs to validate
	@returns [boolean] True if all fields validate, false otherwise.
	*/
	public function validate($fields)
	{
		if(!$this->_schema)
		{
			$this->_errors = array("The Schema has not been initialized.");
			return false;
		}
		
		$this->_errors = array();
		foreach($fields as $key => $value)
		{
			if(!isset($this->_schema[$key]))
			{
				$this->_errors[] = "Invalid field name: ".$key.". ";
			}
			else
			{
				$this->_validateField($this->_schema[$key], $key, $value);
			}
		}
		
		if(empty($this->_errors))
			return true;
		return false; // errors
	}
	
	/*
	Get an array initialized to the default values
	
	@return [array] An associative array of key => value pairs set to the default values as defined
		in the active schema.
	*/
	public function defaultValues()
	{
		$defaults = array();
		if(!$this->_schema)
			return $defaults;
		
		foreach($this->_schema as $key => $definition)
		{
			$value = $definition['default'];
			if($value == "CURRENT_TIMESTAMP")
				$value = date('Y-m-d H:i:s', time());
			$defaults[$key] = $value;
		}
			
		return $defaults;
	}
	
	/*
	Internal function for validating a field
	
	@param [array] $field The field schema object
	@param [string] $name The name of the field
	@param [mixed] $value The value of the field to validate against
	*/
	protected function _validateField($field, $name, $value)
	{
		if(isset($field['primary']) && $field['primary'])
			return;
			
		if($value == "NULL")
		{
			// can this field be null?
			if(!$field['acceptNULL'])
			{
				$this->_errors[] = "Field `".$name."` cannot be set to NULL.";
				return;
			}
		}
		
		// check for custom schema rules
		if(isset($field['rules']))
		{
			foreach($field['rules'] as $rule => $args)
			{
				if(SchemaRules::invoke($rule, $name, $value, $args) === false)
				{
					$this->_errors[] = SchemaRules::$last_error;
					return;
				}
			}
		}
		
		$type = $field['type'];
		
		// check the field type
		if($type == "integer")
		{
			if(!is_int($value) && !is_numeric($value))
			{
				// not a number
				$this->_errors[] = "Type mismatch `".$name."`: Expected integer and found ".Schema::exportVar($value);
				return;
			}
			$int_value = intval($value);
			if($field['size'] == "tinyint")
			{
				if(($field['unsigned'] && ($int_value < 0 || $int_value > 255)) || (!$field['unsigned'] && ($int_value < -128 || $int_value > 127)))
				{
					// out of bounds
					$this->_errors[] = "Out of bounds error `".$name."`: Expected tinyint ".($field['unsigned'] ? "unsigned " : "")."and found ".Schema::exportVar($value);
				}
			}
			else if($field['size'] == "smallint")
			{
				// out of bounds
				if(($field['unsigned'] && ($int_value < 0 || $int_value > 65535)) || (!$field['unsigned'] && ($int_value < -32768 || $int_value > 32767)))
				{
					// out of bounds
					$this->_errors[] = "Out of bounds error `".$name."`: Expected smallint ".($field['unsigned'] ? "unsigned " : "")."and found ".Schema::exportVar($value);
				}
			}
			else if($field['size'] == "mediumint")
			{
				// out of bounds
				if(($field['unsigned'] && ($int_value < 0 || $int_value > 16777215)) || (!$field['unsigned'] && ($int_value < -8388608 || $int_value > 8388607)))
				{
					// out of bounds
					$this->_errors[] = "Out of bounds error `".$name."`: Expected mediumint ".($field['unsigned'] ? "unsigned " : "")."and found ".Schema::exportVar($value);
				}
			}
			else if($field['size'] == "int")
			{
				// out of bounds
				if(($field['unsigned'] && ($int_value < 0 || $int_value > 4294967295)) || (!$field['unsigned'] && ($int_value < -2147483648 || $int_value > 2147483647)))
				{
					// out of bounds
					$this->_errors[] = "Out of bounds error `".$name."`: Expected int ".($field['unsigned'] ? "unsigned " : "")."and found ".Schema::exportVar($value);
				}
			}
			else if($field['size'] == "bigint")
			{
				// out of bounds
				if(($field['unsigned'] && ($int_value < 0 || $int_value > 18446744073709551615)) || (!$field['unsigned'] && ($int_value < -9223372036854775808 || $int_value > 9223372036854775807)))
				{
					// out of bounds
					$this->_errors[] = "Out of bounds error `".$name."`: Expected bigint ".($field['unsigned'] ? "unsigned " : "")."and found ".Schema::exportVar($value);
				}
			}
		}
		else if($type == "float")
		{
			if(!is_float($value) && !is_numeric($value))
			{
				// not a float
				$this->_errors[] = "Type mismatch `".$name."`: Expected float and found ".Schema::exportVar($value);
				return;
			}
		}
		else if($type == "string")
		{
			if($field['length'] != null)
			{
				$length = strlen(strval($value));
				if($length > $field['length'])
				{
					// string exceeds maximum character length
					$this->_errors[] = "String length (".$field['length'].") exceeded for `".$name."`: ".Schema::exportVar($value)." (".$length.")";
				}
			}
		}
		else if($type == "char")
		{
			$length = strlen(strval($value));
			if($length > 255)
			{
				// string exceeds maximum character length
				$this->_errors[] = "Char length (".$field['length'].") exceeded for `".$name."`: ".Schema::exportVar($value)." (".$length.")";
			}
			else if($length > $field['length'])
			{
				// string exceeds maximum character length
				$this->_errors[] = "Char length (".$field['length'].") exceeded for `".$name."`: ".Schema::exportVar($value)." (".$length.")";
			}
		}
		else if($type == "datetime")
		{
			if(preg_match("/^(\d{4})-(\d{2})-(\d{2}) ([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/", $value, $matches)) 
			{ 
				if(!checkdate($matches[2], $matches[3], $matches[1])) { 
					if($value != "0000-00-00 00:00:00")
					{
						// invalid datetime
						$this->_errors[] = "Invalid datetime `".$name."`: ".Schema::exportVar($value);
					}
				}
				else
				{
					if($value < "1000-01-01 00:00:00")
					{
						$this->_errors[] = "Out of bounds error `".$name."`: datetime must be >= '1000-01-01 00:00:00' but found ".$value;
					}
				}
			}
			else
			{
				// invalid datetime
				$this->_errors[] = "Expecting datetime `".$name."`: ".Schema::exportVar($value);
			}
		}
		else if($type == "timestamp")
		{
			if(preg_match("/^(\d{4})-(\d{2})-(\d{2}) ([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/", $value, $matches)) 
			{ 
				if(!checkdate($matches[2], $matches[3], $matches[1])) { 
					// invalid timestamp
					if($value != "0000-00-00 00:00:00")
					{
						$this->_errors[] = "Invalid timestamp `".$name."`: ".Schema::exportVar($value);
					}
				}
				else
				{
					if($value < "1970-01-01 00:00:01")
					{
						$this->_errors[] = "Out of bounds error `".$name."`: timestamp must be >= '1970-01-01 00:00:01' but found ".$value;
					}
				}
			}
			else
			{
				// invalid timestamp
				$this->_errors[] = "Expecting timestamp `".$name."`: ".Schema::exportVar($value);
			}
		}
	}
	
	/*
	Internal function for loading a schema from a mysql database table
	
	@param [string] $table The table name
	@return [array] The schema object as an associated array
	*/
	protected function _loadSchema($table)
	{
		$db = $this->_db;
		$fields = array();
		$result = mysql_query("DESCRIBE ".$table, $db);
		while($row = mysql_fetch_assoc($result))
		{
			$attrs = array();
			$field = $row['Field'];
			$type = $row['Type'];
			$null = $row['Null'];
			$default = $row['Default'];
			
			if($row['Key'] == "PRI")
			{
				// set the primary key
				$this->_id = $field;
				$attrs['primary'] = "1";
			}
			
			// format type
			if(substr($type, 0, 3) == "int" || substr($type, 0, 7) == "tinyint" || substr($type, 0, 8) == "smallint" || substr($type, 0, 9) == "mediumint" || substr($type, 0, 6) == "bigint")
			{
				// get size
				preg_match_all('/\((.*?)\)/', $type, $matches);
				$attrs["type"] = "integer";
				$attrs["size"] = substr($type, 0, strpos($type, "("));
				$attrs["length"] = $matches[1][0];
				if(stripos($type, "unsigned") !== false)
				{
					$attrs["unsigned"] = "1";
				}
				else
				{
					$attrs["unsigned"] = "0";
				}
				
				if($default == null && $null == "NO")
					$default = "0";
			}
			else if(substr($type, 0, 5) == "float" || substr($type, 0, 6) == "double")
			{
				// get size
				preg_match_all('/\((.*?)\)/', $type, $matches);
				$attrs["type"] = "float";
				$attrs["size"] = substr($type, 0, strpos($type, "("));
				$attrs["length"] = $matches[1][0];
			} 
			else if(substr($type, 0, 7) == "varchar")
			{
				// get size
				preg_match_all('/\((.*?)\)/', $type, $matches);
				$attrs["type"] = "string";
				$attrs["length"] = $matches[1][0];
			} 
			else if(substr($type, 0, 4) == "char")
			{
				// get size
				preg_match_all('/\((.*?)\)/', $type, $matches);
				$attrs["type"] = "char";
				$attrs["length"] = $matches[1][0];
			}
			else if(substr($type, 0, 4) == "text" || substr($type, 0, 8) == "longtext")
			{
				// get size
				$attrs["type"] = "string";
				$attrs["length"] = null;
			}
			else
			{
				$attrs["type"] = $type;
			}
			
			// null
			if($null == "NO")
			{
				$attrs["acceptNULL"] = '0';
			}
			else
			{
				$attrs["acceptNULL"] = '1';
			}
			
			// default
			$attrs["default"] = ($default == null ? "" : ($default == "NULL" ? null : $default));
			
			$fields[$field] = $attrs;
		}
		return $fields;
	}
	
	/*
	Print the data and its type
	
	@param [mixed] $var The variable
	@return [string] The printed variable
	*/
	public static function exportVar($var)
	{
		$type = self::type($var);
		
		if($type === "boolean")
			return ($var) ? "true" : "false";
		if($type === "integer")
			return "(integer) ".$var;
		if($type === "float")
			return "(float) ".$var;
		if($type === "string")
			return "(string) '".trim($var)."'";
		if($type === "null")
			return "(null)";
		if($type === "array")
			return "(array)";
		if($type === "resource")
			return "(resource)";
		return "(object) ".$type;
	}
};

?>