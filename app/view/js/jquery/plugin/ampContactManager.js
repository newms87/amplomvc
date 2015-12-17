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
			selectMultiple: false,
			syncFields:     null,
			onChange:       null,
			onEdit:         null,
			loadListings:   true,
			selected:       null,
			url:            $ac.site_url + 'contact/manager',
			listingUrl:     $ac.site_url + 'contact/manager/listing',
			listing:        {}
		}, o);

		$managers.each(function() {
			var $acm = $(this);

			$acm.setOptions(o);

			if ($acm.children().length) {
				$acm.ampContactManager('initTemplate');
			} else {
				$acm.load(o.url, null, function() {
					$acm.ampContactManager('initTemplate');
				});
			}
		})

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
				o.input.val(o.selected);
			}

			if (o.syncFields.length) {
				for (var f in contact) {
					o.syncFields.filter('[data-name=' + f + ']').html(contact[f]);
				}
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

	editContact: function($contact, contact) {
		var $acm = this;
		var o = $acm.getOptions();

		for (var f in contact) {
			$contact.find('[data-name=' + f + ']').html(contact[f]);

			if (o.syncFields) {
				o.syncFields.filter('[data-name=' + f + ']').html(contact[f]);
			}
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

				var $contact = $contactList.ac_template('contact', 'add', contact);

				$contact.data('contact', contact);

				for (var f in contact) {
					$contact.find('[data-name=' + f + ']').html(contact[f]);
				}

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

		$acm.find('.amp-nested-form').ampNestedForm();

		$acm.find('.acm-add-contact').click(function() {
			var $results = $(this).closest('.acm-results').toggleClass('adding');

			var $form = $results.find('.acm-new-contact-form');

			if (!$form.children().length) {
				$form.append(o.contactForm.clone())
			}
		})

		$acm.find('.edit-contact').click(function() {
			var $acm = $(this).closest('.amp-contact-manager');
			$acm.find('.acm-contact').removeClass('editing');

			var $contact = $(this).closest('.acm-contact').addClass('editing');
			var $form = $contact.find('.acm-edit-contact-form');

			if (!$form.children().length) {
				$form.append(o.contactForm.clone())
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

		$searchForm.ampNestedForm('onDone', function(response) {
			$(this).closest('.amp-contact-manager').ampContactManager('results', response.contacts, response.total);
		})

		$searchForm.find('input').ampDelay({
			callback: function() {
				$(this).closest('.amp-nested-form').submit();
			},
			delay:    200,
			on:       'keyup'
		});

		$acm.find('.acm-contact[data-row=__ac_template__]').ac_template('contact');

		if (o.loadListings) {
			$acm.ampContactManager('get');
		}

		return this;
	}
})
