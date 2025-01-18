<?php

declare(strict_types=1);

namespace Drupal\modal_popup\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\Attribute\FieldFormatter;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Plugin implementation of the 'Text in modal' formatter.
 */
#[FieldFormatter(
  id: 'modal_popup_text',
  label: new TranslatableMarkup('Modal popup'),
  field_types: ['string', 'string_long', 'text', 'text_long', 'text_with_summary'],
)]
final class ModalPopupTextFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings(): array {
    $settings['display_text'] = 'See more';
    $settings['title'] = '';
    $settings['trigger_element'] = 'button';
    return $settings + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state): array {
    $elements = parent::settingsForm($form, $form_state);

    $elements['display_text'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Display text'),
      '#description' => $this->t('The text to display as a trigger for the modal popup.'),
      '#default_value' => $this->getSetting('display_text'),
    ];

    $elements['title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#description' => $this->t('The title of the modal popup. Leave blank, to display the parent entity title.'),
      '#default_value' => $this->getSetting('title'),
    ];

    $elements['trigger_element'] = [
      '#type' => 'select',
      '#title' => $this->t('Trigger element'),
      '#description' => $this->t('The type of element to use as the trigger.'),
      '#options' => [
        'button' => $this->t('Button'),
        'link' => $this->t('Link'),
      ],
      '#default_value' => $this->getSetting('trigger_element'),
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary(): array {
    return [
      $this->t('Display text: @display_text', ['@display_text' => $this->getSetting('display_text')]),
      $this->t('Title: @title', ['@title' => $this->getSetting('title') ?: $this->t('Parent entity title')]),
      $this->t('Trigger element: @trigger_element', ['@trigger_element' => $this->getSetting('trigger_element')]),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $element = [];
    foreach ($items as $delta => $item) {
      if (empty($item->value)) {
        continue;
      }

      $element[$delta] = [
        '#type' => 'modal_popup',
        '#display_text' => $this->getSetting('display_text'),
        '#title' => $this->getSetting('title') ?: $item->getEntity()->label(),
        '#trigger_element' => $this->getSetting('trigger_element'),
      ];

      // If type is processed_text, then we need to render the processed text.
      if ($item->getFieldDefinition()->getType() === 'text_with_summary') {
        $element[$delta]['#content'] = [
          '#type' => 'processed_text',
          '#text' => $item->processed,
          '#format' => $item->format,
        ];
      }
      else {
        $element[$delta]['#content'] = [
          '#markup' => $item->value,
        ];
      }
    }
    return $element;
  }

}
