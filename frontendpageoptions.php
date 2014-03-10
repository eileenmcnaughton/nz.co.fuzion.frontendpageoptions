<?php

require_once 'frontendpageoptions.civix.php';

/**
 * Implementation of hook_civicrm_config
 */
function frontendpageoptions_civicrm_config(&$config) {
  _frontendpageoptions_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 */
function frontendpageoptions_civicrm_xmlMenu(&$files) {
  _frontendpageoptions_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 */
function frontendpageoptions_civicrm_install() {
  return _frontendpageoptions_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 */
function frontendpageoptions_civicrm_uninstall() {
  return _frontendpageoptions_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 */
function frontendpageoptions_civicrm_enable() {
  return _frontendpageoptions_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 */
function frontendpageoptions_civicrm_disable() {
  return _frontendpageoptions_civix_civicrm_disable();
}

/**
 * Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 */
function frontendpageoptions_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _frontendpageoptions_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 */
function frontendpageoptions_civicrm_managed(&$entities) {
  return _frontendpageoptions_civix_civicrm_managed($entities);
}


/**
 * Implementation of entity setting hook_civicrm_alterEntitySettingsFolders
 * declare folders with entity settings
 */

function frontendpageoptions_civicrm_alterEntitySettingsFolders(&$folders) {
  static $configured = FALSE;
  if ($configured) return;
  $configured = TRUE;

  $extRoot = dirname( __FILE__ ) . DIRECTORY_SEPARATOR;
  $extDir = $extRoot . 'settings';
  if(!in_array($extDir, $folders)){
    $folders[] = $extDir;
  }
}

/**
 * implements postProcess hook
 * @param string $formName
 * @param object $form
 */
function frontendpageoptions_civicrm_postProcess($formName, &$form) {
  if($formName == 'CRM_Contribute_Form_Contribution_Confirm') {
    _frontendpageoptions_processContributionForm($form);
  }
  if($formName == 'CRM_Event_Form_Registration_Confirm') {
    _frontendpageoptions_processEventForm($form);
  }
}

/**
 * implements buildForm hook
 *
 * Since we can't rely on events to always call the confirm page we also take a look
 * when building the thank you pae
 * @param string $formName
 * @param object $form
 */
function frontendpageoptions_civicrm_buildForm($formName, &$form) {
  if($formName == 'CRM_Event_Form_Registration_ThankYou') {
    _frontendpageoptions_processEventForm($form);
  }
}

/**
 * @param form
 */

function _frontendpageoptions_processEventForm($form) {
  $id = isset($form->_eventId) ? $form->_eventId : $form->get('id');
  $settings = _frontendpageoptions_getsettings($id, 'event');
  if(!empty($settings['event_cidzero_rti'])) {
    if(isset($form->_values['participant']['contact_id'])) {
      $registeredContactID = $form->_values['participant']['contact_id'];
      $loggedinUserContactID = _frontendpageoptions_getloggedincontactid();
      if($loggedinUserContactID && _frontendpageoptions_is_contact_new($registeredContactID)) {
        _frontendpageoptions_create_relationship($registeredContactID, $loggedinUserContactID, $settings['event_cidzero_rti']);
      }
    }
  }
  if(!empty($settings['event_thankyou_redirect'])) {
    CRM_Utils_System::redirect($settings['event_thankyou_redirect']);
  }
}

/**
 *
 * @param CRM_Core_Form $form
 */
function _frontendpageoptions_processContributionForm($form) {
  $settings = _frontendpageoptions_getsettings($form->get('id'), 'contribution_page');
  if(!empty($settings['contribution_page_cidzero_relationship_type_id'])) {
    //@todo - fix civi so it sets the selected contact on the form rather than retrieving from contribution
    $registeredContactID = civicrm_api3('contribution', 'getvalue', array('id' => $form->_contributionID, 'return' => 'contact_id'));
    $loggedinUserContactID = _frontendpageoptions_getloggedincontactid();
    if($loggedinUserContactID && _frontendpageoptions_is_contact_new($registeredContactID)) {
      _frontendpageoptions_create_relationship($registeredContactID, $loggedinUserContactID, $settings['contribution_page_cidzero_relationship_type_id']);
    }
  }
  if(!empty($settings['contribution_page_thankyou_redirect'])) {
    CRM_Utils_System::redirect($settings['contribution_page_thankyou_redirect']);
  }
}

/**
 * Get permission for a given entity id in a given direction
 * @param integer $entity_id
 * @return string
 */
function _frontendpageoptions_getredirect($entity_id, $entity) {
  $entity_settings = civicrm_api3('entity_setting', 'get', array(
    'key' => 'nz.co.fuzion.frontendpageoptions',
    'entity_id' => $entity_id,
    'entity_type' => $entity)
  );
  return CRM_Utils_Array::value('contribution_page_thankyou_redirect', $entity_settings['values']);
}

/**
 * Get permission for a given entity id in a given direction
 * @param integer $entity_id
 * @return string
 */
function _frontendpageoptions_getsettings($entity_id, $entity) {
  static $settings = array();
  $key = $entity . $entity_id;
  if(!empty($settings[$key])) {
    return $settings[$key];
  }
  try {
    $settings[$key] = civicrm_api3('entity_setting', 'getsingle', array(
      'key' => 'nz.co.fuzion.frontendpageoptions',
      'entity_id' => $entity_id,
      'entity_type' => $entity)
    );
    return $settings[$key];
  }
  catch(Exception $e) {
    return array();
  }
}

/**
 * Get contact id of logged in user
 * @return NULL|Integer <mixed, NULL, integer>
 */
function _frontendpageoptions_getloggedincontactid() {
  $session = CRM_Core_Session::singleton();
  if (!is_numeric($session->get('userID'))) {
    return NULL;
  }
  return $session->get('userID');
}

/**
 * Here we check if the contact has only just been created. We don't want to create relationships with dupe matches
 * in case that allows someone to gain access to someone else's details
 * @param integer $contactID
 */
function _frontendpageoptions_is_contact_new($contact_id) {
  $sql = "
    SELECT count(*) FROM civicrm_contact WHERE created_date > DATE_SUB(NOW(), INTERVAL 5 MINUTE)
    AND id = %1
  ";
  return CRM_Core_DAO::singleValueQuery($sql, array(1 => array($contact_id, 'Integer')));
}

/**
 * Create a relationship between the registrant & registered contact
 * @param integer $registeredContactID
 * @param integer $loggedinUserContactID
 */
function _frontendpageoptions_create_relationship($registeredContactID, $loggedinUserContactID, $relationship_type_id) {
  $relationshipType = explode('_', $relationship_type_id);
  $params = array(
    'relationship_type_id' => $relationshipType[0],
    'contact_id_' . $relationshipType[1] => $registeredContactID,
    'contact_id_' . $relationshipType[2] => $loggedinUserContactID,
    'start_date' => 'now',
    'description' => 'relationship from form submission',
  );
  try {
    civicrm_api3('relationship', 'create', $params);
  }
  catch (Exception $e) {
    //take no action as this is a front end form
  }
}