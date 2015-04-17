/**
 * Video kind
 *
 * @package	 mod
 * @subpackage  elang
 * @copyright   2013-2015 University of La Rochelle, France
 * @license	 http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html CeCILL-B license
 *
 * @since	   0.0.1
 */
enyo.kind({
	/**
	 * Name of the kind
	 */
	name: 'Elang.Video',

	/**
	 * Super kind
	 */
	kind: 'Video',

	/**
	 * Published properties:
	 * - track: the track url
	 * - language: the track language (Valid BCP47 code http://dev.w3.org/html5/spec/Overview.html#refsBCP47)
	 * - begin: play beginning
	 * - end: play ending
	 * Each property will have public setter and getter methods
	 */
	published: {track: '', language: '', begin: 0, end: Infinity},

	/**
	 * Handlers:
	 * - ontimeupdate: fired when the video time change
	 */
	handlers: {ontimeupdate: 'timeUpdate'},

	/**
	 * Content tag
	 */
	content: 'Your user agent does not support the HTML5 Video element.',

	/**
	 * Kind classes
	 */
	classes: 'elang',

	/**
	 * Named components:
	 * - track: track tag
	 */
	components: [{name: 'track', tag: 'track', attributes: {kind: 'captions', type: 'text/vtt', default: 'default', srclang: 'en-GB'}}],

	/**
	 * Change a cue text
	 *
	 * @public
	 *
	 * @param   number  integer  The cue number
	 * @param   text	string   The new text
	 *
	 * @return  this
	 *
	 * @since  0.0.1
	 */
	changeCue: function (number, text)
	{
		try
		{
			document.getElementById(this.id).textTracks[0].cues[number].text = text;
		}
		catch (err)
		{
			// For MSIE :(
		}
		return this;
	},

	/**
	 * Render this control
	 *
	 * @public
	 *
	 * @return  this
	 *
	 * @since  1.0.0
	 */
	render: function()
	{
		this.inherited(arguments);
		this.$.track.render();
		return this;
	},

	/**
	 * Handle a timeupdate event on a video
	 *
	 * @protected
	 *
	 * @param   inSender  enyo.instance  Sender of the event
	 * @param   inEvent   Object			Event fired
	 *
	 * @return void
	 *
	 * @since  0.0.1
	 */
	timeUpdate: function (inSender, inEvent)
	{
		if (this.getCurrentTime() >= this.end)
		{
			this.pause();
			this.setCurrentTime(this.begin);
		}

		return false;
	},

	/**
	 * Detect a change in the track source
	 *
	 * @protected
	 *
	 * @param   oldValue  string  The track source old value
	 *
	 * @return void
	 *
	 * @since  0.0.1
	 */
	trackChanged: function (oldValue)
	{
		this.$.track.setAttribute('src', this.track);
	},

	/**
	 * Detect a change in the track language
	 *
	 * @protected
	 *
	 * @param   oldValue  string  The track language old value
	 *
	 * @return void
	 *
	 * @since  0.0.1
	 */
	languageChanged: function (oldValue)
	{
		this.$.track.setAttribute('srclang', this.language);
	},
});
