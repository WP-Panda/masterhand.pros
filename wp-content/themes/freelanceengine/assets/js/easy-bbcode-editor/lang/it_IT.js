/*
  >> Credits
    >> Author: Nexgan
    >> Contact Link: Nexgan#4788 (Discord Tag)
*/
(function ($) {
	$.extend($.editor_lang, {
		'it-IT': {
			font: {
				bold: 'Grassetto',
				italic: 'Corsivo',
				underline: 'Sottolinea',
				strikethrough: 'Barrato',
				supperscript: 'Apice',
				subscript: 'Pedice',
				font_name: 'Carattere',
				font_size: 'Dimensione carattere',
				color: {
					button: 'Colore',
					modal: 'Color Picker'
				}
			},
			text: {
				unordered_list: 'Lista non ordinata',
				ordered_list: 'Lista ordinata',
				align: {
					button: 'Allinea',
					left: 'Sinistra',
					center: 'Centro',
					right: 'Destra'
				}
			},
			inserts: {
				link: {
					button: 'Link',
					modal: 'Inserisci link',
					target: 'Apri in una nuova finestra'
				},
				image: {
					button: 'Immagine',
					site: 'Se stai cercando un sito web in cui puoi caricare immagini e inserirle qui, fai click <a href="https://imgur.com/" class="alert-link" target="_blank">qui</a>.',
					modal: 'Inserisci Immagine'
				},
				media: {
					button: 'Media',
					modal: 'Inserisci Media'
				},
				misc: {
					button: 'Varie',
					modal: 'Varie',
				},
				advcode: {
					button: 'Codice avanzato',
					modal: 'Codice avanzato'
				},
				table: {
					button: 'Tabella',
					modal: 'Inserisci Tabella',
					rows: 'Righe della tabella',
					cols: 'Colonne della tabella'
				}
			},
			preview: {
				button: 'Anteprima',
				modal: 'Anteprima'
			},
			others: {
				blockquote: 'Blockquote',
				code: 'Codice semplice',
				spoiler: 'Spoiler',
				linebreak: 'Interruzione di linea',
				created_by: 'Editor creato da'
			},
			insert: 'Inserire',
			other: 'Altro',
			errors: {
				selector: 'Si prega di specificare un selettore',
				invalid_value: "Valore non valido per opzione '{option}'!",
				lang: 'Por favor, especifique um idioma para o editor!',
				icons: "Si prega di selezionare un'icona per l'editor",
				height: "Si prega di specificare l'altezza per il publisher",
				width: 'Si prega di specificare la larghezza per il publisher',
				max_characters: "Il valore per l'opzione 'maxCharacters' deve essere maggiore di 0!",
				theme: 'Specifica un tema per o un editor di codice avanzato!'
			}
		}
	});
})(jQuery);
