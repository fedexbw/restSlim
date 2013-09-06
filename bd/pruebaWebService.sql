--
-- Base de datos: `pruebaWebService`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_fullname` varchar(25) NOT NULL,
  `user_email` varchar(50) NOT NULL,
  `user_password` varchar(50) NOT NULL,
  `user_status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`user_id`, `user_fullname`, `user_email`, `user_password`, `user_status`) VALUES
(1, 'fatiga', 'fati@gmai.com', '123456', 1),
(2, 'vanes', 'vane@gmail.com', 'asdfgh', 1),
(3, 'Pepe Argento', 'pepe@gmail.com', 'xxx123', 0),
(4, 'Moni Argento', 'moni@gmail.com', 'yyy456', 0);