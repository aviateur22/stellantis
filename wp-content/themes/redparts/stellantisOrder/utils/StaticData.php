<?php
abstract class StaticData {

  
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
      'PREFLIGHT_MI' => '1',
      'ON_PROGRESS_MI' => '2',
      'READY_MI' => '3',
      'DELIVERED_MI' => '4',
      'PREFLIGHT_MA' => '5',
      'ON_PROGRESS_MA' => '6',
      'READY_MA' => '7',
      'DELIVERED_MA' => '8',
      'PREFLIGHT_ST' => '9',
      'ON_PROGRESS_ST' => '10',
      'READY_ST' => '11',
      'DELIVERED_ST' => '12',
      'BLOCKED_MI' => '13',
      'BLOCKED_MA' => '14',
      'BLOCKED_ST' => '15',
      'PREPARATION' => '16'
    ];

    // PREFLFIGHT pour les WipId suivant
    const PREFLIGHT_ID = [1, 5, 9];

    // ON PROGRESS pour les WipId suivant
    const ON_PROGRESS_ID = [2, 6, 10];

    // READAY pour les WipId suivant
    const READY_ID = [3, 7, 11];

    // DELIVERED pour les WipId suivant
    const DELIVERED_ID = [4, 8, 12];

    // BLOCKED pour les WipId suivant
    const BLOCKED_ID = [13, 14, 15];

    // PREPARATION pour les WipId suivant
    const PREPARATION_ID = [16];

    // Millau pour les WIpId suivant:
    const MILLAU_ID = [1, 2, 3, 4, 13];

    // Manchecourt pour les WIpId suivant:
    const MANCHECOURT_ID = [5, 6, 7, 8, 14];

    // Stellantis pour les WIpId suivant:
    const STELLANTIS_ID = [9, 10, 11, 12, 15];
  #endRegion


  #region affichage des wip

    // Affichage des statuts pour Stellantis, Manchecourt et Millau
    const STATUS_DISPLAY_NAME_ROLE_ST_MI_MA = [	
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
      'PREFLIGHT_CLASS_NAME' => 'status--preflight',
      'PROGRESS_CLASS_NAME' => 'status--progress',
      'READY_CLASS_NAME' => 'status--ready',
      'DELIVERED_CLASS_NAME' => 'status--delivered',
      'BLOCKED_CLASS_NAME' => 'status--blocked',
      'BLINKING_CLASS_NAME' => 'status--blinking'
    ];
    
  #endRegion

  #region Maury + Stellantis name
    const MILLAU_FACTORY_NAME = "MILLAU FACTORY";

    const MANCHECOURT_FACTORY_NAME = "MANCHECOURT FACTORY";

    const STELLANTIS_NAME = "STELLANTIS";
  #endRegion
}