<?php
/**
 * In this file the static class '\Beluga\TypeTool' is defined.
 *
 * @author         SagittariusX <unikado+sag@gmail.com>
 * @copyright  (c) 2016, UniKado
 * @package        Beluga\Core
 * @since          2016-07-31
 * @subpackage     Helpers
 * @version        0.1.0
 */


declare( strict_types = 1 );


namespace Beluga;


/**
 * This is a static helper class, to better handle some PHP type juggling.
 *
 * @since  v0.1
 */
abstract class TypeTool
{


   // <editor-fold desc="// = = = =   P R I V A T E   S T A T I C   F I E L D S   = = = = = = = = = = = = = = = = = =">

   /**
    * This is the regular expression to check if a string can be used as integer value.
    *
    * @var string
    */
   private static $rxInt32 = '~^-?(0|[1-9]\d{0,11})$~';

   /**
    * This is the regular expression to check if a string can be used as big integer or long value.
    *
    * @var string
    */
   private static $rxInt64 = '~^-?(0|[1-9]\d{0,19})$~';

   /**
    * This is the regular expression to check if a string can be used as double precission value,
    * with a dot as decimal separator.
    *
    * @var string
    */
   private static $rxD1 = '~^-?((0|[1-9]\d{0,20})?\.\d{0,14}|\.\d{0,14})$~';

   /**
    * This is the regular expression to check if a string can be used as decimal value,
    * with a dot or comma as decimal separator.
    *
    * @var string
    */
   private static $D    = '~^-?((0|[1-9]\d{0,20})?(\.|,)\d{0,14}|(\.|,)\d{1,14})$~';

   /**
    * This is the regular expression to check if a string can be used a boolean TRUE value.
    *
    * @var string
    */
   private static $rxBoolTRUE = '~^([1-9]\d*|t(rue)?|on|yes|ok|enabled|disabled|readonly|autocomplete|autofill|selected)$~i';

   /**
    * This is the regular expression to check if a string can be used a boolean FALSE value.
    *
    * @var string
    */
   private static $rxBoolFALSE = '~^(0|-[1-9]\d*|f(alse)|off|no|out)$~i';

   # </editor-fold>


   // <editor-fold desc="// = = = =   P U B L I C   S T A T I C   M E T H O D S   = = = = = = = = = = = = = = = = = =">

   /**
    * Returns if the defined value is usable as integer value.
    *
    * @param  mixed $value The value to check
    * @return boolean
    */
   public static function IsInteger( $value ) : bool
   {

      return (bool) \preg_match( static::$rxInt32, \strval( $value ) );

   }

   /**
    * Returns if the defined value is usable as a decimal value.
    *
    * @param  mixed   $value            The value to check.
    * @param  boolean $cultureInvariant If TRUE, also the comma can be used as decimal separator.
    * @return boolean
    */
   public static function IsDecimal( $value, bool $cultureInvariant = false ) : bool
   {

      if ( \preg_match( static::$rxInt64, \strval($value) ) )
      {
         return true;
      }

      if ( ! $cultureInvariant )
      {
         return (bool) \preg_match( static::$rxD1, \strval( $value ) );
      }

      return (bool) \preg_match( static::$D, \strval( $value ) );

   }

