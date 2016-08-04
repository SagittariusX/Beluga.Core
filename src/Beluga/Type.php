<?php
/**
 * In this file the class 'Type' is defined.
 *
 * @author         SagittariusX <unikado+sag@gmail.com>
 * @copyright  (c) 2016, UniKado
 * @package        Beluga\Core
 * @since          2016-07-31
 * @version        0.1.0
 */


declare( strict_types = 1 );


namespace Beluga;


/**
 * This class defines a object for getting and holding information about a value.
 *
 * @since  v0.1
 */
class Type
{


   # <editor-fold desc="= = =   C O N S T A N T S   = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =">

   /**
    * The PHP type of double values.
    */
   const PHP_DOUBLE = 'double';

   /**
    * The PHP type of float values.
    */
   const PHP_FLOAT = 'float';

   /**
    * The PHP type of integer values.
    */
   const PHP_INTEGER = 'int';

   /**
    * The PHP type of boolean values.
    */
   const PHP_BOOLEAN = 'bool';

   /**
    * The PHP type of string values.
    */
   const PHP_STRING = 'string';

   /**
    * The PHP type of NULL values.
    */
   const PHP_NULL = 'null';

   /**
    * The PHP type of resource values.
    */
   const PHP_RESOURCE = 'resource';

   /**
    * The PHP type of array values.
    */
   const PHP_ARRAY = 'array';

   /**
    * All other unknown types
    */
   const PHP_UNKNOWN = 'unknown';

   # </editor-fold>


   # <editor-fold desc="= = =   P R I V A T E   F I E L D S   = = = = = = = = = = = = = = = = = = = = = = = = =">

   /**
    * The value.
    *
    * @var mixed
    */
   private $value;

   /**
    * Defines if the value is convertible to a string.
    *
    * @var boolean
    */
   private $isStringConvertible;

   /**
    * The string value associate with the base value.
    *
    * @var string
    */
   private $stringValue;

   /**
    * The name of the base value type.
    *
    * @var string
    */
   private $typeName;

   # </editor-fold>


   # <editor-fold desc="= = =   C O N S T R U C T O R   +   D E S T R U C T O R   = = = = = = = = = = = = = = =">

   /**
    * Init's a new instance.
    *
    * @param  mixed $value The base value.
    * @throws \Beluga\BelugaError If $value is of type {@see Type}
    */
   public function __construct( $value )
   {

      $this->value               = $value;
      $this->isStringConvertible = TypeTool::IsStringConvertible( $value, $this->stringValue );

      if ( ! $this->isStringConvertible )
      {
         $this->stringValue = null;
      }

      if ( \is_null( $value ) )
      {
         $this->typeName = Type::PHP_NULL;
      }
      else if ( \is_string( $value ) )
      {
         $this->typeName = Type::PHP_STRING;
      }
      else if ( \is_array( $value ) )
      {
         $this->typeName = Type::PHP_ARRAY;
      }
      else if ( \is_object( $value ) )
      {
         if ( $value instanceof Type )
         {
            throw new BelugaError( 'Core', 'Could not create \\Beluga\\Type-Instance of a \\Beluga\\Type-Value!' );
         }
         $this->typeName = \get_class( $value );
      }
      else if ( \is_int( $value ) )
      {
         $this->typeName = Type::PHP_INTEGER;
      }
      else if ( \is_double( $value ) )
      {
         $this->typeName = Type::PHP_DOUBLE;
      }
      else if ( \is_float( $value ) )
      {
         $this->typeName = Type::PHP_FLOAT;
      }
      else if ( \is_bool( $value ) )
      {
         $this->typeName = Type::PHP_BOOLEAN;
      }
      else if ( \is_resource( $value ) )
      {
         $this->typeName = Type::PHP_RESOURCE;
      }
      else
      {
         $this->typeName = Type::PHP_UNKNOWN;
      }

   }

   # </editor-fold>


   # <editor-fold desc="= = =   P U B L I C   M E T H O D S   = = = = = = = = = = = = = = = = = = = = = = = = =">

