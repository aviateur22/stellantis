<?php
/**
 * Gestion des forecast
 */
interface ForecastRepositoryInterface {

  /**
   * Renvoi les forecast contenu dans un inteval de semaine
   *
   * @param integer $weekStart
   * @param integer $weekEnd
   * @param string $partNumber
   * @return array
   */
  function findForecastByWeekInterval(string $partNumber, int $weekStart, int $weekEnd): array;
}