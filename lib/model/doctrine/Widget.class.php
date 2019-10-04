<?php

/**
 * Widget
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @package    policat
 * @subpackage model
 * @author     Martin
 * @version    SVN: $Id: Builder.php 6820 2009-11-30 17:27:49Z jwage $
 */
class Widget extends BaseWidget {

  const STATUS_DRAFT = 1;
  const STATUS_ACTIVE = 4;
  const STATUS_BLOCKED = 5;

  static $STATUS_SHOW = array(
      self::STATUS_DRAFT => 'draft',
      self::STATUS_ACTIVE => 'active',
      self::STATUS_BLOCKED => 'blocked'
  );
  static $STATUS_RIGHTS_MATRIX = array(
      null => array
          (
          self::STATUS_DRAFT => array(Permission::NAME_PETITION_CREATE)
      ),
      self::STATUS_DRAFT => array
          (
          self::STATUS_ACTIVE => array(Permission::NAME_PETITION_CREATE)
      ),
      self::STATUS_ACTIVE => array
          (
          self::STATUS_BLOCKED => array(Permission::NAME_PETITION_BLOCK)
      ),
      self::STATUS_BLOCKED => array
          (
          self::STATUS_ACTIVE => array(Permission::NAME_PETITION_BLOCK)
      )
  );
  static $EDIT_RIGHTS = array(
      self::STATUS_DRAFT => array(Permission::NAME_PETITION_EDIT_ALL, Permission::NAME_PETITION_EDIT_DRAFT),
      self::STATUS_ACTIVE => array(Permission::NAME_PETITION_EDIT_ALL, Permission::NAME_PETITION_EDIT_ACTIVE),
      self::STATUS_BLOCKED => array(Permission::NAME_PETITION_EDIT_ALL, Permission::NAME_PETITION_EDIT_ACTIVE)
  );

  const VALIDATION_KIND_NONE = 0;
  const VALIDATION_KIND_EMAIL = 1;

  static $VALIDATION_KIND_SHOW = array(
      self::VALIDATION_KIND_NONE => 'none',
      self::VALIDATION_KIND_EMAIL => 'email'
  );

  const VALIDATION_STATUS_WITHOUT = 0;
  const VALIDATION_STATUS_PENDING = 1;
  const VALIDATION_STATUS_VERIFIED = 2;
  const VALIDATION_STATUS_OWNER = 3;

  static $VALIDATION_STATUS_SHOW = array
      (
      self::VALIDATION_STATUS_WITHOUT => 'without',
      self::VALIDATION_STATUS_PENDING => 'pending',
      self::VALIDATION_STATUS_VERIFIED => 'verified',
      self::VALIDATION_STATUS_OWNER => 'owner'
  );

  public function calcPossibleStatusByPermissions($permissions) {
    return $this->utilCalcPossibleStatusByPermissions(self::$STATUS_RIGHTS_MATRIX, $permissions);
  }

  public function calcIsEditableByPermission($permissions) {
    return $this->utilCalcIsEditableByPermission(self::$EDIT_RIGHTS, $permissions);
  }

  public static function calcStatusShow($statuses) {
    $ret = array();
    foreach ($statuses as $status)
      if (isset(self::$STATUS_SHOW[$status]))
        $ret[$status] = self::$STATUS_SHOW[$status];
    return $ret;
  }

  public function getStatusName() {
    return isset(self::$STATUS_SHOW[$this->getStatus()]) ? self::$STATUS_SHOW[$this->getStatus()] : 'unknown';
  }

  public function getStyling($name, $default = null) {
    $value = $this->utilGetFieldFromArray('stylings', $name, $default);
    if ($name === 'button_primary_color' && !$value) {
      $name = 'button_color';
      $value = $this->utilGetFieldFromArray('stylings', $name, $default);
    }
    if ($name === 'label_color' && !$value) {
      $name = 'body_color';
      $value = $this->utilGetFieldFromArray('stylings', $name, $default);
    }
    if ($default === null && $value === null && in_array($name, WidgetTable::$STYLE_COLOR_NAMES)) {
      $petition = $this->getPetition();
      $value = $petition['style_' . $name];
    }

    return $value;
  }

  public function setStyling($name, $value) {
    $this->utilSetFieldFromArray('stylings', $name, $value);
  }

  public function getFontFamily() {
    $petition = $this->getPetition();
    $petition_font_family = $petition->getStyleFontFamily();

    if ($petition->getWidgetIndividualiseDesign()) {
      return $this->getStyling('font_family', $petition_font_family);
    } else {
      return $petition_font_family;
    }
  }

  public static function genCode() {
    return mt_rand(10000000000, 99999999999);
  }

