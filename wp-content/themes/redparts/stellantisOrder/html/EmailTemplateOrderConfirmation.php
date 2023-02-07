<?php

class EmailTemplateOrderConfirmation {
  
  /**
   * Template pour confirmation des commandes
   *
   * @param string $message - Message de la commande
   * @return string
   */
  static function getTemplate(string $message): string {
    return '<!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">    

        <title> New Order </title>

    </head>
    <body>
      <header>
        <p>
          Bonjour
        </p>

        <p>
          Une nouvelle commande vient d\’être déposée sur la plateforme STELLANTIS
        </p>        
      </header>
      <div>
          <main style="box-sizing: border-box;  display: flex; width: 100%; box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px;">
          '.$message.'
          </main>
      </div>
    </body>
    <footer>
      <div style="display: flex;flex-direction: column;  justify-content: center; background-color:rgb(100, 100, 100) ; padding: 10px">          
          <p style="text-align: center; padding: 5px; background-color:rgb(100, 100, 100); color: white;">
            Mail diffusé automatiquement par la plateforme STELLANTIS. Ne pas répondre.
          </p>      
      </div>
    </footer>';
  }
}