   /**
    * Returns if the defined value is usable as a boolean value. If so, $resultingBoolValue returns the resulting
    * boolean value.
    *
    * @param  mixed   $value              The value to check.
    * @param  boolean $resultingBoolValue Returns the resulting boolean value, if the method returns TRUE
    * @return boolean
    */
   public static function IsBoolConvertible( $value, &$resultingBoolValue ) : bool
   {

      if ( \is_null( $value ) )
      {
         $resultingBoolValue = false;
         return false;
      }

      if ( \is_bool( $value ) )
      {
         $resultingBoolValue = $value;
         return true;
      }

      if ( \is_resource( $value ) )
      {
         $resultingBoolValue = true;
         return true;
      }

      if ( \is_object( $value ) )
      {
         $resultingBoolValue = false;
         return false;
      }

      if ( \is_array( $value ) )
      {
         $resultingBoolValue = \count( $value ) > 0;
         return true;
      }

      if ( static::IsInteger( $value ) )
      {
         $resultingBoolValue = ( \intval( $value ) > 0 );
         return true;
      }

      if ( static::IsDecimal( $value ) )
      {
         $resultingBoolValue = ( \doubleval( $value ) > 0 );
         return true;
      }

      if ( ! static::IsStringConvertible( $value, $strVal ) )
      {
         $resultingBoolValue = false;
         return false;
      }

      if ( \preg_match( static::$rxBoolTRUE, $strVal ) )
      {
         $resultingBoolValue = true;
         return true;
      }

      if ( \preg_match( static::$rxBoolFALSE, $strVal ) )
      {
         $resultingBoolValue = false;
         return true;
      }

      if ( \preg_match( '~^b:[01];$~', $strVal ) )
      {
         // A serialized boolean value
         $resultingBoolValue = \unserialize( $strVal );
         return true;
      }

      if ( \strlen( $strVal ) < 1 )
      {
         $resultingBoolValue = false;
         return true;
      }

      $resultingBoolValue = false;
      return false;

   }

   /**
    * Returns if the defined value is usable as a string value. If so, $resultingString returns the resulting
    * string value.
    *
    * @param  mixed   $value           The value to check.
    * @param  string  $resultingString Returns the resulting string value, if the method returns TRUE
    * @return boolean
    */
   public static function IsStringConvertible( $value, &$resultingString ) : bool
   {

      if ( \is_null( $value ) )
      {
         $resultingString = '';
         return true;
      }

      if ( \is_string( $value ) )
      {
         $resultingString = $value;
         return true;
      }

      if ( \is_bool( $value ) )
      {
         $resultingString = $value ? 'true' : 'false';
         return true;
      }

      if ( \is_resource( $value ) )
      {
         $resultingString = '';
         return false;
      }

      if ( \is_object( $value ) )
      {
         if ( \method_exists( $value, '__toString' ) )
         {
            $resultingString = (string) $value;
            return true;
         }
         if ( $value instanceof \DateTimeInterface )
         {
            $resultingString = $value->format( 'Y-m-d H:i:s' );
            return true;
         }
         $resultingString = '';
         return false;
      }

      if ( \is_array( $value ) )
      {
         $resultingString = \serialize( $value );
         return true;
      }

      if ( is_int( $value ) )
      {
         $resultingString = \strval( $value );
         return true;
      }

      if ( static::IsDecimal( $value ) )
      {
         $resultingString = \strval( $value );
         return true;
      }

      $resultingString = '';
      return false;

   }

