/**
 * Cues kind
 *
 * @package     mod
 * @subpackage  elang
 * @copyright   2013-2015 University of La Rochelle, France
 * @license     http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html CeCILL-B license
 *
 * @since       0.0.1
 */
enyo.kind({
	/**
	 * Name of the kind
	 */
	name: 'Elang.Cues',

	/**
	 * Published properties:
	 * - elements: array of cues
	 * - limit: maximum number of cues per page
	 * Each property will have public setter and getter methods
	 */
	published: {elements: [], limit: 10},

	/**
	 * Handlers:
	 * - onPageChange: fired when the page changes
	 * - onCueSelect: fired when a cue is selected
	 */
	handlers: {onPageChange: 'pageChange', onCueSelect: 'cueSelect'},

	/**
	 * Events:
	 * - onCueDeselect: fired when the cue is deselected
	 */
	events: {onCueDeselect: ''},

	/**
	 * Named components:
	 * - limit: select list
	 * - pagination: table pagination
	 * - body: table body
	 */
	components: [
		// List limit
		{
			classes: 'text-center',
			components: [
				{
					tag: 'label',
					content: $L('limitperpage')
				},
				{
					name: 'limit',
					kind: "Select",
					selected: 1,
					value: 10,
					onchange: "limitSelect",
					components: [{content: 5, value: 5}, {content: 10, value: 10}, {content: 15, value: 15}, {content: 20, value: 20}, {content: 25, value: 25}]
				},
			]
		},

		// Pagination
		{
			kind: 'Elang.Pagination', name: 'pagination'
		},

		{
			tag: 'table',
			classes: 'table table-bordered table-condensed table-striped',
			components: [
				{
					tag: 'caption', content: $L('cuelisting')},
				{
					tag: 'thead',
					components: [
						{
							tag: 'tr',
							components: [
								{tag: 'th', attributes: {width: '7%'}, content: $L('number')},
								{tag: 'th', attributes: {width: '7%'}, content: $L('Status')},
								{tag: 'th', content: $L('Title')}
							]
						}
					]
				},
				// Body
				{
					tag: 'tbody', name: 'body'
				},
			]
		},
	],

	/**
	 * Handle a page change event
	 *
	 * @protected
	 *
	 * @param   inSender  enyo.instance  Sender of the event
	 * @param   inEvent   Object		 Event fired
	 *
	 * @return void
	 *
	 * @since  0.0.1
	 */
	pageChange: function (inSender, inEvent)
	{
		this.fillCues(inEvent.number);
	},

	/**
	 * Handle a cue select event
	 *
	 * @protected
	 *
	 * @param   inSender  enyo.instance  Sender of the event
	 * @param   inEvent   Object		 Event fired
	 *
	 * @return void
	 *
	 * @since  0.0.1
	 */
	cueSelect: function (inSender, inEvent)
	{
		enyo.forEach(
			this.$.body.getClientControls(),
			function (row) {
				if (this != row)
				{
					row.removeClass('info');
				}
			},
			inEvent.originator
		);
	},

	/**
	 * Handle a limit select event
	 *
	 * @protected
	 *
	 * @param   inSender  enyo.instance  Sender of the event
	 * @param   inEvent   Object		 Event fired
	 *
	 * @return void
	 *
	 * @since  0.0.3
	 */
	limitSelect: function (inSender, inEvent)
	{
		this.setLimit(inSender.getValue());
		this.doCueDeselect();
	},

	/**
	 * Detect a change in the limit value
	 *
	 * @protected
	 *
	 * @param  oldValue  integer  Old limit value
	 *
	 * @return  void
	 *
	 * @since  0.0.1
	 */
	limitChanged: function (oldValue)
	{
		this.limit = Number(this.limit);
		if (this.limit > 0)
		{
			var index = [5, 10, 15, 20, 25].indexOf(this.limit);

			if (index > 0)
			{
				this.$.limit.setSelected(index);
			}
			else
			{
				this.$.limit.setSelected(0);
			}

			this.fillCues(0);
		}
		else
		{
			var limit = this.limit;
			this.limit = oldValue;
			throw new RangeError('Limit value "' + limit + '" is incorrect');
		}
	},

	/**
	 * Detect a change in the elements value
	 *
	 * @protected
	 *
	 * @param  oldValue  array  Old elements value
	 *
	 * @return  void
	 *
	 * @since  0.0.1
	 */
	elementsChanged: function (oldValue)
	{
		this.fillCues(0);
	},

	/**
	 * Fill the table
	 *
	 * @protected
	 *
	 * @param  page	 integer  Page of the table
	 *
	 * @return  this
	 *
	 * @since  0.0.1
	 */
	fillCues: function (page)
	{
		this.$.pagination.setTotal(((this.elements.length / this.limit) | 0) + (this.elements.length % this.limit > 0 ? 1 : 0));
		var start = page * this.limit;
		var elements = this.elements.slice(start, start + this.limit);
		this.$.body.destroyClientControls();

		for (var i = 0; i < elements.length; i++)
		{
			this.$.body.createComponent({kind: 'Elang.Cue', number: start + i + 1, data: elements[i]}, {owner: this});
		}

		this.$.body.render();
		this.$.pagination.render();
		return this;
	},
});

