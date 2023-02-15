<?php

/**
 * Interface pour envoie Email
 */
interface MailServiceInterface {
  
  /**
   * Envoie d'un message
   *
   * @param string $to
   * @param string $subject
   * @param string $message
   * @return void
   */
  function sendMessage(string $to, string $subject , string $message): void;
}