<?
/**
 * Interface Model repository
 */
interface LanguageRepositoryInterface {
  
  /**
   * Renvoi les données sur une langue
   * 
   * @param string $codeName
   * @return string
   */
  function findOneByCode(string $codeName): string;

}