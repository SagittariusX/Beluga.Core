<?php
/**
 * In this file the exception class '\Beluga\XmlAttributeHelper' is defined.
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
 * This class "XmlAttributeHelper" defines a object that have some static methods for handling XML attributes
 *
 * @since v0.1
 */
class XmlAttributeHelper
{

   /**
    * Returns the XML attribute value of the defined XML element, or $defaultValue if the attribute not exists.
    *
    * @param  \SimpleXMLElement $element       The XML element
    * @param  string            $attributeName The name of the required XML attribute
    * @param  mixed             $defaultValue  The default value, returned if the attribute not exists.
    * @return string
    */
   public static function GetAttributeValue(
      \SimpleXMLElement $element, string $attributeName, $defaultValue = false ) : string
   {

      if ( isset( $element[ $attributeName ] ) )
      {
         return (string) $element[ $attributeName ];
      }

      if ( isset( $element[ 'attributes' ][ $attributeName ] ) )
      {
         return (string) $element[ 'attributes' ][ $attributeName ];
      }

      if ( isset( $element[ '@attributes' ][ $attributeName ] ) )
      {
         return (string) $element[ '@attributes' ][ $attributeName ];
      }

      $attr = $element->attributes();

      if ( isset( $attr[ $attributeName ] ) )
      {
         return (string) $attr[ $attributeName ];
      }

      return $defaultValue;

   }

}

