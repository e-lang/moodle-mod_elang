/**
 * Cues kind
 *
 * @package     mod
 * @subpackage  elang
 * @copyright   2013 University of La Rochelle, France
 * @license     http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html CeCILL-B license
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
	 * Each property will have a public setter and a getter method
	 */
	published: {elements: [], limit: 10},

	/**
	 * Events:
	 * - onCueSelect: fired when the cue is changed
	 * - onCueDeselect: fired when the cue is deselected
	 */
	events: {onCueSelect: '', onCueDeselect: ''},

	/**
	 * Handlers:
	 * - onPageChange: fired when the page changes
	 */
	handlers: {onPageChange: 'pageChange'},

	/**
	 * Named components:
	 * - pagination: table pagination
	 * - body: table body
	 */
	components: [
		// Pagination
		{
			kind: 'Elang.Pagination', name: 'pagination'
		},

		{
			tag: 'table',
			classes: 'table table-bordered table-condensed table-striped',
			components: [
				{
					tag: 'caption', content: $L('Cue Listing')},
				{
					tag: 'thead',
					components: [
						{
							tag: 'tr',
							components: [
								{tag: 'th', attributes: {width: '7%'}, content: $L('#')},
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
		}
	],

	/**
	 * Handle a page change event
	 *
	 * @protected
	 *
	 * @param  inSender  enyo.instance  Sender of the event
	 * @param  inEvent   Object		    Event fired
	 *
	 * @return void
	 */
	pageChange: function (inSender, inEvent)
	{
		this.fillCues(inEvent.number);
		this.doCueDeselect();
	},

	/**
	 * Handle a tap event on a cue
	 *
	 * @protected
	 *
	 * @param  inSender  enyo.instance  Sender of the event
	 * @param  inEvent   Object		    Event fired
	 *
	 * @return void
	 */
	cueTap: function (inSender, inEvent)
	{
		for (var i in this.$.body.children)
		{
			this.$.body.children[i].removeClass('info');
		}
		if (this.current == inSender.cue)
		{
			this.current = null;
			this.doCueDeselect();
		}
		else
		{
			inSender.parent.parent.addClass('info');
			this.current = inSender.cue;
			this.doCueSelect({cue: inSender.cue});
		}
	},

	/**
	 * Detect a change in the limit value
	 *
	 * @protected
	 *
	 * @param  oldValue  integer  Old limit value
	 *
	 * @return  void
	 */
	limitChanged: function (oldValue)
	{
		if (this.limit > 0)
		{
			this.changeTotal();
		}
		else
		{
			var limit = this.limit;
			this.limit = oldValue;
			throw new RangeError('Limit value "' + limit + '" is incorrect');
		}
	},

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
	 */
	fillCues: function (page)
	{
		this.$.pagination.setTotal(((this.elements.length / this.limit) | 0) + (this.elements.length % this.limit > 0 ? 1 : 0));		
		var start = page * this.limit;
		var elements = this.elements.slice(start, start + this.limit);
		this.$.body.destroyClientControls();
		for (var i=0; i < elements.length; i++)
		{
			this.$.body.createComponent(
				{
					tag: 'tr',
					components:
					[
						{tag: 'td', content: start + i + 1},
						{
							tag: 'td',
							components:
							[
								{
									tag: 'a',
									ontap: 'cueTap',
									cue: this.elements[start + i],
									attributes:
									{
										href:'#'
									},
									content: elements[i].title
								}
							]
						}
					]
				},
				{owner: this}
			);
		}
		return this.render();
	},
});
