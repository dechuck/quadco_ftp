<?php

  /***********************
  * Core Database Tables *
  ***********************/
  define('DB_TABLE_USERS',         'users');
  define('DB_TABLE_SESSIONS',      'session_data');
  define('DB_TABLE_LOGS',          'admin_log');
  define('DB_TABLE_PAGE_VARS',     'page_vars');
  
  /********************
  * WoW Server Tables *
  ********************/
  define('DB_TABLE_SERVER_CATS',  	'server_categories');
  define('DB_TABLE_SERVER_LANGS', 	'server_languages');
  define('DB_TABLE_SERVER_LIST',  	'server_list');
  define('DB_TABLE_SERVER_REGIONS', 'server_regions');
  define('DB_TABLE_SERVER_TYPES',	'server_types');
  
  /*********************************
  * Recount Module Database Tables *
  *********************************/
  define('DB_TABLE_PARSE_TEMP',  			'parse_temp');
  define('DB_TABLE_PARSE_QUEUE',			'parse_queue');
  define('DB_TABLE_PARSE_LIST',				'parse_list');
  define('DB_TABLE_PARSE_PLAYERS', 			'parse_players');
  define('DB_TABLE_PARSE_GUILDS', 			'parse_guilds');
  define('DB_TABLE_PARSE_GUILDS_RANKS', 	'parse_guild_ranks');
  define('DB_TABLE_PARSE_GUILDS_INVITES',	'parse_guilds_invites');
  define('DB_TABLE_PARSE_PETS',				'parse_pets');
  define('DB_TABLE_PARSE_ENCOUNTERS', 		'parse_encounters');
  define('DB_TABLE_PARSE_ATTEMPTS',			'parse_attempts');
  define('DB_TABLE_PARSE_PARTICIPANTS', 	'parse_participants');
  define('DB_TABLE_PARSE_DEATHS', 			'parse_deaths');
  define('DB_TABLE_PARSE_COMPARE', 			'parse_compare');
  define('DB_TABLE_ENCOUNTER_TRIGGERS', 	'encounter_triggers');
  define('DB_TABLE_ENCOUNTER_LIST',			'encounter_list');
  define('DB_TABLE_ENCOUNTER_ZONES',		'encounter_zones');
  define('DB_TABLE_GAME_SPECS',				'game_specs');
  
  /******************
  * Feedback Tables *
  ******************/
  define('DB_TABLE_FEEDBACK_LOG', 'feedback_log');
  define('DB_TABLE_FEEDBACK_USERS', 'feedback_users');

  /********************************
  * WoWHead Cache Database Tables *
  ********************************/
  define('DB_TABLE_CACHE_ITEMS',  'cache_items');
  define('DB_TABLE_CACHE_SPELLS', 'cache_spells');
  
  /*******************************
  * Forum Module Database Tables *
  *******************************/
  define('DB_TABLE_FORUM_CATS',    'forum_cats');
  define('DB_TABLE_FORUM',         'forum_sub');
  define('DB_TABLE_FORUM_TOPICS',  'forum_topics');
  define('DB_TABLE_FORUM_POSTS',   'forum_posts');
  define('DB_TABLE_FORUM_READ',    'forum_readposts');
  define('DB_TABLE_NEWS_CATS',	   'forum_newscats');

?>