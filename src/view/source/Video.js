/**
 * Video kind
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
	name: 'Elang.Video',

	/**
	 * Published properties:
	 * - poster: the video poster
	 * - track: the track url
	 * - language: the track language (Valid BCP47 code http://dev.w3.org/html5/spec/Overview.html#refsBCP47)
	 * - time: current time
	 * - begin: play beginning
	 * - end: play ending
	 * Each property will have public setter and getter methods
	 */
	published: {poster: '', track: '', language: '', time: 0, begin: 0, end: Infinity},

	/**
	 * Handlers:
	 * - ontimeupdate: fired when the video time change
	 */
	handlers: {ontimeupdate: 'timeUpdate'},

	/**
	 * Kind tag
	 */
	tag: 'video',

	/**
	 * Content tag
	 */
	content: 'Your user agent does not support the HTML5 Video element.',

	/**
	 * Kind attributes
	 */
	attributes: {controls: 'controls'},

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
	 * Play the video
	 *
	 * @public
	 *
	 * @return  this
	 */
	play: function ()
	{
		document.getElementById(this.id).play();
		return this;
	},

	/**
	 * Pause the video
	 *
	 * @public
	 *
	 * @return  this
	 */
	pause: function ()
	{
		document.getElementById(this.id).pause();
		return this;
	},

	/**
	 * Change a cue text
	 *
	 * @public
	 *
	 * @param   number  integer  The cue number
	 * @param   text    string   The new text
	 *
	 * @return  this
	 */
	changeCue: function (number, text)
	{
		document.getElementById(this.id).textTracks[0].cues[number].text = text;
		return this;
	},

	/**
	 * Add a source to the video object
	 *
	 * @public
	 *
	 * @param   src   string  URL of the source
	 * @param   type  string  Mime type of the source
	 *
	 * @return  this
	 */
	addSource: function (src, type)
	{
		this.createComponent({tag: 'source', attributes: {src: src, type: type}});
		return this;
	},

	/**
	 * Get the current time
	 *
	 * @public
	 *
	 * @return  The current time
	 */
	getTime: function ()
	{
		return document.getElementById(this.id).currentTime;
	},

	/**
	 * Create function
	 *
	 * @protected
	 *
	 * @return  void
	 */
	create: function ()
	{
		this.inherited(arguments);

		enyo.ready(
			function () {
				// Listen for video events
				enyo.dispatcher.listen(document.getElementById(this.id), 'timeupdate');
			},
			this
		);
	},

	/**
	 * Handle a timeupdate event on a video
	 *
	 * @protected
	 *
	 * @param   inSender  enyo.instance  Sender of the event
	 * @param   inEvent   Object		    Event fired
	 *
	 * @return void
	 */
	timeUpdate: function (inSender, inEvent)
	{
		var vid=document.getElementById(this.id);
		if (vid.currentTime >= this.end)
		{
			vid.pause();
			vid.currentTime = this.begin;
		}
		inEvent.time = document.getElementById(this.id).currentTime;
	},

	/**
	 * Detect a change in the poster property
	 *
	 * @protected
	 *
	 * @param   oldValue  string  The poster old value
	 *
	 * @return void
	 */
	posterChanged: function (oldValue)
	{
		this.setAttribute('poster', this.poster);
	},

	/**
	 * Detect a change in the track source
	 *
	 * @protected
	 *
	 * @param   oldValue  string  The track source old value
	 *
	 * @return void
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
	 */
	languageChanged: function (oldValue)
	{
		this.$.track.setAttribute('srclang', this.language);
	},

	/**
	 * Detect a change in the time property
	 *
	 * @protected
	 *
	 * @param   oldValue  number  The end property old value
	 *
	 * @return void
	 */
	timeChanged: function (oldValue)
	{
		document.getElementById(this.id).currentTime = this.time;
	},
});
