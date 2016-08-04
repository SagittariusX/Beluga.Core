<?php
/**
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
 * The Beluga\ArgumentException class.
 *
 * @since v0.1.2
 */
class ArgumentError extends BelugaError
{


   // <editor-fold desc="// = = = =   P R O T E C T E D   F I E L D S   = = = = = = = = = = = = = = = = = = = = = = =">

   /**
    * The argument name.
    *
    * @var string
    */
   protected $argumentName;

   /**
    * The function or method name.
    *
    * @var string
    */
   protected $functionName;

   // </editor-fold>


   // <editor-fold desc="// = = = =   P U B L I C   C O N S T R U C T O R   = = = = = = = = = = = = = = = = = = = = =">

   /**
    * Init's a new instance.
    *
    * @param string     $arg      The argument/parameter name
    * @param mixed      $value    The argument/parameter value
    * @param string     $package  The optional Beluga package name
    * @param string     $message  The error message (default=null)
    * @param int        $code     The error code (default=\E_USER_ERROR)
    * @param \Throwable $previous A optional previous exception
    */
   public function __construct(
      string $arg, $value, string $package, string $message = null, $code = \E_USER_ERROR, \Throwable $previous = null )
   {

      // Getting the debug backtrace to find out the method/function that is called with an bad argument.
      $trace = \debug_backtrace();
      // Getting the index of the last trace element.
      $lIdx  = \count( $trace ) - 1;

      // Getting the method or function name.
      $fn = ( empty( $trace[ $lIdx ][ 'class' ] )    ? '' : $trace[ $lIdx ][ 'class' ] )
          . ( empty( $trace[ $lIdx ][ 'type' ] )     ? '' : $trace[ $lIdx ][ 'type' ] )
          . ( empty( $trace[ $lIdx ][ 'function' ] ) ? '' : $trace[ $lIdx ][ 'function' ] );

      // Init with parent constructor
      parent::__construct(
         $package,
         'Argument $' . \ltrim( $arg, '$' )
         .  ( empty( $fn ) ? '' : ' of ' . $fn . '(…)' )
         .  ' is Invalid! It uses a value of type '
         .  static::GetTypeStr( $value )
         .  static::appendMessage( $message ),
         $code,
         $previous
      );

      // Remember the parameter name and the function/method name
      $this->argumentName = $arg;
      $this->functionName = $fn;

   }

   // </editor-fold>


   // <editor-fold desc="// - - -   G E T T E R   - - - - - - - - - - - - - - - - - - - - - -">

   /**
    * Returns the name of the error argument/parameter.
    *
    * @return string
    */
   public final function getArgumentName() : string
   {
      return $this->argumentName;
   }

   /**
    * Returns the value of the error argument/parameter.
    *
    * @return mixed
    */
   public final function getFunctionName() : string
   {
      return $this->functionName;
   }

   // </editor-fold>


   // <editor-fold desc="// = = = =   P R O T E C T E D   S T A T I C   M E T H O D S   = = = = = = = = = = = = = = =">

   /**
    * Returns a string, representing the permitted value.
    *
    * @param  mixed $value
    * @return string
    */
   protected static function GetTypeStr( $value ) : string
   {

      if ( \is_null( $value ) )
      {
         return 'NULL';
      }

      if ( \is_resource( $value ) )
      {
         return \get_resource_type( $value ) . '-Resource';
      }

      if ( \is_string( $value ) )
      {
         if ( \strlen( $value ) > 128 )
         {
            return 'string with value (' . \substr( $value, 0, 126 ) . '…)';
         }
         return 'string with value (' . $value . ')';
      }

      if ( \is_bool( $value ) )
      {
         return 'boolean with value (' . ( $value ? 'true' : 'false' ) . ')';
      }

      if ( \is_int( $value ) )
      {
         return 'integer with value (' . $value . ')';
      }

      if ( \is_float( $value ) )
      {
         return 'float with value (' . $value . ')';
      }

      if ( \is_double( $value ) )
      {
         return 'double with value (' . $value . ')';
      }

      if ( \is_array( $value ) )
      {
         return 'Array: ' . \json_encode( $value );
      }

      if ( \is_object( $value ) )
      {
         return \get_class( $value ) . ' object: ' . \json_encode( $value );
      }

      return \gettype( $value );

   }

   // </editor-fold>


}

