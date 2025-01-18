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

              // Initialize the Drupal dialog for this trigger.
              const dialog = Drupal.dialog(`<div>${content}</div>`, {
                title: title,
                width: modalSettings.options?.width || 800,
                height: modalSettings.options?.height || 600,
                dialogClass: modalSettings.options?.dialogClass || '',
                modal: true, // Ensure modal behavior
                closeOnEscape: true, // Allow closing on Escape key press
              });

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
