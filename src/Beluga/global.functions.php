<?php
/**
 * Here all framework core functions are defined.
 *
 * The most of the here defined functions are for better string handling.
 *
 * @author         SagittariusX <unikado+sag@gmail.com>
 * @copyright  (c) 2016, UniKado
 * @package        Beluga\Core
 * @since          2016-07-31
 * @subpackage     Functions
 * @version        0.1.0
 */


declare( strict_types = 1 );


namespace Beluga;


const ESCAPE_HTML_ALL = 'html_all';
const ESCAPE_HTML     = 'html';
const ESCAPE_URL      = 'url';
const ESCAPE_JSON     = 'json';
const ESCAPE_HTM_ALL = 'html_all';


/**
 * Returns the length of a string with a defined encoding.
 * Encapsulate PHPs {@link http://php.net/mb_strlen mb_strlen} function.
 *
 * @param      string $str The string
 * @param      string $charset Encoding of the string (defaults to 'UTF-8')
 * @return     integer
 * @uses       Multibyte extension The function requires that PHP have the Multibyte extension mb_string enabled.
 * @since      v0.1
 */
function strLen( string $str, string $charset = 'UTF-8' ) : int
{

   return \mb_strlen( $str, $charset );

}

/**
 * Extracts a sub \string from $str with a defined encoding.
 *
 * @param      \string $str The string to work with.
 * @param      int    $start Start index (0-n) where the extraction begins.
 * @param      int    $length Length og the substring to extract or all, if the default value NULL is used.
 *             It also can use a negative value.
 * @param      string $charset Encoding of the string (defaults to 'UTF-8')
 * @return     string
 * @uses       Multibyte extension The function requires that PHP have the Multibyte extension mb_string enabled.
 * @since      v0.1
 */
function substr( string $str, int $start, int $length = null, string $charset = 'UTF-8' ) : string
{

   // Return a empty string, if $str is NULL
   if ( \is_null( $str ) )
   {
      return '';
   }

   // If no length of $str is defined get it
   if ( \is_null( $length ) )
   {
      $length = strLen( $str, $charset ) - $start;
   }

   // return the substr result
   return \mb_substr( $str, $start, $length, $charset );

}

/**
 * Returns the first position (index 0-n) of $needle inside $str, or (bool)FALSE if $needle is not contained.
 *
 * @param  string $str      The string.
 * @param  string $needle   The sub string to locate in $str
 * @param  bool   $caseless Ignore the case? (defaults to FALSE)
 * @param  string $charset  Encoding of the string (defaults to 'UTF-8')
 * @param  int    $offset   Start position of search, or NULL
 * @return integer|FALSE        Returns the resulting index, or boolean FALSE if not found.
 * @uses   Multibyte extension The function requires that PHP have the Multibyte extension mb_string enabled.
 * @since  v0.1
 */
function strPos( string $str, string $needle, bool $caseless = false, string $charset = 'UTF-8', int $offset = 0 )
{

   // If a required parameter is wrong, return FALSE
   if ( \is_null( $str ) ||
        \is_null( $needle ) ||
        \strlen( $needle ) < 1 ||
        \strlen( $str ) < 1 )
   {
      return false;
   }

   if ( $caseless )
   {
      // getting the caseless position
      $result =  \mb_stripos( $str, $needle, $offset, $charset );
   }
   else
   {
      // Getting the position depending to the case
      $result = \mb_strpos( $str, $needle, $offset, $charset );
   }

   // if noting was found, return FALSE
   if ( $result < 0 )
   {
      return false;
   }

   return $result;

}

/**
 * Returns the last position (index 0-n) of $needle inside $str, or (bool)FALSE if $needle is not contained.
 *
 * @param  string $str      The string.
 * @param  string $needle   The sub string to locate in $str
 * @param  bool   $caseless Ignore the case? (defaults to FALSE)
 * @param  string $charset  Encoding of the string (defaults to 'UTF-8')
 * @return integer|FALSE    Returns the resulting index, or boolean FALSE if not found.
 * @uses   Multibyte extension The function requires that PHP have the Multibyte extension mb_string enabled.
 * @since  v0.1
 */