/**
 * Cue kind
 *
 * @package     mod
 * @subpackage  elang
 * @copyright   2013-2015 University of La Rochelle, France
 * @license     http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html CeCILL-B license
 *
 * @since       0.0.1
 */
enyo.kind({
	/**
	 * Name of the kind
	 */
	name: 'Elang.Cue',

	/**
	 * Published properties:
	 * - number: number of the cue
	 * - data: data hold by the cue
	 * - remaining: texts to find
	 * Each property will have public setter and getter methods
	 */
	published: {number: 0, data: {}, remaining: 0},

	/**
	 * Events:
	 * - onCueSelect: fired when the cue is changed
	 * - onCueDeselect: fired when the cue is deselected
	 */
	events: {onCueSelect: '', onCueDeselect: ''},

	/**
	 * tag for this kind
	 */
	tag: 'tr',

	/**
	 * Named components:
	 * - number: cue number
	 * - remaining: texts to find
	 * - title: the cue title
	 */
	components: [
		{name: 'number', tag: 'td'},
		{
			tag: 'td',
			components: [
				{tag: 'span', classes: 'label label-warning', name: 'remaining'},
			],
		},
		{tag: 'td', components: [{name: 'title', tag: 'a', ontap: 'cueTap', attributes: {href:'#'}}]}
	],

	/**
	 * Create function
	 *
	 * @protected
	 *
	 * @since  0.0.1
	 */
	create: function ()
	{
		this.inherited(arguments);
		this.$.number.content = this.number;
		this.$.title.content = this.data.title;
		this.remaining = this.data.remaining;
		if (this.data.remaining != '0')
		{
			this.$.remaining.content = this.data.remaining;
		}
	},

	/**
	 * Handle a tap event on a cue
	 *
	 * @protected
	 *
	 * @param   inSender  enyo.instance  Sender of the event
	 * @param   inEvent   Object		 Event fired
	 *
	 * @return  true
	 *
	 * @since  0.0.1
	 */
	cueTap: function (inSender, inEvent)
	{
		if (this.hasClass('info'))
		{
			this.removeClass('info');
			this.doCueDeselect();
		}
		else
		{
			this.addClass('info');
			this.doCueSelect();
		}

		// Prevents event propagation
		return true;
	},

	/**
	 * Detect a change in the remaining value
	 *
	 * @protected
	 *
	 * @param  oldValue  array  Old remaining value
	 *
	 * @return  void
	 *
	 * @since  0.0.1
	 */
	remainingChanged: function (oldValue)
	{
		if (this.remaining == 0)
		{
			this.$.remaining.content = '';
		}
		else
		{
			this.$.remaining.content = this.remaining;
		}
	}
});
