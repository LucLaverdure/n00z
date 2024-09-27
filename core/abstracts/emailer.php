<?php

namespace MeshMVC;

abstract class Emailer {

    public abstract function send($to, $subject, $message, $when);
    public abstract function cron(); // send mail as cron job

}