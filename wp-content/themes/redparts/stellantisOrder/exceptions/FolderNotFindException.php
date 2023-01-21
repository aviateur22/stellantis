<?php
/**
 * Exception Dossier pas trouvé
 */
class FolderNotFindException extends Exception {
  function __construct(string $message = '', int $code = 0 , \Throwable $previous = null)
  {
    parent::__construct($message, $code , $previous);
    $this->message = 'Directory not find';
    $this->code = 500;
    
  }
}