<?php
/**
 * Exception Module PHPExcel
 */
class PhpExcelException extends Exception {
  function __construct(string $message = '', int $code = 0 , \Throwable $previous = null)
  {
    parent::__construct($message, $code , $previous);
    $this->message = 'Fichier PHPExcel manquant';
    $this->code = 500;    
  }
}