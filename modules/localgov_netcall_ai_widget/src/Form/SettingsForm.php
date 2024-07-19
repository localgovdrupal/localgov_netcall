<?php

declare(strict_types = 1);

namespace Drupal\localgov_netcall_ai_widget\Form;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configure Content Access settings for this site.
 */
final class SettingsForm extends ConfigFormBase {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */

  protected $entityTypeManager;

  /**
   * Constructs a new SettingsForm.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'localgov_netcall_ai_widget_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['localgov_netcall_ai_widget.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    // Add text field for workspace ID.
    $form['workspace_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Workspace ID'),
      '#default_value' => $this->config('localgov_netcall_ai_widget.settings')->get('workspace_id'),
      '#description' => $this->t('The workspace ID for the Netcall AI Widget.'),
    ];
    // Add text field for partition ID.
    $form['partition_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Partition ID'),
      '#default_value' => $this->config('localgov_netcall_ai_widget.settings')->get('partition_id'),
      '#description' => $this->t('The partition ID for the Netcall AI Widget.'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->config('localgov_netcall_ai_widget.settings')
      ->set('workspace_id', $form_state->getValue('workspace_id'))
      ->set('partition_id', $form_state->getValue('partition_id'))
      ->save();
    parent::submitForm($form, $form_state);
  }

}
