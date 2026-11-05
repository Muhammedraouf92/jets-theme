/**
 * Contact inquiry form: client-side validation + AJAX submit against
 * admin-ajax.php (inc/inquiries.php). Vanilla, no dependency — this isn't
 * one of the theme's GSAP/Swiper exceptions (PROJECT_CONTEXT.md §8).
 * Only loaded on the Contact page template (inc/enqueue.php).
 */
(function () {
  "use strict";

  var form = document.querySelector("[data-inquiry-form]");
  if (!form) {
    return;
  }

  var statusEl = form.querySelector("[data-form-status]");
  var submitBtn = form.querySelector('[type="submit"]');

  // Country-code dropdown on the phone field (self-hosted intl-tel-input,
  // inc/enqueue.php). Degrades to a plain text input — validation just
  // falls back to "non-empty" below — if the vendor script fails to load.
  var phoneField = form.querySelector('[name="phone"]');
  var phoneIti = null;
  if (phoneField && window.intlTelInput) {
    phoneIti = window.intlTelInput(phoneField, {
      // No geoIP auto-detect: that would mean silently calling a
      // third-party lookup service on every page load. The real Contact
      // address entered for this site is in Cairo, so Egypt is the
      // sensible fixed default rather than an arbitrary one.
      initialCountry: "eg",
      separateDialCode: true,
    });
  }

  function fieldWrapper(field) {
    return field.closest(".field");
  }

  function setFieldError(field, message) {
    var wrapper = fieldWrapper(field);
    if (!wrapper) {
      return;
    }
    wrapper.classList.add("field--error");
    field.setAttribute("aria-invalid", "true");
    var messageEl = wrapper.querySelector(".field__message");
    if (messageEl) {
      messageEl.textContent = message || "";
    }
  }

  function clearAllErrors() {
    form.querySelectorAll(".field--error").forEach(function (wrapper) {
      wrapper.classList.remove("field--error");
      var fieldEl = wrapper.querySelector(".field__input, .field__textarea");
      if (fieldEl) {
        fieldEl.removeAttribute("aria-invalid");
      }
      var messageEl = wrapper.querySelector(".field__message");
      if (messageEl) {
        messageEl.textContent = "";
      }
    });
  }

  // After a failed validation (client or server-side), sighted keyboard
  // users and screen-reader users alike land directly on the first
  // problem field instead of staying parked on the submit button with no
  // indication anything happened beyond the status region's text.
  function focusFirstInvalidField() {
    var firstInvalid = form.querySelector('[aria-invalid="true"]');
    if (firstInvalid) {
      firstInvalid.focus();
    }
  }

  function setStatus(type, message) {
    if (!statusEl) {
      return;
    }
    statusEl.textContent = message || "";
    statusEl.classList.remove("form__status--success", "form__status--error");
    if (type) {
      statusEl.classList.add("form__status--" + type);
    }
    statusEl.hidden = !message;
  }

  function validate() {
    var valid = true;
    clearAllErrors();

    var nameField = form.querySelector('[name="name"]');
    if (nameField && !nameField.value.trim()) {
      setFieldError(nameField, nameField.dataset.errorRequired);
      valid = false;
    }

    var emailField = form.querySelector('[name="email"]');
    if (emailField) {
      var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailPattern.test(emailField.value.trim())) {
        setFieldError(emailField, emailField.dataset.errorInvalid);
        valid = false;
      }
    }
    if (phoneField) {
      if (!phoneField.value.trim()) {
        setFieldError(phoneField, phoneField.dataset.errorRequired);
        valid = false;
      } else if (phoneIti && !phoneIti.isValidNumber()) {
        setFieldError(phoneField, phoneField.dataset.errorInvalid);
        valid = false;
      }
    }

    var messageField = form.querySelector('[name="message"]');
    if (messageField && !messageField.value.trim()) {
      setFieldError(messageField, messageField.dataset.errorRequired);
      valid = false;
    }

    return valid;
  }

  form.addEventListener("submit", function (event) {
    event.preventDefault();

    if (!validate()) {
      setStatus("error", form.dataset.errorSummary);
      focusFirstInvalidField();
      return;
    }

    setStatus(null, "");
    if (submitBtn) {
      submitBtn.disabled = true;
    }

    // Submit the full E.164 number (e.g. "+201111653752"), not just
    // whatever digits were typed, so it stores/displays consistently
    // regardless of which country's format the visitor typed in.
    if (phoneIti && phoneField && phoneField.value.trim()) {
      phoneField.value = phoneIti.getNumber();
    }

    var data = new FormData(form);
    data.append("action", "jets_submit_inquiry");

    var ajaxUrl = window.jetsFormsData
      ? window.jetsFormsData.ajaxUrl
      : form.action;

    fetch(ajaxUrl, {
      method: "POST",
      credentials: "same-origin",
      body: data,
    })
      .then(function (response) {
        return response.json();
      })
      .then(function (response) {
        if (response && response.success) {
          setStatus("success", response.data && response.data.message);
          form.reset();
        } else {
          var fieldErrors = response && response.data && response.data.fields;
          if (fieldErrors) {
            Object.keys(fieldErrors).forEach(function (key) {
              var field = form.querySelector('[name="' + key + '"]');
              if (field) {
                setFieldError(field, fieldErrors[key]);
              }
            });
            focusFirstInvalidField();
          }
          setStatus(
            "error",
            response && response.data && response.data.message,
          );
        }
      })
      .catch(function () {
        setStatus("error", form.dataset.errorNetwork);
      })
      .finally(function () {
        if (submitBtn) {
          submitBtn.disabled = false;
        }
      });
  });
})();