function strLastPos( string $str, string $needle, bool $caseless = false, string $charset = 'UTF-8' )
{

   // If a required parameter is wrong, return FALSE
   if ( \is_null( $str ) ||
        \is_null( $needle ) ||
        \strlen( $needle ) < 1 ||
        \strlen( $str ) < 1 )
   {
      return false;
   }

   if ( $caseless )
   {
      // getting the caseless position
      $idx =  \mb_strripos( $str, $needle, 0, $charset );
   }
   else
   {
      // Getting the position depending to the case
      $idx = \mb_strrpos( $str, $needle, 0, $charset );
   }

   // if noting was found, return FALSE
   if ( $idx < 0 )
   {
      return false;
   }

   return $idx;

}

/**
 * Returns all Positions of $needle in $str.
 *
 * @param  string $str      The string.
 * @param  string $needle   The sub string to locate in $str
 * @param  bool   $caseless Ignore the case? (defaults to FALSE)
 * @param  string $charset  Encoding of the string (defaults to 'UTF-8')
 * @return array|FALSE      Return a numeric indicated array with all positions of $needle in $str or (bool)FALSE if
 *                          nothing was found
 * @uses   Multibyte extension The function requires that PHP have the Multibyte extension mb_string enabled.
 * @uses   \Beluga\strPos
 * @uses   \Beluga\strLen
 * @since  v0.1
 */
function strPositions( string $str, string $needle, bool $caseless = false, string $charset = 'UTF-8' )
{

   // Init the array of positions, to return
   $positions = array();

   // If nothing was found, return FALSE
   if ( false === ( $idx = strPos( $str, $needle, $caseless, $charset ) ) )
   {
      return false;
   }

   // Adding the first position to the postions result array
   $positions[] = $idx;

   // Getting the length of the needle string
   $needleLen = strLen( $needle, $charset );

   // Finding all next positions
   while ( false !== ( $next = strPos( $str, $needle, $caseless, $charset, $idx + $needleLen ) ) )
   {
      $idx = $next;
      $positions[] = $idx;
   }

   return $positions;

}

/**
 * Returns, if $str starts with $needle.
 *
 * @param  string  $str      The string to check.
 * @param  string  $needle   The string, where $str must starts with
 * @param  boolean $caseless Ignore the case? (defaults to FALSE)
 * @param  string  $charset Encoding of the string (defaults to 'UTF-8')
 * @return boolean Returns TRUE on success, FALSE otherwise.
 * @uses   Multibyte extension The function requires that PHP have the Multibyte extension mb_string enabled.
 * @uses   \Beluga\strPos
 * @since  v0.1
 */
function strStartsWith( string $str, string $needle, bool $caseless = false, string $charset = 'UTF-8' ) : bool
{

   return ( 0 === strPos( $str, $needle, $caseless, $charset ) );

}

/**
 * Returns, if $str ends with $needle.
 *
 * @param  string  $str      The string to check.
 * @param  string  $needle   The string, where $str must ends with
 * @param  boolean $caseless Ignore the case? (defaults to FALSE)
 * @param  string  $charset  Encoding of the string (defaults to 'UTF-8')
 * @return boolean Returns TRUE on success, FALSE otherwise.
 * @uses   Multibyte extension The function requires that PHP have the Multibyte extension mb_string enabled.
 * @uses   \Beluga\strlen
 * @uses   \Beluga\strLastPos
 * @since  v0.1
 */
function strEndsWith( string $str, string $needle, bool $caseless = false, string $charset = 'UTF-8' ) : bool
{

   // If $str is not a string, or if $needle is no string, return false
   if ( !\is_string( $str ) || !\is_string( $needle ) )
   {
      return false;
   }

   // Getting the lengths of $needle and $str
   $nl = strLen( $needle, $charset );
   $sl = strLen( $str, $charset );

   // If $needle is empty, or if $needle is longer than $str, return FALSE
   if ( ( $nl < 1 ) || ( $sl < $nl ) )
   {
      return false;
   }

   return ( ($sl - $nl) == strLastPos($str, $needle, $caseless, $charset) );

}

