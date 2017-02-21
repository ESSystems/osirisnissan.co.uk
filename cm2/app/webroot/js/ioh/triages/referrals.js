/**
 *
 */

Ext.ns('IOH.Triages');

IOH.Triages.Referrals = Ext.extend(Ext.TabPanel,
{
	initComponent: function () {
		var cfg = {
			border: false,
			plain: true,
			deferredRender1: true,
			defaults: {
				xtype: 'container',
				layout: 'border',
				border: false
			},
			items: [{
				title: 'New',
				ref: 'tabNew',
				items: [
					new IOH.Triages.Referrals.Grid({
						type: 'new',
						region: 'center',
						ref: 'grid'
					}), new IOH.Triages.Referrals.Details.Referral({
						region: 'south',
						type: 'new',
						collapsible: true,
				        collapsed: true,
				        listeners: {
				        	'bind': function() {this.expand();}
				        }
					})
				]
			},{
				title: 'Accepted',
				ref: 'tabAccepted',
				items: [
					new IOH.Triages.Referrals.Grid({
						type: 'accepted',
						ref: 'grid',
						region: 'center'
					}), new IOH.Triages.Referrals.Details.Referral({
						region: 'south',
						type: 'accepted',
						collapsible: true,
				        collapsed: true,
				        listeners: {
				        	'bind': function() {
				        		this.expand();
				        	}
				        }
					})
				]
			},{
				title: 'Declined',
				ref: 'tabDeclined',
				items: [
					new IOH.Triages.Referrals.Grid({
						region: 'center',
						type: 'declined',
						ref: 'grid'
					}), new IOH.Triages.Referrals.Details.Referral({
						region: 'south',
						type: 'declined',
						collapsible: true,
				        collapsed: true,
				        listeners: {
				        	'bind': function() {
				        		this.expand();
				        	}
				        }
					})
				]
			},{
				title: 'Cancelled',
				ref: 'tabCanceled',
				items: [
					new IOH.Triages.Referrals.Grid({
						region: 'center',
						type: 'canceled',
						ref: 'grid'
					}), new IOH.Triages.Referrals.Details.Referral({
						region: 'south',
						type: 'canceled',
						collapsible: true,
						collapsed: true,
						listeners: {
							'bind': function() {
								this.expand();
							}
						}
					})
				]
			}],
			activeTab: 0
		};

		Ext.apply(this, cfg);

		IOH.Triages.Referrals.superclass.initComponent.apply(this, arguments);

		this.tabNew.details.relayEvents(this.tabNew.grid, ['bind','unbind']);
		this.tabNew.grid.relayEvents(this.tabNew, ['activate']);

		this.tabAccepted.details.relayEvents(this.tabAccepted.grid, ['bind','unbind']);
		this.tabAccepted.grid.relayEvents(this.tabAccepted, ['activate']);

		this.tabDeclined.details.relayEvents(this.tabDeclined.grid, ['bind','unbind']);
		this.tabDeclined.grid.relayEvents(this.tabDeclined, ['activate']);

		this.tabCanceled.details.relayEvents(this.tabCanceled.grid, ['bind','unbind']);
		this.tabCanceled.grid.relayEvents(this.tabCanceled, ['activate']);

		this.relayEvents(this.tabNew.details, ['accept', 'decline', 'referral_changed']);
		this.relayEvents(this.tabAccepted.details, ['referral_changed']);
		this.relayEvents(this.tabDeclined.details, ['referral_changed']);
		this.relayEvents(this.tabCanceled.details, ['referral_changed']);

		this.on({
			accept: this.onAccept,
			decline: this.onDecline,
			referral_changed: this.onReferralChanged,
			scope: this
		});
	},

	onAccept: function (rec) {
		IOH.Appointments.Window.show(rec, {
			success: function () {
				this.tabNew.grid.store.reload();
				Ext.Msg.alert('Success', 'Referral accepted. Now make an appointment, please.');
				IOH.APP.navigator().go('diary-section');
			},
			scope: this
		});
	},

	onDecline: function (rec) {
		IOH.Declinations.Window.show(rec, {
			success: function () {
				this.tabNew.grid.store.reload();
			},
			scope: this
		});
	},

	onReferralChanged: function() {
		currentTab = eval("this." + this.getActiveTab().ref);
		detailsTab = currentTab.details;
		detailsTab.el.mask('Reloading changes...', 'x-mask-loading');
		currentTab.grid.store.reload();
		currentTab.grid.store.on('load', function() {
			detailsTab.el.unmask(true);
		});
	}
});

Ext.reg('IOH.Triages.Referrals', IOH.Triages.Referrals);