   /**
    * Converts a string to defined native PHP type.
    *
    * @param  string $string   The string to convert
    * @param  string $typename The name of the required resulting PHP type.
    *         Allowed types are (bool|boolean|double|float|int|integer|string|array)
    * @return mixed
    */
   public static function StrToType( string $string, $typename )
   {

      if ( null === $string )
      {
         return null;
      }

      $t = new Type( $string );
      if ( ! $t->hasAssociatedString() )
      {
         return null;
      }

      $string = $t->getStringValue();

      switch ( \strtolower( $typename ) )
      {

         case 'bool':
         case 'boolean':
            $res = false;
            static::IsBoolConvertible( $string, $res );
            return $res;

         case 'float':
            return \floatval( \str_replace( ',', '.', $string ) );

         case 'double':
            if ( ! static::IsDecimal( $string, true ) )
            {
               return null;
            }
            $res = \str_replace( ',', '.', $string );
            $tmp = \explode( '.', $res );
            $ts  = \count( $tmp );
            if ( $ts > 2 )
            {
               $dv = $tmp[ $ts - 1 ];
               unset ( $tmp[ $ts - 1 ] );
               $dv = \join( '', $tmp ) . '.' . $dv;
               return \doubleval( $dv );
            }
            return \doubleval( $res );

         case 'int':
         case 'integer':
            if ( static::IsInteger( $string ) || static::IsDecimal( $string, true ) )
            {
               return \intval( $string );
            }
            return null;

         case 'string':
            return $string;

         case 'array':
            if ( \strlen( $string ) < 1 )
            {
               return [];
            }
            if ( \strlen( $string ) > 3 )
            {
               if ( \substr( $string, 0, 2 ) == 'a:' )
               {
                  try
                  {
                     $res = \unserialize( $string );
                     if ( \is_array( $res ) )
                     {
                        return $res;
                     }
                  }
                  catch ( \Exception $ex ) { }
               }
               if ( strStartsWith( $string, '[' ) &&
                    strEndsWith( $string, ']' ))
               {
                  try { return (array) \json_decode( $string ); }
                  catch ( \Exception $ex ) { }
               }
               else if ( strStartsWith( $string, '{' ) &&
                         strEndsWith( $string, '}' ) )
               {
                  try { return (array) \json_decode( $string ); }
                  catch ( \Exception $ex ) { }
               }
            }
            return array( $string );

         default:
            if ( \strlen( $string ) < 1 )
            {
               return null;
            }
            if ( \strlen( $string ) > 3 )
            {
               if ( \substr( $string, 0, 2 ) == 'O:' &&
                    \preg_match( '~^O:[^"]+"' . $typename . '":~', $string ) )
               {
                  try
                  {
                     $res = \unserialize( $string );
                     if ( ! \is_object( $res ) )
                     {
                        return null;
                     }
                     if ( \get_class( $res ) == $typename )
                     {
                        return $res;
                     }
                  }
                  catch ( \Throwable $ex ) { }
               }
            }
            return null;

      }

   }

   /**
    * Extracts typed data from defined XML element.
    *
    * For it, the XML element must define the data of a single value.
    *
    * The type name can be defined by 'type' attribute or by the
    * &lt;Type&gt;Typename&lt;/Type&gt; element.
    *
    * The type value can be defined as string by 'value' attribute or by the
    * &lt;Value&gt;A value&lt;/Value&gt; element, or by the string value defined
    * inside the XML element it self. (&lt;XmlElement type="..."&gt;A Value&lt;/XmlElement&gt;)
    *
    * @param  \SimpleXMLElement $xmlElement The XML element defining the typed value.
    * @return mixed The typed value, or NULL if no usable data are defined
    */
   public static function XmlToType( \SimpleXMLElement $xmlElement )
   {

      $type = null;
      $value = null;

      if ( false !== ( $type = XmlAttributeHelper::GetAttributeValue( $xmlElement, 'type' ) ) )
      {
         $type = (string) $type;
      }
      else if ( isset( $xmlElement->type ) )
      {
         $type = (string) $xmlElement->type;
      }
      else if ( isset( $xmlElement->Type ) )
      {
         $type = (string) $xmlElement->Type;
      }
      else
      {
         return $value;
      }

      if ( false !== ( $value = XmlAttributeHelper::GetAttributeValue( $xmlElement, 'value' ) ) )
      {
         $value = (string) $value;
      }
      elseif ( isset( $xmlElement->value ) )
      {
         $value = (string) $xmlElement->value;
      }
      elseif ( isset( $xmlElement->Value ) )
      {
         $value = (string) $xmlElement->Value;
      }
      else
      {
         $value = (string) $xmlElement;
      }

      return static::StrToType( $value, $type );

   }

