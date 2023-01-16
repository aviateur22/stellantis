<?php
/**
 * Exception format de données invalide
 */
class FileNotFindException extends Exception {
  function __construct(string $message = '', int $code = 0 , \Throwable $previous = null)
  {
    parent::__construct($message, $code , $previous);    
    $this->code = 500;
    $this->setExceptionMessage();
  }

  function setExceptionMessage() {
    if(empty($this->message)) {
      $this->message = 'Fichier inconnu';
    }
  }
}