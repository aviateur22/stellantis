<?php
/**
 * Format de données Exception
 */
class InvalidFormatException extends Exception {
  function __construct(string $message = '', int $code = 0 , \Throwable $previous = null)
  {
    parent::__construct($message, $code , $previous);
    $this->message = 'Format de données invalide';
    $this->code = 500;
    
  }
}