<?
/**
 * Interface Model repository
 */
interface ModelRepositoryInterface {
  
  /**
   * Renvoi les données sur un model
   * 
   * @param string $codeName
   * @return string
   */
  function findOneByCode(string $codeName): string;

}