/*!
  jQuery WhatsApp
  Created by 2TInteractive - https://2tinteractive.com
  Ver. 2.0.0
*/
// whatsAppAnyForm
// whatsAppAnyForm function starts
$.fn.whatsAppAnyForm = function (options) {
    // extend default options
    options = $.extend(true, {}, $.whatsAppAnyForm.defaultOptions, options);
    // iterate through plugin
    new $.whatsAppAnyForm(this, options);
    // This form
    var $thisForm = $(this),
        // Misc Function
        fnMisc = {
            /**
             * Trim Function
             *
             * @param   string  text  string to trim
             *
             * @return  string
             */
            trim: function (text) {
                if (text) {
                    return text.trim();
                }
                return text;
            },
        };

    /**
     * Capture form submit method
     */
    $thisForm.on("submit", function (e) {
        // prevent default form behavior
        e.preventDefault();
        // text to send holder
        var textToSend = "``` ",
            // targeted form
            $form = $(e.target),
            // serialized array for the submitted form
            formData = $form.serializeArray(),
            // get form title for whatsapp message
            formTitle = fnMisc.trim($form.data("title"));
        // check if form is valid
        if (!$form.valid()) {
            return false;
        }
        // if form title present prepend it
        if (formTitle) {
            textToSend += formTitle + "\n\n";
        }
        // go through the each item of the submitted form
        for (var formItem in formData) {
            if (Object.hasOwnProperty.call(formData, formItem)) {
                // form value item
                var element = formData[formItem],
                    // label for the value in message from
                    // data-label attribute
                    dataLabel = fnMisc.trim($(element).data("label"));
                // If not present in the above attribute
                // see check if there is any placeholder set to use
                if (!dataLabel) {
                    dataLabel = fnMisc.trim(
                        $('[name="' + element.name + '"]').prop("placeholder")
                    );
                }
                // If not present in the above attribute
                // see if the parent has the data-label attribute so we can use that
                if (!dataLabel) {
                    dataLabel = fnMisc.trim(
                        $('[name="' + element.name + '"]')
                            .parent()
                            .data("label")
                    );
                }
                if (!dataLabel) {
                    dataLabel = fnMisc.trim(
                        $('[name="' + element.name + '"]')
                            .parents("[data-label]")
                            .data("label")
                    );
                }
                // If still present in the all above items
                // set the name of the field value as label
                if (!dataLabel) {
                    dataLabel = element.name;
                }
                // prepare message for each form item
                textToSend += dataLabel + "  :  " + fnMisc.trim(element.value) + "\n";
            }
        }
        // Add divider after form items
        textToSend += "\n--------------------------------\n";
        // closing of the message
        textToSend += "```";

        // preparing api url
        var textWithApi =
            "https://api.whatsapp.com/send?phone=" +
            options.whatsAppNumber +
            "&text=" +
            encodeURIComponent(textToSend);

        $form.html(options.submissionMessage);
        // check if it needs to be open in new window
        if (options.openInNewWindow === true) {
            // open in new window to submit message via WhatsApp
            window.open(textWithApi);
        } else {
            // redirect user to submit message via WhatsApp
            location.href = textWithApi;
        }
    });
};
(jQuery);
