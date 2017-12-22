create exemple;

use exemple;

CREATE TABLE `users` (
  `id` int(11) NOT NULL auto_increment,
  `author` varchar(100) NOT NULL,
  `title` varchar(100) NOT NULL,
  `message` LONGTEXT NOT NULL,
  `picture` varchar(200) NULL,
  `link` LONGTEXT NULL,
  PRIMARY KEY  (`id`)
);
