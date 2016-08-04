<?php
/**
 * In this file the exception class '\Beluga\MissingExtensionError' is defined.
 *
 * @author         SagittariusX <unikado+sag@gmail.com>
 * @copyright  (c) 2016, UniKado
 * @package        Beluga\Core
 * @since          2016-07-28
 * @subpackage     Errors
 * @version        0.1.0
 */


declare( strict_types = 1 );


namespace Beluga;


/**
 * This exception should be thrown, if a required PHP extension is not loaded.
 *
 * The class extends from {@see \Beluga\BelugaError}
 *
 * @since  v0.1
 */
class MissingExtensionError extends BelugaError
{


   # <editor-fold desc="= = =   P R I V A T E   F I E L D S   = = = = = = = = = = = = = = = = = = = = = = = = =">

   /**
    * The name of the missed PHP extension.
    *
    * @var string
    */
   private $extensionName;

   # </editor-fold>


   # <editor-fold desc="= = =   C O N S T R U C T O R   +   D E S T R U C T O R   = = = = = = = = = = = = = = =">

   /**
    * Init's a new instance.
    *
    * @param string     $extensionName  The name of the missing extension
    * @param string     $package        The optional Beluga package name
    * @param string     $message        The error message (default=null)
    * @param mixed      $code           The error code (default=\E_USER_ERROR)
    * @param \Throwable $previous       A optional previous exception
    */
   public function __construct(
      string $extensionName, string $package, string $message = null, $code = 0, \Throwable $previous = null )
   {

      parent::__construct(
         $package,
         \sprintf( 'PHP extension "%s" is not loaded with current PHP config.', $extensionName )
            . static::appendMessage( $message ),
         $code,
         $previous
      );

      $this->extensionName = $extensionName;

   }

   # </editor-fold>


   # <editor-fold desc="= = =   G E T T E R S   = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =">

   /**
    * Returns the name of the missed extension.
    *
    * @return string
    */
   final public function getExtensionName() : string
   {

      return $this->extensionName;

   }

   # </editor-fold>


}

