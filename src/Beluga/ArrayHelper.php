<?php
/**
 * In this file the class '\Beluga\ArrayHelper' is defined.
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
 * A static class for doing some array helping things.
 *
 * @since  v0.1
 */
abstract class ArrayHelper
{


   // <editor-fold desc="// = = = =   P U B L I C   S T A T I C   M E T H O D S   = = = = = = = = = = = = = = = = = =">

   /**
    * Extracts some associative array data from a XML attribute format string. (e.g.: a="20")
    *
    * Entities are auto converted to unicode UTF-8 characters!
    *
    * yes|no|on|off|true|false will be converted automatically to a boolean value.
    *
    * @param  string  $attributeStr The XML attribute string to parse.
    * @param  boolean $lowerKeys    Convert all keys (attribute names) to lower case? (defaults to FALSE)
    * @param  boolean $autoBoolean  Auto convert the values yes|no|on|off|true|false to boolean? (defaults to TRUE)
    * @return array
    */
   public static function ParseAttributes(
      string $attributeStr, bool $lowerKeys = false, bool $autoBoolean = true ) : array
   {

      // This init the resulting attribute array
      $attributes = [];

      // It will save the hits from preg_match_all
      $hits       = null;

      // Find all defined attributes
      if ( ! \preg_match_all( '~(?<=\A|[ \r\n\t])(\w+)=(\'([^\']*)\'|"([^"]+)")~', $attributeStr, $hits ) )
      {
         // No attributes was found. Return the empty result array
         return $attributes;
      }

      // Loop all found attribute regexp hits
      for ( $i = 0, $j = \count( $hits[ 0 ] ); $i < $j; $i++ )
      {

         // Get the defined key (in lowercase if defined)
         $key = $lowerKeys
            ? \strtolower( $hits[ 1 ][ $i ] )
            : $hits[ 1 ][ $i ];

         if ( isset( $hits[ 4 ][ $i ] ) && ! empty( $hits[ 4 ][ $i ] ) )
         {
            // Get the value from inside double quotes
            $attributes[ $key ] = unescapeXML( $hits[ 4 ][ $i ], true );
         }
         else
         {
            // Get the value from inside single quotes
            $attributes[ $key ] = unescapeXML( $hits[ 3 ][ $i ], true );
         }

         // Convert the value to boolean if required and if it makes sense
         if ( $autoBoolean )
         {

            if ( \preg_match( '~^(yes|no|on|off|true|false)$~i', $attributes[ $key ] ) )
            {
               $attributes[ $key ] = TypeTool::StrToType( $attributes[ $key ], Type::PHP_BOOLEAN );
            }
            elseif ( \preg_match( '~^(multiple|selected|disabled|readonly|checked)$~i', $key ) )
            {
               $attributes[ $key ] = \strtolower( $key ) === \strtolower( $attributes[ $key ] );
            }

         }

      }

      // Return the resulting array
      return $attributes;

   }

   /**
    * Extracts some associative array data from a HTML attribute format string. (e.g.: a="20" b=foo)
    *
    * Entities are auto converted to unicode UTF-8 characters!
    *
    * yes|no|on|off|true|false will be converted automatically to a boolean value.
    *
    * @param  string  $attributeStr The HTML attribute string to parse.
    * @param  boolean $lowerKeys    Convert all keys (attribute names) to lower case? (defaults to FALSE)
    * @param  boolean $autoBoolean  Auto convert the values yes|no|on|off|true|false to boolean? (defaults to FALSE)
    * @return array
    */
   public static function ParseHtmlAttributes( string $attributeStr, bool $lowerKeys = false, bool $autoBoolean = false ) : array
   {

      // This init the resulting attribute array
      $attributes = array();

      $dc = new \DOMDocument();

      try
      {
         if ( ! $dc->loadHTML( '<html><body><p ' . $attributeStr . '></p></body></html>' ) )
         {
            return $attributes;
         }
      }
      catch ( \Throwable $ex )
      {
         return $attributes;
      }

      $element = $dc->getElementsByTagName( 'p' )->item( 0 );

      if ( ! $element->hasAttributes() )
      {
         return $attributes;
      }

      foreach ( $element->attributes as $attr )
      {

         $key = $lowerKeys ? \strtolower( $attr->nodeName ) : (string) $attr->nodeName;
         $attributes[ $key ] = (string) $attr->nodeValue;

         // Convert the value to boolean if required and if it makes sense
         if ( $autoBoolean )
         {

            if ( \preg_match( '~^(yes|no|on|off|true|false)$~i', $attributes[ $key ] ) )
            {
               $attributes[ $key ] = TypeTool::StrToType( $attributes[ $key ], Type::PHP_BOOLEAN );
            }
            else if ( \preg_match( '~^(multiple|selected|disabled|readonly|checked)$~i', $key ) )
            {
               $attributes[ $key ] = \strtolower( $key ) === \strtolower( $attributes[ $key ] );
            }

         }

      }


      return $attributes;

   }

   /**
    * Returns if the defined array is numerically indicated. (0-n)
    *
    * @param  array $array The array to check
    * @return boolean
    */
   public static function IsNumericIndicated( array $array ) : bool
   {

      // Create the representative value (the joined array keys must be equal to it)
      $nums = \join( '', \range( 0, \count( $array ) - 1 ) );

      // check the required array keys with the given.
      return ( $nums === \join( '', \array_keys( $array ) ) );

   }

