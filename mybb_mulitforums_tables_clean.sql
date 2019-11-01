SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `ads`
-- ----------------------------
DROP TABLE IF EXISTS `ads`;
CREATE TABLE `ads` (
  `id` int(11) NOT NULL auto_increment,
  `adname` varchar(50) default NULL,
  `text` varchar(3000) default NULL,
  `impressions` int(11) default NULL,
  `actualimpressions` int(11) default NULL,
  `created` date default NULL,
  `status` varchar(15) default NULL,
  `forumheader` int(11) default '0',
  `forumfooter` int(11) default '0',
  `site` int(11) default '0',
  `custom` int(11) default '0',
  PRIMARY KEY  (`id`),
  KEY `status` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of ads
-- ----------------------------

-- ----------------------------
-- Table structure for `dbcons`
-- ----------------------------
DROP TABLE IF EXISTS `dbcons`;
CREATE TABLE `dbcons` (
  `id` int(11) NOT NULL auto_increment,
  `conname` varchar(150) default NULL,
  `dbname` varchar(60) default NULL,
  `issignupdb` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dbcons
-- ----------------------------
INSERT INTO `dbcons` VALUES ('1', 'dbmain', 'YOUR_PRIMARY_DATABASE', '1');

-- ----------------------------
-- Table structure for `domainmap`
-- ----------------------------
DROP TABLE IF EXISTS `domainmap`;
CREATE TABLE `domainmap` (
  `id` int(11) NOT NULL auto_increment,
  `forumname` varchar(20) default NULL,
  `domain` varchar(100) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of domainmap
-- ----------------------------

-- ----------------------------
-- Table structure for `links`
-- ----------------------------
DROP TABLE IF EXISTS `links`;
CREATE TABLE `links` (
  `id` int(11) NOT NULL auto_increment,
  `linktext` varchar(150) default NULL,
  `linkurl` varchar(250) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of links
-- ----------------------------
INSERT INTO `links` VALUES ('1', 'Home', 'index.php');
INSERT INTO `links` VALUES ('2', 'Create Forum', 'create.php');
INSERT INTO `links` VALUES ('6', 'Terms of Service', 'tos.php');
INSERT INTO `links` VALUES ('7', 'MyBB Multiforums', 'http://www.rusnakweb.com/forum');

-- ----------------------------
-- Table structure for `settings`
-- ----------------------------
DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(20) default NULL,
  `value` varchar(350) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of settings
-- ----------------------------
INSERT INTO `settings` VALUES ('1', 'isinstalled', 'yes');
INSERT INTO `settings` VALUES ('2', 'version', '3.0.0b1');
INSERT INTO `settings` VALUES ('3', 'templateurl', 'templates/default/template.htm');
INSERT INTO `settings` VALUES ('4', 'browsertitle', 'MyBB Multiforums Mod 3.0.0 BETA');
INSERT INTO `settings` VALUES ('5', 'sitename', 'MyBB Multiforums Mod Demonstration Site');
INSERT INTO `settings` VALUES ('6', 'admincontact', 'any@email.com');
INSERT INTO `settings` VALUES ('7', 'systememail', 'any@email.com');
INSERT INTO `settings` VALUES ('8', 'paypalemail', 'any@email.com');
INSERT INTO `settings` VALUES ('9', 'trialpayemail', 'any@email.com');
INSERT INTO `settings` VALUES ('10', 'slogan', 'Your Site Slogan Here');
INSERT INTO `settings` VALUES ('11', 'allowregs', 'yes');
INSERT INTO `settings` VALUES ('12', 'mforumprefix', 'mybb_');
INSERT INTO `settings` VALUES ('13', 'uforumprefix', 'mybb_');
INSERT INTO `settings` VALUES ('14', 'enableregs', 'yes');
INSERT INTO `settings` VALUES ('15', 'verifymethod', 'emailandcaptcha');
INSERT INTO `settings` VALUES ('16', 'recaptchakey', 'RECAPTCHA_PUBLIC_KEY');
INSERT INTO `settings` VALUES ('17', 'recaptchapkey', 'RECAPTCHA_PRIVATE_KEY');
INSERT INTO `settings` VALUES ('18', 'backupmode', 'free');
INSERT INTO `settings` VALUES ('19', 'enabledirectory', 'yes');
INSERT INTO `settings` VALUES ('20', 'supporturl', '');
INSERT INTO `settings` VALUES ('21', 'showlogin', 'yes');
INSERT INTO `settings` VALUES ('22', 'regurl', 'URL TO YOUR MYBB FORUM WITH USER ACCOUNTS');
INSERT INTO `settings` VALUES ('23', 'reqlogintomake', 'no');
INSERT INTO `settings` VALUES ('24', 'pathtomybbfiles', 'mybb');
INSERT INTO `settings` VALUES ('25', 'newusedb', 'YOUR_PRIMARY_DATABASE');
INSERT INTO `settings` VALUES ('26', 'notfounderr', '<br><center>The forum you are looking for cannot be found.  It may have been suspended or deleted.</center>');
INSERT INTO `settings` VALUES ('27', 'createaddiv', 'yes');

-- ----------------------------
-- Table structure for `site_content`
-- ----------------------------
DROP TABLE IF EXISTS `site_content`;
CREATE TABLE `site_content` (
  `id` int(11) NOT NULL auto_increment,
  `page` varchar(20) default NULL,
  `title` varchar(50) default NULL,
  `value` varchar(3500) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of site_content
-- ----------------------------
INSERT INTO `site_content` VALUES ('1', 'index', 'Welcome to the MyBB Multiforums Mod', 'Welcome to the MyBB Multiforums Mod.  This is your site\\\'s index page content.  You can change all of this content in your site\\\'s Admin CP.');
INSERT INTO `site_content` VALUES ('2', 'tos', 'TOS Page Content', 'Here you can put your Terms of Service.');

-- ----------------------------
-- Table structure for `site_hosted_forums`
-- ----------------------------
DROP TABLE IF EXISTS `site_hosted_forums`;
CREATE TABLE `site_hosted_forums` (
  `id` int(11) NOT NULL auto_increment,
  `forumname` varchar(20) default NULL,
  `email` varchar(50) default NULL,
  `assocaccount` varchar(50) default NULL,
  `created` date default NULL,
  `fromip` varchar(30) default NULL,
  `actcode` varchar(40) default NULL,
  `actstatus` int(11) NOT NULL default '0',
  `backupmode` int(11) default NULL,
  `adstatus` varchar(50) default NULL,
  `adfreeimptotal` int(11) NOT NULL default '0',
  `adfreeimpused` int(11) NOT NULL default '0',
  `adfreeuntil` date default NULL,
  `usesdbconn` varchar(60) default NULL,
  PRIMARY KEY  (`id`),
  KEY `forumname` (`forumname`),
  KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of site_hosted_forums
-- ----------------------------

-- ----------------------------
-- Table structure for `themes`
-- ----------------------------
DROP TABLE IF EXISTS `themes`;
CREATE TABLE `themes` (
  `id` int(11) NOT NULL auto_increment,
  `themeName` varchar(200) NOT NULL,
  `themeURL` varchar(300) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of themes
-- ----------------------------

-- ----------------------------
-- Table structure for `reserved_access_names`
-- ----------------------------
DROP TABLE IF EXISTS `reserved_access_names`;
CREATE TABLE `reserved_access_names` (
  `id` int(11) NOT NULL auto_increment,
  `forumname` varchar(20) default NULL,
  `reasonforreserve` varchar(250) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of reserved_access_names
-- ----------------------------
INSERT INTO `reserved_access_names` VALUES ('1', 'install', 'Forums with install in the name break the system.');
INSERT INTO `reserved_access_names` VALUES ('2', 'insttst', 'Test forum name.');
INSERT INTO `reserved_access_names` VALUES ('3', 'admin', 'I think we know why this is.');
INSERT INTO `reserved_access_names` VALUES ('4', 'mybb', 'This will mess something up for sure unless we block it.');

