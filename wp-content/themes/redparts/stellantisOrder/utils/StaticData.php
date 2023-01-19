<?php
abstract class StaticData {

  const FACTORY_AUTH_ORDER = [
    'poissy',
    'rennes',
    'sochaux',
    'mulhouse',
    'kenitra',
    'administrator'
  ];

  const STATUS_WIP = [
    'PREFLIGHT_MI' => 1,
    'IN_PROGRESS_MI' => 2,
    'READY_MI' => 3,
    'DELIVERED_MI' => 4,    
    'PREFLIGHT_MA' => 5,
    'IN_PROGRESS_MA' => 6,
    'READY_MA' => 7,
    'DELIVERED_MA' => 8,
  ];
}