/**
 * Returns, if $str contains $needle.
 *
 * @param  string  $str      The string to check.
 * @param  string  $needle   he string, where $str must contains
 * @param  boolean $caseless Ignore the case? (defaults to FALSE)
 * @param  string  $charset  Encoding of the string (defaults to 'UTF-8')
 * @return boolean Returns TRUE on success, FALSE otherwise.
 * @uses   Multibyte extension The function requires that PHP have the Multibyte extension mb_string enabled.
 * @uses   \Beluga\strPos
 * @since  v0.1
 */
function strContains( string $str, string $needle, bool $caseless = false, string $charset = 'UTF-8' ) : bool
{

   return ( false !== strPos( $str, $needle, $caseless, $charset ) );

}

/**
 * Escapes a string for use as a XML element value. The characters &amp;&lt;&gt; will be converted to entities.
 *
 * @param  string $str The string
 * @return string
 * @since  v0.1
 */
function escapeXML( string $str ) : string
{

   return \str_replace(
      [ '&',     '<',    '>' ],
      [ '&amp;', '&lt;', '&gt;' ],
      $str
   );

 }

/**
 * Escapes a string for use as XML element attribute value. The chars &amp;&lt;&gt;" will be converted to entities.
 *
 * @param  string $str The string
 * @return string
 * @since  v0.1
 */
function escapeXMLArg( string $str ) : string
{

   return \str_replace(
      [ '&',     '<',    '>',    '"',      "'"  ],
      [ '&amp;', '&lt;', '&gt;', '&quot;', '&#39;' ],
      $str
   );

 }

/**
 * Escapes the defined value, depending to the defined type.
 *
 * @param  mixed  $str  The value to escape
 * @param  string $type The escaping type (One of the ESCAPE_* constants.
 * @return string
 * @uses   \Beluga\escapeXML
 * @uses   \url_encode
 * @uses   \json_encode
 * @uses   \Beluga\escapeXMLArg
 * @since  v0.1
 */
function escape( string $str, $type = ESCAPE_HTML_ALL ) : string
{

   switch ( $type )
   {

      case ESCAPE_HTML:
         return escapeXML( $str );

      case ESCAPE_URL:
         return \urlencode( $str );

      case ESCAPE_JSON:
         return \json_encode( $str );

      default:
         return escapeXMLArg( $str );

   }

}

/**
 * Unescapes (HT|X)ML entities in a string.
 *
 * @param  string  $str  The string
 * @param  boolean $full Unescape all entities?
 * @return string
 * @since  v0.1
 */
function unescapeXML( string $str, bool $full = false ) : string
{

   // This strings will be replaced (if $full is TRUE it are regular expressions)
   $search = ! $full
      ? [
         '&auml;', '&Auml;', '&ouml;', '&Ouml;', '&uuml;', '&Uuml;',
         '&lt;', '&gt;', '&quot;', '&#39;', '&amp;', '&deg;', '&szlig;'
      ]
      : [
         '~&(quot|#34|#034|#x22);~i',
         '~&(amp|#38|#038|#x26);~i',
         '~&(lt|#60|#060|#x3c);~i',
         '~&(gt|#62|#062|#x3e);~i',
         '~&(nbsp|#160|#xa0);~i',
         '~&(iexcl|#161);~i',
         '~&(cent|#162);~i',
         '~&(pound|#163);~i',
         '~&(copy|#169);~i',
         '~&(reg|#174);~i',
         '~&(deg|#176);~i',
         '~&(#39|#039|#x27);~',
         '~&(euro|#8364);~i',
         '~&a(uml|UML);~',
         '~&o(uml|UML);~',
         '~&u(uml|UML);~',
         '~&A(uml|UML);~',
         '~&O(uml|UML);~',
         '~&U(uml|UML);~',
         '~&szlig;~i',
         '~&#(\d{1,4});~e'
      ];

   // The replacements depending to $full
   $replace = ! $full
      ? [
         'ä', 'Ä', 'ö', 'Ö', 'ü', 'Ü', '<', '>', '"', "'", '&', '°', 'ß'
      ]
      : [
         '"', '&', '<', '>', ' ', \chr( 161 ), \chr( 162 ), \chr( 163 ), '©', '®', '°', \chr( 39 ), '€',
         'ä', 'ö', 'ü', 'Ä', 'Ö', 'Ü', 'ß', 'chr($1)'
      ];

   return ! $full
      ? \str_replace ( $search, $replace, $str )
      : \preg_replace( $search, $replace, $str );

}

