enyo.kind({
	name: "elang.input",
		components: [		
		{classes: "onyx-sample-tools", components:
			[
				{kind:"Button", classes:"btn", name:" Text", content: "Text", id:1, ontap:"buttonTapped"},
				{kind:"Button", classes:"btn", name:" Input", content: "Input", id:2, ontap:"buttonTapped"},
				{kind:"Button", classes:"btn", name:" Check", content: "Check", id:3, ontap:"buttonTapped"},
				{kind:"Button", classes:"btn", name:" Help", content: "Help", id:4, ontap:"buttonTapped"},
				{kind:"Button", classes:"btn", name:" Reset", content: "Reset", id:5, ontap:"buttonTapped"},
				{kind:"Button", classes:"btn", name:" Render", content: "Render", id:6, ontap:"buttonTapped"},
				{kind:"Button", classes:"btn", name:" ReplaceVideo", content: "Render", id:6, ontap:"buttonTapped"},
				
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
	
	addText: function(noSec, text) {
		this.$.result.createComponent({tag:"p", content: text});
		this.$.result.render();	
	},
	
	addInput: function() {
		this.$.result.createComponent(		
		{

			tag:"div", classes:"salut", id:22, components: [

				{kind:"Input", id:28, classes:"inputInfo"},
				{kind:"Button", classes:"btn btn-success", ontap:"checkTapped", id:100, name:"Check", content:"Check"},
				{kind:"Button", classes:"btn btn-info",  ontap:"checkTapped", id:101, name:"Help", content:"Help"},	
			]},
			{owner: this}
		);
		this.$.result.render();	
		//alert(document.getElementById(22).getAttribute('id'));

		document.getElementById(28).setAttribute("classes","control-group success");

		
		//alert(document.getElementsByClassName('btn btn-success'));
		//document.getElementsByClassName('btn btn-success').setAttribute('id',121);
		//alert(document.getElementsByName('Help').getAttribute());
		this.$.result.render();
	},

	addTextCheck: function(noSec, text) {
		this.$.result.createComponent(
			{tag:"div", classes:"control-group success", components:
			[
				{kind:"Input", classes:"inputSuccess", name:"Render", value:text}
			]}				
		);
		
		//this.$.result.createComponent({tag:"p", classes:"text-success", content: text});
		this.$.result.render();	
	},
	
	addTextHelp: function(noSec, text) {	
		this.$.result.createComponent(
			{tag:"div",  components:
			[
				{kind:"Input", classes:"inputInfo", name:"Render", value:text}
			]}
		);
		this.$.result.render();	
	},
	
	reset: function() {

		this.$.result.destroyComponents();			

		this.$.result.destroyComponents();	
		//faire aussi pour ceux créés : 
		//this.destroyComponents();

	},
	
	render: function() {
		this.$.result.render();	
	},
		
		
	checkTapped: function(inSender, inEvent) {
		switch (inSender.id) {

		case 100 :
			//document.getElementById(28).setAttribute("enyo-input","control-group success");
			document.getElementById(22).setAttribute("class", "control-group success");
			//alert("Check");
			break;
		case 101 :
			alert("Help");
			break;
		default:
			alert("!!!");
			break;
		}
	},

	handlers: {
		onItemTapped : "itemTapped"
	},
	itemTapped:function(inSender,inEvent){
		myContent = inEvent.originator.content;
		alert(myContent+" button was tapped");
		//this.addText.setContent(myContent);
    }
	
});

