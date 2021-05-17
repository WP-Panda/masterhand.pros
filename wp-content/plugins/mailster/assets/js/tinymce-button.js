jQuery(document).ready(function ($) {

	"use strict"

	var l10n = mailster_mce_button.l10n,
		forms = mailster_mce_button.forms,
		designs = mailster_mce_button.designs;

	tinymce.PluginManager.add('mailster_mce_button', function (editor, url) {
		editor.addButton('mailster_mce_button', {
			title: l10n.title,
			type: 'menubutton',
			icon: 'icon mailster-shortcodes-icon',
			menu: [

				/* Forms */
				{
					text: l10n.forms,
					menu: $.map(forms, function (name, id) {
						return {
							text: name,
							onclick: function () {
								editor.insertContent('[newsletter_signup_form id=' + id + ']');
							}
						};
					})
				}, // End Forms


				/* Newsletter Homepage */
				{
					text: l10n.homepage.menulabel,
					onclick: function () {
						editor.windowManager.open({
							title: l10n.homepage.title,
							body: [{
								type: 'textbox',
								name: 'pre',
								label: l10n.homepage.prelabel,
								value: l10n.homepage.pre
							}, {
								type: 'textbox',
								name: 'confirm',
								label: l10n.homepage.confirmlabel,
								value: l10n.homepage.confirm
							}, {
								type: 'textbox',
								name: 'unsub',
								label: l10n.homepage.unsublabel,
								value: l10n.homepage.unsub
							}, {
								type: 'listbox',
								name: 'form',
								label: l10n.form,
								'values': $.map(forms, function (name, id) {
									return {
										text: name,
										value: id
									};
								})
							}],
							onsubmit: function (e) {
								editor.insertContent('[newsletter_signup]' + e.data.pre + '[newsletter_signup_form id=' + e.data.form + '][/newsletter_signup][newsletter_confirm]' + e.data.confirm + '[/newsletter_confirm][newsletter_unsubscribe]' + e.data.unsub + '[/newsletter_unsubscribe]');
							}
						});
					}
				}, // End Newsletter Homepage


				/* Newsletter Homepage */
				{
					text: l10n.button.menulabel,
					onclick: function () {
						editor.windowManager.open({
							title: l10n.button.title,
							body: [{
								type: 'textbox',
								name: 'label',
								label: l10n.button.labellabel,
								value: l10n.button.label
							}, {
								type: 'checkbox',
								name: 'count',
								label: ' ',
								text: l10n.button.count,
								value: 1
							}, {
								type: 'checkbox',
								name: 'countabove',
								label: ' ',
								text: l10n.button.countabove,
								value: 1
							}, {
								type: 'listbox',
								name: 'design',
								label: l10n.button.design,
								'values': $.map(designs, function (name, id) {
									return {
										text: name,
										value: id
									};
								})
							}, {
								type: 'listbox',
								name: 'form',
								label: l10n.form,
								'values': $.map(forms, function (name, id) {
									return {
										text: name,
										value: id
									};
								})
							}],
							onsubmit: function (e) {
								var code = '[newsletter_button id=' + e.data.form;
								code += ' label="' + e.data.label + '"';
								if (e.data.count) code += ' showcount="true"';
								if ('default' != e.data.design || e.data.countabove)
									code += ' design="' + e.data.design + (e.data.countabove ? ' ontop' : '') + '"';
								code += ']';
								editor.insertContent(code);
							}
						});
					}
				} // End Newsletter Homepage

			]
		});
	});
});