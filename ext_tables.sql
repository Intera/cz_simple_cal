###
# Domain model "Event"
###
CREATE TABLE tx_czsimplecal_domain_model_event (
	title VARCHAR(220) DEFAULT '',
	start_day INT(11) DEFAULT '0',
	start_time INT(11) DEFAULT NULL,
	end_day INT(11) DEFAULT NULL,
	end_time INT(11) DEFAULT NULL,
	event_languages VARCHAR(255) DEFAULT '',
	timezone VARCHAR(20) DEFAULT 'GMT',
	teaser TEXT,
	description TEXT,
	images INT(11) DEFAULT '0' NOT NULL,
	files INT(11) DEFAULT '0' NOT NULL,
	recurrance_type VARCHAR(30) DEFAULT 'none',
	recurrance_subtype VARCHAR(30) DEFAULT '',
	recurrance_until INT(11) DEFAULT NULL,
	location VARCHAR(255) DEFAULT '',
	location_inline INT(11) DEFAULT '0',
	organizer VARCHAR(255) DEFAULT '',
	organizer_inline INT(11) DEFAULT '0',
	categories INT(11) UNSIGNED DEFAULT '0' NOT NULL,
	show_page_instead VARCHAR(255) DEFAULT '' NOT NULL,
	exceptions INT(11) UNSIGNED DEFAULT '0' NOT NULL,
	exception_groups INT(11) UNSIGNED DEFAULT '0' NOT NULL,
	twitter_hashtags VARCHAR(255) DEFAULT '',
	status VARCHAR(255) DEFAULT NULL,
	flickr_tags VARCHAR(255) DEFAULT '',
	slug VARCHAR(250) DEFAULT '' NOT NULL,
	last_indexed INT(11) DEFAULT '0',

	cruser_fe INT(11) DEFAULT '0',
	crgroup_fe INT(11) DEFAULT '0',

	KEY slug(slug)
);

###
# Domain model "EventIndex"
###
CREATE TABLE tx_czsimplecal_domain_model_eventindex (
	start INT(11) NOT NULL DEFAULT '0',
	end INT(11) NOT NULL DEFAULT '0',
	event INT(11) NOT NULL DEFAULT '0',
	slug VARCHAR(250) DEFAULT '',
	status VARCHAR(255) DEFAULT NULL,
	teaser TEXT,

	KEY slug(slug)
);

###
# Domain model "Exception"
###
CREATE TABLE tx_czsimplecal_domain_model_exception (
	parent_uid INT(11) DEFAULT '0' NOT NULL,
	parent_table VARCHAR(255) DEFAULT '' NOT NULL,
	parent_field VARCHAR(255) DEFAULT '' NOT NULL,

	type VARCHAR(30) DEFAULT 'hide_event',
	title TEXT,
	status VARCHAR(255) DEFAULT NULL,
	teaser TEXT,
	start_day INT(11) DEFAULT '0',
	start_time INT(11) DEFAULT NULL,
	end_day INT(11) DEFAULT NULL,
	end_time INT(11) DEFAULT NULL,
	timezone VARCHAR(20) DEFAULT 'GMT',
	recurrance_type VARCHAR(30) DEFAULT 'none',
	recurrance_subtype VARCHAR(30) DEFAULT '',
	recurrance_until INT(11) DEFAULT NULL,

	KEY parentrecord(parent_uid, parent_table, parent_field)
);

###
# Domain model "ExceptionGroup"
###
CREATE TABLE tx_czsimplecal_domain_model_exceptiongroup (
	title TEXT,
	exceptions INT(11) UNSIGNED DEFAULT '0' NOT NULL
);

###
# Relation Table "Event" to "ExceptionGroup"
###
CREATE TABLE tx_czsimplecal_event_exceptiongroup_mm (
	uid INT(10) NOT NULL AUTO_INCREMENT,
	pid INT(11) DEFAULT '0' NOT NULL,

	uid_local INT(11) UNSIGNED DEFAULT '0' NOT NULL,
	uid_foreign INT(11) UNSIGNED DEFAULT '0' NOT NULL,
	sorting INT(11) UNSIGNED DEFAULT '0' NOT NULL,
	sorting_foreign INT(11) UNSIGNED DEFAULT '0' NOT NULL,

	tstamp INT(10) UNSIGNED DEFAULT '0' NOT NULL,
	crdate INT(10) UNSIGNED DEFAULT '0' NOT NULL,
	hidden TINYINT(3) UNSIGNED DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent(pid)
);


###
# Domain model "Category"
###
CREATE TABLE tx_czsimplecal_domain_model_category (
	color VARCHAR(10) DEFAULT '' NOT NULL,
	title TEXT,
	show_page_instead VARCHAR(255) DEFAULT '' NOT NULL
);

###
# Domain model "Address"
###
CREATE TABLE tx_czsimplecal_domain_model_address (
	name VARCHAR(255) DEFAULT '' NOT NULL,
	address TEXT,
	zip VARCHAR(10) DEFAULT '' NOT NULL,
	city VARCHAR(255) DEFAULT '' NOT NULL,
	country VARCHAR(3) DEFAULT '' NOT NULL,
	homepage VARCHAR(255) DEFAULT '' NOT NULL,
	event_uid INT(11) UNSIGNED DEFAULT '0' NOT NULL,
	event_field VARCHAR(255) DEFAULT '' NOT NULL
);

###
# Relation Table "Event" to "Category"
###
CREATE TABLE tx_czsimplecal_event_category_mm (
	uid INT(10) NOT NULL AUTO_INCREMENT,
	pid INT(11) DEFAULT '0' NOT NULL,

	uid_local INT(11) UNSIGNED DEFAULT '0' NOT NULL,
	uid_foreign INT(11) UNSIGNED DEFAULT '0' NOT NULL,
	tablenames VARCHAR(255) DEFAULT '' NOT NULL,
	sorting INT(11) UNSIGNED DEFAULT '0' NOT NULL,
	sorting_foreign INT(11) UNSIGNED DEFAULT '0' NOT NULL,

	tstamp INT(10) UNSIGNED DEFAULT '0' NOT NULL,
	crdate INT(10) UNSIGNED DEFAULT '0' NOT NULL,
	hidden TINYINT(3) UNSIGNED DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent(pid)
);
