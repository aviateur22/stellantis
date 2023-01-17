<?php
/**
 * Gestion des forecast
 */
interface ForecastRepositoryInterface {

  /**
   * Renvoi les forecast contenu dans un inteval de semaine
   *
   * @param string $partNumber
   * @param string $dayStart
   * @param string $dayEnd
   * 
   * @return array
   */
  function findForecastByWeekInterval(string $partNumber, string $dayStart, string $dayEnd): array;
}