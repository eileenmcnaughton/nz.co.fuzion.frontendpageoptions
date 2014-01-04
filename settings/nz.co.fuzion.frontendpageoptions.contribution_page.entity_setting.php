<?php

return array (
  array(
    'key' => 'nz.co.fuzion.frontendpageoptions',
    'entity' => 'contribution_page',
    'name' => 'contribution_page_thankyou_redirect',
    'type' => 'String',
    'html_type' => 'text',
    'add' => '1.0',
    'title' => 'Thank You page Redirect',
    'description' => 'Page to redirect to instead of the normal Thank You page',
    'help_text' => 'Please enter the full or relative url including http for full',
    'add_to_setting_form' => TRUE,
    'form_child_of_parents_parent' => 'is_confirm_enabled',
  ),
  array(
    'key' => 'nz.co.fuzion.frontendpageoptions',
    'entity' => 'contribution_page',
    'name' => 'contribution_page_cidzero_relationship_type_id',
    'type' => 'Integer',
    'html_type' => 'select',
    'options_callback' => array(
      'class' => 'CRM_Contact_BAO_Relationship',
      'method' => 'getContactRelationshipType',
      'arguments' => array(NULL, NULL, NULL, NULL, TRUE),
    ),
    'add' => '1.0',
    'title' => 'Relationship for On Behalf Forms',
    'description' => 'Relationship type to create on related contributions or memberships',
    'help_text' => 'When cid=0 is in the url the payment is for someone else. The relationship will be created if the contact is new',
    'add_to_setting_form' => TRUE,
    'form_child_of_parents_parents' => 'is_confirm_enabled',
    'required' => FALSE,
  ),
);