/**
 * Trim the string $str to a max. length and appends, if trimming is required, a $appendix.
 *
 * <code>
 * // Example
 *
 * $str = 'My name is Max';
 * $newStr = \Beluga\strMax( $str, 11, 'Art' );
 * echo "'{$newStr}'";
 *
 * // Results in output: 'My name is Art'
 * </code>
 *
 * @param  string  $str       The string.
 * @param  integer $maxLength The resulting string max. length (incl. $appendix)
 * @param  string  $appendix  Optional appendix (Defaults to '…')
 * @param  string  $charset   Encoding of the string (defaults to 'UTF-8')
 * @return string
 * @uses   \Beluga\strLen
 * @uses   \Beluga\substr
 * @since  v0.1
 */
function strMax( string $str, int $maxLength, string $appendix = '…', string $charset = 'UTF-8' ) : string
{

   // If no trimming is required return the origin string
   if ( strLen( $str, $charset ) <= $maxLength )
   {
      return $str;
   }

   return \Beluga\substr( $str, 0, $maxLength - strLen( $appendix, $charset ) ) . $appendix;

 }

/**
 * str_replace with a caseless string handling.
 *
 * @param  string|array $search     What will be replaced?
 * @param  string       $replace    The Replacement
 * @param  string       $subject    The String to work with
 * @param  boolean      $useUnicode Use Unicode for internal regex? (defaults to TRUE)
 * @return string
 * @since  v0.1
 */
function strIReplace( string $search, string $replace, string $subject, bool $useUnicode = true ) : string
{

   // Make sure $search is a array
   if ( ! \is_array( $search ) )
   {
      $search  = array( $search );
   }

   // Quote a search strings for use inside a regular expression
   for ( $i = 0, $j = \count( $search ); $i < $j; $i++ )
   {
      $search[$i] = \preg_quote( $search[ $i ] );
   }

   // Build the regular expression
   $search = '~(' . \join( '|', $search ) . ')~i' . ( $useUnicode ? 'u' : '' );

   return \preg_replace( $search, $replace, $subject );

}

/**
 * Removes all HTML/XML Markup from defined string.
 *
 * @param  string $str
 * @return string
 * @since  v0.1
 */
function stripTags( string $str ) : string
{

   $search = [
      '~<' . 'script[^>]*?>.*?</script>~siu',
      '~<' . 'style[^>]*?>.*?</style>~siu',
      '~<![\s\S]*?--[ \t\n\r]*>~u'
   ];

   return \strip_tags( \preg_replace( $search, '', $str ) );

}

/**
 * Does a normal print_r but outputs inside &lt;pre&gt; Elements with a optional class-Attribute.
 *
 * @param  mixed  $value    The value to print out
 * @param  string $preClass Optional class attribute value of generated pre HTML element
 * @return string
 * @uses   \Beluga\mask_xml
 * @since  v0.1
 */
function print_h( string $value, string $preClass = null )
{

   echo '<pre',
      ( ! empty( $preClass ) ? " class=\"{$preClass}\">" : '>' ),
      escapeXML( \print_r( $value, true ) ),
      '</pre>';

}

/**
 * Explodes the defined string, at every line break (\r\n|\n|\r)
 *
 * @param  string $string
 * @return array
 * @since  v0.1
 */
function splitLines( string $string ) : array
{

   return \preg_split( "~(\r\n|\n|\r)~", $string );

}

