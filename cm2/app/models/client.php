<?php

class Client extends AppModel
{
	var $name         = 'Client';
	var $useTable     = 'client';
	var $primaryKey   = 'ClientID';
	var $displayField = 'ClientName';
}