//
var ajaxRequest = [[2.1,16.1],[16.1,20.1],[20.1,29.0],[30.1,38.306],[38.5,44.5],[49.5,55]];
var soustitreajax = ["arduino-en.vtt","en_UK"];
var videoajax = ["mov_bbb.mp4","mov_bbb.mp4"];

//variable est à true si c'est la première fois que l'on joue la video
//var firstPlay = true;
//variable à true si la vidéo est sur play, et sur false si la vidéo est en pause
var boolPP = false;
//slide bar défilant suivant l'évolution de la timeline de la vidéo
var movingSlide;
//variable contenant l'objet video
var video;

//Toute l'application est encapsulée dans ce kind
enyo.kind({
	name: "Video",
	kind: "FittableRows",
	fit: true,
	components:[
		//Video
		{kind: "elang.Video", name : "video", source:videoajax, sstitre : [soustitreajax]}//,
		],
	
	//play de la video et gère le premier play de la video (initialisation de la slideBar)
	//mise en place de la balise video et de la balise de sous titres (track)
	create: function(){
		this.inherited(arguments);
	}
	
});

//balise video
enyo.kind({
	name:"elang.Video",
	published:{
		source : [],
		sstitre : []
		//width : 200
	},
	components:[
		{
			name: "html",
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
	/*fonction de control, play pause...*/
	play: function() {
		document.getElementById(this.$.html.getAttribute('id')).play();
	},
	pause: function() {
		document.getElementById(this.$.html.getAttribute('id')).pause();
	},	
	stop: function() {
		document.getElementById(this.$.html.getAttribute('id')).pause();
		document.getElementById(this.$.html.getAttribute('id')).currentTime = 0 ;	
	},
	/***/
	
	/*fonction associée au boutton de durée de la video*/
	prev: function(){
		//document.getElementById(this.$.html.getAttribute('id')).currentTime -=  1;
		var bool = false;
		
			for(var i in ajaxRequest){
				if(this.getCurrentTime()-1<=ajaxRequest[i][0]){
					bool = true;
					if(i > 1){
						document.getElementById(this.$.html.getAttribute('id')).currentTime = ajaxRequest[i-1][0];
					}else{
						document.getElementById(this.$.html.getAttribute('id')).currentTime = 0.1;
					}
					break;
				}
			}
			if(!bool){
				document.getElementById(this.$.html.getAttribute('id')).currentTime = ajaxRequest[ajaxRequest.length-1][0];
			}
		
	},
	next: function(){
		//document.getElementById(this.$.html.getAttribute('id')).currentTime += 1;
		
			for(var i in ajaxRequest){
				if(this.getCurrentTime()<ajaxRequest[i][1]){
					document.getElementById(this.$.html.getAttribute('id')).currentTime = ajaxRequest[i][1];
					break;
				}
			}
		
	},
	/***/
		
	/*slider de durée*/
	setCurrentTime: function(value) {
		if(value < document.getElementById(this.$.html.getAttribute('id')).duration){
			document.getElementById(this.$.html.getAttribute('id')).currentTime = value;
		}
	},
	getCurrentTime: function() {
		return document.getElementById(this.$.html.getAttribute('id')).currentTime;
	},
	getMaxDuration : function(){
		return document.getElementById(this.$.html.getAttribute('id')).duration;
	},
	/***/
	
	/*fonction utiles, récupération de l'id*/
	getId: function() {
		return this.$.html.getAttribute('id');
	},
	clearSource: function(){
		
	},
	/***/
	
	/*function d'initialisation de la balise*/
	create : function(){
		this.inherited(arguments);
		this.$.html.setAttribute("width",this.width);
		for(var sour in this.source){
			this.$.html.createComponent({
				tag: "source",
				attributes:{src:this.source[sour]}
			});
		}
		this.$.soustitre.setAttribute("src",this.sstitre[0][0]);
		this.$.soustitre.setAttribute("srclang",this.sstitre[0][1]);
		this.$.soustitre.setAttribute("type","text/vtt");
		this.$.soustitre.setAttribute("default","default");
		for(var sour =1; sour < this.sstitre.length; sour++){
			this.$.html.createComponent({
				tag: "track",
				attributes:{
					kind:"captions",
					src:this.sstitre[sour][0],
					type:"text/vtt",
					srclang:this.sstitre[sour][1]
				}
			});
		}
		
	}
});
