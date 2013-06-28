/**
 * Input kind
 *
 * @package     mod
 * @subpackage  elang
 * @copyright   2013 University of La Rochelle, France
 * @license     http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html CeCILL-B license
 *
 * @since       0.0.3
 */
enyo.kind({
	/**
	 * Name of the kind
	 */
	name: "Elang.Input",

	/**
	 * Tag of the kind
	 */
	tag: 'span',

	/**
	 * css classes
	 */
	classes: 'input-append control-group',

	/**
	 * Published properties:
	 * - value: the current input value
	 * - error: the error state (true or false)
	 * - size: the size of the input
	 * - number: the item number
	 * Each property will have public setter and getter methods
	 */
	published: {value: null, error: null, help: null, size: null, number: null},

	/**
	 * Named components:
	 * - input: The input control
	 */
	components: [
		{
			kind: 'Input',
			name: 'input',
			onchange: 'textChange',
			attributes: {type: 'text'}
		},
	],

	/**
	 * Events:
	 * - onTextChange: fired when the input text has changed
	 * - onHelpTap: fired when the help button was tapped
	 */
	events: {onTextChange: '', onHelpTap: ''},

	/**
	 * Detect a change in the value property
	 *
	 * @protected
	 *
	 * @param   oldValue  string|null  The old value
	 *
	 * @since  0.0.3
	 */
	valueChanged: function (oldValue)
	{
		this.$.input.setValue(this.value);
	},

	/**
	 * Detect a change in the error property
	 *
	 * @protected
	 *
	 * @param   oldValue  boolean|null  The error old value
	 *
	 * @since  0.0.3
	 */
	errorChanged: function (oldValue)
	{
		if (this.error)
		{
			this.addClass('error');
		}
		else
		{
			this.removeClass('error');
		}
	},

	/**
	 * Detect a change in the size property
	 *
	 * @protected
	 *
	 * @param   oldValue  integer|null  The size old value
	 *
	 * @since  0.0.3
	 */
	sizeChanged: function (oldValue)
	{
		this.$.input.setClassAttribute(this.size > 50 ? 'input-xxlarge' : this.size > 40 ? 'input-xlarge' : this.size > 30 ? 'input-large' : '');
	},

	/**
	 * Detect a change in the help property
	 *
	 * @protected
	 *
	 * @param   oldValue  boolean|null  The help old value
	 *
	 * @since  0.0.3
	 */
	helpChanged: function (oldValue)
	{
		if (this.help)
		{
			this.createComponent(
				{
					name: 'help',
					tag: 'a',
					ontap: 'helpTap',
					classes: 'btn',
					attibutes: {href: '#'},
					components: [{tag: 'i', classes: 'icon-info-sign'}]
				}
			);
		}
		else if (oldValue)
		{
			this.$.help.destroy();
			this.render();
		}
	},

	/**
	 * Create method
	 *
	 * @protected
	 *
	 * @since  0.0.3
	 */
	create: function ()
	{
		this.inherited(arguments);
		this.valueChanged();
		this.sizeChanged();
		this.helpChanged();
		this.errorChanged();
	},

	/**
	 * Handle text change event
	 *
	 * @protected
	 *
	 * @param   inSender  enyo.instance  Sender of the event
	 * @param   inEvent   Object		 Event fired
	 *
	 * @return  true
	 *
	 * @since  0.0.3
	 */
	textChange: function (inSender, inEvent)
	{
		this.value = inSender.getValue();
		this.doTextChange();

		// Prevents event propagation
		return true;
	},

	/**
	 * Handle help tap event
	 *
	 * @protected
	 *
	 * @param   inSender  enyo.instance  Sender of the event
	 * @param   inEvent   Object		 Event fired
	 *
	 * @return  true
	 *
	 * @since  0.0.3
	 */
	helpTap: function (inSender, inEvent)
	{
		this.doHelpTap();

		// Prevents event propagation
		return true;
	},
});
