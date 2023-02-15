<?php
require_once ('/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/exceptions/PhpExcelException.php');

/**
 * Excel Helper pour initialisation du fichier
 * 
 */
class ExcelFileHelper {

  /**
   * Feuille Excel Active
   *
   * @var PHPExcel_Worksheet|null
   */
  // protected  PHPExcel_Worksheet|null $activeSheet = null;
  protected  $activeSheet = null;

  /**
   * Liste des pages du document Excel
   *
   * @var array
   */
  protected array $workSheets = [];

  /**
   * Nom du fichier Excel
   *
   * @var string
   */
  protected string $fileName;

  function __construct(string $fileName)
  {
    $this->fileName = $fileName;
  }

  /**
   * Vérification module PHPExcel
   *
   * @return void
   */
  protected function isPhpExcelAvailbale(): void {    
    $PHPExcel = "/home/mdwfrkglvc/www/wp-content/themes/redparts/PHPExcel-1.8/Classes/PHPExcel.php";
  
    if (file_exists($PHPExcel)) {
      require_once $PHPExcel;
    }
    else {
      throw new PhpExcelException();
    }    
  }  

  /**
   * Récupération Page active
   * 
   * @param string fileName
   * @return void
   */
  protected function getActiveSheet(): void {
    // Initialisation d'un object PHPExcel 
    $objPHPExcel = $this->initializeExcelReader();

    // Récupérationd de la page active
    $this->activeSheet = $objPHPExcel->getActiveSheet();
  }

  /**
   * Désignation de la feuille active
   *
   * @return void
   */
  protected function setActiveSheet(int $sheetIndex): void {
    $this->activeSheet = $this->initializeExcelReader()->getSheet($sheetIndex);
  }

  /**
   * Renvoie la liste des page du classeur Excel
   *
   * @return array
   */
  protected function getSheetsName(): array {
    // Initialisation d'un object PHPExcel
    $objPHPExcel = $this->initializeExcelReader();

    // Récupérations des pages du fichier Excel
    $workSheets = $objPHPExcel->getSheetNames();
    return $workSheets;
  }

  /**
   * Recherche une page a partir de son nom
   *
   * @return int - Index de la page recherché
   */
  protected function findSheet(string $wordToCheck): int {
    foreach($this->workSheets as $key=>$value) {
      if(str_contains(strtolower($value), strtolower($wordToCheck))) {
        return (int) $key;
      }
    }
    return -1;
  }

  /**
   * Vérification validité du fichier XLS
   *
   * @return boolean
   */
  protected function isSheetValid(array $wordToCheck, string $exceptionMessage): bool {    

    // Vérification concordence des mots
    foreach($wordToCheck as $word) {
      $wordOnFile = preg_replace("/\s+/", "", strtolower($this->readCellValue($word['ROW'], $word['COLULMN'])));
      
      if($wordOnFile !== $word['WORD']) {
        throw new \Exception($exceptionMessage);
      }
    };
    
    // Renvoie fichier Valid
    return true;
  }

  /**
   * Renvoie les données d'une cellule
   *
   * @param integer $row
   * @param integer $col
   * @param bool $isDate - Si date a renvoyer
   * @return string|null
   */
  protected function readCellValue(int $row, int $col, bool $isDate = false): string {       
    if(!$isDate) {      
      return is_null($this->activeSheet->getCell(PHPExcel_Cell::stringFromColumnIndex($col).$row)->getValue())  ?
        '' :
        $this->activeSheet->getCell(PHPExcel_Cell::stringFromColumnIndex($col).$row)->getValue();
    }

    $date = is_null($this->activeSheet->getCell(PHPExcel_Cell::stringFromColumnIndex($col).$row)->getValue())  ?
      '' :
      $this->activeSheet->getCell(PHPExcel_Cell::stringFromColumnIndex($col).$row)->getValue();
    
   return \PHPExcel_Style_NumberFormat::toFormattedString($date, 'YYYY-MM-DD');
  }

  /**
   * Récupération Instance PHPExcel
   *
   * @return \PHPExcel
   */
  private function initializeExcelReader(): \PHPExcel {
    $excelReader = PHPExcel_IOFactory::createReaderForFile($this->fileName);
    return $excelReader->load($this->fileName);
  }
}