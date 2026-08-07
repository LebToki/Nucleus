/*!
 * jQuery WhatsApp
 * Created by 2TInteractive - https://2tinteractive.com
 * Version: 2.0.0
 */
(function ($) { // Define the WhatsAppSender plugin
	$.fn.whatsAppSenderForm = function (options) {

		// Extend default options
		// Function to check if no number is present
		var settings = $.extend(true, {}, $.fn.whatsAppSenderForm.defaultOptions, options);
		if (!options.whatsAppNumber) {

			throw new Error("Error: Missing required option 'whatsAppNumber'");

		}

		// Iterate through each form
		return this.each(function () {

			var $form = $(this);

			// Capture form submit event
			$form.on("submit", function (e) {

				e.preventDefault(); // Prevent default form submission behavior

				var formData = $form.serializeArray(); // Serialize form data
				var textToSend = prepareMessageText(formData, $form, settings);
				// Prepare message text

				// Prepare API URL
				var apiURL = "https://api.whatsapp.com/send?phone=" + settings.whatsAppNumber + "&text=" + encodeURIComponent(textToSend);

				// Replace form content with submission message
				$form.html(settings.submissionMessage);

				// Open WhatsApp in new window or redirect to WhatsApp
				if (settings.openInNewWindow) {

					window.open(apiURL);

				} else {

					window.location.href = apiURL;

				}

			});

		});

	};

	// Default options for whatsAppSenderForm plugin
	$.fn.whatsAppSenderForm.defaultOptions = {

		whatsAppNumber: "", // WhatsApp number to send the message
		openInNewWindow: false, // Whether to open WhatsApp in a new window
		submissionMessage: "Submitting your message...", // Message to display after form submission

	};

	// Function to prepare message text
	function prepareMessageText(formData, $form, settings) {

		var textToSend = "```\n";
		var formTitle = $.trim($form.data("title"));

		// Add form title to message text if present
		if (formTitle) {

			textToSend += formTitle + "\n\n";

		}

		// Iterate through form data and add each field to the message text
		$.each(formData, function (index, element) {

			var label = $.trim($(element).data("label")) || $.trim($("[name='" + element.name + "']").prop("placeholder")) || $.trim($("[name='" + element.name + "']").parent().data("label")) || element.name;
			textToSend += label + "  :  " + $.trim(element.value) + "\n";

		});

		// Add divider after form items
		textToSend += "\n--------------------------------\n";
		textToSend += "```";

		return textToSend;

	}
})(jQuery);
