<?php
/**
 * Exception si action interdite
 */
class ForbiddenException extends Exception {
  function __construct(string $message = '', int $code = 0 , \Throwable $previous = null)
  {
    parent::__construct($message, $code , $previous);    
    $this->code = 403;
    $this->setExceptionMessage();
  }

  function setExceptionMessage() {
    if(empty($this->message)) {
      $this->message = 'Action interdite';
    }
  }
}