<?php
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/interfaces/OrderRepositoryInterface.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/interfaces/DocumentationOrderRepositoryInterface.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/interfaces/DocPDFRepositoryInterface.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/interfaces/ModelRepositoryInterface.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/interfaces/ForecastRepositoryInterface.php';

/**
 * Modèle sur les base de données
 */
class RepositoriesModel  {

  /**
   * Order Repository
   *
   * @var OrderRepositoryInterface
   */
  protected OrderRepositoryInterface $orderRepository;

  /**
   * DocumentationOrder Repository
   *
   * @var DocumentationOrderRepositoryInterface
   */
  protected DocumentationOrderRepositoryInterface $documentationOrderRepository;

  /**
   * DocPDF Repository
   *
   * @var DocPDFRepositoryInterface
   */
  protected DocPDFRepositoryInterface $docPDFRepository;

  /**
   * Model Repository
   *
   * @var ModelRepositoryInterface
   */
  protected ModelRepositoryInterface $modelRepository;

  /**
   * Forecast Repository
   *
   * @var ForecastRepositoryInterface
   */
  protected ForecastRepositoryInterface $forecastRepository;

  function __construct(
    OrderRepositoryInterface $orderRepository,
    DocumentationOrderRepositoryInterface $documentationOrderRepository,
    DocPDFRepositoryInterface $docPDFRepository,
    ModelRepositoryInterface $modelRepository,
    ForecastRepositoryInterface $forecastRepository
  ) {
    $this->orderRepository = $orderRepository;
    $this->modelRepository = $modelRepository;
    $this->docPDFRepository = $docPDFRepository;
    $this->documentationOrderRepository = $documentationOrderRepository;
    $this->forecastRepository = $forecastRepository;
  }

  #region getters

  /**
   * Get Repository Order
   *
   * @return OrderRepositoryInterface
   */
  function getOrderRepository(): OrderRepositoryInterface {
    return $this->orderRepository;
  }

  /**
   * Get Repository ForecastRepository
   *
   * @return ForecastRepositoryInterface
   */
  function getForecastRepository(): ForecastRepositoryInterface {
    return $this->forecastRepository;
  }

  /**
   * Get Model Repository
   *
   * @return ModelRepositoryInterface
   */
  function getModelRepository(): ModelRepositoryInterface {
    return $this->modelRepository;
  }

  /**
   * Get DocPDFRepository
   *
   * @return DocPDFRepositoryInterface
   */
  function getDocPDFRepository(): DocPDFRepositoryInterface {
    return $this->docPDFRepository;
  }

  /**
   * Get DocumentOrder Repository
   *
   * @return DocumentationOrderRepositoryInterface
   */
  function getDocumentOrderRepository(): DocumentationOrderRepositoryInterface {
    return $this->documentationOrderRepository;
  }

  #endRegion
}