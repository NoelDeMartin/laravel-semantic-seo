<?php

namespace NoelDeMartin\SemanticSEO\Types;

use NoelDeMartin\SemanticSEO\Types\Contracts\HasTwitterHandle;
use NoelDeMartin\SemanticSEO\Types\Concerns\HasTwitterHandle as HasTwitterHandleTrait;

class Person extends Thing implements HasTwitterHandle
{
    use HasTwitterHandleTrait;
}