   /**
    * Builds a XML conform attribute string from a associative array (1 dimensional array!)
    *
    * If a array key not begins with a-z, A-Z or a underscore it is prefixed by a underscore!
    *
    * @param  array $attributes The associative array, defining the attributes
    * @return string
    */
   public static function CreateAttributeString( array $attributes ) : string
   {

      if ( empty( $attributes ) || static::IsNumericIndicated( $attributes ) )
      {
         // If no attributes are defined, or if the are not a associative array, return a empty string
         return '';
      }

      $res = [];

      foreach ( $attributes as $k => $v )
      {
         $key = $k;
         if ( ! \preg_match( '~^[A-Za-z_]~', $k ) )
         {
            // numeric keys (attribute names) should be prefixed with a underscore
            $key = '_' . $k;
         }
         if ( \is_bool( $v ) )
         {
            $vl = ( $v ? '1' : '0' );
         }
         elseif ( \is_int( $v ) || \is_double( $v ) || \is_float( $v ) )
         {
            $vl = \strval( $v );
         }
         else
         {
            $vl = escapeXMLArg( (string) $v );
         }
         $res[] = \sprintf( '%s="%s"', $key, $vl );
      }

      return ' ' . \join( ' ', $res );

   }

   /**
    * Inserts a new element to $array, at defined index.
    *
    * @param  array   $array   The array where to insert the new element.
    * @param  mixed   $element The element to insert.
    * @param  integer $index   The index of the new element. If lower than 0, 0 is used. If to large its appended to the end.
    * @return array
    */
   public static function Insert( array $array, $element, int $index ) : array
   {

      $cnt = \count( $array );

      if ( $index < 0 )
      {
         $index = 0;
      }
      elseif ( $index > $cnt )
      {
         $index = $cnt;
      }

      if ( $index == $cnt )
      {
         $array[] = $element;
         return $array;
      }

      if ( $index == 0 )
      {
         if ( \is_array( $element ) )
         {
            $array = \array_merge( array( $element ), $array );
         }
         else
         {
            $array = \array_merge( array( \rtrim( $element, "\r\n" ) ), $array );
         }
         return $array;
      }

      $tmp = \array_slice( $array, 0, $index );
      $tmp[] = \is_array( $element ) ? $element : \rtrim( $element, "\r\n" );

      return \array_merge( $tmp, \array_slice( $array, $index ) );

   }

   /**
    * Removes the element with defined index from array and reset the array element index after the removed element.
    *
    * @param  array   $array THe array.
    * @param  integer $index The index of the element to remove.
    * @return array
    */
   public static function Remove( array $array, int $index ) : array
   {

      if ( $index < 0 || $index >= \count( $array ) )
      {
         return $array;
      }

      if ( $index == 0 )
      {
         return \array_slice( $array, 1 );
      }

      if ( $index + 1 == \count( $array ) )
      {
         return \array_slice( $array, 0, -1 );
      }

      $neu = \array_slice( $array, 0, $index );

      return \array_merge( $neu, \array_slice( $array, $index + 1 ) );

   }

   /**
    * Removes all elements of the permitted array, beginning at $indexStart, with the defined count, or to the
    * end if no count is defined.
    *
    * @param  array        $array      The array
    * @param  integer      $indexStart The index of the first element to remove.
    * @param  integer|null $length     Optional count of elements to remove (can also be negative, so it counts backward from array end)
    * @return array
    */
   public static function RemoveRange( array $array, int $indexStart, int $length = null ) : array
   {

      $cnt = \count( $array );

      if ( $indexStart < 0 )
      {
         $indexStart = 0;
      }
      elseif ( $indexStart >= $cnt )
      {
         return $array;
      }

      if ( \is_null( $length ) )
      {
         $length = $cnt - $indexStart;
      }
      elseif ( $length < 0 )
      {
         $length = $cnt - $indexStart + $length;
      }

      if ( $length <= 0 )
      {
         return $array;
      }

      \array_splice( $array, $indexStart, $length );

      return $array;

   }

   /**
    * Returns the depth of permitted array.
    *
    * @param  array $array The array to check
    * @return integer Returns the depth.
    */
   public static function GetMaxDepth( array $array ) : int
   {

      if ( \count( $array ) < 1 )
      {
         return 0;
      }

      $c = 1;
      foreach ( $array as $v )
      {

         if ( \is_array( $v ) )
         {
            $x = 1 + static::GetMaxDepth( $v );
            if ( $x > $c )
            {
               $c = $x;
            }
         }

      }

      return $c;

   }

   /**
    * Returns if array has only a depth of 1 level.
    *
    * @param  array $array The array to check
    * @return bool
    */
   public static function IsSingleDepth( array $array ) : bool
   {

      if ( \count( $array ) < 1 )
      {
         return false;
      }

      foreach ( $array as $v )
      {

         if ( \is_array( $v ) || \is_object( $v ) )
         {
            return false;
         }

      }

      return true;

   }

   /**
    * Extracts all elements beginning at element with index $startIndex with defined length/count (or to the end)
    *
    * @param  array   $array      The array
    * @param  integer $startIndex The index of the element where extraction starts
    * @param  integer $length     The optional lenght/count of required array elements (default=null mens to the end)
    * @return array
    */
   public static function Extract( array $array, int $startIndex, int $length = null ) : array
   {

      $count = \count( $array );

      if ( $count < 1 || $startIndex >= $count )
      {
         return array();
      }

      if ( $startIndex == $count - 1)
      {
         return array( $array[$startIndex] );
      }

      if ( ! \is_integer( $length ) )
      {
         $length = $count - $startIndex;
      }

      if ( $length == 0 )
      {
         return array();
      }
      elseif ( $length < 0 )
      {
         $length = ($count - $startIndex) + $length;
      }

      if ( $length == $count )
      {
         return $array;
      }

      return \array_slice( $array, $startIndex, $length );

   }

   # </editor-fold>


}

