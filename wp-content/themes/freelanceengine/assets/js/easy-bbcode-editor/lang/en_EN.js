/*
  >> Credits
    >> Author: MrAnonymusz
    >> Contact Link: https://github.com/MrAnonymusz
*/
(function($) {
	$.editor_lang = $.extend('', {
		'en-EN': {
			font: {
				bold: 'Bold',
				italic: 'Italic',
				underline: 'Underline',
				strikethrough: 'Strikethrough',
				supperscript: 'Superscript',
				subscript: 'Subscript',
				font_name: 'Font Family',
				font_size: 'Font Size',
				color: {
					button: 'Color',
					modal: 'Color Picker',
					input: 'Color Picker Input'
				}
			},
			text: {
				unordered_list: 'Unordered List',
				ordered_list: 'Ordered List',
				align: {
					button: 'Align',
					left: 'Left',
					center: 'Center',
					right: 'Right'
				}
			},
			inserts: {
				link: {
					button: 'Link',
					modal: 'Insert Link',
					text: 'Insert Link Text',
					insert: 'Insert Link',
				},
				image: {
					button: 'Image',
					site: 'If you are looking for a website where you can upload images then insert them here please click <a href="https://imgur.com/" class="alert-link" target="_blank">here</a>.',
					modal: 'Insert Image',
					url: 'Url',
					insert_url: 'Insert Url',
					upload: 'Upload',
					choose_file: 'Choose file'
				},
				media: {
					button: 'Media',
					modal: 'Insert Media',
					insert_url: 'Insert Media Url'
				},
				misc: {
					button: 'Misc',
					modal: 'Misc',
				},
				advcode: {
					button: 'Advanced Code',
					modal: 'Advanced Code'
				},
				table: {
					button: 'Table',
					modal: 'Insert Table',
					rows: 'Table Rows',
					cols: 'Table Columns'
				}
			},
			preview: {
				button: 'Preview',
				modal: 'Preview'
			},
			others: {
				blockquote: 'Blockquote',
				code: 'Simple Code',
				spoiler: 'Spoiler',
				linebreak: 'Linebreak',
				created_by: 'Editor created by'
			},
			insert: 'Insert',
			other: 'Other',
			errors: {
				selector: 'Please specify a selector!',
				invalid_value: "Invalid value for the option '{option}'!",
				lang: 'Please specify a language for the editor!',
				icons: 'Please specify an icon set for the editor!',
				height: 'Please specify a height for the editor!'
			}
		}
	});
})(jQuery);