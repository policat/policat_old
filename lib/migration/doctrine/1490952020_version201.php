<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Version201 extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->createForeignKey('invitation_campaign', 'invitation_campaign_invitation_id_invitation_id', array(
             'name' => 'invitation_campaign_invitation_id_invitation_id',
             'local' => 'invitation_id',
             'foreign' => 'id',
             'foreignTable' => 'invitation',
             'onUpdate' => '',
             'onDelete' => 'CASCADE',
             ));
        $this->createForeignKey('invitation_campaign', 'invitation_campaign_campaign_id_campaign_id', array(
             'name' => 'invitation_campaign_campaign_id_campaign_id',
             'local' => 'campaign_id',
             'foreign' => 'id',
             'foreignTable' => 'campaign',
             'onUpdate' => '',
             'onDelete' => 'CASCADE',
             ));
        $this->addIndex('invitation_campaign', 'invitation_campaign_invitation_id', array(
             'fields' => 
             array(
              0 => 'invitation_id',
             ),
             ));
        $this->addIndex('invitation_campaign', 'invitation_campaign_campaign_id', array(
             'fields' => 
             array(
              0 => 'campaign_id',
             ),
             ));
    }

    public function down()
    {
        $this->dropForeignKey('invitation_campaign', 'invitation_campaign_invitation_id_invitation_id');
        $this->dropForeignKey('invitation_campaign', 'invitation_campaign_campaign_id_campaign_id');
        $this->removeIndex('invitation_campaign', 'invitation_campaign_invitation_id', array(
             'fields' => 
             array(
              0 => 'invitation_id',
             ),
             ));
        $this->removeIndex('invitation_campaign', 'invitation_campaign_campaign_id', array(
             'fields' => 
             array(
              0 => 'campaign_id',
             ),
             ));
    }
}