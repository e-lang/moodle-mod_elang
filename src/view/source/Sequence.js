enyo.kind({
    name: "Sequences",
	tag: "div", 
	classes:"pagination",

	
	published:{
		tabSequences : [],
		idSequenceCourante : null // id sequence courante
	},
	components: [
	{
		components: [
		{
			name: "list",
			tag: "ul"
		},
		{
			name: "TableSeq",
			tag: "table",
			classes: "table table-striped table-bordered table-condensed"			
		}]
	}],
	
    create: function(){
		this.inherited(arguments);
		
    },
	
	updateSequences:function(listSequences){
		if(typeof(listSequences)!='undefined')
		{
			
			for (i in listSequences){
				this.tabSequences[i]=this.createComponent(
					{
						kind:"Sequence",
						id:listSequences[i].id,
						titre:listSequences[i].titre,
						debut:listSequences[i].debut,
						fin:listSequences[i].fin,
						type: "notVerified"
					},
					{owner: this.tabSequences}
				);
			}
		}
		this.letsGo();
	},

    sequenceItemTapped:function(inSender,inEvent){
		this.idSequenceCourante = inSender.name;
		this.bubble("onSequenceItemTapped",inEvent);
    },
	
	createTab: function(title){
	
		this.$.list.createComponent({
			tag:'li',
			class:'active',
			components: [
				{
					tag: "a",
					classes: 'btn-link',
					attributes: {
						href:''
					},
					content: title,
					ontap: 'changeTab'
				}
			]
		},
		{owner: this}
		);
    },
	
	createSequence: function(id, title, type){
		var status;
		if(type=='notVerified') {status = 'error';}
		else if(type=='verified')  {status = 'success';}
		else if(type=='help')  {status = 'warning';}			
		this.$.TBODY.createComponent({
			tag: 'tr',
			name: id,
			classes: status,
			ontap: 'sequenceItemTapped',
			components: [
				{
					tag:'td',
					components: [
						{
							tag: "a",
							classes: 'btn-link',
							attributes: {
								href:''
							},
							content: title
						}
					]
				}
			]
		},
		{owner: this}
		);
    },
	
	createTbody: function(){
		this.$.TableSeq.createComponent({
			tag: 'tbody',
			name: 'TBODY'
		},
		{owner: this}
		);
	},


	deleteTbody: function(){
		this.$.TBODY.destroy();
	},
	
	letsGo: function(){

			nbSequenceDerniereTab = (this.tabSequences.length%10); //modulo pour savoir combien de sequences seront affichées sur la derniere tab
			if(nbSequenceDerniereTab==0) { //si il n'y a pas de séquence sur la dernière tab, soit nombre de séquence division par 10 sans reste
				nbTotalTab = Math.floor(this.tabSequences.length/10); //alors on ne fait rien
			}
			else {
				nbTotalTab = Math.floor(this.tabSequences.length/10)+1; //sinon on ajoute une tab pour afficher les dernière séquences
			}
			for(t=1; t<=nbTotalTab; t++) { //on créer les tab 
				this.createTab(t);
			}
			this.remplissageSequences(0);
    },
	
	changeTab: function(inSender, inEvent){
			nbFirstSequence = (inSender.content-1)*10; //on recupere l'id de la tab, -1 car on ne commence pas à 0
			this.deleteTbody();
			this.remplissageSequences(nbFirstSequence);
    },
	
	remplissageSequences: function(startSequence){
		this.createTbody(); //on créer un tobdy associé à la table pour lui insérer les séquences
			for(s=0; s<10; s++) { //on remplit le tbody de la tab cliquée avec les séquences corresponantes
				nbId = startSequence+s;
				if (typeof (this.tabSequences[nbId]) != 'undefined') {
					this.titre = this.tabSequences[nbId].titre;	
					this.newtitre = (this.titre.length > 100)?this.titre.substring(0,97)+"...":this.titre;
					this.createSequence(this.tabSequences[nbId].id, this.newtitre, this.tabSequences[nbId].type);
				}
			}
			this.render();
	},
	
	setType: function(type)
	{
		for (i in this.tabSequences)
		{
			if(this.tabSequences[i].id ==this.idSequenceCourante)
			{
				this.tabSequences[i].type=type;
			}
		}
		// verified, help
		this.render();
		// mettre à jour
	}
});

enyo.kind({
	name:"Sequence",
	published:{
		id : null,
		titre : null,
		debut : null,
		fin : null,
		type : null
	},
	create: function(){
		this.inherited(arguments);
	}
});
