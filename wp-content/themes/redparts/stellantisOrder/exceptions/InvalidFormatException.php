<?php
/**
 * Format de données Exception
 */
class InvalidFormatException extends Exception {
  function __construct(string $message = '', int $code = 0 , \Throwable $previous = null)
  {
    parent::__construct($message, $code , $previous);
    if(empty($this->message)) {
      $this->message = 'Invalid format';
    }    
    $this->code = 500;
    
  }
}