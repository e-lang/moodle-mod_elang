//
var ajaxRequest = [[2.1,16.1],[16.1,20.1],[20.1,29.0],[30.1,38.306],[38.5,44.5],[49.5,55]];
var soustitreajax = ["arduino-en.vtt","en_UK"];
var videoajax = ["mov_bbb.ogv","mov_bbb.mp4"];

//taille de la video et de la slidebar
var tailleVideo = 100%;
//variable est à true si c'est la première fois que l'on joue la video
var firstPlay = true;
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
		{kind: "elang.Video", name : "video", source:videoajax, sstitre : [soustitreajax], width: tailleVideo},
		//slide bar défilant suivant l'évolution de la timeline de la vidéo
		{kind: "onyx.Slider", name:"movingSlide", style: "display : none", lockBar: true, value: 0, onChange:"sliderVideoChanged"},
		//Boutons de controle
		{kind: "onyx.Toolbar", components: [
			{kind: "onyx.Button", style: "height: 35px;width: 35px;", components: [{kind: "onyx.Icon", src: "im/pass_chap_prec.png", style:"background-position:center top; background-size: 22px 22px"}], ontap: "prev"},
			{kind: "onyx.Button", style: "height: 35px;width: 35px;", name:"PP", components: [{kind: "onyx.Icon", name:"icone", src: "im/play.png", style:"background-position:center top;background-size: 22px 22px"}], ontap: "PP"},
			{kind: "onyx.Button", style: "height: 35px;width: 35px;", components: [{kind: "onyx.Icon", src: "im/stop.png", style:"background-position:center top;background-size: 22px 22px"}], ontap: "stop"},
			{kind: "onyx.Button", style: "height: 35px;width: 35px;", components: [{kind: "onyx.Icon", src: "im/pass_chap_suiv.png", style:"background-position:center top;background-size: 22px 22px"}], ontap: "next"},
			{kind: "onyx.Button", style: "height: 35px;width: 35px;", components: [{kind: "onyx.Icon", src: "im/vol_down.png", style:"background-position:center top;background-size: 22px 22px"}], ontap: "volmoins"},
			{kind: "onyx.Button", style: "height: 35px;width: 35px;", components: [{kind: "onyx.Icon", src: "im/vol_up.png", style:"background-position:center top;background-size: 22px 22px"}], ontap: "volplus"},
			{kind: "onyx.Button", style: "height: 35px;width: 35px;", components: [{kind: "onyx.Icon", src: "im/vol_off_rouge.png", style:"background-position:center top;background-size: 20px 20px"}], ontap: "voloff"},
			{kind: "onyx.Slider", name:"volum",style: "width: 40px;",min:0, max:1, lockBar: true, value: 1, onChange:"sliderChanged"}
		]}
	],
	//Fonction gérant l'utilisation d'un bouton unique pour faire play et pause
	PP: function(){
		boolPP = !boolPP;
		if(boolPP){
			this.play();
			this.$.icone.setSrc('im/pause.png');
		}else{
			this.pause();
			this.$.icone.setSrc('im/play.png');
		}
	},
	//play de la video et gère le premier play de la video (initialisation de la slideBar)
	play: function() {
		if(firstPlay){
			this.$.movingSlide.setMax(this.$.video.getMaxDuration());
			this.$.movingSlide.setStyle("width: "+(tailleVideo-40)+"px");
			firstPlay = !firstPlay;
			movingSlide = this.$.movingSlide.getAttribute('id');
			video = this.$.video.getId();
			setInterval(function(){
				document.getElementById(movingSlide).childNodes[0].style.width = 
					((document.getElementById(video).currentTime/document.getElementById(video).duration)*100)+
					"%";
				document.getElementById(movingSlide).childNodes[2].style.left = 
					((document.getElementById(video).currentTime/document.getElementById(video).duration)*100)+
					"%";
			},(this.$.video.getMaxDuration()/tailleVideo));
		}
		this.$.video.play();
	},
	//fonction pause de la video
	pause: function() {
		this.$.video.pause();
	},
	//fonction pause de la video + retour au début
	stop: function() {
		this.$.video.stop();
		if(boolPP){
			boolPP = false;
			this.$.icone.setSrc('im/play.png');
		}
	},
	//fonction renvoyant au début du chapitre puis au chapitre précédent
	prev: function() {
		if(!firstPlay){
			this.$.video.prev();
		}
	},
	//fonction renvoyant au chapitre suivant
	next: function(){
		if(!firstPlay){
			this.$.video.next();
		}
	},
	//augmentation du volume
	volplus: function(){
		this.$.video.volPlus();
		this.majVol();
	},
	//diminution du volume
	volmoins: function(){
		this.$.video.volMoins();
		this.majVol();
	},
	//mise à zéro du volume
	voloff: function(){
		this.$.video.volOff();
		this.majVol();
	},
	//mise a jour de la barre son
	majVol: function(){
		document.getElementById(this.$.volum.getAttribute('id')).childNodes[0].style.width = 
			(this.$.video.getVol()*100)+
			"%";
		document.getElementById(this.$.volum.getAttribute('id')).childNodes[2].style.left = 
			(this.$.video.getVol()*100)+
			"%";
	},
	//slider gérant le volume
	sliderChanged: function(inSender, inEvent) {
		this.$.video.setVolum(inSender.getValue());
	},
	//slider gérant le changement de la timeline de la vidéo en cliquant dessus
	sliderVideoChanged: function(inSender, inEvent) {
		this.$.video.setCurrentTime(inSender.getValue());
	},
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
		sstitre : [],
		width : 200
	},
	components:[
		{
			name: "html",
			tag: "video",
			content: "Your user agent does not support the HTML5 Video element.",
			components:[
				{
					name:"soustitre",
					tag: "track",
					attributes:{
						kind:"captions",
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
	
	/*fonction associées aux boutons de volumes*/
	volPlus: function(){
		if(document.getElementById(this.$.html.getAttribute('id')).volume < 0.9){
			document.getElementById(this.$.html.getAttribute('id')).volume += 0.1;
		}
	},
	volMoins: function(){
		if(document.getElementById(this.$.html.getAttribute('id')).volume > 0.1){
			document.getElementById(this.$.html.getAttribute('id')).volume -= 0.1;
		}
	},
	volOff: function(){
		document.getElementById(this.$.html.getAttribute('id')).volume = 0;
	},
	getVol: function(){
		return document.getElementById(this.$.html.getAttribute('id')).volume;
	},
	/***/
	
	/*slider volum*/
	setVolum: function(value) {
		document.getElementById(this.$.html.getAttribute('id')).volume = value;
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
