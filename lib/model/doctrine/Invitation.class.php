<?php

/**
 * Invitation
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    policat
 * @subpackage model
 * @author     Martin
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class Invitation extends BaseInvitation {

  public function applyToUser(sfGuardUser $user) {
    $con = InvitationTable::getInstance()->getConnection();
    $con->beginTransaction();
    try {

      foreach ($this->getInvitationCampaign() as $invitationCampaign) {
        /* @var $invitationCampaign InvitationCampaign */
        $invitationCampaign->applyToUser($user);
      }

      $this->delete();
      $con->commit();
    } catch (\Exception $e) {
      $con->rollback();
    }
  }

}
