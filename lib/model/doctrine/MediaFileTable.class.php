<?php

/**
 * MediaFileTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class MediaFileTable extends Doctrine_Table {

  /**
   * Returns an instance of this class.
   *
   * @return MediaFileTable The table instance
   */
  public static function getInstance() {
    return Doctrine_Core::getTable('MediaFile');
  }

  /**
   *
   * @return Doctrine_Query
   */
  public function queryAll() {
    return $this->createQuery('m')->orderBy('m.id ASC');
  }

  /**
   *
   * @param int $petitionId
   * @return Doctrine_Query
   */
  public function queryByPetitionId($petitionId) {
    return $this->queryAll()->where('m.petition_id = ?', $petitionId);
  }

}
