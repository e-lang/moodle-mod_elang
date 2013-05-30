// Main application structure
enyo.kind({
	name : "App",
	classes: "container-fluid",
	handlers: {
		onSequenceItemTapped : "sequenceTapped",
		onRenderTapped : "renderTapped",
		onHelpTapped : "helpTapped",	
	},
	components:[
		// Title and description
		{tag: "div", name: "header", classes:"row-fluid", components:[
			{tag: "div", name: "title_span12", classes:"span12", components:[
				{tag: "div", name:"title_frame", classes:"well well-small", components:[
					{kind: "Head", name :"head"}
				]}
			]}
		]},
		{tag: "div", name: "body", classes:"row-fluid", components:[
			// Video
			{tag: "div", name: "video_span6", classes:"span6", components:[
				{kind: "Video", name : "video"},
			]},
			// Sequence list
			{tag: "div", name: "list_span6", classes:"span6", components:[
				{kind: "testsequences", name : "list"},
			]}
		]},
		// Exercise
		{tag: "div", name: "footer", classes:"row-fluid", components:[
			{tag: "div", name: "input_span12", classes:"span12", components:[
				//{kind: "testinputs"},
				{kind: "elang.input", name :"inputs"}
			]}
		]}
	], 
	
	
	/* 
	TODO : JULIE 
	// create: function(){
	// },
	
	// getData: function(){
	// },
	
	// getDataResponse: function(inRequest, inResponse){
	// },
	*/
	
	sequenceTapped:function(inSender,inEvent){
      //alert("sequenceTapped");
	  this.$.input.setInput();
    }
	
	helpTapped:function(inSender,inEvent){
      //alert("helpTapped");
	  this.$.sequences.setHelp();
    }
	
	renderTapped:function(inSender,inEvent){
      //alert("renderTapped");
	  this.$.sequences.setColor();
    }
	
	
});

