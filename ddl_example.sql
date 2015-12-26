
DROP TABLE IF EXISTS `session`;

CREATE TABLE `session` (
  `session_id` varchar(64) NOT NULL,
  `login_id` varchar(255) DEFAULT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `last_access_dt` datetime DEFAULT NULL,
  `session_data` longtext,
  `input_dt` datetime DEFAULT NULL,
  `ip_addr` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


ALTER TABLE `session`
  ADD PRIMARY KEY (`session_id`),
  ADD KEY `USER_ID` (`user_id`),
  ADD KEY `LOGIN_ID` (`login_id`);

DROP TABLE IF EXISTS `user`;

CREATE TABLE `user` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `login_id` varchar(255) DEFAULT NULL,
  `user_name` varchar(255) DEFAULT NULL,
  `user_password` varchar(255) DEFAULT NULL,
  `input_dt` datetime DEFAULT NULL,
  `last_login_dt` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`);

