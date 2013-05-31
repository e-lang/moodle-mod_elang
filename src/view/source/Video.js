//enyo.dispatcher.listen(document, "timeupdate",function(){alert("ok");});
var ajaxRequest = [[2.1,16.1],[16.1,20.1],[20.1,29.0],[30.1,38.306],[38.5,44.5],[49.5,55]];
var soustitreajax = ["arduino-en.vtt","en_UK"];
var videoajax = ["http://captionatorjs.com/git/video/arduino.ogv"];//
//var videoajax=["mov_bbb.mp4","mov_bbb.mp4"];
//enyo.dispatcher.listen(document, "myEvent");
//variable est à true si c'est la première fois que l'on joue la video
// var myvid=document.getElementById(this.$.myvideo.getAttribute('id'));

//Toute l'application est encapsulée dans ce kind
enyo.kind({
	name: "Video",
	source:videoajax, 
	sstitre : [soustitreajax],
	fit: true,
	components:[
	{name: "myvideo",
			tag: "video controls",
			content: "Your user agent does not support the HTML5 Video element.",
			components:[
				{
					name:"soustitre",
					tag: "track",
					attributes:{
						kind:"captions"
					}
				}	
			]
		}
	],
	published:{
		currentSequenceBegin:0,
		currentSequenceEnd:Infinity
	},
	handleTimeUpdate:function(){
		var vid=document.getElementById(this.$.myvideo.getAttribute('id'));
		if (vid.currentTime >= this.currentSequenceEnd || vid.currentTime < this.currentSequenceBegin){
			vid.pause();
			vid.currentTime=this.currentSequenceBegin;
		}
	},
	updateSubtitles:function(sequenceID,sequenceText){
		var vid=document.getElementById(this.$.myvideo.getAttribute('id'));
		var tracks=vid.textTracks;
		tracks[0].cues[sequenceID].text.processedCue=sequenceText;		
	},
	clearSource: function(){
	},
	setSequence : function(begin,end){
		this.currentSequenceBegin=begin;
		this.currentSequenceEnd=end;
		var myvid = document.getElementById(this.$.myvideo.getAttribute('id'));
		myvid.currentTime=this.currentSequenceBegin;
	//	myvid.play();
	},
		/*function d'initialisation de la balise*/
	create : function(){
		this.inherited(arguments);
		for(var sour in this.source){
			this.$.myvideo.createComponent({
				tag: "source",
				attributes:{src:this.source[sour]}
			});
		}
		this.$.soustitre.setAttribute("src",this.sstitre[0][0]);
		this.$.soustitre.setAttribute("srclang",this.sstitre[0][1]);
		this.$.soustitre.setAttribute("type","text/vtt");
		this.$.soustitre.setAttribute("default","default");
		}

});