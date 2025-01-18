<?php

declare(strict_types=1);

namespace Drupal\modal_popup\Element;

use Drupal\Component\Utility\Html;
use Drupal\Core\Render\Attribute\RenderElement;
use Drupal\Core\Render\Element\RenderElementBase;
use Drupal\Core\Url;

/**
 * Provides a render element to display a modal popup.
 *
 * Properties:
 * - #display_text: The text to display as a trigger for the modal popup.
 * - #trigger_element_type: The type of element to use as the trigger. Defaults
 * to 'button'.
 * - #trigger_element_attributes: An array of attributes to be applied to the
 * trigger element.
 * - #title: The title of the modal popup.
 * - #content: The content of the modal popup. Can be a render array or a
 * string.
 * - #attributes: An array of attributes to be applied to the modal popup.
 * - #options: An array of options passed to the AJAX dialog.
 *
 * Usage Example:
 * @code
 * $build['modal_popup'] = [
 *   '#type' => 'modal_popup',
 *   '#display_text' => 'Open Modal Popup',
 *   '#title' => 'Modal Popup Title',
 *   '#content' => [
 *     '#markup' => 'Modal Popup Content',
 *     '#attributes' => [
 *       'class' => ['modal-popup-content'],
 *       'data-foo' => 'bar',
 *       'data-baz' => 'qux',
 *     ],
 *   ],
 *   '#attributes' => [
 *     'class' => ['modal-popup'],
 *   ],
 *   '#options' => [
 *     'width' => 800,
 *     'height' => 600,
 *     'dialogClass' => 'modal-popup-dialog',
 *   ],
 *   '#trigger_element_attributes' => [
 *     'class' => ['button', 'button--primary'],
 *   ],
 * ];
 * @endcode
 */
#[RenderElement('modal_popup')]
final class ModalPopup extends RenderElementBase {

  /**
   * {@inheritdoc}
   */
  public function getInfo(): array {
    return [
      '#pre_render' => [
        [self::class, 'preRenderEntityElement'],
      ],
      '#display_text' => '',
      '#trigger_element_type' => 'button',
      '#trigger_element_attributes' => [],
      '#title' => '',
      '#content' => '',
      '#attributes' => [],
      '#options' => [],
    ];
  }

  /**
   * Modal popup element pre render callback.
   *
   * @param array $element
   *   An associative array containing the properties of the modal_popup element.
   *
   * @return array
   *   The modified element.
   */
  public static function preRenderEntityElement(array $element): array {
    // Generate a unique ID for the modal popup.
    $id = Html::getUniqueId('modal-popup');
    $element['#id'] = $id;

    // Ensure attributes are correctly applied to the modal.
    $element['#attributes'] += [
      'class' => ['modal-popup'],
      'data-dialog-type' => 'modal',
      'data-dialog-options' => json_encode($element['#options']),
    ];

    // Set up the trigger element with the unique ID.
    $trigger_attributes = $element['#trigger_element_attributes'] ?? [];
    $trigger_attributes['data-dialog-target'] = '#' . $id;

    $trigger_element_type = $element['#trigger_element'] ?? 'button';
    $element['trigger'] = [
      '#type' => $trigger_element_type,
      '#attributes' => $trigger_attributes,
    ];

    if ($trigger_element_type === 'button') {
      $element['trigger']['#value'] = $element['#display_text'];
    } else {
      $element['trigger']['#title'] = $element['#display_text'];
      $element['trigger']['#url'] = Url::fromRoute('<current>');
    }

    // Set up the modal content.
    $element['content'] = [
      '#type' => 'container',
      '#attributes' => [
        'id' => $id,
        'class' => ['modal-content'],
        'style' => 'display: none;', // Hide the modal content initially
      ],
      'content' => $element['#content'],
    ];

    // Attach the library and settings.
    $element['#attached']['library'][] = 'modal_popup/modal_popup';
    $element['#attached']['drupalSettings']['modal_popup'][$id] = [
      'title' => $element['#title'],
      'content' => $element['#content'],
    ];

    return $element;
  }

}
