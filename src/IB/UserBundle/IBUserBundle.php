<?php

namespace IB\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class IBUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
