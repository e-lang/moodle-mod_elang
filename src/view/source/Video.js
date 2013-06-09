/**
 * Video kind
 *
 * @package     mod
 * @subpackage  elang
 * @copyright   2013 University of La Rochelle, France
 * @license     http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html CeCILL-B license
 */
enyo.kind({
	name: 'Elang.Video',

	published: {poster: '', track: '', language: '', time: 0, begin: 0, end: Infinity},

	handlers: {ontimeupdate: 'timeUpdated'},

	tag: 'video',

	content: 'Your user agent does not support the HTML5 Video element.',

	attributes: {controls: 'controls'},

	classes: 'elang',

	components:
	[
		{
			name: 'track',
			tag: 'track',
			attributes: {kind: 'captions', type: 'text/vtt', default: 'default', srclang: 'en-GB'}
		}	
	],

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

	timeUpdated: function (inSender, inEvent)
	{
		var vid=document.getElementById(this.id);
		if (vid.currentTime >= this.end)
		{
			vid.pause();
			vid.currentTime = this.begin;
		}
		inEvent.time = document.getElementById(this.id).currentTime;
	},

	play: function ()
	{
		document.getElementById(this.id).play();
	},

	pause: function ()
	{
		document.getElementById(this.id).pause();
	},

	changeCue: function (number, text)
	{
		document.getElementById(this.id).textTracks[0].cues[number].text = text;
	},

	clearSource: function ()
	{
	},

	/**
	 * Add a source to the video object
	 *
	 * @param   src   string  URL of the source
	 * @param   type  string  Mime type of the source
	 */
	addSource: function (src, type)
	{
		this.createComponent({tag: 'source', attributes: {src: src, type: type}});
		return this;
	},

	/**
	 * Detect a change in the poster property
	 *
	 * @param   oldValue  string  The poster old value
	 */
	posterChanged: function (oldValue)
	{
		this.setAttribute('poster', this.poster);
	},

	/**
	 * Detect a change in the track source
	 *
	 * @param   oldValue  string  The track source old value
	 */
	trackChanged: function (oldValue)
	{
		this.$.track.setAttribute('src', this.track);
	},

	/**
	 * Detect a change in the track language
	 *
	 * @param   oldValue  string  The track language old value
	 */
	languageChanged: function (oldValue)
	{
		this.$.track.setAttribute('srclang', this.language);
	},

	/**
	 * Detect a change in the time property
	 *
	 * @param   oldValue  number  The end property old value
	 */
	timeChanged: function (oldValue)
	{
		document.getElementById(this.id).currentTime = this.time;
	},

	/**
	 * Get the current time
	 */
	getTime: function ()
	{
		return document.getElementById(this.id).currentTime;
	},

});
