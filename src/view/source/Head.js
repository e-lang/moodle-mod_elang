/**
 * Head kind
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
	name : "Elang.Head",

	/**
	 * Published properties:
	 * - title: exercise title
	 * - pdf: URL to get the pdf
	 * Each property will have a public setter and a getter method
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
							ontap: 'printTapped',
							components: [{tag :'i', classes: 'icon-print icon-white'}, {tag: 'span', content: $L(' Get a pdf version')}]
						}
					]
				}
			]
		},
	],

	/**
	 * Detect a change in the title property
	 *
	 * @param   oldValue  string  The title old value
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
	 * @param  inSender  enyo.instance  Sender of the event
	 * @param  inEvent   Object		    Event fired
	 *
	 * @return void
	 */
	printTapped: function (inSender, inEvent)
	{
		window.open(this.pdf);
	},
});
