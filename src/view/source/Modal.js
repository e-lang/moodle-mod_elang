/**
 * Modal to alert when the ajax request failed
 *
 * @package     mod
 * @subpackage  elang
 * @copyright   2013 University of La Rochelle, France
 * @license     http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html CeCILL-B license
 */
enyo.kind({
	name: 'Elang.Modal',

	classes: 'modal hide fade',

	published:
	{
		message: ''
	},

	components:
	[
		{
			classes: 'modal-header',
			components:
			[
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
					content: 'Error'
				},
			]
		},
		{
			classes: 'modal-body',
			components:
			[
				{
					name: 'message',
					classes: 'alert',
					tag: 'p',
				}
			]
		},
		{
			classes: 'modal-footer',
			components:
			[
				{
					tag: 'button',
					classes: 'btn',
					attributes:
					{
						'data-dismiss' :
						'modal',
						'aria-hidden': 'true'
					},
					content: 'Close'
				}
			]
		},
	],

	messageChanged: function (oldValue)
	{
		this.$.message.content = this.message;
		this.render();
		$('#' + this.id).modal('toggle');
	},
});

