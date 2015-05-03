CREATE TABLE IF NOT EXISTS `allegro_maze` (
  `maze_id` int(11) NOT NULL AUTO_INCREMENT,
  `maze_body` varchar(2024) COLLATE utf8_unicode_ci NOT NULL COMMENT 'encoded in json',
  `maze_entrance` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'encoded in json',
  PRIMARY KEY (`maze_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;