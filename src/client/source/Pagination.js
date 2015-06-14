/**
 * Pagination kind
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
	name: 'Elang.Pagination',

	/**
	 * Use the nav tag
	 */
	tag: 'nav',

	/**
	 * css classes
	 */
	classes: 'text-center',

	/**
	 * Published properties:
	 * - total: total number of pages
	 * Each property will have public setter and getter methods
	 */
	published: {total: 0},

	/**
	 * Named components
	 */
	components: [{name: 'pages', tag: 'ul', classes: 'pagination'}],

	/**
	 * Events:
	 * - onPageChange: fired when a page is tapped
	 */
	events: {onPageChange: ''},

	/**
	 * Detect a change in the total number of pages
	 *
	 * @protected
	 *
	 * @param  oldValue  integer  Old total number of pages
	 *
	 * @return  void
	 *
	 * @since  0.0.1
	 */
	totalChanged: function (oldValue)
	{
		this.$.pages.destroyClientControls();
		if (this.total > 1)
		{
			for (var i = 0; i < this.total; i++)
			{
				this.$.pages.createComponent(
					{
						tag: 'li',
						number: i,
						classes: i == 0 ? 'active' : '',
						components: [{tag: 'a', ontap: 'pageTap', attributes: {href: '#'}, content: i + 1}]
					},
					{owner: this}
				);
			}

			this.$.pages.show();
		}
		else if (this.total >= 0)
		{
			this.$.pages.hide();
		}
		else
		{
			var total = this.total;
			this.total = oldValue;
			throw new RangeError('Total value "' + total + '" is incorrect');
		}
	},

	/**
	 * Handle tap event on a page number
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
	pageTap: function(inSender, inEvent)
	{
		enyo.forEach(
			this.$.pages.getClientControls(),
			function (page) {
				if (page.hasClass('active'))
				{
					if (page != inSender.container)
					{
						page.removeClass('active');
						this.doPageChange({number: inSender.container.number});
					}
				}
			},
			this
		);
		inSender.container.addClass('active');

		// Prevents event propagation
		return true;
	},
});
