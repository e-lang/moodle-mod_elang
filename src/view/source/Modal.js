/**
 * Modal to alert when an ajax request failed
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
	name: 'Elang.Modal',

	/**
	 * Published properties:
	 * - message: message to be displayed
	 * Each property will have a public setter and a getter method
	 */
	published: {message: ''},

	/**
	 * css classes
	 */
	classes: 'modal hide fade',

	/**
	 * Named components:
	 * - message: where to display the message
	 */
	components: [
		{
			classes: 'modal-header',
			components: [
				{
					tag: 'button',
					type: 'button',
					classes: 'close',
					attributes:
					{
						'data-dismiss': 'modal',
						'aria-hidden': 'true'
					},
					content: 'x'
				},
				{
					tag: 'h1',
					content: $L('Error')
				},
			]
		},
		{
			classes: 'modal-body',
			components: [
				{
					name: 'message',
					classes: 'alert',
					tag: 'p',
				}
			]
		},
		{
			classes: 'modal-footer',
			components: [
				{
					tag: 'button',
					classes: 'btn',
					attributes:
					{
						'data-dismiss' :
						'modal',
						'aria-hidden': 'true'
					},
					content: $L('Close')
				}
			]
		},
	],

	/**
	 * Detect a change in the message property
	 *
	 * @protected
	 *
	 * @param   oldValue  string  The message old value
	 *
	 * @return  void
	 */
	messageChanged: function (oldValue)
	{
		this.$.message.content = this.message;
		this.render();
		$('#' + this.id).modal('toggle');
	},
});

