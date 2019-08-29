$(document).ready(function ()
{

	// UPDATE CHECKLIST STATUS
	$("#update-check-list-status").on("submit", function (e)
	{
		e.preventDefault();

		var thisForm = $(this);

		var statusClass = $(thisForm).attr("class");

		var requestUrl = $(thisForm).attr("action");

		var submitBnt = $(thisForm).find("input[type='submit']");

		$.ajax({
			type: 'POST',
			url: requestUrl + "&" + $(thisForm).serialize(),
			success: function (data)
			{
				if (data)
				{
					if (data.status == 'not_saved')
					{
						$(submitBnt).val("feil ved lagring");
						if (data.message)
						{
							if (data.error === 'missing_billable_hours')
							{
								var check_list_id = $(thisForm).find("input[name='check_list_id']").val();
								add_billable_hours(check_list_id);
							}
							else
							{
								alert(data.message);
							}
						}

					}
					else if (data.status == '1')
					{
						$(submitBnt).val("Utført");
						$("#update-check-list-status-value").val(0);
						//          $("#update-check-list-status-icon.not_done").hide();
						//        	$("#update-check-list-status-icon-done.done").show();
					}
					else
					{
						$(submitBnt).val("Sett status: Utført");
						$("#update-check-list-status-value").val(1);
						//         $("#update-check-list-status-icon.not_done").show();
						//         $("#update-check-list-status-icon-done.done").hide();
					}
				}
			}
		});
	});
});

var alertify;

/**
 * Open a prompt for input
 * @param {type} id
 * @param {type} location_id
 * @param {type} attribute_id
 * @param {type} input_text
 * @param {type} lang_new_value
 * @returns {undefined}
 */

function add_billable_hours(check_list_id)
{
	var oArgs = {
		menuaction: 'controller.uicheck_list.add_billable_hours',
		check_list_id: check_list_id
	};
	var requestUrl = phpGWLink('index.php', oArgs, true);
	//	var new_value = prompt(input_text, "");

	/*
	 * @title {String or DOMElement} The dialog title.
	 * @message {String or DOMElement} The dialog contents.
	 * @value {String} The default input value.
	 * @onok {Function} Invoked when the user clicks OK button.
	 * @oncancel {Function} Invoked when the user clicks Cancel button or closes the dialog.
	 *
	 * alertify.prompt(title, message, value, onok, oncancel);
	 *
	 */
	alertify.prompt('input_text', 'lang_new_value', ''
		, function (evt, value)
		{
			var new_value = value;
			if (new_value !== null && new_value !== "")
			{
				var xmlhttp = new XMLHttpRequest();
				xmlhttp.onreadystatechange = function ()
				{
					if (this.readyState == 4 && this.status == 200)
					{
						var data = JSON.parse(this.responseText);

						if (data.status == 'ok')
						{
							alertify.success('You entered: ' + value);
							
						}
						else
						{
							alertify.error('Error');
						}
					}
				};
				xmlhttp.open("POST", requestUrl, true);
				xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				var params = 'billable_hours=' + new_value;
				xmlhttp.send(params);
			}
			else
			{
				alertify.error('Cancel');
			}
		}
	, function ()
	{
		alertify.error('Cancel')
	});

}