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
);