<?php
/**
 * @author         SagittariusX <unikado+sag@gmail.com>
 * @copyright  (c) 2016, UniKado
 * @package        Beluga\Core
 * @since          2016-07-31
 * @subpackage     Errors
 * @version        0.1.0
 */


declare( strict_types = 1 );


namespace Beluga;


/**
 * This is the Beluga library wide base exception.
 *
 * @since v0.1.0
 */
class BelugaError extends \Exception
{


   // <editor-fold desc="// = = = =   P R O T E C T E D   F I E L D S   = = = = = = = = = = = = = = = = = = = = = = =">

   /**
    * The optional Beluga package name.
    *
    * @type string
    */
   protected $package;

   // </editor-fold>


   // <editor-fold desc="// = = = =   P U B L I C   C O N S T R U C T O R   = = = = = = = = = = = = = = = = = = = = =">

   /**
    * Init a new BelugaError instance.
    *
    * @param string          $package  The optional Beluga package name.
    * @param string          $message  The optional error message.
    * @param int             $code     The optional error code.
    * @param \Throwable|null $previous Optional previous throwable.
    */
   public function __construct ( string $package, string $message = '', $code = 0, \Throwable $previous = null )
   {

      $msgPrefix = empty( $package ) ? '' : ( 'Beluga package "' . $package . '" error.' );

      parent::__construct(
         $msgPrefix . static::appendMessage( $message ),
         $code,
         $previous
      );

      $this->package  = $package;
      $trace    = \debug_backtrace();
      print_r( $trace );
      exit;

   }

   // </editor-fold>


   // <editor-fold desc="// = = = =   P U B L I C   M E T H O D S   = = = = = = = = = = = = = = = = = = = = = = = = =">

   /**
    * Overrides __toString, to return a more detailed string, when object is casted to a string.
    *
    * @return string
    */
   public function __toString() : string
   {

      return $this->toCustomString();

   }

   /**
    * Gets the name of the package that throws the error
    *
    * @return string
    */
   public final function getPackage() : string
   {

      return $this->package;

   }

   /**
    * Gets if an previous throwable is defined.
    *
    * @return bool
    */
   public final function hasPrevious() : bool
   {

      return ! \is_null( $this->getPrevious() );

   }

   /**
    * Gets if this error was thrown in the context of an specific class
    *
    * @return bool
    */
   public final function isClassContext() : bool
   {

      return ! empty( $this->class );

   }

   /**
    * Extends the origin getMessage method, so also previous messages are include, if defined.
    *
    * @param  bool $appendPreviousByNewline If a prev. Exception is defined append it by a new line? (' ' other)
    * @return string
    */
   public function getErrorMessage( bool $appendPreviousByNewline = false ) : string
   {

      // Getting a optional previous exception
      $prev = $this->getPrevious();

      if ( \is_null( $prev ) )
      {
         // If no previous exception is used
         return \sprintf(
            '%s(%d): %s',
            static::GetCodeName( $this->getCode() ),
            $this->getCode(),
            $this->getMessage()
         );
      }

      // Define the separator between current and previous exception.
      $separator = $appendPreviousByNewline ? "\n" : ' ';

      if ( ( $prev instanceof BelugaError ) )
      {
         return \sprintf(
            '%s(%d): %s%s%s',
            static::GetCodeName( $this->getCode() ),
            $this->getCode(),
            $this->getMessage(),
            $separator,
            $prev->getErrorMessage( $appendPreviousByNewline )
         );
      }

      return \sprintf(
         '%s(%d): %s%s%s',
         static::GetCodeName( $this->getCode() ),
         $this->getCode(),
         $this->getMessage(),
         $separator,
         $prev->getMessage()
      );

   }

   /**
    * Allows the definition of the sub exception level if there is a parent exception that contains this exception.
    *
    * @param  int     $subExceptionLevel
    * @param  string  $indentSpaces      Spaces to use for a single indention level.
    * @return string
    */
   public function toCustomString( int $subExceptionLevel = 0, string $indentSpaces = '   ' ) : string
   {

      // Concatenate the base error message from usable elements
      $msg = \sprintf(
         "%s%s in %s[%d]. %s",
         \str_repeat( $indentSpaces, $subExceptionLevel ),
         \get_class( $this ),
         $this->file,
         $this->line,
         \str_replace( "\n", "\n" . \str_repeat( $indentSpaces, $subExceptionLevel ), $this->message )
      );

      // getting a may defined previous exception
      $previous = $this->getPrevious();

      // if no previous exception is defined return the current generated message
      if ( \is_null( $previous ) || ! ( $previous instanceof \Throwable ) )
      {
         return $msg;
      }

      // If previous message is a framework internal exception
      if ( $previous instanceof BelugaError )
      {

         // Simple cast the exception to a string and append it with rewrite the indention
         $msg .= "\n" . $previous->toCustomString( $subExceptionLevel + 1, $indentSpaces );

         // And return the message
         return $msg;

      }

      // Else its a normal PHP exception

      // Concatenate the previous error message from usable elements
      $msg .= \sprintf(
         "\n%s%s %s in %s[%d]\n    %s",
         \str_repeat( $indentSpaces, $subExceptionLevel + 1 ),
         \get_class( $previous ),
         static::GetCodeName( $previous->getCode() ),
         $previous->file,
         $previous->line,
         \str_replace( "\n", "\n" . \str_repeat( $indentSpaces, $subExceptionLevel + 1 ), $previous->message )
      );

      // And return the message
      return $msg;

   }

   // </editor-fold>


   // <editor-fold desc="// = = = =   P U B L I C   S T A T I C   M E T H O D S   = = = = = = = = = = = = = = = = = =">

   /**
    * Returns a string, representing the defined error code.
    *
    * @param  int|string $code e.g.: \E_USER_ERROR
    * @return string
    */
   public static function GetCodeName( $code ) : string
   {

      switch ( $code )
      {

         case \E_ERROR:
         case \E_USER_ERROR:
            return 'ERROR';

         case \E_WARNING:
         case \E_USER_WARNING:
            return 'WARNING';

         case \E_DEPRECATED:
         case \E_USER_DEPRECATED:
            return 'DEPRECATED';

         case \E_NOTICE:
         case \E_USER_NOTICE:
            return 'NOTICE';

         case \E_PARSE:
            return 'PARSE';
         case \E_RECOVERABLE_ERROR:
            return 'RECOVERABLE ERROR';

         case \E_STRICT:
            return 'STRICT';

         default:
            if ( \is_string( $code ) )
            {
               return $code;
            }
            return 'OTHER';

      }

   }

   // </editor-fold>


   // <editor-fold desc="// = = = =   P R O T E C T E D   S T A T I C   M E T H O D S   = = = = = = = = = = = = = = =">

   /**
    * Appends a message, if its not empty, separated by ' '.
    *
    * @param  string $message
    * @return string
    */
   protected static function appendMessage( $message ) : string
   {

      return empty( $message ) ? '' : ( ' ' . $message );

   }

   # </editor-fold>


}

