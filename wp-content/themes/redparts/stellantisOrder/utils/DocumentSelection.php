<?php
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/utils/StaticData.php';
require_once ('/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/model/RepositoriesModel.php');
require_once ('/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/services/MySqlOrderRepository.php');
require_once ('/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/services/MySqlForecastRepository.php');
require_once ('/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/services/MySqlModelRepository.php');
require_once ('/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/services/MySqlDocPDFRepository.php');
require_once ('/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/services/MySqlDocumentationOrderRepository.php');

/**
 * Selection des Documents 
 */
class DocumentSelection {
  /**
   * Type de repository
   *
   * @var string
   */
  protected string $documentType;

  function __construct(string $documentType) {
    $this->documentType = $documentType;
  }

  /**
   * Selection des repositories
   *
   * @return RepositoriesModel
   */
  function selectOrderSource(): void {
    switch ($this->documentType) {
      case StaticData::DOCUEMENT_TYPE_XLS:
        break;
      default: throw new \Exception('Not a valid document Type'); break;
    }
  }
}