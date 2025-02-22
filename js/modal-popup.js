(function (Drupal, once) {
  Drupal.behaviors.modalPopup = {
    attach: function (context, settings) {
      // Loop through all modal popup settings defined in drupalSettings.
      Object.keys(settings.modal_popup || {}).forEach((id) => {
        const modalSettings = settings.modal_popup[id];
        const triggers = once('modal-popup-trigger', context.querySelectorAll(`[data-dialog-target="#${id}"]`));
        const contentContainer = context.querySelector(`#${id}`);

        // Ensure the content container exists.
        if (contentContainer) {
          // Attach click event to each trigger.
          triggers.forEach((trigger) => {
            trigger.addEventListener('click', function (event) {
              // Prevent default link behavior for <a> elements.
              if (trigger.tagName.toLowerCase() === 'a') {
                event.preventDefault();
              }

              // Extract the specific content for this modal.
              const content = contentContainer.innerHTML;
              const title = modalSettings.title || 'Modal';

              // content has html_entities encoded. Decode them.
              const parser = new DOMParser();
              const decodedContent = parser.parseFromString(content, 'text/html').body.textContent;

              // Encapsulate the content in a <div> element with a unique ID in
              // order for the dialog to properly encapsulate it and not strip
              // content.
              const div = document.createElement('div');
              div.innerHTML = decodedContent;
              div.id = id + '-content';

              modalSettings.dialogClass = modalSettings.dialogClass || '';
              modalSettings.title = modalSettings.title || title;
              modalSettings.width = modalSettings.width || 800;
              modalSettings.height = modalSettings.height || 600;
              modalSettings.modal = modalSettings.modal || true;
              modalSettings.closeOnEscape = modalSettings.closeOnEscape || true;

              const dialog = Drupal.dialog(div, modalSettings);

              // Attach custom behavior for clicking outside the modal (on overlay).
              dialog.showModal();

              const $overlay = document.querySelector('.ui-widget-overlay');
              if ($overlay) {
                $overlay.addEventListener('click', function () {
                  dialog.close(); // Close the dialog when overlay is clicked
                });
              }
            });
          });
        }
      });
    },
  };
})(Drupal, once);
