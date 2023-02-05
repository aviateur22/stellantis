<?php
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/interfaces/MailServiceInterface.php';
require_once('/home/mdwfrkglvc/www/wp-config.php');

/**
 * Envoie Email
 */
class MailService implements MailServiceInterface {
  

  /**
   * From
   *
   * @var string
   */
  protected string $from = 'From: Plateforme t-dm admin@t-dm.fr';

  /**
   * Envoie d'un message
   *
   * @param string $to
   * @param string $subject
   * @param string $message
   * @return void
   */
  function sendMessage(string $to, string $subject , string $message): void {    
    wp_mail($to, $subject, $message, $this->headers());
  }

  /**
   * Préparation des header d'envoie
   *
   * @return array
   */
  private function headers(): array {
    $headers [] = 'From: Plateforme t-dm admin@t-dm.fr';
    return $headers;
  }
}