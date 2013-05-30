enyo.kind({
	name : "Head",
	kind : enyo.Control,
	components:[
		{tag:'h1', components:[
			{tag: "a", name:"videoTitle", attributes:{'data-placement' :'right', 'data-toggle' :'tooltip'}}
		]}
	], 
	
	published: {
		headTitle: '',
		headDescription: ''
	},

	updateData: function(){
		this.$.videoTitle.content = this.headTitle;
		this.$.videoTitle.render();
		$('a').data('tooltip',false).attr('data-original-title', this.headDescription);
		$("a").tooltip();
	},
	
	create:function(){
		this.inherited(arguments);
	}
	
	
})