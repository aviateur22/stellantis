<?php
abstract class StaticData {
  // Nombre de semaine pour calculer les prévisions
  const PRINT_FORECAST_WEEK = 8;

  const MINIMUM_ORDER_QUANTITY_MANCHECOURT = 2000;
  
  // Affichage jour du Dashboard
  const DASHBOARD_INTERVAL_DAY = 7;

  #region user role

  // Role MILLAU
  const MILLAU_FACTORY_ROLE_NAME = 'MILLAU';
    
  // Role Manchecourt
  const MANCHECOURT_FACTORY_ROLE_NAME = 'MANCHECOURT';

  // Role Stellantis
  const STELLANTIS_ROLE_NAME = 'STELLANTIS';

  // Liste des roles usine du groupe Stellantis
  const FACTORY_STELLANTIS_ROLES_NAMES = [
    'poissy',
    'rennes',
    'sochaux',
    'mulhouse',
    'kenitra'
  ];

  #endRegion

  #region regroupement des WipId

    // Constante des Status de commande 
    const ORDER_STATUS = [
      'BEFORE_PREFLIGHT_MI' => '1',
      'PREFLIGHT_MI' => '2',
      'ON_PROGRESS_MI' => '3',
      'READY_MI' => '4',
      'DELIVERED_MI' => '5',
      'BEFORE_PREFLIGHT_MA'=> '6',
      'PREFLIGHT_MA' => '7',
      'ON_PROGRESS_MA' => '8',
      'READY_MA' => '9',
      'DELIVERED_MA' => '10',
      'BEFORE_PREFLIGHT_ST'=> '11',
      'PREFLIGHT_ST' => '12',
      'ON_PROGRESS_ST' => '13',
      'READY_ST' => '14',
      'DELIVERED_ST' => '15',
      'BLOCKED_MI' => '16',
      'BLOCKED_MA' => '17',
      'BLOCKED_ST' => '18',
      'PREPARATION' => '19'
    ];

    // BEFORE PRFLIGHT pour les WIPID suivant: 
    const BEFORE_PREFLIGHT_ID = [1, 6, 11];

    // PREFLFIGHT pour les WipId suivant
    const PREFLIGHT_ID = [2, 7, 12];

    // ON PROGRESS pour les WipId suivant
    const ON_PROGRESS_ID = [3, 8, 13];

    // READAY pour les WipId suivant
    const READY_ID = [4, 9, 14];

    // DELIVERED pour les WipId suivant
    const DELIVERED_ID = [5, 10, 15];

    // BLOCKED pour les WipId suivant
    const BLOCKED_ID = [16, 17, 18];

    // PREPARATION pour les WipId suivant
    const PREPARATION_ID = [19];

    // Millau pour les WIpId suivant:
    const MILLAU_ID = [1, 2, 3, 4, 5, 16];

    // Manchecourt pour les WIpId suivant:
    const MANCHECOURT_ID = [6, 7, 8, 9, 10, 17];

    // Stellantis pour les WIpId suivant:
    const STELLANTIS_ID = [11, 12, 13, 14, 15, 18];
  #endRegion


  #region affichage des wip

    // Affichage des statuts pour Stellantis, Manchecourt et Millau
    const STATUS_DISPLAY_NAME_ROLE_ST_MI_MA = [
      'BEFORE_PREFLIGHT' => 'WAITING PREFLIGHT',
      'PREFLIGHT' => 'PREFLIGHT',
      'ON_PROGRESS' => 'ON PROGRESS',
      'READY' => 'READY',
      'DELIVERED' => 'DELIVERED',
      'BLOCKED' => 'BLOCKED',    
    ];

    // Affichage des statuts pour un admin ou autre roles
    const STATUS_DISPLAY_NAME_ROLE_OTHER = [
      'PREFLIGHT_MI' => 'PREFLIGHT_MI',
      'ON_PROGRESS_MI' => 'ON_PROGRESS_MI',
      'READY_MI' => 'READY_MI',
      'DELIVERED_MI' => 'DELIVERED_MI',
      'PREFLIGHT_MA' => 'PREFLIGHT_MA',
      'ON_PROGRESS_MA' => 'ON_PROGRESS_MA',
      'READY_MA' => 'READY_MA',
      'DELIVERED_MA' => 'DELIVERED_MA',
      'PREFLIGHT_ST' => 'PREFLIGHT_ST',
      'ON_PROGRESS_ST' => 'ON_PROGRESS_ST',
      'READY_ST' => 'READY_ST',
      'DELIVERED_ST' => 'DELIVERED_ST',
      'BLOCKED_MI' => 'BLOCKED_MI',
      'BLOCKED_MA' => 'BLOCKED_MA',
      'BLOCKED_ST' => 'BLOCKED_ST',
      'PREPARATION' => 'PREPARATION'
    ];
  #endRegion

  #region className affichant la couleur des commandes

    const CLASS_NAME_ORDERS_COLORS = [
      'BEFORE_PREFLIGHT_CLASS_NAME' => 'status--before--preflight',
      'PREFLIGHT_CLASS_NAME' => 'status--preflight',
      'PROGRESS_CLASS_NAME' => 'status--progress',
      'READY_CLASS_NAME' => 'status--ready',
      'DELIVERED_CLASS_NAME' => 'status--delivered',
      'BLOCKED_CLASS_NAME' => 'status--blocked',
      'BLINKING_CLASS_NAME' => 'status--blinking'
    ];

    const CLASS_NAME_HEADER_COLOR = [
      'HEADER_COLOR_1' => 'header--color--one',
      'HEADER_COLOR_2' => 'header--color--two'
    ];
    
  #endRegion

  #region Maury + Stellantis name
    const MILLAU_FACTORY_NAME = "MILLAU FACTORY";

    const MANCHECOURT_FACTORY_NAME = "MANCHECOURT FACTORY";

    const STELLANTIS_NAME = "STELLANTIS";
  #endRegion
}