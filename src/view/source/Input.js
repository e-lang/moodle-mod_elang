enyo.kind({
	name: "elang.input",
	classes: "input",


		components: [
		
		{classes: "onyx-sample-tools", components:
			[
				{kind:"onyx.Button", name:" Text", content: "Text", id:1, ontap:"buttonTapped"},
				{kind:"onyx.Button", name:" Input", content: "Input", id:2, ontap:"buttonTapped"},
				{kind:"onyx.Button", name:" Check", content: "Check", id:3, ontap:"buttonTapped"},
				{kind:"onyx.Button", name:" Help", content: "Help", id:4, ontap:"buttonTapped"},
				{kind:"onyx.Button", name:" Reset", content: "Reset", id:5, ontap:"buttonTapped"},
				{kind:"onyx.Button", name:" Render", content: "Render", id:6, ontap:"buttonTapped"}
			]
		}/* a supprimper lors du push*/,

		
		{name:"result", kind: enyo.Control, components:[]}
		
	
	],
	
	buttonTapped: function(inSender, inEvent) {
		
		switch (inSender.id) {
		case 1 : 
			this.addText(10,"The button was tapped");
			break;
		case 2 : 
			this.addInput();
			break;
		case 3 : 
			this.addTextCheck(11, "check");
			break;
		case 4 : 
			this.addTextHelp(12, "help");
			break;
		case 5 : 
			this.reset();
			break;		
		case 6 : 
			this.render();
			break;		
		}

	},
	
	addText: function(noSec, txt) {
		//this.$.result.addContent(text);
		this.$.result.createComponent({tag:"p", content: txt});
	},
	
	addInput: function(/*noSec, noInp*/) {
		this.$.result.createComponent({tag:'input'});
	},

	addTextCheck: function(noSec, text) {
	},
	
	addTextHelp: function(noSec, text) {
	},
	
	reset: function() {
		this.$.result.components = [];
			
	},
	
	render: function() {
		this.$.result.render();	
	}	
	
});

