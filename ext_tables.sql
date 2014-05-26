
###
# Domain model "Event"
###
CREATE TABLE tx_czsimplecal_domain_model_event (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,


	title varchar(220) DEFAULT '',
	start_day int(11) DEFAULT '0',
	start_time int(11) DEFAULT NULL,
	end_day int(11) DEFAULT NULL,
	end_time int(11) DEFAULT NULL,
	timezone varchar(20) DEFAULT 'GMT',
	teaser text,
	description text,
	images int(11) DEFAULT '0' NOT NULL,
	files int(11) DEFAULT '0' NOT NULL,
	recurrance_type varchar(30) DEFAULT 'none',
	recurrance_subtype varchar(30) DEFAULT '',
	recurrance_until int(11) DEFAULT NULL,
	location int(11) DEFAULT '0',
	location_inline int(11) DEFAULT '0',
	organizer int(11) DEFAULT '0',
	organizer_inline int(11) DEFAULT '0',
	categories int(11) unsigned DEFAULT '0' NOT NULL,
	show_page_instead varchar(255) DEFAULT '' NOT NULL,
	exceptions int(11) unsigned DEFAULT '0' NOT NULL,
	exception_groups int(11) unsigned DEFAULT '0' NOT NULL,
	twitter_hashtags varchar(255) DEFAULT '',
	status varchar(255) DEFAULT NULL,
	flickr_tags varchar(255) DEFAULT '',
	slug varchar(250) DEFAULT '',
	last_indexed int(11) DEFAULT '0',

	cruser_fe int(11) DEFAULT '0',

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,

	t3ver_oid int(11) DEFAULT '0' NOT NULL,
	t3ver_id int(11) DEFAULT '0' NOT NULL,
	t3ver_wsid int(11) DEFAULT '0' NOT NULL,
	t3ver_label varchar(30) DEFAULT '' NOT NULL,
	t3ver_state tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_stage tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_count int(11) DEFAULT '0' NOT NULL,
	t3ver_tstamp int(11) DEFAULT '0' NOT NULL,
	t3ver_move_id int(11) DEFAULT '0' NOT NULL,
	t3_origuid int(11) DEFAULT '0' NOT NULL,

	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l18n_parent int(11) DEFAULT '0' NOT NULL,
	l18n_diffsource mediumblob NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY slug (slug)
);

###
# Domain model "EventIndex"
###
CREATE TABLE tx_czsimplecal_domain_model_eventindex (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	start int(11) NOT NULL DEFAULT '0',
	end int(11) NOT NULL DEFAULT '0',
	event int(11) NOT NULL DEFAULT '0',
	slug varchar(250) DEFAULT '',
	status varchar(255) DEFAULT NULL,
	teaser text,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY slug (slug)
);

###
# Domain model "Exception"
###
CREATE TABLE tx_czsimplecal_domain_model_exception (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	parent_uid int(11) DEFAULT '0' NOT NULL,
	parent_table varchar(255) DEFAULT '' NOT NULL,
	parent_field  varchar(255) DEFAULT '' NOT NULL,

	type varchar(30) DEFAULT 'hide_event',
	title text,
	status varchar(255) DEFAULT NULL,
	teaser text,
	start_day int(11) DEFAULT '0',
	start_time int(11) DEFAULT NULL,
	end_day int(11) DEFAULT NULL,
	end_time int(11) DEFAULT NULL,
	timezone varchar(20) DEFAULT 'GMT',
	recurrance_type varchar(30) DEFAULT 'none',
	recurrance_subtype varchar(30) DEFAULT '',
	recurrance_until int(11) DEFAULT NULL,

	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parentpage (pid),
	KEY parentrecord (parent_uid,parent_table,parent_field)
);

###
# Domain model "ExceptionGroup"
###
CREATE TABLE tx_czsimplecal_domain_model_exceptiongroup (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	title text,
	exceptions int(11) unsigned DEFAULT '0' NOT NULL,

	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);

###
# Relation Table "Event" to "ExceptionGroup"
###
CREATE TABLE tx_czsimplecal_event_exceptiongroup_mm (
	uid int(10) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	uid_local int(11) unsigned DEFAULT '0' NOT NULL,
	uid_foreign int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,
	sorting_foreign int(11) unsigned DEFAULT '0' NOT NULL,

	tstamp int(10) unsigned DEFAULT '0' NOT NULL,
	crdate int(10) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(3) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);


###
# Domain model "Category"
###
CREATE TABLE tx_czsimplecal_domain_model_category (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,


	title text,
	show_page_instead varchar(255) DEFAULT '' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,

	t3ver_oid int(11) DEFAULT '0' NOT NULL,
	t3ver_id int(11) DEFAULT '0' NOT NULL,
	t3ver_wsid int(11) DEFAULT '0' NOT NULL,
	t3ver_label varchar(30) DEFAULT '' NOT NULL,
	t3ver_state tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_stage tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_count int(11) DEFAULT '0' NOT NULL,
	t3ver_tstamp int(11) DEFAULT '0' NOT NULL,
	t3ver_move_id int(11) DEFAULT '0' NOT NULL,
	t3_origuid int(11) DEFAULT '0' NOT NULL,

	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l18n_parent int(11) DEFAULT '0' NOT NULL,
	l18n_diffsource mediumblob NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);

###
# Domain model "Address"
###
CREATE TABLE tx_czsimplecal_domain_model_address (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	name varchar(255) DEFAULT '' NOT NULL,
	address text,
	zip varchar(10) DEFAULT '' NOT NULL,
	city varchar(255) DEFAULT '' NOT NULL,
	country varchar(3) DEFAULT '' NOT NULL,
	event_uid int(11) unsigned DEFAULT '0' NOT NULL,
	event_field varchar(255) DEFAULT '' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,

	t3ver_oid int(11) DEFAULT '0' NOT NULL,
	t3ver_id int(11) DEFAULT '0' NOT NULL,
	t3ver_wsid int(11) DEFAULT '0' NOT NULL,
	t3ver_label varchar(30) DEFAULT '' NOT NULL,
	t3ver_state tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_stage tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_count int(11) DEFAULT '0' NOT NULL,
	t3ver_tstamp int(11) DEFAULT '0' NOT NULL,
	t3ver_move_id int(11) DEFAULT '0' NOT NULL,
	t3_origuid int(11) DEFAULT '0' NOT NULL,

	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l18n_parent int(11) DEFAULT '0' NOT NULL,
	l18n_diffsource mediumblob NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);

###
# Relation Table "Event" to "Category"
###
CREATE TABLE tx_czsimplecal_event_category_mm (
	uid int(10) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	uid_local int(11) unsigned DEFAULT '0' NOT NULL,
	uid_foreign int(11) unsigned DEFAULT '0' NOT NULL,
	tablenames varchar(255) DEFAULT '' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,
	sorting_foreign int(11) unsigned DEFAULT '0' NOT NULL,

	tstamp int(10) unsigned DEFAULT '0' NOT NULL,
	crdate int(10) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(3) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);


