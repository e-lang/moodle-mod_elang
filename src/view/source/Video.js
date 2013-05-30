//enyo.dispatcher.listen(document, "timeupdate",function(){alert("ok");});
var ajaxRequest = [[2.1,16.1],[16.1,20.1],[20.1,29.0],[30.1,38.306],[38.5,44.5],[49.5,55]];
var soustitreajax = ["arduino-en.vtt","en_UK"];
var videoajax = ["mov_bbb.mp4","mov_bbb.mp4"];
//enyo.dispatcher.listen(document, "myEvent");
//variable est à true si c'est la première fois que l'on joue la video
// var myvid=document.getElementById(this.$.html.getAttribute('id'));

//Toute l'application est encapsulée dans ce kind

enyo.kind({
	name: "Video",
	kind: "FittableRows",
	source:videoajax, 
	sstitre : [soustitreajax],
	fit: true,
	components:[
		{name: "html",
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
		},
		{kind:"onyx.Button", content: "Button Test",name : "btntest", ontap:"initSequence"}
	],
	beginvid:3,
	endvid:5,
	handleTimeUpdate:function(){
		var vid=document.getElementById(this.$.html.getAttribute('id'));
		if (vid.currentTime >= this.endvid){
			vid.pause();
			vid.currentTime=this.beginvid;
		}
	},
	// echo:function() {
		// alert('echo');
		// //document.getElementById(this.$.html.getAttribute('id')).addEventListener('pause', function() {alert('echo');});
	// },
	clearSource: function(){
	},
	initSequence : function(inSender,inEvent){
		var myvid = document.getElementById(this.$.html.getAttribute('id'));
		myvid.currentTime=this.beginvid;
	//	myvid.play();
	},
		/*function d'initialisation de la balise*/
	create : function(){
		this.inherited(arguments);
		
		//this.$.html.setAttribute("width",this.width);
		//document.timeupdate=enyo.dispatch;
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
		this.getId("testmsg");
		}
		
});