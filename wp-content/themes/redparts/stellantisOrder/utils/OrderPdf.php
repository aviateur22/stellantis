<?php
require_once ('/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/model/OrderEntity.php');

class OrderPdf {

  /**
   * Renvoie les liens PDF
   * @param array $order
   * @return array
   */
  function getPdfLink($order): array {
    // Type de manuel à imprimer
    $pdfTypes = [];

    $documentations = $order['documentationPDFInformations'];

    // Pas de données
    if(count($documentations) === 0) {
      $pdfTypes[] = 'documentationUndefined';
      return $pdfTypes;
    }

    // Parcours des documentations
    foreach($documentations as $documentation) {
      switch(count($documentation['PdfDetail'])) {
        case 0: 
          $pdfTypes[] = 'intDocumentationUndefined';
          $pdfTypes[] = 'couvDocumentationUndefined';  
          break;
        case 1:
          if(trim(strtolower($documentation['intOrCouv'])) === 'int') {
            $pdfTypes[] = $documentation['type'] .'_'. $documentation['intOrCouv'] .'_'. $documentation['link'];
            $pdfTypes[] = 'couvDocumentationUndefined';

          } elseif(trim(strtolower($documentation['intOrCouv'])) === 'couv') {
            $pdfTypes[] = $documentation['type'] .'_'. $documentation['intOrCouv'] .'_'. $documentation['link'];
            $pdfTypes[] = 'intDocumentationUndefined';

          } else {
            $pdfTypes[] = $documentation['type'] .'_'. $documentation['intOrCouv'] .'_'. $documentation['link'];
            $pdfTypes[] = $documentation['type'] .'_'. $documentation['intOrCouv'] .'_'. $documentation['link'];
          }      
          break;
        case 2: 
          foreach($documentation['PdfDetail'] as $pdf) {
            $pdfTypes[] = $pdf['type'] .'_'. $pdf['intOrCouv'] .'_'. $pdf['link'];
          }
          break;
        default:
          foreach($documentation['PdfDetail'] as $pdf) {
            $pdfTypes[] = $pdf['type'] .'_'. $pdf['intOrCouv'] .'_'. $pdf['link'];
          } 
        break;
      }
    }
    return $pdfTypes;    
  }

  
  /**
   * Renvoie les liens PDF pour les mail
   *
   * @param [type] $order
   * @return array
   */
  function getPdfLinkForMail(array $order): array {
    // Type de manuel à imprimer
    $pdfTypes = [];

    $documentations = $order['documentationPDFInformations'];

    // Pas de données
    if(count($documentations) === 0) {
      $pdfTypes[] = 'Documentation Undefined';
      return $pdfTypes;
    }

    // Parcours des documentations
    foreach($documentations as $documentation) {
      $pdfTypes[] = $documentation['documentationDetail']['type'];
    }

    return $pdfTypes;
  }


  /**
   * Renvoi lien doc pour génération d'un fichier XLS
   *
   * @return array
   */
  function getPdfLinkForXls(array $order): array {
    // Type de manuel à imprimer
    $pdfTypes = [];

    $documentations = $order['documentationPDFInformations'];

    // Pas de données
    if(count($documentations) === 0) {
      $pdfTypes[] = [
        'fileName' => 'Documentation Undefined',
        'fileType' => 'Type Undefined'
      ];
      ;
      return $pdfTypes;
    }

    // Parcours des documentations
    foreach($documentations as $documentation) {
      $pdfTypes[] = [
        'fileName' =>$documentation['documentationDetail']['docRef'],
        'fileType' =>$documentation['documentationDetail']['type']
      ];
    }

    return $pdfTypes;
  }

  function formatText() {

  }
}