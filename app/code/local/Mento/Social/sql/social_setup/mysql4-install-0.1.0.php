<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
create table IF NOT EXISTS mento (mento_id int not null auto_increment, mento_value varchar(250), primary key(mento_id));
    insert into mento values(1,'');
    insert into mento values(2,'');
		
SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 