   /**
    * Checks if the current instance is equal to defined value. If strict is FALSE only the value is checked.
    * Otherwise also the type is checked.
    *
    * @param  mixed   $value  The other value.
    * @param  boolean $strict Check strictly also the type
    * @return boolean         TRUE oder FALSE
    */
   public function equals( $value, bool $strict = false ) : bool
   {

      if ( ! ( $value instanceof Type ) )
      {
         $value = new Type( $value );
      }

      $res = ( $value->typeName == $this->typeName );

      if ( ! $res || ! $strict )
      {
         return $res;
      }

      if ( $value->isStringConvertible )
      {
         return ( $value->stringValue == $this->stringValue );
      }

      return ( $value->value == $this->value );

   }

   /**
    * Returns the base value.
    *
    * @return mixed
    */
   public final function getValue()
   {

      return $this->value;

   }

   /**
    * Returns the associated string value, if the base value is convertible to a string.
    * Otherwise the defined default value is returned.
    *
    * @param  string $default THis will be returned if the base value is not string convertible (default='')
    * @return string
    */
   public final function getStringValue( string $default='' ) : string
   {

      if ( ! $this->isStringConvertible )
      {
         return $default;
      }

      return $this->stringValue;

   }

   /**
    * Returns if the base value can be converted to a string.
    *
    * @return boolean
    */
   public final function hasAssociatedString() : bool
   {

      return $this->isStringConvertible;

   }

   /**
    * Returns the type name of the base value.
    *
    * @return string
    */
   public final function getType() : string
   {

      return $this->typeName;

   }

   /**
    * Returns if the base value is a PHP resource type.
    *
    * @return boolean
    */
   public final function isResource() : bool
   {

      return $this->typeName == Type::PHP_RESOURCE;

   }

   /**
    * To string.
    *
    * @return string
    */
   public final function __toString()
   {

      return $this->stringValue;

   }

   /**
    * Returns the PHP code, defining the current base value.
    *
    * @return string
    */
   public final function getPhpCode() : string
   {

      $str = $this->stringValue;

      switch ( $this->typeName )
      {

         case Type::PHP_BOOLEAN:
         case 'boolean':
            return ( $this->value ? 'true' : 'false' );

         case Type::PHP_DOUBLE:
         case Type::PHP_FLOAT:
         case Type::PHP_INTEGER:
         case 'integer':
            return $str;

         case Type::PHP_STRING:
            if ( \preg_match( "~[\r\n\t]+~", $str ) )
            {
               $str = \str_replace(
                  array( '\\',  "\r", "\n", "\t", "\0", '"',  '$' ),
                  array( '\\\\','\\r','\\n','\\t','\\0','\\"','\\$' ),
                  $str
               );
               return '"' . $str . '"';
            }
            return "'" . \str_replace(
                  array( '\\',   "'" ),
                  array( '\\\\', "\\'" ),
                  $str
               )
               . "'";

         case Type::PHP_RESOURCE:
         case Type::PHP_NULL:
         case Type::PHP_UNKNOWN:
            return 'null';

         default:
            $str = \serialize( $this->value );
            if ( \preg_match( "~[\r\n\t]+~", $str ) )
            {
               $str = \str_replace(
                  array( '\\',  "\r", "\n", "\t", "\0", '"',  '$' ),
                  array( '\\\\','\\r','\\n','\\t','\\0','\\"','\\$' ),
                  $str
               );
            }
            else
            {
               $str = \str_replace(
                  array('\\',   '"',   '$'),
                  array('\\\\', '\\"', '\\$'),
                  $str );
            }
            return '\unserialize("' . $str . '")';

      }

   }

   /**
    * Makes the Type instance cloneable.
    *
    * @return Type
    */
   public function __clone()
   {

      $res = null;

      switch ( $this->typeName )
      {
         case Type::PHP_BOOLEAN:
         case Type::PHP_DOUBLE:
         case Type::PHP_FLOAT:
         case Type::PHP_INTEGER:
         case Type::PHP_NULL:
         case Type::PHP_RESOURCE:
         case Type::PHP_STRING:
            $res = new Type( $this->value );
            break;

         default:
            $val = clone( $this->value );
            $res = new Type( $val );
            break;

      }

      return $res;

   }

   # </editor-fold>


}

