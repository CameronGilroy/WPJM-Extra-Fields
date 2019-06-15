<?php
/**
 * Plugin Name: WPJM Custom Fields
 * Description: Adds an extra Submission Deadline and Submission Instructions fields to WP Job Manager job listings
 * Version: 1.0.0
 * Author: Cameron Gilroy
 * Author URI: http://celestia.design/
 * Text Domain: wpjm-custom-fields
 * Domain Path: /languages
 *
 * License: GPLv2 or later
 */

/**
 * Prevent direct access data leaks
 **/
if (!defined('ABSPATH')) {
  exit;
}

// if ( ! class_exists( 'WP_Job_Manager' )) {

//   add_action('admin_notices', 'gma_wpjmef_admin_notice__error');
// } else {

  // add_action('plugin_action_links_' . plugin_basename(__FILE__), 'gma_wpjmef_add_support_link_to_plugin_page');

  add_filter('submit_job_form_fields', 'gma_wpjmef_frontend_add_salary_field');
  add_filter('submit_job_form_fields', 'gma_wpjmef_frontend_add_important_info_field');

  add_filter('job_manager_job_listing_data_fields', 'gma_wpjmef_admin_add_salary_field');
  add_filter('job_manager_job_listing_data_fields', 'gma_wpjmef_admin_add_important_info_field');

  add_action('single_job_listing_meta_end', 'gma_wpjmef_display_job_submission_deadline_data');
  add_action('single_job_listing_meta_end', 'gma_wpjmef_display_important_info_data');
// }

function custom_submit_job_form_fields_twitter( $fields ) {
    // in this example, we remove the job_tags field
    unset($fields['company']['company_twitter']);

    // And return the modified fields
    return $fields;
}

/**
 * Adds a direct support link under the Plugins Page once the plugin is activated
 **/
function gma_wpjmef_add_support_link_to_plugin_page($links)
{

  $links = array_merge(array(
    '<a href="https://wordpress.org/support/plugin/wpjm-custom-fields" target="_blank">' . __('Support', 'wpjm-custom-fields') . '</a>'
  ), $links);
  return $links;
}

/**
 * Adds a new optional "Submission Deadline" text field at the "Submit a Job" form, generated via the [submit_job_form] shortcode
 **/
function gma_wpjmef_frontend_add_salary_field($fields)
{

  $fields['job']['job_submission_deadline'] = array(
    'label'       => __('Submission Deadline', 'wpjm-custom-fields'),
    'type'        => 'text',
    'required'    => false,
    'placeholder' => '12/23/2019 or ASAP',
    'description' => '',
    'priority'    => 7,
  );

  return $fields;
}

/**
 * Adds a new optional "Submission Instructions" text field at the "Submit a Job" form, generated via the [submit_job_form] shortcode
 **/
function gma_wpjmef_frontend_add_important_info_field($fields)
{

  $fields['job']['job_Ssbmission_instructions'] = array(
    'label'       => __('Submission Instructions: ', 'wpjm-custom-fields'),
    'type'        => 'textarea',
    'required'    => false,
    'placeholder' => 'e.g. Work visa required',
    'description' => '',
    'priority'    => 8,
  );

  return $fields;
}

/**
 * Adds a text field to the Job Listing wp-admin meta box named “Submission Deadline”
 **/
function gma_wpjmef_admin_add_salary_field($fields)
{

  $fields['_job_submission_deadline'] = array(
    'label'       => __('Submission Deadline', 'wpjm-custom-fields'),
    'type'        => 'text',
    'placeholder' => '12/23/2019 or ASAP',
    'description' => ''
  );

  return $fields;
}

/**
 * Adds a text field to the Job Listing wp-admin meta box named "Submission Instructions"
 **/
function gma_wpjmef_admin_add_important_info_field($fields)
{

  $fields['_job_submission_instructions'] = array(
    'label'       => __('Submission Instructions', 'wpjm-custom-fields'),
    'type'        => 'textarea',
    'placeholder' => 'e.g. Work visa required',
    'description' => ''
  );

  return $fields;
}

/**
 * Displays "Submission Deadline" on the Single Job Page, by checking if meta for "_job_submission_deadline" exists and is displayed via do_action( 'single_job_listing_meta_end' ) on the template
 **/
function gma_wpjmef_display_job_submission_deadline_data()
{

  global $post;

  $salary = get_post_meta($post->ID, '_job_submission_deadline', true);
  $important_info = get_post_meta($post->ID, '_job_submission_instructions', true);

  if ($salary) {
    echo '<li>' . __('Submission Deadline: ') . esc_html($salary) . '</li>';
  }
}

/**
 * Displays the content of the "Submission Instructions" text-field on the Single Job Page, by checking if meta for "_job_submission_instructions" exists and is displayed via do_action( 'single_job_listing_meta_end' ) on the template
 **/
function gma_wpjmef_display_important_info_data()
{

  global $post;

  $important_info = get_post_meta($post->ID, '_job_submission_instructions', true);

  if ($important_info) {
    echo '<li>' . esc_html($important_info) . '</li>';
  }
}

/**
 * Display an error message notice in the admin if WP Job Manager is not active
 */
function gma_wpjmef_admin_notice__error()
{

  $class = 'notice notice-error';
  $message = __('An error has occurred. WP Job Manager must be installed in order to use this plugin', 'wpjm-custom-fields');

  printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
}