   /**
    * Writes all data of the defined Value to the XmlWriter.
    *
    * You can write it in 3 different ways:
    *
    * - &lt;$name type="..." value="..." /&gt;
    *   Is created if $short is boolean TRUE.
    *
    * If $short is FALSE you can write in the following 2 ways:
    *
    * - &lt;$name type="..."&gt;$value&lt;/$name&gt;
    *   Is created if $separateElements is boolean FALSE.
    * - &lt;$name&gt;&lt;Type&gt;...&lt;/Type&gt;&lt;Value&gt;$value&lt;/Value&gt;&lt;/$name&gt;
    *   Is created if $separateElements is boolean TRUE.
    *
    * @param \XMLWriter $w                The XmlWriter to use.
    * @param mixed      $value            The value to write.
    * @param string     $name             The name of the element to write
    * @param boolean    $short            Use the short notation? (type and value as attributes)
    * @param boolean    $separateElements Write type and value in separate elements, if $short is FALSE
    */
   public static function WriteTypedXmlValue(
      \XMLWriter $w, $value, string $name, bool $short = true, bool $separateElements = false)
   {

      if ( ! ( $value instanceof Type ) )
      {
         $value = new Type( $value );
      }

      $v = null;
      $t = null;

      switch ( $value->getType() )
      {

         case Type::PHP_ARRAY:
            $v = \serialize( $value->getValue() );
            $t = Type::PHP_ARRAY;
            break;

         case Type::PHP_BOOLEAN:
            $v = $value->getValue() ? 'true' : 'false';
            $t = Type::PHP_BOOLEAN;
            break;

         case Type::PHP_DOUBLE:
         case Type::PHP_FLOAT:
         case Type::PHP_INTEGER:
            $v = $value->getValue();
            $t = $value->getType();
            break;

         case Type::PHP_NULL:
            $v = '';
            $t = Type::PHP_NULL;
            break;

         case Type::PHP_RESOURCE:
             # Ignore some resources
            break;

         case Type::PHP_STRING:
            $v = $value->getValue();
            $t = Type::PHP_STRING;
            break;

         case Type::PHP_UNKNOWN:
            $v = $value->getStringValue();
            $t = Type::PHP_STRING;
            break;

         default:
            $v = \serialize( $value->getValue() );
            $t = $value->getType();
            break;

      }

      if ( ! \is_null( $t ) && ! \is_null( $v ) )
      {

         $w->startElement( $name );

         if ( $short )
         {
            $w->writeAttribute( 'type', $t );
            $w->writeAttribute( 'value', $v );
         }
         else
         {
            if ( $separateElements )
            {
               $w->writeElement( 'type',  $t );
               $w->writeElement( 'value', $v );
            }
            else
            {
               $w->writeAttribute( 'type', $t );
               $w->text( $v );
            }
         }

         $w->endElement();

      }

   }

   /**
    * Returns if the type of defined value is a native PHP type.
    *
    * Native types are: boolean, integer, double, float, string, array
    *
    * @param  mixed $value THe value to check.
    * @return boolean
    */
   public static function IsNativeType( $value ) : bool
   {

      return (
            \is_bool(   $value ) ||
            \is_int(    $value ) ||
            \is_string( $value ) ||
            \is_double( $value ) ||
            \is_float(  $value ) ||
            \is_array(  $value )
      );

   }

   /**
    * Returns the native type name of the defined value.
    *
    * @param  mixed $value The value.
    * @return string Returns the name of the type (see \Beluga\Type::PHP_* constants) or boolean FALSE if the value has not native type
    */
   public static function GetNativeType( $value )
   {

      if ( \is_string( $value ) )
      {
         return Type::PHP_STRING;
      }

      if ( \is_int( $value ) )
      {
         return Type::PHP_INTEGER;
      }

      if ( \is_bool( $value ) )
      {
         return Type::PHP_BOOLEAN;
      }

      if ( \is_double( $value ) )
      {
         return Type::PHP_DOUBLE;
      }

      if ( \is_float( $value ) )
      {
         return Type::PHP_FLOAT;
      }

      return false;

   }

