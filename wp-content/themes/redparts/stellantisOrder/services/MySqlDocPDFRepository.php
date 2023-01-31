<?php

/**
 * DOCPDF MYSQL Repository
 */
class MySqlDocPDFRepository implements DocPDFRepositoryInterface {

  /**
   * Undocumented function
   *
   * @param Order $order
   * @return array
   */
  function findFullGuide(Order $order): array {
    return [];
  }

  /**
   * Undocumented function
   *
   * @param Order $order
   * @return array
   */
  function findMaintenanceBook(Order $order): array  {
    return [];
  }
}