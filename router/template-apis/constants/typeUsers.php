<?php

function isAdmin()
{
  return isset($_SESSION['dataUser']) && $_SESSION['dataUser']['rol'] == 1;
}

function isUser()
{
  return isset($_SESSION['dataUser']) && $_SESSION['dataUser']['rol'] == 2;
}