   /**
    * Returns the type name of the defined value.
    *
    * @param  mixed $value The value.
    * @return string
    */
   public static function GetTypeName( $value ) : string
   {

      if ( \is_null( $value ) )
      {
         return Type::PHP_NULL;
      }

      if ( \is_object( $value ) )
      {
         return \get_class( $value );
      }

      if ( \is_string( $value ) )
      {
         return Type::PHP_STRING;
      }

      if ( \is_int( $value ) )
      {
         return Type::PHP_INTEGER;
      }

      if ( \is_bool( $value ) )
      {
         return Type::PHP_BOOLEAN;
      }

      if ( \is_double( $value ) )
      {
         return Type::PHP_DOUBLE;
      }

      if ( \is_float( $value ) )
      {
         return Type::PHP_FLOAT;
      }

      if ( \is_resource( $value ) )
      {
         return Type::PHP_RESOURCE;
      }

      if ( \is_array( $value ) )
      {
         return Type::PHP_ARRAY;
      }

      return Type::PHP_UNKNOWN;

   }

   /**
    * Converts the value! It must be a native PHP type (bool, int, float, double, string) to a other native PHP type.
    *
    * @param  mixed  $sourceValue The value to convert
    * @param  string $newType Native PHP type as target type. (See \Beluga\Type::PHP_* constants)
    * @return mixed
    * @throws \Beluga\ArgumentError
    */
   public static function ConvertNative( $sourceValue, string $newType )
   {

      if ( \is_null( $sourceValue ) )
      {
         return $sourceValue;
      }

      if ( false === ( $sourceType = static::GetNativeType( $sourceValue ) ) )
      {
         throw new ArgumentError(
            'sourceValue',
            $sourceValue,
            'Core TypeTool::ConvertNative',
            'Can not convert a value of a type that is not a native PHP type! (bool, int, double, float, string)'
         );
      }

      if ( $sourceType == $newType )
      {
         return $sourceValue;
      }

      switch ( $sourceType )
      {

         case Type::PHP_BOOLEAN:
         case 'boolean':
            switch ( $newType )
            {
               case Type::PHP_DOUBLE:
                  return (double) ( $sourceValue ? 1 : 0 );
               case Type::PHP_FLOAT:
                  return (float) ( $sourceValue ? 1 : 0 );
               case Type::PHP_INTEGER:
                  return ( $sourceValue ? 1 : 0 );
               case Type::PHP_STRING:
                  return ( $sourceValue ? '1' : '0' );
            }
            break;

         case Type::PHP_DOUBLE:
            switch ( $newType )
            {
               case Type::PHP_FLOAT:
                  return \doubleval( $sourceValue );
               case Type::PHP_INTEGER:
                  return \intval( $sourceValue );
               case Type::PHP_STRING:
                  return '' . $sourceValue;
               case Type::PHP_BOOLEAN:
               case 'boolean':
                  return $sourceValue > 0;
            }
            break;

         case Type::PHP_FLOAT:
            switch ( $newType )
            {
               case Type::PHP_DOUBLE:
                  return \floatval( $sourceValue );
               case Type::PHP_INTEGER:
                  return \intval( $sourceValue );
               case Type::PHP_STRING:
                  return '' . $sourceValue;
               case Type::PHP_BOOLEAN:
               case 'boolean':
                  return $sourceValue > 0;
            }
            break;

         case Type::PHP_INTEGER:
            switch ( $newType )
            {
               case Type::PHP_DOUBLE:
                  return \intval( $sourceValue );
               case Type::PHP_FLOAT:
                  return \intval( $sourceValue );
               case Type::PHP_STRING:
                  return '' . $sourceValue;
               case Type::PHP_BOOLEAN:
               case 'boolean':
                  return $sourceValue > 0;
            }
            break;

         case Type::PHP_STRING:
            switch ( $newType )
            {
               case Type::PHP_INTEGER:
               case Type::PHP_BOOLEAN:
               case 'boolean':
               case Type::PHP_FLOAT:
               case Type::PHP_DOUBLE:
                  return TypeTool::StrToType($sourceValue, $newType);
            }
            break;

      }

      throw new ArgumentError(
         'newType',
         $newType,
         'Can not convert a value of a type that isnt a native PHP type! (bool, int, double, float, string)'
      );

   }

   # </editor-fold>


}

