enyo.kind({
	name: "elang.input",
	classes: "input",


		components: [
		
		{classes: "onyx-sample-tools", components:
			[
				{kind:"onyx.Button", name:"Fishbowl Button", content: "Button", ontap:"buttonTapped"}
			]
		}/* a supprimper lors du push*/,

		{kind: "onyx.Groupbox", classes:"onyx-sample-result-box", components: 
			[
				{kind: "onyx.GroupboxHeader", content: "Result"},
				{name:"result", classes:"onyx-sample-result"}
			]
		}
		
	
	],
	
	buttonTapped: function(inSender, inEvent) {
		if (inSender.content){
			this.addText(10,"The button was tapped");
			this.addInput();
		} else {
			this.$.result.setContent("The \"" + inSender.getName() + "\" button was tapped");
		}
	},
	
	addText: function(noSec, text) {
			//this.$.input_text.setContent("addText the test");
			 this.$.result.addContent(noSec+" "+text);
			},
	
	addInput: function(/*noSec, noInp*/) {
			//this.$.result.addContent("addInput the test");
			
	},
	
	reset: function() {
			this.$.result.setContent("");
			
	}

	
});

