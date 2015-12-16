//Amplo Contact Manager jQuery plugin
$.ampExtend($.ampContactManager = function() {}, {
	instanceId: 0,
	init:       function(o) {
		var $acm = this.use_once().addClass('amp-contact-manager');

		if (!$acm.length) {
			return this;
		}

		o = $.extend({}, {
			onChange:     null,
			contact_id:   null,
			loadListings: true,
			url:          $ac.site_url + 'contact/manager',
			listingUrl:   $ac.site_url + 'contact/manager/listing',
			listing:      {}
		}, o);

		$acm.setOptions(o);

		$acm.each(function() {
			var $acm = $(this).attr('data-instance-id', $.ampContactManager.instanceId++);

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

	get: function(listing) {
		var $acm = this;
		var o = $acm.getOptions();

		$.post(o.listingUrl, $.extend(true, o.listing, listing), function(response) {
			$acm.ampContactManager('results', response.contacts, o.contact_id, response.total);
		})

		return this;
	},

	results: function(contacts, selected_id, total) {
		var $contactList = this.find('.contact-list'),
			isEmpty = typeof contacts !== 'object' || $.isEmptyObject(contacts);

		var prev_id = $contactList.find('.contact-id-group:checked').val();
		selected_id = selected_id || prev_id;

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

				$contact.find('.contact-id-group').val(contact.id);

				$contactList.append($contact);
			}

			//Hack to keep radio groups separated
			$contactList.find('[form]').attr('form', 'ocm-instance-' + this.attr('data-instance-id'));
			$contactList.closest('.amp-contact-manager').ampContactManager('groupRadioButtons');

			var $contact_id = $contactList.find('.contact-id-group[value=' + selected_id + ']').prop('checked', true);

			if (prev_id != selected_id) {
				$contact_id.change()
			}

			$contact_id.closest('.contact').addClass('selected');
		}

		return this;
	},

	showSelected: function() {
		this.find('.contact-id-group:checked').closest('.contact').addClass('selected');
		this.find('.contact').not('.selected').remove();
	},

	initTemplate: function() {
		var $acm = this;
		var o = $acm.getOptions();

		$acm.find('.amp-nested-form').ampNestedForm();

		$acm.find('.add-contact').click(function() {
			$(this).closest('.contact-results').toggleClass('adding');
		})

		$acm.find('.edit-contact').click(function() {
			$(this).closest('.contact').addClass('editing');
		})

		$acm.find('.cancel-contact').click(function() {
			$(this).closest('.contact').removeClass('editing');
		})

		$acm.change(function(e) {
			var $target = $(e.target);

			if ($target.is('.contact-id-group')) {
				var $acm = $target.closest('.amp-contact-manager');
				var o = $acm.getOptions();
				$acm.find('.contact.selected').removeClass('selected');
				var $contact = $target.closest('.contact').addClass('selected');

				if (typeof o.onChange === 'function') {
					o.onChange.call($acm, $contact, $contact.data('contact'));
				}
			}
		})

		$acm.find('.contact .contact-form').ampNestedForm('onDone', function(response) {
			var $contact = $(this).closest('.contact');

			if (response.success) {
				for (var f in response.data) {
					$contact.find('[data-name=' + f + ']').html(response.data[f]);
				}

				$contact.removeClass('editing');
			}

			$contact.show_msg(response);
		})

		$acm.find('.new-contact-form').ampNestedForm('onDone', function(response) {
			var $form = $(this);

			if (response.success) {
				var $cm = $form.closest('.amp-contact-manager').ampContactManager('results', {0: response.data}, response.data.contact_id);
				$cm.find('.contact-results').removeClass('adding');
				$form.find('input').val('');
			}

			$form.show_msg(response);
		})

		var $searchForm = $acm.find('.contact-search-form');

		$searchForm.ampNestedForm('onDone', function(response) {
			$(this).closest('.amp-contact-manager').ampContactManager('results', response.contacts, null, response.total);
		})

		$searchForm.find('input').ampDelay({
			callback: function() {
				$(this).closest('.amp-nested-form').submit();
			},
			delay:    200,
			on:       'keyup'
		});

		$acm.find('.contact[data-row=__ac_template__]').ac_template('contact');

		//Check if browser supports form attribute
		$acm.ampContactManager('groupRadioButtons');

		console.log(o);

		if (o.loadListings) {
			$acm.ampContactManager('get');
		}

		return this;
	},

	registerNewInstance: function() {
		return this.attr('data-instance-id', $.ampContactManager.instanceId++).ampContactManager('groupRadioButtons');
	},

	//Radio button grouping hack
	groupRadioButtons: function() {
		var $acm = this;

		var $inputs = $acm.find('.contact-id-group');

		if ($inputs.length && ($inputs[0].form == null || $inputs.attr('form') !== $inputs[0].form.getAttribute('id'))) {
			$acm.find('.contact-id-group').each(function() {
				$(this).attr('name', 'contact_id_' + $acm.attr('data-instance-id'));
			})
		} else {
			var id = 'ocm-instance-' + $acm.attr('data-instance-id');

			$('body').append($("<form />").attr('id', id))
			$inputs.attr('form', id);
		}

		return this;
	}
})