  public static function getSecretHash() {
    return sfConfig::get('app_hashes_widget');
  }

  public function getLastHash() {
    return self::calcLastHash($this->getId(), array(
          $this['object_version'],
          $this['Campaign']['object_version'],
          $this['Petition']['object_version'],
          $this['PetitionText']['object_version']));
  }

  public static function calcLastHash($id, $versions) {
    sort($versions);
    $last = end($versions);
    $hash_of_style = substr(md5($last . self::getSecretHash()), 16);
    $hash_key = substr(md5($hash_of_style . self::getSecretHash() . $id), 16);
    return "$hash_of_style$hash_key";
  }

  public static function isValidLastHash($id, $hash) {
    $hash_of_style = substr($hash, 0, 16);
    $hash_key_got = substr($hash, 16);
    $hash_key_expected = substr(md5($hash_of_style . self::getSecretHash() . $id), 16);
    return ($hash_key_expected == $hash_key_got);
  }

  public function getFinalPaypalEmail() {
    $email = $this->getPaypalEmail();
    if (is_string($email) && strpos($email, '@') && strlen($email) > 5) {
      return $email;
    }
    if (empty($email)) { // not == 'ignore'
      $email = $this->getPetition()->getPaypalEmail();

      if (is_string($email) && strpos($email, '@') && strlen($email) > 5) {
        return $email;
      }
    }

    if ($email === 'ignore') {
      return false;
    }

    return null;
  }

  public function getIdentString() {
    return $this->getId() . ' ' . $this->getTitle() . ' (' . $this->getEmail() . ')';
  }

  public function isDataOwner(sfGuardUser $user) {
    return $user && $user->getId() == $this->getUserId() && $this->getDataOwner() == WidgetTable::DATA_OWNER_YES;
  }

  public function countSignings() {
    return PetitionSigningTable::getInstance()->countByWidget($this);
  }

  public function countSubscriberSignings() {
    return PetitionSigningTable::getInstance()->countSubscriberByWidget($this);
  }

  public function countSigningsPending() {
    return PetitionSigningTable::getInstance()->countPendingByWidget($this);
  }

  public function findLandingUrl($petition = null, $try2nd) {
    if ($try2nd) {
      $landing_url = $this->getLanding2Url();

      if ($landing_url) {
        return $landing_url;
      }
    }

    $landing_url = $this->getLandingUrl();

    if ($landing_url) {
      return $landing_url;
    }

    if ($try2nd) {
      $landing_url_text = $this->getPetitionText()->getLanding2Url();
      if ($landing_url_text) {
        return $landing_url_text;
      }
    }

    $landing_url_text = $this->getPetitionText()->getLandingUrl();
    if ($landing_url_text) {
      return $landing_url_text;
    }

    if ($petition === null) {
      $petition = $this->getPetition();
    }

    $landing_url_petition = $petition->getLandingUrl();
    if ($landing_url_petition) {
      return $landing_url_petition;
    }

    return null;
  }

  public function getLastRefShort($length = 12) {
    $ref = $this->getLastRef();
    if (!$ref) {
      return '';
    }
    if (strpos($ref, 'http://') === 0) {
      $ref = substr($ref, 7);
    } elseif (strpos($ref, 'https://') === 0) {
      $ref = substr($ref, 8);
    }

    if (mb_strlen($ref, 'UTF-8') > $length) {
      return mb_substr($ref, 0, $length, 'UTF-8') . "…";
    }

    return $ref;
  }

  public function getLastRefShy() {
    $ref = $this->getLastRef();
    if (!$ref) {
      return '';
    }

    $shy = html_entity_decode('&shy;', ENT_COMPAT, 'UTF-8');
    return strtr($ref, array(
        '/' => '/' . $shy,
        '?' => '?' . $shy,
        ';' => ';' . $shy,
        '&' => '&' . $shy,
        '=' => '=' . $shy,
    ));
  }

  public function getInheritLandingUrl() {
    $landing_url = trim($this->getLandingUrl());

    if (!$landing_url && $this->getParentId()) {
      $landing_url = trim($this->getParent()->getLandingUrl());
    }

    if (!$landing_url && $this->getPetitionTextId()) {
      $landing_url = trim($this->getPetitionText()->getLandingUrl());
    }

    if (!$landing_url && $this->getPetitionId()) {
      $landing_url = trim($this->getPetition()->getLandingUrl());
    }

    return $landing_url;
  }

  public function getRequireBilling() {
    return StoreTable::value(StoreTable::BILLING_ENABLE) && $this->getCampaign()->getBillingEnabled() && !$this->getCampaign()->getQuotaId();
  }

