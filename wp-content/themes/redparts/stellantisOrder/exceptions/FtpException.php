<?php
/**
 * Exception format de données invalide
 */
class FtpException extends Exception {
  function __construct(string $message = '', int $code = 0 , \Throwable $previous = null)
  {
    parent::__construct($message, $code , $previous);
    $this->message = 'Connexion FTP impossible';
    $this->code = 500;
    
  }
}