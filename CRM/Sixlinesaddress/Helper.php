<?php

class CRM_Sixlinesaddress_Helper {
  public static function get($contactId) {
    if (!self::isValidContactId($contactId)) {
      return '';
    }

    $addressLines = [
      '',
      '',
      '',
      '',
      '',
      '',
    ];

    $i = 0;
    $daoContact = self::getContact($contactId);
    self::addName($daoContact, $addressLines, $i);
    self::addStreet($daoContact, $addressLines, $i);
    self::addSupplementalAddress1($daoContact, $addressLines, $i);
    self::addSupplementalAddress2($daoContact, $addressLines, $i);
    self::addPostalCodeAndCity($daoContact, $addressLines, $i);
    self::addCountry($daoContact, $addressLines, $i);

    return implode('<br>', $addressLines);
  }

  private static function isValidContactId($contactId) {
    if (!empty($contactId) && $contactId > 0) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

  private static function getContact($contactId) {
    $sql = "
      select
        ov.label as prefix,
        c.first_name,
        c.last_name,
        c.addressee_custom,
        c.addressee_display,
        a.street_address,
        a.supplemental_address_1,
        a.supplemental_address_2,
        a.city,
        a.postal_code,
        a.country_id,
        ctry.name as country_name
      from
        civicrm_contact c
      left outer join
        civicrm_address a on a.contact_id = c.id
      left outer join
        civicrm_country ctry on ctry.id = a.country_id
      left outer JOIN
        civicrm_option_value ov on c.prefix_id = ov.value and ov.option_group_id = 6
      where
        c.id = %1
    ";
    $sqlParams = [
      1 => [$contactId, 'Integer'],
    ];

    $dao = CRM_Core_DAO::executeQuery($sql, $sqlParams);
    if ($dao->fetch()) {
      return $dao;
    }
    else {
      throw new Exception(__METHOD__ . " - Cannot find contact $contactId");
    }
  }

  private static function addName($daoContact, &$addressLines, &$i) {
    //$addressLines[$i] = empty($daoContact->addressee_custom) ? $daoContact->addressee_display : $daoContact->addressee_custom;
    $addressLines[$i] = $daoContact->prefix . ' ' . $daoContact->first_name . ' ' . $daoContact->last_name;
    $i++;
  }

  private static function addSupplementalAddress1($daoContact, &$addressLines, &$i) {
    if (!empty($daoContact->supplemental_address_1)) {
      $addressLines[$i] = $daoContact->supplemental_address_1;
      $i++;
    }
  }

  private static function addSupplementalAddress2($daoContact, &$addressLines, &$i) {
    if (!empty($daoContact->supplemental_address_2)) {
      $addressLines[$i] = $daoContact->supplemental_address_2;
      $i++;
    }
  }

  private static function addStreet($daoContact, &$addressLines, &$i) {
    if (!empty($daoContact->street_address)) {
      $addressLines[$i] = $daoContact->street_address;
      $i++;
    }
  }

  private static function addPostalCodeAndCity($daoContact, &$addressLines, &$i) {
    if (!empty($daoContact->postal_code) || !empty($daoContact->city)) {
      $addressLines[$i] = $daoContact->postal_code . ' ' . $daoContact->city;
      $i++;
    }
  }

  private static function addCountry($daoContact, &$addressLines, &$i) {
    if (!empty($daoContact->country_id)) {
      if ($daoContact->country_name != CRM_Core_BAO_Country::defaultContactCountryName()) {
        $addressLines[$i] = CRM_Core_PseudoConstant::country($daoContact->country_id);
        $i++;
      }
    }
  }

}
