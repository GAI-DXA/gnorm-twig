<?php

namespace Gnorm;

class GnormTwigConfiguration
{
    static public function decode($json_config)
    {
        return json_decode($json_config, true);
    }
}
