<?
/**
 * Interface Model repository
 */
interface ModelRepositoryInterface {
  
  /**
   * Renvoi les données sur un model
   * 
   * @param string $codeName
   * @return array - données du model
   */
  function findOneByCode(string $codeName): array;

}