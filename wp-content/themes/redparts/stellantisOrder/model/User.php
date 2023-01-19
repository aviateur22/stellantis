<?php

/**
 * Utilisateur
 */
class User {

  /**
   * id
   *
   * @var integer
   */
  protected int $userId;

  /**
   * Pénom
   *
   * @var string
   */
  protected string $firstName;

  /**
   * Nom
   *
   * @var string
   */
  protected string $lastName;

  /**
   * Liste des roles
   *
   * @var array
   */
  protected array $roles;

  function __construct(int $userId, string $firstName, string $lastName, array $roles) {
    $this->userId = $userId;
    $this->firstName = $firstName;
    $this->lastName = $lastName;
    $this->roles = $roles;
  }

  #region getter

    /**
     * Renvoie id
     *
     * @return integer
     */
    public function getId(): int {
      return $this->userId;
    }

    /**
     * Renvoie le lastName
     *
     * @return string
     */
    public function getLastName(): string {
      return $this->lastName;
    }

    /**
     * Renvoie le firdtName
     *
     * @return string
     */
    public function getFirstName():string {
      return $this->lastName;
    }

    /**
     * Renvoie lastName + FirstName
     *
     * @return string
     */
    public function getFistNameAndLastName(): string {
      return $this->lastName ." ". $this->firstName;
    }

    /**
     * Renvoie les roles
     *
     * @return array
     */
    public function getRoles(): array {
      return $this->roles;
    }

    /**
     * Renvoie le 1er role trouvé de l'utilisateur
     *
     * @return string|null
     */
    public function getFirstRole(): string {
      
      foreach($this->roles as $role) {
        if(!empty($role)) {
          return $role;
        }
      }     
      return null;      
    }


  #endRegion
}