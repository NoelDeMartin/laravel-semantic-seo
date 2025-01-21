<?php

namespace NoelDeMartin\SemanticSEO\Types;

use NoelDeMartin\SemanticSEO\Types\Concerns\HasTwitterHandle as HasTwitterHandleTrait;
use NoelDeMartin\SemanticSEO\Types\Contracts\HasTwitterHandle;

class Person extends Thing implements HasTwitterHandle
{
    use HasTwitterHandleTrait;
}
