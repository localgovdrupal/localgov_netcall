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
    // Add textarea for extra pages to override the default
    $form['partition_overrides'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Partition Overrides'),
      '#default_value' => $this->config('localgov_netcall_ai_widget.settings')->get('partition_overrides'),
      '#description' => $this->t('
        <p>Add overrides here, in the format of <strong>partition_id|path</strong>, one per line.</p>
        <p>For example:</p>
        <ul>
          <li>123|/news</li>
          <li>123|/news/*</li>
          <li>456|/events</li>
          <li>789|services/*</li>
        </ul>
        <p>
          The above will mean that partition <strong>123</strong> will be used
          on the <strong>/news</strong> page and also any other pages under
          <strong>news</strong>, and partition <strong>456</strong> will be used
          on the <strong>/events</strong> page <em>only</em>, and
          <strong>789</strong> will appear on all pages under
          <strong>/services</strong> <em>but not</em> services itself.
        </p>
      '),
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
      ->set('partition_overrides', $form_state->getValue('partition_overrides'))
      ->save();
    parent::submitForm($form, $form_state);
  }

}
