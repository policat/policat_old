<?php

/**
 * BaseInvitation
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $email_address
 * @property string $validation_code
 * @property integer $register_user_id
 * @property sfGuardUser $RegisterUser
 * @property Doctrine_Collection $InvitationCampaign
 * 
 * @method integer             getId()                 Returns the current record's "id" value
 * @method string              getEmailAddress()       Returns the current record's "email_address" value
 * @method string              getValidationCode()     Returns the current record's "validation_code" value
 * @method integer             getRegisterUserId()     Returns the current record's "register_user_id" value
 * @method sfGuardUser         getRegisterUser()       Returns the current record's "RegisterUser" value
 * @method Doctrine_Collection getInvitationCampaign() Returns the current record's "InvitationCampaign" collection
 * @method Invitation          setId()                 Sets the current record's "id" value
 * @method Invitation          setEmailAddress()       Sets the current record's "email_address" value
 * @method Invitation          setValidationCode()     Sets the current record's "validation_code" value
 * @method Invitation          setRegisterUserId()     Sets the current record's "register_user_id" value
 * @method Invitation          setRegisterUser()       Sets the current record's "RegisterUser" value
 * @method Invitation          setInvitationCampaign() Sets the current record's "InvitationCampaign" collection
 * 
 * @package    policat
 * @subpackage model
 * @author     Martin
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseInvitation extends myDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('invitation');
        $this->hasColumn('id', 'integer', 4, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             'length' => 4,
             ));
        $this->hasColumn('email_address', 'string', 80, array(
             'type' => 'string',
             'notnull' => true,
             'unique' => true,
             'length' => 80,
             ));
        $this->hasColumn('validation_code', 'string', 40, array(
             'type' => 'string',
             'length' => 40,
             ));
        $this->hasColumn('register_user_id', 'integer', 4, array(
             'type' => 'integer',
             'notnull' => false,
             'length' => 4,
             ));

        $this->option('symfony', array(
             'form' => false,
             'filter' => false,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('sfGuardUser as RegisterUser', array(
             'local' => 'register_user_id',
             'foreign' => 'id',
             'onDelete' => 'CASCADE'));

        $this->hasMany('InvitationCampaign', array(
             'local' => 'id',
             'foreign' => 'invitation_id'));

        $timestampable0 = new Doctrine_Template_Timestampable(array(
             ));
        $this->actAs($timestampable0);
    }
}