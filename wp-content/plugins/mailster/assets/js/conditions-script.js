mailster = (function (mailster, $, window, document) {

	"use strict";

	mailster.conditions = mailster.conditions || {};

	$.each($('.mailster-conditions'), function () {

		var _self = $(this),
			conditions = _self.find('.mailster-conditions-wrap'),
			groups = _self.find('.mailster-condition-group'),
			cond = _self.find('.mailster-condition');

		groups.eq(0).appendTo(_self.find('.mailster-condition-container'));

		!mailster.util.trim(conditions.html()) && conditions.empty();

		datepicker();

		_self
			.on('click', '.add-condition', function () {
				var id = groups.length,
					clone = groups.eq(0).clone();

				clone.removeAttr('id').appendTo(conditions).data('id', id).show();
				$.each(clone.find('input, select'), function () {
					var _this = $(this),
						name = _this.attr('name');
					_this.attr('name', name.replace(/\[\d+\]/, '[' + id + ']')).prop('disabled', false);
				});
				clone.find('.condition-field').val('').focus();
				datepicker();
				groups = _self.find('.mailster-condition-group');
				cond = _self.find('.mailster-condition');
			})
			.on('click', '.add-or-condition', function () {
				var cont = $(this).parent(),
					id = cont.find('.mailster-condition').last().data('id'),
					clone = cond.eq(0).clone();

				clone.removeAttr('id').appendTo(cont).data('id', ++id);
				$.each(clone.find('input, select'), function () {
					var _this = $(this),
						name = _this.attr('name');
					_this.attr('name', name.replace(/\[\d+\]\[\d+\]/, '[' + cont.data('id') + '][' + id + ']')).prop('disabled', false);
				});
				clone.find('.condition-field').val('').focus();
				datepicker();
				cond = _self.find('.mailster-condition');
			});

		conditions
			.on('click', '.remove-condition', function () {
				var c = $(this).parent();
				if (c.parent().find('.mailster-condition').length == 1) {
					c = c.parent();
				}
				c.slideUp(100, function () {
					$(this).remove();
					mailster.trigger('updateCount');
				});
			})
			.on('change', '.condition-field', function () {

				var condition = $(this).closest('.mailster-condition'),
					field = $(this).val(),
					operator_field, value_field;

				condition.find('div.mailster-conditions-value-field').removeClass('active').find('.condition-value').prop('disabled', true);
				condition.find('div.mailster-conditions-operator-field').removeClass('active').find('.condition-operator').prop('disabled', true);

				value_field = condition.find('div.mailster-conditions-value-field[data-fields*=",' + field + ',"]').addClass('active').find('.condition-value').prop('disabled', false);
				operator_field = condition.find('div.mailster-conditions-operator-field[data-fields*=",' + field + ',"]').addClass('active').find('.condition-operator').prop('disabled', false);

				if (!value_field.length) {
					value_field = condition.find('div.mailster-conditions-value-field-default').addClass('active').find('.condition-value').prop('disabled', false);
				}
				if (!operator_field.length) {
					operator_field = condition.find('div.mailster-conditions-operator-field-default').addClass('active').find('.condition-operator').prop('disabled', false);
				}

				if (!value_field.val()) {
					if (value_field.is('.hasDatepicker')) {
						value_field.datepicker("setDate", "yy-mm-dd");;
					}
				}

				mailster.trigger('updateCount');

			})
			.on('change', '.condition-operator', function () {
				mailster.trigger('updateCount');
			})
			.on('change', '.condition-value', function () {
				mailster.trigger('updateCount');
			})
			.on('click', '.mailster-condition-add-multiselect', function () {
				$(this).parent().clone().insertAfter($(this).parent()).find('.condition-value').select().focus();
				return false;
			})
			.on('click', '.mailster-condition-remove-multiselect', function () {
				$(this).parent().remove();
				mailster.trigger('updateCount');
				return false;
			})
			.on('change', '.mailster-conditions-value-field-multiselect > .condition-value', function () {
				if (0 == $(this).val() && $(this).parent().parent().find('.condition-value').size() > 1) $(this).parent().remove();
			})
			.on('click', '.mailster-rating > span', function (event) {
				var _this = $(this),
					_prev = _this.prevAll(),
					_all = _this.siblings();
				_all.removeClass('enabled');
				_prev.add(_this).addClass('enabled');
				_this.parent().parent().find('.condition-value').val((_prev.length + 1) / 5).trigger('change');
			})
			.find('.condition-field').prop('disabled', false).trigger('change');

		mailster.trigger('updateCount');

		function datepicker() {
			conditions.find('.datepicker').datepicker({
				dateFormat: 'yy-mm-dd',
				firstDay: mailster.l10n.conditions.start_of_week,
				showWeek: true,
				dayNames: mailster.l10n.conditions.day_names,
				dayNamesMin: mailster.l10n.conditions.day_names_min,
				monthNames: mailster.l10n.conditions.month_names,
				prevText: mailster.l10n.conditions.prev,
				nextText: mailster.l10n.conditions.next,
				showAnim: 'fadeIn',
			});
		}

	});

	return mailster;

}(mailster || {}, jQuery, window, document));