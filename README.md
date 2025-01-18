## INTRODUCTION

The Modal Popup module provides a modal popup that can be triggered by a link
or a button. The content of the modal popup is a render array.

Additionally, the module provides a text formatter that can be used to render
a link that triggers the modal popup that shows the contents of the field.

## REQUIREMENTS

No requirements. The module only relies in Drupal core and field API.

## INSTALLATION

Install the modal through composer.

## USAGE

1. Enable the module.
2. In your form, you may add the following:

  ```php
  $build['modal_popup'] = [
    '#type' => 'modal_popup',
    '#display_text' => 'Open Modal Popup',
    '#title' => 'Modal Popup Title',
    '#content' => [
      '#markup' => 'Modal Popup Content',
    ],
  ];
  ```

For the field formatter, simply go to the display settings of the field and
select the "Modal popup" formatter.
