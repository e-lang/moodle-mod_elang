/**
 * Input kind
 *
 * @package     mod
 * @subpackage  elang
 * @copyright   2013-2018 University of La Rochelle, France
 * @license     http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html CeCILL-B license
 *
 * @since       0.0.3
 */
enyo.kind(
	{
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
		classes: 'input-group form-group',

		/**
		 * Published properties:
		 * - value: the current input value
		 * - error: the error state (true or false)
		 * - help: the help state (true or false)
		 * - link: the link (string)
		 * - size: the size of the input
		 * - number: the item number
		 * Each property will have public setter and getter methods
		 */
		published: {value: null, error: null, help: null, link: null, size: null, number: null},

		/**
		 * Named components:
		 * - input: The input control
		 */
		components: [{
			kind: 'Input',
			name: 'input',
			classes: 'form-control',
			attributes: {type: 'text'}
		}],

		/**
		 * Events:
		 * - onTextChange: fired when the input text has changed
		 * - onHelpTap: fired when the help button was tapped
		 */
		events: {onTextChange: '', onHelpTap: ''},

		/**
		 * Handler:
		 * -onkeypress: fired when the user press a key in the input
		 */
		handlers: {onkeypress: 'keyPress'},

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
				this.addClass('has-error');
			}
			else
			{
				this.removeClass('has-error');
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
			this.$.input.applyStyle('width', (Math.ceil(this.size / 10) * 5) + 'em');
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
						classes: 'btn input-group-addon',
						attributes: {title: $L('inputhelp'), 'data-toggle':'tooltip', href: '#'},
						components: [{tag: 'span', classes: 'glyphicon glyphicon-info-sign'}]
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
		 * Detect a change in the link property
		 *
		 * @protected
		 *
		 * @param   oldValue  string|null  The link old value
		 *
		 * @since  0.0.3
		 */
		linkChanged: function (oldValue)
		{
			if (this.link)
			{
				this.createComponent(
					{
						name: 'link',
						tag: 'a',
						classes: 'btn input-group-addon',
						attributes: {title: $L('inputlink'), 'data-toggle':'tooltip', href: this.link, target: '_blank'},
						components: [{tag: 'span', classes: 'glyphicon glyphicon-link'}]
					}
				);
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
			this.linkChanged();
			this.errorChanged();
		},

		/**
		 * Handle keypress event
		 *
		 * @protected
		 *
		 * @param   inSender  enyo.instance  Sender of the event
		 * @param   inEvent   Object		 Event fired
		 *
		 * @return  true
		 *
		 * @since  1.0.0
		 */
		keyPress: function (inSender, inEvent)
		{
			if (inEvent.keyCode == 13 && this.value != inSender.getValue())
			{
				this.value = inSender.getValue();
				this.doTextChange();

				// Prevents event propagation
				return true;
			}

			return false;
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
	}
);
