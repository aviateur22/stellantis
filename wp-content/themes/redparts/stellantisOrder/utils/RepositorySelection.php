<?php
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/utils/StaticData.php';
require_once ('/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/model/RepositoriesModel.php');
require_once ('/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/services/MySqlOrderRepository.php');
require_once ('/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/services/MySqlForecastRepository.php');
require_once ('/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/services/MySqlModelRepository.php');
require_once ('/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/services/MySqlDocumentationOrderRepository.php');
require_once ('/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/services/MySqlOrderPdfRepository.php');
require_once ('/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/services/MySqlPartNumberToPdfRepository.php');
require_once ('/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/services/MySqlPdfPrintRepository.php');

/**
 * Selection des Repository
 */
class RepositorySelection {
  /**
   * Type de repository
   *
   * @var string
   */
  protected string $repositoryType;

  function __construct(string $repositoryType) {
    $this->repositoryType = $repositoryType;
  }

  /**
   * Selection des repositories
   *
   * @return RepositoriesModel
   */
  function selectRepositories(User $user = null):RepositoriesModel {
    switch ($this->repositoryType) {
      case StaticData::REPOSITORY_TYPE_MYSQL:
        return new RepositoriesModel(
          new MySqlOrderRepository($user),          
          new MySqlModelRepository(),
          new MySqlForecastRepository(),
          new MySqlOrderPdfRepository(),          
          new MySqlDocumentationOrderRepository(),
          new MySqlPartNumberToPdfRepository(),
          new MySqlPdfPrintRepository()          
        );
        break;
      default: throw new \Exception('Not a valid repository Type '); break;
    }
  }
}