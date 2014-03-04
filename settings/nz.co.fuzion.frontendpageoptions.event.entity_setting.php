<?php

return array (
  array(
    'key' => 'nz.co.fuzion.frontendpageoptions',
    'entity' => 'event',
    'name' => 'event_thankyou_redirect',
    'type' => 'String',
    'html_type' => 'text',
    'add' => '1.0',
    'title' => 'Thank You page Redirect',
    'description' => 'Page to redirect to instead of the normal Thank You page',
    'help_text' => 'Please enter the full or relative url including http for full',
    'add_to_setting_form' => TRUE,
    'form_child_of_parents_parents_parent' => 'thankyou_title',
  ),
  array(
    'key' => 'nz.co.fuzion.frontendpageoptions',
    'entity' => 'event',
    //note this name field was originally event_cidzero_relationship_type_id & it was dropped on some
    // servers until I shorted it
    'name' => 'event_cidzero_rti',
    'type' => 'Integer',
    'html_type' => 'select',
    'options_callback' => array(
      'class' => 'CRM_Contact_BAO_Relationship',
      'method' => 'getContactRelationshipType',
      'arguments' => array(NULL, NULL, NULL, NULL, TRUE),
    ),
    'add' => '1.0',
    'title' => 'Relationship for On Behalf Forms',
    'description' => 'Relationship type to create on related registrations',
    'help_text' => 'When cid=0 is in the url the registration is for someone else. The relationship will be created if the contact is new',
    'add_to_setting_form' => TRUE,
    'form_child_of_parents_parents_parent' => 'expiration_time',
    'required' => FALSE,
  ),
);