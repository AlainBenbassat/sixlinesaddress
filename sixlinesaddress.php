<?php

require_once 'sixlinesaddress.civix.php';
// phpcs:disable
use CRM_Sixlinesaddress_ExtensionUtil as E;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
// phpcs:enable

function sixlinesaddress_civicrm_container(ContainerBuilder $container) {
  $container->addResource(new FileResource(__FILE__));
  $container->findDefinition('dispatcher')->addMethodCall('addListener',
    ['civi.token.list', 'sixlinesaddress_register_tokens']
  )->setPublic(TRUE);
  $container->findDefinition('dispatcher')->addMethodCall('addListener',
    ['civi.token.eval', 'sixlinesaddress_evaluate_tokens']
  )->setPublic(TRUE);
}

function sixlinesaddress_register_tokens(\Civi\Token\Event\TokenRegisterEvent $e) {
  $e->entity('contact')
    ->register('sixlinesaddress', ts('Six Lines Address'));
}

function sixlinesaddress_evaluate_tokens(\Civi\Token\Event\TokenValueEvent $e) {
  foreach ($e->getRows() as $row) {
    /** @var TokenRow $row */
    $row->format('text/html');
    $row->tokens('contact', 'sixlinesaddress',  CRM_Sixlinesaddress_Helper::get($row->context['contactId']));
  }
}

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function sixlinesaddress_civicrm_config(&$config): void {
  _sixlinesaddress_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function sixlinesaddress_civicrm_install(): void {
  _sixlinesaddress_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function sixlinesaddress_civicrm_enable(): void {
  _sixlinesaddress_civix_civicrm_enable();
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_preProcess
 */
//function sixlinesaddress_civicrm_preProcess($formName, &$form): void {
//
//}

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_navigationMenu
 */
//function sixlinesaddress_civicrm_navigationMenu(&$menu): void {
//  _sixlinesaddress_civix_insert_navigation_menu($menu, 'Mailings', [
//    'label' => E::ts('New subliminal message'),
//    'name' => 'mailing_subliminal_message',
//    'url' => 'civicrm/mailing/subliminal',
//    'permission' => 'access CiviMail',
//    'operator' => 'OR',
//    'separator' => 0,
//  ]);
//  _sixlinesaddress_civix_navigationMenu($menu);
//}
