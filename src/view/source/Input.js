enyo.kind({
	name: "elang.input",
	classes: "input",


		components: [
		
		{classes: "onyx-sample-tools", components:
			[
				{kind:"Button", classes:"btn", name:" Text", content: "Text", id:1, ontap:"buttonTapped"},
				{kind:"Button", classes:"btn", name:" Input", content: "Input", id:2, ontap:"buttonTapped"},
				{kind:"Button", classes:"btn", name:" Check", content: "Check", id:3, ontap:"buttonTapped"},
				{kind:"Button", classes:"btn", name:" Help", content: "Help", id:4, ontap:"buttonTapped"},
				{kind:"Button", classes:"btn", name:" Reset", content: "Reset", id:5, ontap:"buttonTapped"},
				{kind:"Button", classes:"btn", name:" Render", content: "Render", id:6, ontap:"buttonTapped"},
				//{kind:"Button",  content:"Check", onclick:"buttonTapped"}
				
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
		default :
			alert("autre");
			break;
		}

	},
		
	checkTapped: function(inSender, inEvent) {
		switch (inSender.id) {
		case 100 :			
			alert("Check");
			break;
		case 101 :			
			alert("Help");
			break;
		default:
			alert("!!!");
			break;
		}
	},
	

	
	addText: function(noSec, text) {
		this.$.result.createComponent({tag:"p", content: text});
	},
	
	addInput: function() {
		this.$.result.createComponent(		
		{
			tag:"div", components: [
				{kind:"Input", onchange: "buttonTapped"},
				{kind:"Button", classes:"btn btn-success", id:100, content:"Check", onclick:"javascript:alert('a')"},
				{kind:"Button",  classes:"btn btn-info",  id:101, content:"Help", onclick:"checkTapped"},
			]}		
		);
	},

	addTextCheck: function(noSec, text) {
		this.$.result.createComponent(
			{tag:"div", classes:"control-group success", components:
			[
				{kind:"Input", classes:"inputSuccess", name:"Render", value:text}
			]}
		);
		
		//this.$.result.createComponent({tag:"p", classes:"text-success", content: text});
		
	},
	
	addTextHelp: function(noSec, text) {	
		this.$.result.createComponent(
			{tag:"div", classes:"control-group info", components:
			[
				{kind:"Input", classes:"inputInfo", name:"Render", value:text}
			]}
		);
	},
	
	reset: function() {
		this.$.result.destroyComponents();			
	},
	
	render: function() {
		this.$.result.render();	
	},
	
	
});

