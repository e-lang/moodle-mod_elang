enyo.kind({
	name: 'Elang.Cues',

	published:
	{
		cues: [],
		page: 10
	},

	current: null,

	events: {onCueTapped: ''},

	components:
	[
		{
			// Pagination
			classes: 'pagination pagination-centered',
			components: [{name: 'pagination', tag: 'ul'}]
		},

		{
			tag: 'table',
			classes: 'table table-bordered table-condensed table-striped',
			components:
			[
				{tag: 'caption', content: $L('Cue Listing')},
				{
					tag: 'thead',
					components:
					[
						{
							tag: 'tr',
							components:
							[
								{tag: 'th', attributes: {width: '7%'}, content: $L('#')},
								{tag: 'th', content: $L('Title')}
							]
						}
					]
				},
				// Body
				{tag: 'tbody', name: 'body'},
			]
		}
	],

	/**
	 * Change the number of pages
	 *
	 * @param  pagination  integer  New number of pages
	 *
	 * @return  this
	 */
	setPagination: function (pagination)
	{
		this.$.pagination.destroyClientControls();
		if (pagination > 1)
		{
			for (var i=1; i<=pagination; i++)
			{
				this.$.pagination.createComponent(
					{
						tag: 'li',
						classes: i == 1 ? 'active' : '',
						components:
						[{tag: 'a', ontap: 'paginationTapped', attributes: {href: '#'}, content: i}]
					},
					{owner: this}
				);
			}
			this.$.pagination.show();
		}
		else
		{
			this.$.pagination.hide();
		}
		return this;
	},

	/**
	 * Handle tap event on a cue
	 *
	 * @param  inSender  enyo.instance  Sender of the event
	 * @param  inEvent   Object		 Event fired
	 *
	 * @return void
	 */
	cueTapped: function(inSender, inEvent)
	{
		for (var i in this.$.body.children)
		{
			this.$.body.children[i].removeClass('info');
		}
		if (this.current == inSender.cue)
		{
			this.current = null;
			this.doCueTapped({cue: null});
		}
		else
		{
			inSender.parent.parent.addClass('info');
			this.current = inSender.cue;
			this.doCueTapped({cue: inSender.cue});
		}
	},

	/**
	 * Handle tap event on a page number
	 *
	 * @param  inSender  enyo.instance  Sender of the event
	 * @param  inEvent   Object		 Event fired
	 *
	 * @return void
	 */
	paginationTapped: function(inSender, inEvent)
	{
		for (var i in this.$.pagination.children)
		{
			this.$.pagination.children[i].removeClass('active');
		}
		inSender.parent.addClass('active');
		this.$.body.destroyClientControls();
		this.setTables(
			(inSender.content - 1) * this.page, this.cues.slice((inSender.content - 1) * this.page,
			inSender.content * this.page)
		);
		this.$.body.render();
	},

	/**
	 * Fill the table
	 *
	 * @param  start	 integer  Start of the table
	 * @param  elements  array	Elements of the table
	 *
	 * @return  this
	 */
	setTables: function(start, elements)
	{
		for (var i in elements)
		{
			this.$.body.createComponent(
				{
					tag: 'tr',
					components:
					[
						{tag: 'td', content: start + parseInt(i) + 1},
						{
							tag: 'td',
							components:
							[
								{
									tag: 'a',
									ontap: 'cueTapped',
									cue: this.cues[start + parseInt(i)],
									attributes:
									{
										href:'#'
									},
									content: elements[i].title
								}
							]
						}
					]
				},
				{owner: this}
			);
		}
		return this;
	},

	/**
	 * Update the element
	 *
	 * @return this
	 */
	update: function ()
	{
		this.setPagination(((this.cues.length / this.page) | 0) + (this.cues.length % this.page > 0 ? 1 : 0));
		this.setTables(0, this.cues.slice(0, this.page));
		return this.render();
	},

	//Changement du type 'notVerified', 'verified', 'help'
	setType: function(type)
	{
		//On cherche la séquence courante (dans tabCues)
		for (i in this.tabCues)
		{
			if(this.tabCues[i].id ==this.idCueCourante)
			{
				//Changement du type de la séquence
				this.tabCues[i].type=type;

				var status;
				if(type=='notVerified') {status = 'error';}
				else if(type=='verified')  {status = 'success';}
				else if(type=='help')  {status = 'warning';}

				//On récupère la ligne avec l'id de la séquence courante
				var ligne = document.getElementById('app_cues_'+this.tabCues[i].id);
				ligne.className = status;
			}
		}
	}
});
