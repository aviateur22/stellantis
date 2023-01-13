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
   * TODO :: Vérification format du Document orderWorksheet   * 
   *
   * @return boolean
   */
  protected function isOrderWorksheetReadable(): bool {  
  
    return true;
  }

  /**
   * Récupération Page active de la commande
   * 
   * @param string fileName
   * @return void
   */
  protected function getActiveSheet(): void {

    $excelReader = PHPExcel_IOFactory::createReaderForFile($this->fileName);
    $objPHPExcel = $excelReader->load($this->fileName);

    $this->activeSheet = $objPHPExcel->getActiveSheet();
  }

}