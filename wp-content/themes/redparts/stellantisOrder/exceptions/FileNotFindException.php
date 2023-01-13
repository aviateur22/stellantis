<?php
/**
 * Exception format de données invalide
 */
class FileNotFindException extends Exception {
  function __construct(string $message = '', int $code = 0 , \Throwable $previous = null)
  {
    parent::__construct($message, $code , $previous);
    $this->message = 'Fichier inconnu';
    $this->code = 500;
    
  }
}