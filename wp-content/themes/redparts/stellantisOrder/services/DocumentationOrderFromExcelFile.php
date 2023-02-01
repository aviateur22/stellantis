<?php
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/helpers/ExcelFileHelper.php';
/**
 * Relation PartNumber - Docuementation PDF à partir d'un fichier XLS
 */
class DocumentationOrderFromExcelFile extends ExcelFileHelper {

  /**
   * Liste ayant les données du fichier
   *
   * @var array
   */
  protected array $documents = [];

  /**
   * Liste des pages du classeur
   *
   * @var array
   */
  protected array $worksheets = [];

  /**
   * Nom du model de voiture
   *
   * @var string
   */
  protected string $model;

  /**
   * Nom du fichier Excel 
   *
   * @var string
   */
  protected string $fileName = '';

  function __construct(string $model) {
    parent::__construct($this->fileName);    
    $this->model = $model;
  }

  /**
   * Structure du docuement Excel pour les commandes
   *
   */
  const FILE_STRUCTURE = [
    'ROW_START' => 7,
    'COLUMN_START' => 1
  ];

  /**
   * Données permettant de valider le document Excel
   * Espaces entre les mots supprimés
   * Text en lowercase
   *
   */
  const VALIDATE_WORDS = [
    [
      'WORD' => 'brand',
      'ROW' => 1,
      'COLULMN' => 0
    ],
    [
      'WORD' => 'carlinename',
      'ROW' => 1,
      'COLULMN' => 2
    ],
    [
      'WORD' => 'country',
      'ROW' => 5,
      'COLULMN' => 2
    ],
    [
      'WORD' => 'codeof',
      'ROW' => 5,
      'COLULMN' => 3
    ],
    [
      'WORD' => 'codepochddb',
      'ROW' => 5,
      'COLULMN' => 4
    ]
  ];

  /**
   * Parcours du contenu du fichier
   *
   * @return void
   */
  function readSourceData(): void {

    // Recherche de la page ayant les données a analyser   
    $pageIndex = $this->findSheet($this->model);

    if($pageIndex < 0) {
      throw new \Exception('There is no page available to check PDF docuementation', 400);
    }

    // Active la page avec les données
    $this->setActiveSheet($pageIndex);

    // Vérification si lapage est valide pour trouver les données
    $isSheetvalid = $this->isSheetValid(self::VALIDATE_WORDS, 'XLS File not valid to check Documentation PDF');
  }

  /**
   * Object pour stocker les données de 1 commande
   *
   * @return stdClass
   */
  private function getNewOrderStdClass(): stdClass {
    // orderStdClass
    $orderStdClass = new stdClass;

    return $orderStdClass;
  }

}