  /**
   * @param type $petition_id
   */
  public function followsWidgetId($petition_id) {
    return WidgetTable::getInstance()->fetchWidgetIdByOrigin($petition_id, $this->getId());
  }

  public function getDataOwnerSubst($newline = "\n", $petition = null) {
    if (!$petition) {
      $petition = $this->getPetition();
    }
    $widget_data_owner = ($this->getDataOwner() == WidgetTable::DATA_OWNER_YES && $this->getUserId()) ? $this->getUser() : null;
    $data_owner = $widget_data_owner ? $widget_data_owner : ($petition->getCampaign()->getDataOwnerId() ? $petition->getCampaign()->getDataOwner() : null);
    /* @var $data_owner sfGuardUser */
    $orga = $data_owner ? $data_owner->getOrganisation() : '';
    $name = $data_owner ? $data_owner->getFullName() : '';
    $street = $data_owner ? $data_owner->getStreet() : '';
    $postcode = $data_owner ? $data_owner->getPostCode() : '';
    $city = $data_owner ? $data_owner->getCity() : '';
    $country = $data_owner ? $data_owner->getCountry() : '';
    if ($country) {
      sfContext::getInstance()->getConfiguration()->loadHelpers(array('I18N'));
      $country = format_country($country);
    }
    $address = $orga . ($orga ? $newline : '');
    $address .= $name . ($name ? $newline : '');
    $address .= $street . ($street ? $newline : '');
    $address .= $postcode . ' ' . $city . (($postcode || $city) ? $newline : '');
    $address .= $country . ($country ? $newline : '');

    return array(
        '#DATA-OFFICER-NAME#' => $name,
        '#DATA-OFFICER-ORGA#' => $orga,
        '#DATA-OFFICER-EMAIL#' => $data_owner ? $data_owner->getEmailAddress() : '',
        '#DATA-OFFICER-WEBSITE#' => $data_owner ? $data_owner->getWebsite() : '',
        '#DATA-OFFICER-PHONE#' => $data_owner ? $data_owner->getPhone() : '',
        '#DATA-OFFICER-MOBILE#' => $data_owner ? $data_owner->getMobile() : '',
        '#DATA-OFFICER-STREET#' => $street,
        '#DATA-OFFICER-POST-CODE#' => $postcode,
        '#DATA-OFFICER-CITY#' => $city,
        '#DATA-OFFICER-COUNTRY#' => $country,
        '#DATA-OFFICER-ADDRESS#' => $address
    );
  }

  public function getStylingsArray() {
    $this->utilGetAsJsonArray('stylings');
  }

  private static function firstValue($widget, $petition_text, $field) {
    if (!self::isEmpty($widget, $field))
      return $widget[$field];
    if (!self::isEmpty($petition_text, $field))
      return $petition_text[$field];
    return '';
  }

  public static function isEmpty($object, $field) {
    if (is_object($object) || is_array($object) && isset($object[$field])) {
      $field = $object[$field];
      if (!empty($field) && is_scalar($field)) {
        return !(strlen(trim($field)) > 0);
      }
    }
    return true;
  }

  public function getSubst() {
    $petition_text = $this->getPetitionText();

    $subst = array();
    foreach (array(
      'TITLE' => 'title', // deprecated
      'TARGET' => 'target', // deprecated
      'BACKGROUND' => 'background', // deprecated
      'INTRO' => 'intro', // deprecated
      'FOOTER' => 'footer', // deprecated
      'EMAIL-SUBJECT' => 'email_subject', // deprecated
      'EMAIL-BODY' => 'email_body', // deprecated
      '#TITLE#' => 'title',
      '#TARGET#' => 'target',
      '#BACKGROUND#' => 'background',
      '#INTRO#' => 'intro',
      '#FOOTER#' => 'footer',
      '#EMAIL-SUBJECT#' => 'email_subject',
      '#EMAIL-BODY#' => 'email_body'
    ) as $keyword => $field) {
      $subst[$keyword] = self::firstValue($this, $petition_text, $field);
    }
    $subst['BODY'] = $petition_text['body'];  // deprecated
    $subst['#BODY#'] = $petition_text['body'];

    /* @var $petition_text PetitionText */
    $petition = $petition_text->getPetition();
    /* @var $petition Petition */
    if ($petition->isEmailKind()) {
      $action_text = $subst['#EMAIL-SUBJECT#'] . "\n\n" . $subst['#EMAIL-BODY#'] . "\n";
    } else {
      $action_text = $subst['#INTRO#'] . "\n\n" . $subst['#BODY#'] . "\n\n" . $subst['#FOOTER#'] . "\n";
    }

    $subst['#ACTION-TEXT#'] = $action_text;

    return $subst;
  }

}
