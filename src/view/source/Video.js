/**
 * Kind video
 */
enyo.kind({
	name: "Elang.Video",

	published: {
		poster: '',
		track: '',
		language: '',
		currentSequenceBegin:0,
		end:Infinity
	},

	tag: "video",

	content: "Your user agent does not support the HTML5 Video element.",

	attributes: {controls: "controls"},

	classes: "elang",

	components:[
		{
			name:"track",
			tag: "track",
			attributes:{
				kind: 'captions',
				type: 'text/vtt',
				default: 'default'
			}
		}	
	],

	handleTimeUpdate:function(){
		var vid=document.getElementById(this.getAttribute('id'));
		if (vid.currentTime >= this.end || vid.currentTime < this.currentSequenceBegin){
			vid.pause();
			vid.currentTime=this.currentSequenceBegin;
		}
	},

	play: function() {
		this.end = Infinity;
	},

	updateSubtitles:function(sequenceID,sequenceText){
		var vid=document.getElementById(this.getAttribute('id'));
		var tracks=vid.textTracks;
		tracks[0].cues[sequenceID].text.processedCue=sequenceText;		
	},

	clearSource: function() {
	},

	setSequence : function(begin,end){
		this.currentSequenceBegin=begin;
		this.end=end;
		var myvid = document.getElementById(this.getAttribute('id'));
		myvid.currentTime=this.currentSequenceBegin;
	//	myvid.play();
	},

	/**
	 * Method to add a source to the video object
	 *
	 * @param   src   string  URL of the source
	 * @param   type  string  Mime type of the source
	 */
	addSource: function(src, type) {
		this.createComponent({
			tag: "source",
			attributes: {src: src, type: type}
		});
		return this;
	},

	/**
	 * Method to detect a change in the poster property
	 *
	 * @param   oldValue  string  The poster old value
	 */
	posterChanged: function (oldValue) {
		this.setAttribute('poster', this.poster);
	},

	/**
	 * Method to detect a change in the track source
	 *
	 * @param   oldValue  string  The track source old value
	 */
	trackChanged: function (oldValue) {
		this.$.track.setAttribute('src', this.track);
	},

	/**
	 * Method to detect a change in the track language
	 *
	 * @param   oldValue  string  The track language old value
	 */
	languageChanged: function (oldValue) {
		this.$.track.setAttribute('srclang', this.language);
	}
});
