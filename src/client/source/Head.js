/**
 * Head kind
 *
 * @package     mod_elang
 *
 * @copyright   2013-2018 University of La Rochelle, France
 * @license     http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html CeCILL-B license
 *
 * @since       0.0.1
 */
enyo.kind(
	{
		/**
		 * Name of the kind
		 */
		name : "Elang.Head",

		/**
		 * Published properties:
		 * - title: exercise title
		 * - pdf: URL to get the pdf
		 * Each property will have public setter and getter methods
		 */
		published: {title: '', pdf: ''},

		/**
		 * Named components:
		 * - title: the title
		 */
		components: [
			{
				classes: 'page-header',
				components: [
					{
						tag: 'h1',
						components: [
							{tag: 'span', name: 'title'},
							{tag: 'span', content: ' '},
							{
								tag: 'a',
								classes: 'btn btn-primary',
								attributes: {href: '#'},
								ontap: 'printTap',
								components: [
									{tag :'span', classes: 'glyphicon glyphicon-print glyphicon-white', "aria-hidden": "true"},
									{tag :'span', content: ' '},
									{tag: 'span', content: $L('getpdf')}
								]
							}
						]
					}
				]
			},
		],

		/**
		 * Detect a change in the title property
		 *
		 * @protected
		 *
		 * @param   oldValue  string  The title old value
		 *
		 * @since  0.0.1
		 */
		titleChanged: function (oldValue)
		{
			this.$.title.content = this.title;
		},

		/**
		 * Handle tap event on the print button
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
		printTap: function (inSender, inEvent)
		{
			window.open(this.pdf);

			// Prevents event propagation
			return true;
		},
	}
);
