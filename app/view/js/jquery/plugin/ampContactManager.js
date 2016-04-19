//Amplo Contact Manager jQuery plugin
$.ampExtend($.ampContactManager = function() {}, {
	instanceId: 0,
	init:       function(o) {
		var $managers = this.use_once().addClass('amp-contact-manager');

		if (!$managers.length) {
			return this;
		}

		o = $.extend({}, {
			contact_id:      null,
			input:           null,
			selected:        null,
			type:            'contact',
			showAddress:     true,
			template:        null,
			selectMultiple:  false,
			deselectOnClick: true,
			syncFields:      null,
			onChange:        null,
			onEdit:          null,
			onOpenEditor:    null,
			onResults:       null,
			onSync:          null,
			url:             $ac.site_url + 'manager/contact/',
			removeUrl:       null,
			listingUrl:      null,
			loadListings:    true,
			listing:         {}
		}, o);

		o.removeUrl = o.removeUrl || o.url + 'remove';
		o.listingUrl = o.listingUrl || o.url + 'listing';

		o.template_id = 'acm-contact-' + $.ampContactManager.instanceId++;

		if (o.type) {
			o.listing.filter || (o.listing.filter = {});
			o.listing.filter.type = o.type;
		}

		if (o.input && o.selected === null) {
			o.selected = o.input.val();
		}

		$managers.each(function() {
			var $acm = $(this);

			$acm.setOptions(o);

			if ($acm.children().length) {
				$acm.ampContactManager('initTemplate');
			} else {
				$acm.load(o.url, {show_address: +o.showAddress, template: o.template}, function() {
					$acm.ampContactManager('initTemplate');
				});
			}
		})

		return this;
	},

	sync: function($fields, contact) {
		var $acm = this;
		var o = $acm.getOptions();

		if (!contact) {
			$fields.html('');
		} else {
			for (var f in contact) {
				var value = value = contact[f];

				if (typeof value === 'string' && value.match(/^\{.*\}$/)) {
					value = contact[f] = $.parseJSON(value);
				}

				if (typeof value === 'object') {
					for (var v in value) {
						$field = $fields.filter('[data-name="' + f + '[' + v + ']"]');

						$field.html($field.is('[data-type=select]') ? v = o.contactForm.find('[name="' + f + '[' + v + ']"] option[value=' + value[v] + ']').html() : value[v]);
					}
				} else {
					var $field = $fields.filter('[data-name=' + f + ']');

					$field.html($field.is('[data-type=select]') ? o.contactForm.find('[name=' + f + '] option[value=' + value + ']').html() : value);
				}
			}
		}

		if (o.onSync) {
			o.onSync.call($acm, $fields, contact);
		}

		return this;
	},

	select: function($contact) {
		var $acm = this;
		var o = $acm.getOptions(), is_changed = false, selected = null;

		if (typeof $contact !== 'object') {
			$contact = $acm.find('.acm-contact[data-contact-id=' + $contact + ']');
		}

		if (!$contact.length) {
			is_changed = $acm.find('.acm-contact.is-selected').length;
			$acm.find('.acm-contact').removeClass('is-selected');
		} else if (o.selectMultiple) {
			$contact.toggleClass('is-selected');

			selected = []

			$acm.find('.acm-contact.is-selected').each(function() {
				selected.push($(this).attr('data-contact-id'))
			})

			is_changed = o.selected.toString() === selected.toString();
		} else {
			var isSelected = o.deselectOnClick ? !$contact.hasClass('is-selected') : true;

			$acm.find('.acm-contact').removeClass('is-selected');
			$contact.toggleClass('is-selected', isSelected);
			isSelected || ($contact = $('body'));
			selected = $contact.attr('data-contact-id') || '';
			is_changed = o.selected !== selected;
		}

		o.selected = selected;

		if (is_changed) {
			var contact = $contact.data('contact');

			if (o.input.length) {
				o.input.val(o.selected).change();
			}

			if (o.syncFields) {
				$acm.ampContactManager('sync', o.syncFields, contact);
			}

			if (o.onChange) {
				o.onChange.call($acm, o.selected, $contact, contact);
			}
		}

		return this;
	},

	getSelected: function() {
		return this.getOptions().selected;
	},

	getSelectedData: function() {
		var $acm = this;
		var o = this.getOptions(), $selected = $acm.find('.acm-contact.is-selected');

		if (o.selectMultiple) {
			var data = {};

			$selected.each(function() {
				var $c = $(this);
				data[$c.attr('data-contact-id')] = $c.data('contact')
			})

			return data;
		} else {
			return $selected.data('contact');
		}
	},

	addContact: function() {
		var o = this.getOptions();

		var $results = this.find('.acm-results').toggleClass('adding');

		var $form = $results.find('.acm-new-contact-form');

		if (!$form.children().length) {
			$form.append(o.contactForm.clone())
		}

		return this;
	},

	editContact: function($contact, contact) {
		var $acm = this;
		var o = $acm.getOptions();

		$acm.ampContactManager('sync', $contact.find('[data-name]'), contact);

		if (o.syncFields) {
			$acm.ampContactManager('sync', o.syncFields, contact);
		}

		$contact.data('contact', contact);

		if (o.onEdit) {
			o.onEdit.call($acm, $contact, contact);
		}

		return this;
	},

	openEditor: function($contact) {
		var $acm = this;
		var o = $acm.getOptions();

		$acm.find('.acm-contact').removeClass('editing');

		var $form = $contact.addClass('editing').find('.acm-edit-contact-form');

		if (!$form.children().length) {
			$form.append(o.contactForm.clone())

			var contact = $contact.data('contact');

			for (var f in contact) {
				if (f === 'address') {
					for (var a in contact[f]) {
						$form.find('[name="address[' + a + ']"]').val(contact[f][a]);
					}
				} else {
					$form.find('[name=' + f + ']').val(contact[f]);
				}
			}
		}

		if (o.onOpenEditor) {
			o.onOpenEditor.call($acm, $contact);
		}

		return this;
	},

	get: function(listing) {
		var $acm = this;
		var o = $acm.getOptions();

		$.post(o.listingUrl, $.extend(true, o.listing, listing), function(response) {
			$acm.ampContactManager('results', response.contacts, response.total);

			if (!listing) {
				var hasRecords = +response.total > 0;
				$acm.toggleClass('has-records', hasRecords).toggleClass('no-records', !hasRecords);

				if (!hasRecords) {
					$acm.ampContactManager('addContact');
				}
			}
		})

		return this;
	},

	results: function(contacts, total) {
		var $acm = this;
		var o = $acm.getOptions();
		var $contactList = $acm.find('.acm-contact-list').html(''),
			isEmpty = typeof contacts !== 'object' || $.isEmptyObject(contacts);

		$acm.toggleClass('is-empty', isEmpty).toggleClass('is-filled', !isEmpty);

		if (!isEmpty) {
			for (var c in contacts) {
				var contact = contacts[c];
				contact.id = contact.contact_id;

				var $contact = $contactList.ac_template(o.template_id, 'add', contact);

				$contact.data('contact', contact);

				$acm.ampContactManager('sync', $contact.find('[data-name]'), contact);

				$contact.attr('data-contact-id', contact.id);

				$contactList.append($contact);
			}

			if (o.selectMultiple) {
				for (var s in o.selected) {
					$contactList.find('[data-contact-id=' + o.selected[s] + ']').addClass('is-selected');
				}
			} else {
				$contactList.find('[data-contact-id=' + o.selected + ']').addClass('is-selected');
			}
		}

		if (o.onResults) {
			o.onResults.call($acm, $contactList, contacts, total);
		}

		return this;
	},

	remove: function($record) {
		var $am = this;
		var o = $am.getOptions(), data = {};

		$.ampConfirm({
			title:     "Remove Contact",
			text:      "Are you sure you want to remove this contact?",
			onConfirm: function() {
				data['contact_id'] = $record.attr('data-contact-id');

				$.get(o.removeUrl, data, function(response) {
					if (response.success) {
						$record.remove();
					}

					$am.show_msg(response);
				})
			}
		})

		return $am;
	},

	removeUnselected: function() {
		this.find('.acm-contact').not('.is-selected').remove();
	},

	initTemplate: function() {
		var $acm = this;
		var o = $acm.getOptions();

		o.contactForm = $acm.find('.acm-contact-form').remove().removeClass('hidden');

		if (o.type) {
			o.contactForm.find('[name=type]').val(o.type);
		}

		$acm.find('.amp-nested-form').ampNestedForm();

		$acm.find('.acm-add-contact').click(function() {
			$(this).closest('.amp-contact-manager').ampContactManager('addContact');
		})

		$acm.find('.am-deselect').click(function() {
			$(this).closest('.amp-contact-manager').ampContactManager('select', '');
		})

		$acm.find('.edit-contact').click(function() {
			$(this).closest('.amp-contact-manager').ampContactManager('openEditor', $(this).closest('.acm-contact'));
		})

		$acm.find('.cancel-contact').click(function() {
			$(this).closest('.acm-contact').removeClass('editing');
		})

		$acm.find('.acm-remove-contact').click(function() {
			$(this).closest('.amp-contact-manager').ampContactManager('remove', $(this).closest('.acm-contact'));
			return false;
		})

		$acm.find('.acm-contact').click(function(e) {
			var $acm = $(e.target).closest('.amp-contact-manager, .amp-click-void');
			if ($acm.is('.amp-contact-manager')) {
				$acm.ampContactManager('select', $(this));
			}
		})

		$acm.find('.acm-contact .acm-edit-contact-form').ampNestedForm('onDone', function(response) {
			var $acm = $(this).closest('.amp-contact-manager');
			var $contact = $(this).closest('.acm-contact');

			if (response.success) {
				$acm.ampContactManager('editContact', $contact, response.data)

				$contact.removeClass('editing');
			}

			$contact.show_msg(response);
		})

		$acm.find('.acm-new-contact-form').ampNestedForm('onDone', function(response) {
			var $form = $(this);

			if (response.success) {
				var $acm = $form.closest('.amp-contact-manager').ampContactManager('results', {0: response.data}, 1);
				$acm.removeClass('no-records').addClass('has-records');
				$acm.find('.acm-results').removeClass('adding');
				$form.find('input').val('');

				$acm.ampContactManager('select', response.data.contact_id)
			}

			$form.show_msg(response);
		})

		var $searchForm = $acm.find('.acm-search-form');

		$searchForm.ampNestedForm('onSubmit', function() {
			var listing = {
				filter: {
					keywords: this.find('[name="filter[keywords]"]').val()
				}
			}

			this.closest('.amp-contact-manager').ampContactManager('get', listing);

			return false;
		})

		$searchForm.find('input')
			.on('keyup', function(e) {
				if (e.keyCode === 13) {
					e.stopPropagation();
					return false;
				}
			})
			.ampDelay({
				callback: function() {
					$(this).closest('.amp-nested-form').submit();
				},
				delay:    200,
				on:       'keyup'
			});

		$acm.find('.acm-contact[data-row=__ac_template__]').ac_template(o.template_id);

		if (o.loadListings) {
			$acm.ampContactManager('get');
		}

		return this;
	}
})
