//Amplo Contact Manager jQuery plugin
$.ampExtend($.ampContactManager = function() {}, {
	instanceId: 0,
	init:       function(o) {
		var $managers = this.use_once().addClass('amp-contact-manager');

		if (!$managers.length) {
			return this;
		}

		o = $.extend({}, {
			contact_id:     null,
			input:          null,
			selected:       null,
			type:           'contact',
			showAddress:    true,
			template:       null,
			selectMultiple: false,
			syncFields:     null,
			onChange:       null,
			onEdit:         null,
			url:            $ac.site_url + 'manager/contact',
			listingUrl:     $ac.site_url + 'manager/contact/listing',
			loadListings:   true,
			listing:        {}
		}, o);

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

	sync: function($fields, contact){
		var $acm = this;
		var o = $acm.getOptions();

		for (var f in contact) {
			var $field = $fields.filter('[data-name=' + f + ']');
			var value = value = contact[f];

			if ($field.is('[data-type=select]')) {
				value = o.contactForm.find('[name=' + f + '] option[value=' + value + ']').html();
			}

			$field.html(value);
		}

		return this;
	},

	select: function($contact) {
		var $acm = this;
		var o = $acm.getOptions(), is_changed = false;

		if (typeof $contact !== 'object') {
			$contact = $acm.find('.acm-contact[data-contact-id=' + $contact + ']');
		}

		if (o.selectMultiple) {
			$contact.toggleClass('is-selected');

			selected = []

			$acm.find('.acm-contact.is-selected').each(function() {
				selected.push($(this).attr('data-contact-id'))
			})

			is_changed = o.selected.toString() === selected.toString();
		} else {
			$acm.find('.acm-contact').removeClass('is-selected');
			selected = $contact.addClass('is-selected').attr('data-contact-id');
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

	get: function(listing) {
		var $acm = this;
		var o = $acm.getOptions();

		$.post(o.listingUrl, $.extend(true, o.listing, listing), function(response) {
			$acm.ampContactManager('results', response.contacts, response.total);
		})

		return this;
	},

	results: function(contacts, total) {
		var $acm = this;
		var o = $acm.getOptions();
		var $contactList = $acm.find('.acm-contact-list'),
			isEmpty = typeof contacts !== 'object' || $.isEmptyObject(contacts);

		$contactList.toggleClass('empty', isEmpty).html('');

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

		return this;
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
			var $acm = $(this).closest('.amp-contact-manager');
			var o = $acm.getOptions();

			var $results = $(this).closest('.acm-results').toggleClass('adding');

			var $form = $results.find('.acm-new-contact-form');

			if (!$form.children().length) {
				$form.append(o.contactForm.clone())
			}
		})

		$acm.find('.edit-contact').click(function() {
			var $acm = $(this).closest('.amp-contact-manager');
			var o = $acm.getOptions();

			$acm.find('.acm-contact').removeClass('editing');

			var $contact = $(this).closest('.acm-contact').addClass('editing');
			var $form = $contact.find('.acm-edit-contact-form');

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
		})

		$acm.find('.cancel-contact').click(function() {
			$(this).closest('.acm-contact').removeClass('editing');
		})

		$acm.find('.acm-contact').click(function() {
			$(this).closest('.amp-contact-manager').ampContactManager('select', $(this));
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
