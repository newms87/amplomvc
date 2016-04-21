//ampSelect jQuery Plugin
$.ampExtend($.ampSelect = function() {}, {
	instanceCount: 0,
	init:          function(o) {
		o = $.extend({
			style:            null, //modal, inline, checkboxes or null (for auto detect)
			source:           null, //object (eg: {url: 'http://example-source.com', query:{...}} ) or function
			sourceLabel:      null,
			sourceValue:      null,
			preloadSource:    true,
			selectOptions:    [],
			selectedValues:   null, //array of values, or null to use <input> / <select> values
			selectMultiple:   null, //true, false, or null (for auto detect),
			allowNewOptions:  true,
			showNewAsOption:  true,
			showNoneAsOption: null,
			allowRemove:      false,
			onRemove:         null,
			onSelect:         null,
			onCreateNew:      null
		}, {}, o);

		return this.use_once('amp-select-enabled').each(function() {
			var $field = $(this).removeClass('amp-select').addClass('amp-select-field'),
				$ampSelect = $("<div />").addClass('amp-select');

			if (o.source === null && !$field.is('select')) {
				return $.error("ampSelect Error: source not set and instance is not a <select> element");
			}

			o.style = o.style || $field.attr('data-amp-select-style') || ($field.is('input') ? 'inline' : 'modal');

			if (o.selectMultiple === null) {
				o.selectMultiple = o.style !== 'inline';
			}

			if (!o.allowNewOptions) {
				o.showNewAsOption = false;
			}

			if (o.showNoneAsOption === null) {
				o.showNoneAsOption = o.style === 'inline'
			}

			//Save Options to Instance
			$ampSelect.setOptions($.extend(true, {}, o, {
				placeholder:     $field.attr('data-placeholder') || 'Select Items...',
				optionGroupName: 'amp_option_' + $.ampSelect.instanceCount++
			}));

			//Build Template
			$field.before($ampSelect);
			$ampSelect.append($field);

			switch (o.style) {
				case 'modal':
					$ampSelect.ampSelect('initSelectModal');
					break;
				case 'checkboxes':
					$ampSelect.ampSelect('initCheckboxes');
					break;
				case 'inline':
				default:
					$ampSelect.ampSelect('initInline');
					break;
			}

			$field.change(function() {
				$(this).closest('.amp-select').ampSelect('setSelected', $(this).val());
			});

			if (o.preloadSource) {
				$ampSelect.ampSelect('loadSourceOptions', o.source || $field);
			} else {
				$ampSelect.one('amp-open', function() {
					var $ampSelect = $(this);
					var o = $ampSelect.getOptions();
					if (!$ampSelect.is('.amp-source-loaded')) {
						$ampSelect.ampSelect('loadSourceOptions', o.source);
					}
				})
			}

			if (o.selectedValues) {
				$ampSelect.ampSelect('setSelected', o.selectedValues);
			}
		});
	},

	open: function() {
		var $ampSelect = this;
		var o = $ampSelect.getOptions();

		switch (o.style) {
			case 'modal':
				$ampSelect.find('.amp-modal').ampModal('open');
				break;

			case 'inline':
				var $options = $ampSelect.find('.amp-select-options').addClass('is-active').removeClass('is-dormant');
				var $input = $ampSelect.find('.amp-select-input');
				var css = $input.offset();
				css.top += $input.outerHeight() - $('body').scrollTop();
				css.minWidth = $input.outerWidth();
				$options.css(css)
				$ampSelect.ampSelect('setFirstActive');
				$(document).one('scroll', function() {
					$ampSelect.ampSelect('close')
				})
				break;

			default:
				break;
		}

		return this.trigger('amp-open');
	},

	close: function() {
		var o = this.getOptions();

		switch (o.style) {
			case 'modal':
				this.find('.amp-modal').ampModal('close');
				break;

			case 'inline':
				this.find('.amp-select-options').addClass('is-dormant').removeClass('is-active');
				break;

			default:
				break;
		}

		return this.trigger('amp-close');
	},

	checkall: function(checked) {
		var $ampSelect = $(this).closest('.amp-select');
		$ampSelect.find('.amp-option input').prop('checked', typeof checked === 'boolean' ? checked : $ampSelect.find('.amp-select-checkall input').is(':checked')).first().change();
	},

	sortable: function(s) {
		var $ampSelect = $(this).closest('.amp-select');
		var o = $ampSelect.getOptions();

		$ampSelect.find('.amp-select-options').sortable(o.sortable || {});
	},

	isSelected: function(value) {
		var $field = this.find('.amp-select-field');

		if ($field.is('input')) {
			if ($field.val() == value) {
				return true;
			}
		} else {
			return $field.find('option[value="' + value + '"]:selected').length;
		}
	},

	getSelected: function() {
		return this.find('.amp-select-field').val();
	},

	setSelected: function(values) {
		var $ampSelect = this;
		var o = $ampSelect.getOptions(),
			$selectedOption = $('nothing'),
			$options = $ampSelect.find('.amp-select-options'),
			$field = $ampSelect.find('.amp-select-field'),
			$input = $ampSelect.find('.amp-select-input'),
			placeholder = '';

		if (values === null) {
			$selectedOption = $selectedOption.add($options.find('.amp-option-none'))
		} else {
			if (typeof values !== 'object') {
				values = [values];
			}

			for (var s in values) {
				var val = values[s]
				var $opt = $options.find('[data-value="' + val + '"]').not('.amp-option-new');

				if (o.style === 'inline') {
					if (o.allowNewOptions && !$opt.length && val) {
						o.selectOptions[val] = val;
						$ampSelect.ampSelect('setSelectOptions', o.selectOptions);
						$opt = $options.find('[data-value="' + val + '"]');

						if (o.onCreateNew) {
							o.onCreateNew.call($ampSelect, val, o.selectOptions);
						}
					}
				}

				$selectedOption = $selectedOption.add($opt);

				if (!$opt.is('.amp-option-none')) {
					placeholder += (placeholder ? ', ' : '') + $opt.find('.label').html();
				}
			}
		}

		if ($selectedOption.length || o.selectMultiple) {
			$options.find('input').prop('checked', false);
			$selectedOption.find('input').prop('checked', true)

			switch (o.style) {
				case 'inline':
					$ampSelect.find('.amp-select-input').val(placeholder);
					$input.data('textValue', placeholder);
					break;

				case 'modal':
					$ampSelect.find('.amp-selected .value').html(placeholder || o.placeholder);
					break;
			}

			$field.val(values);

			if (o.onSelect) {
				o.onSelect.call($ampSelect, $selectedOption, values, placeholder);
			}
		}

		return $ampSelect;
	},

	startLoading: function() {
		var $loading = $('<div/>').addClass('amp-loading').html("Loading...");
		this.find('.amp-select-options').prepend($loading);
		return this;
	},

	stopLoading: function() {
		this.find('.amp-select-options .amp-loading').remove();

		return this;
	},

	loadSourceOptions: function(source) {
		var $ampSelect = this;
		var o = this.getOptions();

		$ampSelect.ampSelect('startLoading');

		if (typeof source === 'function') {
			options = source.call($ampSelect);
		} else if (source instanceof jQuery) {
			options = {};

			source.find('option').each(function() {
				var $o = $(this);
				options[$o.attr('value')] = $o.html();
			});
		} else if (typeof source === 'object') {
			options = source;
		}

		if (o.sourceLabel || o.sourceValue) {
			for (var i in options) {
				if (o.sourceLabel) {
					options[i].label = options[i][o.sourceLabel]
				}

				if (o.sourceValue) {
					options[i].value = options[i][o.sourceValue]
				}
			}
		}

		if (typeof options === 'object') {
			$ampSelect.ampSelect('setSelectOptions', options);
		}

		return this;
	},

	addSelectOption: function(option) {
		var $ampSelect = $(this).closest('.amp-select');
		var $options = $ampSelect.find('.amp-select-options');
		var o = $ampSelect.getOptions();

		option = $.extend({}, {
			isRemovable: true
		}, option)

		var $option = $('<label />').addClass('amp-option ' + (o.selectMultiple ? 'checkbox' : 'radio')).attr('data-value', option.value)
			.append($('<input/>').attr('type', o.selectMultiple ? 'checkbox' : 'radio').attr('name', o.optionGroupName).attr('value', option.value).prop('checked', $ampSelect.ampSelect('isSelected', option.value)))
			.append($('<span/>').addClass('label').html(option.label));

		if (o.allowRemove && option.isRemovable) {
			var $remove = $('<a/>').addClass('amp-option-remove').append($('<b />').addClass('fa fa-trash-o'));
			$remove.click(function() {
				$(this).closest('.amp-select').ampSelect('removeSelectOption', $(this).closest('.amp-option'));
			});
			$option.append($remove);
		}

		$options.append($option.attr('data-sort-order', option.sortOrder || 0).addClass(option.class || ''));

		return $option;
	},

	removeSelectOption: function($option, force) {
		var $ampSelect = this;
		var o = $ampSelect.getOptions(),
			$field = $ampSelect.find('.amp-select-field');

		if (typeof $option !== 'object') {
			$option = $ampSelect.find('.amp-select-options .amp-option[data-value="' + $option + '"]');
		}

		if (!force && o.onRemove) {
			if (o.onRemove.call($ampSelect, $option) === false) {
				return this;
			}
		}

		var val = $option.attr('data-value');

		delete o.selectOptions[val]
		$option.remove();

		if ($field.val() === val) {
			$field.val('');
			$ampSelect.ampSelect('selectActive');
		}

		return this;
	},

	getSelectOptions: function() {
		return this.getOptions().selectOptions;
	},

	setSelectOptions: function(options) {
		var $ampSelect = $(this).closest('.amp-select');
		var $options = $ampSelect.find('.amp-select-options');
		var o = $ampSelect.getOptions();

		$options.children().remove();

		o.selectOptions = options;

		if (o.showNewAsOption) {
			$ampSelect.ampSelect('addSelectOption', {
				label:       "+ New Record",
				value:       "",
				class:       "amp-option-new hidden",
				isRemovable: false
			})
		}

		if (o.showNoneAsOption) {
			var noneOpt = {
				label:       typeof o.showNoneAsOption === 'string' ? o.showNoneAsOption : "(None)",
				value:       "",
				class:       "amp-option-none",
				isRemovable: false
			};

			if (options['']) {
				noneOpt.label = typeof options[''] === 'string' ? options[''] : options[''].label
			}

			$ampSelect.ampSelect('addSelectOption', noneOpt)
			delete options['']
		}

		var s = 1;

		for (var opt in options) {
			var option = options[opt];

			if (typeof option === 'string') {
				option = {
					label:     option,
					value:     opt,
					sortOrder: s++
				}
			} else {
				option.value = option.value || opt;
				s++
			}

			$ampSelect.ampSelect('addSelectOption', option);
		}

		$options.find('.amp-option input').change(function() {
			var $ampSelect = $(this).closest('.amp-select');
			var values = [], $field = $ampSelect.find('.amp-select-field');

			$ampSelect.find('.amp-option input:checked').each(function() {
				values.push($(this).val());
			})

			$field.val($field.is('input') ? values[0] : values).change();
		})

		if (o.sortable) {
			$ampSelect.ampSelect('sortable', typeof o.sortable === 'object' ? o.sortable : {});
		}

		return $ampSelect.ampSelect('setSelected', $ampSelect.find('.amp-select-field').val());
	},

	getActive: function() {
		return this.find('.amp-option.is-active');
	},

	setActive: function(value) {
		var $options = this.find('.amp-select-options');
		$options.find('.amp-option').removeClass('is-active');
		var $option = $options.find('.amp-option[data-value="' + value + '"]:visible').addClass('is-active');

		if ($option.length) {
			var pos = $option.position(),
				box = {top: 0},
				scrollTop = $options.scrollTop(),
				optHeight = $option.outerHeight(),
				boxHeight = $options.height();

			pos.top = pos.top;
			pos.bottom = pos.top + optHeight;
			box.bottom = boxHeight;

			if (pos.top < box.top) {
				$options.scrollTop(scrollTop + Math.ceil(pos.top));
			} else if (pos.bottom > box.bottom) {
				$options.scrollTop(scrollTop + Math.floor(pos.bottom) - $options.height());
			}
		}

		return this;
	},

	setFirstActive: function() {
		this.ampSelect('setActive', this.find('.amp-option:visible').first().find('input').val());
		return this;
	},

	nextActive: function(dir) {
		var $active = this.ampSelect('getActive');
		dir || (dir = 1);

		if (!this.find('.amp-select-options').is('.is-active')) {
			this.ampSelect('open');
		}

		var $next = dir > 0 ? $active.nextAll(':visible').first() : $active.prevAll(':visible').first();

		if (!$next.length) {
			$next = dir > 0 ? this.find('.amp-option:first-child') : this.find('.amp-option:last-child');
		}

		return this.ampSelect('setActive', $next.find('input').val());
	},

	selectActive: function() {
		var active = this.ampSelect('getActive').attr('data-value');
		if (typeof active === 'undefined' || active === null) {
			active = this.find('.amp-select-input').val()
		}
		this.ampSelect('setSelected', [active]);
		this.ampSelect('close');
		this.find('.amp-select-input, .amp-select-field').change();
		return this;
	},

	filter: function(value) {
		var $ampSelect = this, exactMatch = false;
		var $options = $ampSelect.find('.amp-select-options'),
			regex = new RegExp('.*' + value + '.*', 'i');

		$options.find('.amp-option-none').toggleClass('hidden', !!value);

		$options.find('.amp-option').not('.amp-option-new, .amp-option-none').each(function() {
			var $this = $(this);
			var str = $this.find('.label').html();
			$this.toggleClass('hidden', !str.match(regex));
			exactMatch = exactMatch || str === value;
		})

		var isNew = value && !exactMatch;
		if (($newOption = $options.find('.amp-option-new').toggleClass('hidden', !isNew)).length && isNew) {
			$newOption.attr('data-value', value).find('.label').html(value);
			$newOption.find('input').val(value)
		}

		$ampSelect.ampSelect('open');

		return $ampSelect;
	},

	initInline: function() {
		var $ampSelect = this;
		var o = $ampSelect.getOptions(), $field = $ampSelect.find('.amp-select-field');

		var $box = $('<div/>').addClass('amp-select-box amp-select-inline'),
			$input = $field.is('input') ? $field : $('<input/>').attr('type', 'text'),
			$options = $('<div/>').addClass('amp-select-options no-parent-scroll is-dormant on-active');

		//Setup Box
		$box.append($options)

		$box.append($input.addClass('amp-select-input'));

		$input.data('textValue', $input.val());

		$input.on('focus click', function() {
			$(this).closest('.amp-select').ampSelect('open')
		})

		$input.blur(function(e) {
			var $ampSelect = $(this).closest('.amp-select');
			//XXX: use timeout to allow change events to fire first!
			setTimeout(function() {$ampSelect.ampSelect('close')}, 100);
		})

		$input.keydown(function(e) {
			var $ampSelect = $(this).closest('.amp-select');

			var k = e.which || e.keyCode || e.charCode;

			switch (k) {
				//Enter Key
				case 13:
					$ampSelect.ampSelect('selectActive');
					$(this).change().blur();
					return false;

				//Up Arrow / Down Arrow
				case 38:
				case 40:
					$ampSelect.ampSelect('nextActive', k === 38 ? -1 : 1);
					return false;

				//Tab
				case 9:
					if (!$ampSelect.find('.amp-select-options').is('.is-active')) {
						return;
					}

					$ampSelect.ampSelect('nextActive', e.shiftKey ? -1 : 1);
					return false;
			}
		})

		$input.keyup(function() {
			var $this = $(this);

			if ($this.data('textValue') !== $this.val()) {
				$this.closest('.amp-select').ampSelect('filter', $this.val())
				$this.data('textValue', $this.val());
			}
		})

		$ampSelect.append($box);

		return this;
	},

	initCheckboxes: function() {
		var $box = $('<div/>').addClass('amp-select-box amp-select-checkboxes'),
			$options = $('<div/>').addClass('amp-select-options no-parent-scroll');

		return this.append($box.append($options));
	},

	initSelectModal: function() {
		var $ampSelect = this;
		var o = $ampSelect.getOptions();
		var $field = $ampSelect.find('.amp-select-field');

		o.allowNewOptions = false;
		o.showNewAsOption = false;

		var $selected = $("<div />")
			.addClass($field.attr('class').replace('amp-select-enabled', 'amp-selected'))
			.append($('<div/>').addClass('value'))
			.append($('<div/>').addClass('amp-select-button').append($('<div />').addClass('align-middle no-ws-hack')).append($('<div />').addClass('amp-select-button-icon fa fa-ellipsis-h')));

		var $box = $('<div/>').addClass('amp-select-box amp-select-modal'),
			$options = $('<div/>').addClass('amp-select-options no-parent-scroll'),
			$checkall = $('<label/>').addClass('amp-select-checkall checkbox white').append($('<input/>').attr('type', 'checkbox')).append($('<span/>').addClass('label')),
			$actions = $('<div/>').addClass('amp-select-actions'),
			$done = $('<a/>').addClass('amp-select-done button').html('Done'),
			$title = $('<div/>').addClass('amp-select-title').append($('<div/>').addClass('text').html($field.attr('data-label') || 'Select one or more items'));

		$ampSelect.append($selected);

		//Events
		$checkall.find('input').change($.ampSelect.checkall);

		//Actions
		$selected.click(function() {
			$(this).closest('.amp-select').ampSelect('open');
		});
		$done.click(function() {
			$(this).closest('.amp-select').ampSelect('close');
		});

		//Setup Box
		$box.append($options).append($actions.append($done))

		$box.ampModal({
			title:   $title.prepend($checkall),
			context: $ampSelect
		});

		if ($selected.is(':visible')) {
			$selected.width($selected.width())
		}

		return this;
	}
})
