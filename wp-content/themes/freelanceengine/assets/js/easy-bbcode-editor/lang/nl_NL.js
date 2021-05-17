/*
  >> Credits
    >> Author: Paul.
    >> Contact Link: Paul.#0933 (Discord Tag)
*/
(function($) {
	$.extend($.editor_lang, {
		'nl-NL': {
			font: {
				bold: 'Dik gedrukt',
				italic: 'Schuin',
				underline: 'Onderstreept',
				strikethrough: 'Doorstreept',
				supperscript: 'Superscript',
				subscript: 'Onderscript',
				font_name: 'Letter type',
				font_size: 'Letter grootte',
				color: {
					button: 'Kleur',
					modal: 'Kleurenwiel',
					input: 'Kleurenwiel invoer'
				}
			},
			text: {
				unordered_list: 'Ongesorteerde lijst',
				ordered_list: 'Gesorteerde lijst',
				align: {
					button: 'Uitlijnen',
					left: 'Links',
					center: 'Midden',
					right: 'Rechts'
				}
			},
			inserts: {
				link: {
					button: 'Link',
					modal: 'Voeg link in',
					text: 'Voeg link tekst in',
					insert: 'Voeg link in',
				},
				image: {
					button: 'Foto',
					site: 'Als je op zoek bent naar een website voor het uploaden van fotos klik dan <a href="https://imgur.com/" class="alert-link" target="_blank">hier</a>.',
					modal: 'Voeg foto in',
					url: 'Url',
					insert_url: 'Voeg url in',
					upload: 'Uploaden',
					choose_file: 'Kies een bestand'
				},
				media: {
					button: 'Media',
					modal: 'Voeg media in',
					insert_url: 'Voeg media url in'
				},
				misc: {
					button: 'Diversen',
					modal: 'Diversen',
				},
				advcode: {
					button: 'Gevorderde code',
					modal: 'Gevorderde code'
				},
				table: {
					button: 'Tabel',
					modal: 'Voeg tabel in',
					rows: 'Tabel rijen',
					cols: 'Tabel kolommen'
				}
			},
			preview: {
				button: 'Voorbeeld',
				modal: 'Voorbeeld'
			},
			others: {
				blockquote: 'Blockquote',
				code: 'Makkelijke code',
				spoiler: 'Spoiler',
				linebreak: 'Lijn breuk',
				created_by: 'Editor gemaakt door'
			},
			insert: 'Invoegen',
			other: 'Andere',
			errors: {
			  selector: 'Kies een goede waarde a.u.b!',
			  invalid_value: "Ongeldige waarde voor de optie '{option}'!",
			  lang: 'Kies een taal voor de bewerker a.u.b!',
			  icons: 'Kies een iconen set voor de bewerker a.u.b!',
			  height: 'Kies een hoogte voor de bewerker a.u.b!'
			}
		}
	});
})(jQuery);