/**
 * Converts a string from any of various encodings to UTF-8.
 *
 * @param  string  $string   The string to encode.
 * @param  string  $encoding The encoding of the string (default='ISO-8859-1')
 * @param  boolean $safeMode If the safe mode is set to TRUE, the original string is retunred on errors.
 * @return string|FALSE Returns the encoded string, or FALSE on error
 * @since  v0.1
 */
function utf8Encode( string $string = '', string $encoding = 'iso-8859-1', bool $safeMode = false ) : string
{

   // Remember the string if we are in safe mode
   $safe = ( $safeMode ) ? $string : false;

   $encodingUpper = \strtoupper( $encoding );

   if ( $encodingUpper == 'UTF-8'
     || $encodingUpper == 'UTF8' )
   {
      return $string;
   }
   if ( $encodingUpper == 'ISO-8859-1' )
   {
      return \utf8_encode( $string );
   }
   if ( $encodingUpper == 'WINDOWS-1252' )
   {
      return \utf8_encode( map_w1252_iso8859_1( $string ) );
   }

   if ( $encodingUpper == 'UNICODE-1-1-UTF-7' )
   {
      $encodingUpper = 'UTF-7';
   }

   if ( \function_exists( '\\mb_convert_encoding' ) )
   {
      try
      {
         $conv = \mb_convert_encoding( $string, 'UTF-8', $encodingUpper );
         if ( $conv )
         {
            return $conv;
         }
      }
      catch ( \Exception $ex ) { $ex = null; }
   }

   if ( \function_exists( '\\iconv' ) )
   {
      try
      {
         $conv = \iconv( $encodingUpper, 'UTF-8', $string );
         if ( $conv )
         {
            return $conv;
         }
      }
      catch ( \Exception $ex ) { $ex = null; }
   }

   return $safe;

}

/**
 * Converts a string from UTF-8 to any of various encodings
 *
 * @param  string  $string   The string to decode
 * @param  string  $encoding The target encoding. (default='ISO-8859-1')
 * @param  boolean $safeMode If set to TRUE, the original string is returned on errors
 * @return string  Returns the decoded string or FALSE on error.
 * @since  v0.1
 */
function utf8Decode( string $string = '', string $encoding = 'iso-8859-1', bool $safeMode = false ) : string
{

   // Remember the string if we are in safe mode
   $safe = ( $safeMode ) ? $string : false;

   // Use default encoding if none is defined
   if ( empty( $encoding ) )
   {
      $encoding = 'ISO-8859-1';
   }

   $encodingUpper = \strtoupper( $encoding );

   if ( $encodingUpper == 'UTF-8'
     || $encodingUpper == 'UTF8' )
   {
      return $string;
   }

   if ( $encodingUpper == 'ISO-8859-1' )
   {
      return \utf8_decode( $string );
   }

   if ( $encodingUpper == 'WINDOWS-1252' )
   {
      return map_iso8859_1_w1252( \utf8_decode( $string ) );
   }

   if ( $encodingUpper == 'UNICODE-1-1-UTF-7' )
   {
      $encodingUpper = 'UTF-7';
   }

   if ( \function_exists( '\\mb_convert_encoding' ) )
   {
      try
      {
         $conv = \mb_convert_encoding( $string, $encodingUpper, 'UTF-8' );
         if ( $conv )
         {
            return $conv;
         }
      }
      catch ( \Throwable $ex ) { $ex = null; }
   }

   if ( \function_exists( '\\iconv' ) )
   {
      try
      {
         $conv = \iconv('UTF-8', $encodingUpper, $string );
         if ( $conv )
         {
            return $conv;
         }
      }
      catch ( \Throwable $ex ) { $ex = null; }
   }

   return $safe;

}

/**
 * Special treatment for our guys in Redmond,
 * Windows-1252 is basically ISO-8859-1 -- with some exceptions, which get accounted for here.
 *
 * @param  string $string Your input in Win1252
 * @return string         The resulting ISO-8859-1 string
 * @since  v0.1
 */
