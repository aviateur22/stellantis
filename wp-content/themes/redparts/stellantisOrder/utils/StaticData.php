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

  const STATUS_WIP_MI = [
    'MI' => [
      'PREFLIGHT' => 1,
      'IN PROGRESS' => 2,
      'READY' => 3,
      'DELIVERED' => 4,
      'BLOCKED' => 5
    ],
    'MA' => [
      'PREFLIGHT' => 6,
      'IN PROGRESS' => 7,
      'READY' => 8,
      'DELIVERED' => 9,
      'BLOCKED' => 10
    ]
  ];

  const STATUS_WIP_MA = [
    'PREFLIGHT' => 6,
    'IN PROGRESS' => 7,
    'READY' => 8,
    'DELIVERED' => 9,
    'BLOCKED' => 10
  ];

  const STATUS_WIP_ST = [    
    'PREFLIGHT' => 11,
    'READY' => 12
  ];
}