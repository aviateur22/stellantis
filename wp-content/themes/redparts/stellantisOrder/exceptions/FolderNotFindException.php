<?php
/**
 * Exception Dossier pas trouvé
 */
class FolderNotFindException extends Exception {
  function __construct(string $message = '', int $code = 0 , \Throwable $previous = null)
  {
    parent::__construct($message, $code , $previous);
    $this->message = 'Le dossier n\'est pas trouvé';
    $this->code = 500;
    
  }
}