function map_w1252_iso8859_1( string $string = '' ) : string
{
   if ( $string == '' )
   {
      return '';
   }

   $return = '';

   for ( $i = 0; $i < \strlen( $string ); ++$i )
   {
      $c = \ord( $string[ $i ] );
      switch ( $c )
      {
         case 129:
            $return .= \chr( 252 );
            break;
         case 132:
            $return .= \chr( 228 );
            break;
         case 142:
            $return .= \chr( 196 );
            break;
         case 148:
            $return .= \chr( 246 );
            break;
         case 153:
            $return .= \chr( 214 );
            break;
         case 154:
            $return .= \chr( 220 );
            break;
         case 225:
            $return .= \chr( 223 );
            break;
         default:
            $return .= \chr( $c  );
            break;
      }
   }

   return $return;

}

/**
 * Special treatment for our guys in Redmond.
 * Windows-1252 is basically ISO-8859-1 -- with some exceptions, which get accounted for here.
 *
 * @param  string $string Your input in ISO-8859-1
 * @return string         The resulting Win1252 string
 * @since  v0.1
 */
function map_iso8859_1_w1252( string $string = '' ) : string
{

   if ( $string == '' )
   {
      return '';
   }

   $return = '';

   for ( $i = 0; $i < \strlen( $string ); ++$i )
   {
      $c = \ord( $string[ $i ] );
      switch ( $c )
      {
         case 196:
            $return .= \chr( 142 );
            break;
         case 214:
            $return .= \chr( 153 );
            break;
         case 220:
            $return .= \chr( 154 );
            break;
         case 223:
            $return .= \chr( 225 );
            break;
         case 228:
            $return .= \chr( 132 );
            break;
         case 246:
            $return .= \chr( 148 );
            break;
         case 252:
            $return .= \chr( 129 );
            break;
         default:
            $return .= \chr( $c );
            break;
      }
   }

   return $return;

}

/**
 * This function is an extended {@see preg_match()} with the ability to use a callback function for detailed checks
 * of regexp parts/matches.
 *
 * @param  string   $pattern  The PHP typical PCRE regular expression that does the main check.
 * @param  string   $subject  The string that should be checked
 * @param  callable $callback The callback function for doing special checks with the matching groups of $patter.
 *                            Function signature must be "boolean function( array $matches )"
 * @param  array    $matches  It returns the resulting matches if the $pattern hits successful the $subject
 * @param  integer  $flags    can be the following flag:
 *                            PREG_OFFSET_CAPTURE: If this flag is passed, for every occurring match the appended
 *                            string offset will also be returned. Note that this changes the value of matches into an
 *                            array where every element is an array consisting of the matched string at offset 0 and
 *                            its string offset into subject at offset 1.
 * @param  integer $offset    Normally, the search starts from the beginning of the subject string. The optional
 *                            parameter offset can be used to specify the alternate place from which to start the
 *                            search (in bytes).
 * @return boolean            Returns if the $pattern and $callback matches successful for $subject.
 */
function preg_match_callback( string $pattern, string $subject, callable $callback, &$matches = null, $flags = 0, $offset = 0 )
{

   if ( !\preg_match( $pattern, "$subject", $matches, $flags, $offset ) )
   {
      // The base regexp does not match, we are done here...
      return false;
   }

   if ( \is_callable( $callback ) )
   {
      // $callback is callable, so we can use it (returns boolean)
      // and it becomes the $matches array from preg_match() as parameter
      return \call_user_func( $callback, $matches );
   }

   // Only a successful regexp check
   return true;

}

/**
 * Decodes from JSON and JSONP format.
 *
 * @param  string  $json  The JSON or JSONP string
 * @param  boolean $assoc
 * @return array|object
 */
function jsonDecode( string $json, bool $assoc = false )
{

   if ( '[' !== $json[ 0 ]
     && '{' !== $json[ 0 ] )
   {
      // we have JSONP: Remove the defined function call
      $json = \substr( $json, \strpos( $json, '(' ) );
   }

   return \json_decode( \trim( $json,'();' ), $assoc );

}

